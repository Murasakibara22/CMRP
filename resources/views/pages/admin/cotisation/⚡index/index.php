<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\Cotisation;
use App\Models\Customer;
use App\Models\TypeCotisation;
use App\Models\HistoriqueCotisation;
use App\Traits\UtilsSweetAlert;
use Carbon\Carbon;

new  class extends Component
{
    use WithPagination, UtilsSweetAlert;

    /* ── Filtres liste ──────────────────────────────────── */
    public string $search      = '';
    public string $tabStatut   = 'tous';
    public string $filterType  = 'tous';
    public string $filterMois  = 'tous';
    public string $filterMode  = 'tous';

    /* ── Modal détail ───────────────────────────────────── */
    public ?int $detailId = null;

    /* ── Modal créer / modifier ─────────────────────────── */
    public ?int    $editId               = null;   // null = création, int = modification
    public ?int    $customerId           = null;
    public string  $searchFidele        = '';
    public ?int    $typeCotisationId    = null;
    public ?int    $mois                 = null;
    public ?int    $annee                = null;
    public ?int    $montantPaye          = null;
    public string  $modePaiement         = '';
    public string  $reference            = '';
    public bool    $validerImmediatement = true;
    public bool    $alerteEngagement     = false;

    /* ── Reset pagination ───────────────────────────────── */
    public function updatedSearch(): void      { $this->resetPage(); }
    public function updatedTabStatut(): void   { $this->resetPage(); }
    public function updatedFilterType(): void  { $this->resetPage(); }
    public function updatedFilterMois(): void  { $this->resetPage(); }
    public function updatedFilterMode(): void  { $this->resetPage(); }

    /* ═══════════════════════════════════════════════════════
       MODAL DÉTAIL
    ═══════════════════════════════════════════════════════ */
    public function openDetail(int $id): void
    {
        $this->detailId = $id;
        $this->launch_modal('modalDetailCotisation');
    }

    /* ═══════════════════════════════════════════════════════
       MODAL CRÉER
    ═══════════════════════════════════════════════════════ */
    public function openCreate(?int $customerId = null): void
    {
        $this->resetForm();
        $this->mois  = now()->month;
        $this->annee = now()->year;
        if ($customerId) {
            $this->customerId = $customerId;
        }
        $this->launch_modal('modalCreateCotisation');
    }

    /* ═══════════════════════════════════════════════════════
       MODAL MODIFIER (si non encore validée)
    ═══════════════════════════════════════════════════════ */
    public function openEdit(int $id): void
    {
        $cot = Cotisation::with(['customer', 'typeCotisation'])->findOrFail($id);

        // Sécurité : on ne peut modifier que si non validée
        if ($cot->validated_at) {
            $this->send_event_at_sweet_alert_not_timer(
                'Action impossible',
                'Cette cotisation a déjà été validée. Elle ne peut plus être modifiée.',
                'warning'
            );
            return;
        }

        $this->resetForm();
        $this->editId            = $id;
        $this->customerId        = $cot->customer_id;
        $this->typeCotisationId  = $cot->type_cotisation_id;
        $this->mois              = $cot->mois;
        $this->annee             = $cot->annee;
        $this->montantPaye       = $cot->montant_paye;
        $this->modePaiement      = $cot->mode_paiement ?? '';
        $this->reference         = $cot->reference ?? '';
        $this->validerImmediatement = false;

        $this->launch_modal('modalCreateCotisation');
    }

    /* ── Recherche fidèle live ──────────────────────────── */
    public function selectFidele(int $id): void
    {
        $this->customerId       = $id;
        $this->searchFidele     = '';
        $this->alerteEngagement = false;
    }

    public function selectMode(string $mode): void
    {
        $this->modePaiement = $mode;
    }

    /* ═══════════════════════════════════════════════════════
       SAVE — Création + logique report mensuel complète
    ═══════════════════════════════════════════════════════ */
    public function save(): void
    {
        $this->validate([
            'customerId'       => 'required|integer|exists:customers,id',
            'typeCotisationId' => 'required|integer|exists:type_cotisation,id',
            'montantPaye'      => 'required|integer|min:1',
            'modePaiement'     => 'required|string|in:espece,mobile_money,virement',
        ]);

        $customer = Customer::findOrFail($this->customerId);
        $tc       = TypeCotisation::findOrFail($this->typeCotisationId);

        /* ── Mode MODIFICATION ──────────────────────────── */
        if ($this->editId) {
            $this->_updateCotisation($customer, $tc);
            return;
        }

        /* ── Mode CRÉATION ──────────────────────────────── */
        $this->_createCotisation($customer, $tc);
    }

    /* ── Création avec report mensuel ───────────────────── */
    private function _createCotisation(Customer $customer, TypeCotisation $tc): void
    {
        $isMensuel = $tc->type === 'mensuel';

        /* Vérif engagement obligatoire */
        if ($isMensuel && $tc->is_required && ! $customer->montant_engagement) {
            $this->alerteEngagement = true;
            $this->send_event_at_sweet_alert_not_timer(
                'Engagement requis',
                "Le fidèle {$customer->prenom} {$customer->nom} n'a pas de montant d'engagement mensuel. Définissez-le d'abord dans sa fiche.",
                'warning'
            );
            return;
        }

        /* ── Cas NON mensuel : enregistrement simple ──── */
        if (! $isMensuel) {
            $cot = Cotisation::create([
                'customer_id'        => $customer->id,
                'type_cotisation_id' => $tc->id,
                'mois'               => null,
                'annee'              => null,
                'montant_du'         => null,
                'montant_paye'       => $this->montantPaye,
                'montant_restant'    => 0,
                'statut'             => 'a_jour',
                'mode_paiement'      => $this->modePaiement,
                'reference'          => $this->reference ?: null,
                'validated_by'       => $this->validerImmediatement ? auth()->id() : null,
                'validated_at'       => $this->validerImmediatement ? now() : null,
            ]);
            HistoriqueCotisation::log($cot, 'creation', $this->montantPaye);
            $this->_createPaiementTransaction($cot, $this->montantPaye, $this->modePaiement, $this->reference, $this->validerImmediatement);
            $this->_finishSave();
            return;
        }

        /* ── Cas MENSUEL : logique report complète ──────── */
        $engagement = $customer->montant_engagement;
        $budget     = $this->montantPaye;

        /*
         * 1) Récupérer toutes les cotisations mensuelles
         *    non soldées de ce fidèle, triées du plus ancien au plus récent
         */
        $enRetard = Cotisation::where('customer_id', $customer->id)
            ->where('type_cotisation_id', $tc->id)
            ->whereIn('statut', ['en_retard', 'partiel'])
            ->orderBy('annee')
            ->orderBy('mois')
            ->get();

        /*
         * 2) Solder les mois en retard dans l'ordre
         */
        foreach ($enRetard as $cot) {
            if ($budget <= 0) break;

            $restant  = $cot->montant_restant;
            $toCredit = min($budget, $restant);
            $budget  -= $toCredit;

            $nouveauPaye    = $cot->montant_paye + $toCredit;
            $nouveauRestant = max(0, $cot->montant_du - $nouveauPaye);
            $nouveauStatut  = $nouveauRestant === 0 ? 'a_jour' : 'partiel';

            $cot->update([
                'montant_paye'    => $nouveauPaye,
                'montant_restant' => $nouveauRestant,
                'statut'          => $nouveauStatut,
                'mode_paiement'   => $this->modePaiement,
                'reference'       => $this->reference ?: null,
                'validated_by'    => $this->validerImmediatement ? auth()->id() : null,
                'validated_at'    => $this->validerImmediatement ? now() : null,
            ]);

            HistoriqueCotisation::log(
                $cot, 'paiement', $toCredit,
                "Règlement mois {$cot->mois}/{$cot->annee} – {$nouveauStatut}"
            );

            $this->_createPaiementTransaction($cot, $toCredit, $this->modePaiement, $this->reference, $this->validerImmediatement);
        }

        /*
         * 3) Trouver le dernier mois enregistré pour ce fidèle
         *    (après mise à jour ci-dessus) pour calculer le mois suivant
         */
        $derniere = Cotisation::where('customer_id', $customer->id)
            ->where('type_cotisation_id', $tc->id)
            ->orderByDesc('annee')
            ->orderByDesc('mois')
            ->first();

        // Mois de départ pour les nouvelles cotisations
        if ($derniere) {
            $prochainMois  = Carbon::create($derniere->annee, $derniere->mois)->addMonth();
        } else {
            // Aucune cotisation existante : on commence au mois saisi
            $prochainMois  = Carbon::create($this->annee, $this->mois);
        }

        /*
         * 4) Créer des cotisations avec le budget restant
         *    - si budget >= engagement → cotisation à jour + boucle
         *    - si budget < engagement  → cotisation partielle (1 seule)
         *    - si budget = 0           → rien à créer
         */
        $premiereCotCree = null;

        while ($budget > 0) {
            // Vérifier si une cotisation existe déjà pour ce mois
            $exists = Cotisation::where('customer_id', $customer->id)
                ->where('type_cotisation_id', $tc->id)
                ->where('mois', $prochainMois->month)
                ->where('annee', $prochainMois->year)
                ->first();

            if ($exists) {
                // Mois déjà existant → on crédite dessus
                $toCredit = min($budget, $exists->montant_restant);
                if ($toCredit > 0) {
                    $budget -= $toCredit;
                    $nouveauPaye    = $exists->montant_paye + $toCredit;
                    $nouveauRestant = max(0, $engagement - $nouveauPaye);
                    $nouveauStatut  = $nouveauRestant === 0 ? 'a_jour' : 'partiel';
                    $exists->update([
                        'montant_paye'    => $nouveauPaye,
                        'montant_restant' => $nouveauRestant,
                        'statut'          => $nouveauStatut,
                        'mode_paiement'   => $this->modePaiement,
                        'validated_by'    => $this->validerImmediatement ? auth()->id() : null,
                        'validated_at'    => $this->validerImmediatement ? now() : null,
                    ]);
                    HistoriqueCotisation::log($exists, 'paiement', $toCredit, "Crédit mois {$prochainMois->month}/{$prochainMois->year}");

                    $this->_createPaiementTransaction($exists, $toCredit, $this->modePaiement, $this->reference, $this->validerImmediatement);
                }
                $prochainMois->addMonth();
                continue;
            }

            // Nouveau mois à créer
            $montantCe     = min($budget, $engagement);
            $restantCe     = $engagement - $montantCe;
            $statutCe      = $restantCe === 0 ? 'a_jour' : 'partiel';
            $budget       -= $montantCe;

            $nouvCot = Cotisation::create([
                'customer_id'        => $customer->id,
                'type_cotisation_id' => $tc->id,
                'mois'               => $prochainMois->month,
                'annee'              => $prochainMois->year,
                'montant_du'         => $engagement,
                'montant_paye'       => $montantCe,
                'montant_restant'    => $restantCe,
                'statut'             => $statutCe,
                'mode_paiement'      => $this->modePaiement,
                'reference'          => $this->reference ?: null,
                'validated_by'       => $this->validerImmediatement ? auth()->id() : null,
                'validated_at'       => $this->validerImmediatement ? now() : null,
            ]);

            if (! $premiereCotCree) $premiereCotCree = $nouvCot;

            HistoriqueCotisation::log(
                $nouvCot, 'creation', $montantCe,
                "Cotisation {$prochainMois->month}/{$prochainMois->year} – {$statutCe}"
            );

            $this->_createPaiementTransaction($nouvCot, $montantCe, $this->modePaiement, $this->reference, $this->validerImmediatement);

            // Si partiel → stop (on ne crée pas le mois suivant avec 0)
            if ($statutCe === 'partiel') break;

            $prochainMois->addMonth();
        }

        $this->_finishSave();
    }

    /* ── Modification simple (non validée) ──────────────── */
    private function _updateCotisation(Customer $customer, TypeCotisation $tc): void
    {
        $cot = Cotisation::findOrFail($this->editId);

        $isMensuel      = $tc->type === 'mensuel';
        $montantDu      = $isMensuel ? ($customer->montant_engagement ?? $this->montantPaye) : null;
        $montantRestant = $montantDu ? max(0, $montantDu - $this->montantPaye) : 0;

        $statut = 'a_jour';
        if ($isMensuel && $montantDu) {
            if ($this->montantPaye >= $montantDu)      $statut = 'a_jour';
            elseif ($this->montantPaye > 0)            $statut = 'partiel';
            else                                       $statut = 'en_retard';
        }

        $cot->update([
            'type_cotisation_id' => $this->typeCotisationId,
            'mois'               => $isMensuel ? $this->mois : null,
            'annee'              => $isMensuel ? $this->annee : null,
            'montant_du'         => $montantDu,
            'montant_paye'       => $this->montantPaye,
            'montant_restant'    => $montantRestant,
            'statut'             => $statut,
            'mode_paiement'      => $this->modePaiement,
            'reference'          => $this->reference ?: null,
            'validated_by'       => $this->validerImmediatement ? auth()->id() : null,
            'validated_at'       => $this->validerImmediatement ? now() : null,
        ]);

        HistoriqueCotisation::log($cot, 'ajustement', $this->montantPaye, 'Modification manuelle BO');

        $this->_createPaiementTransaction($cot, $this->montantPaye, $this->modePaiement, $this->reference, $this->validerImmediatement);

        $this->closeModal_after_edit('modalCreateCotisation');
        $this->resetForm();
        $this->send_event_at_toast('Cotisation modifiée avec succès', 'success', 'top-end');
    }

    private function _finishSave(): void
    {
        $this->closeModal_after_edit('modalCreateCotisation');
        $this->resetForm();
        $this->send_event_at_toast('Cotisation enregistrée avec succès', 'success', 'top-end');
    }

    /* ═══════════════════════════════════════════════════════
       CHANGER STATUT MANUELLEMENT
    ═══════════════════════════════════════════════════════ */
    public function changerStatut(int $id, string $nouveauStatut): void
    {
        $cot          = Cotisation::findOrFail($id);
        $ancienStatut = $cot->statut;
        $cot->update(['statut' => $nouveauStatut]);
        HistoriqueCotisation::log($cot, 'ajustement', $cot->montant_paye, "Statut : {$ancienStatut} → {$nouveauStatut}");
        // Si on passe à "à jour", on considère que c'est payé à 100% même si montant_paye < montant_du (cas de régularisation par ex)
        if ($nouveauStatut === 'a_jour' ) {
           //valider le dernier paiement lié à la cotisation s'il n'est pas encore validé
            $lastPaiement = $cot->paiements()->latest()->first();
            if ($lastPaiement && $lastPaiement->statut !== 'success') {
                $lastPaiement->update([
                    'statut'       => 'success',
                ]);
            }else{
                //créer un paiement de régularisation si aucun paiement n'existe
                if (!$lastPaiement) {
                    $this->_createPaiementTransaction($cot, $cot->montant_restant, 'regul', 'Régularisation statut à jour', true);
                }
            }
        }
        $this->send_event_at_toast('Statut mis à jour', 'success', 'top-end');
    }

    /* ═══════════════════════════════════════════════════════
       VALIDER PAIEMENT ESPÈCES
    ═══════════════════════════════════════════════════════ */
    public function confirmerValidation(int $id): void
    {
        $cot = Cotisation::findOrFail($id);
        $this->sweetAlert_confirm_options_with_button(
            $cot,
            'Valider ce paiement ?',
            'Vous confirmez la réception de ' . number_format($cot->montant_paye, 0, ',', ' ') . ' FCFA en espèces.',
            'validerPaiement', 'question', 'Oui, valider', 'Annuler'
        );
    }

    #[On('validerPaiement')]
    public function validerPaiement(int $id): void
    {
        $cot = Cotisation::findOrFail($id);
        $cot->update(['validated_by' => auth()->id(), 'validated_at' => now()]);
        HistoriqueCotisation::log($cot, 'validation', $cot->montant_paye, 'Validation admin espèces');
        //valider le dernier paiement liees a la cotisation
            $lastPaiement = $cot->paiements()->latest()->first();
            if ($lastPaiement) {
                $lastPaiement->update([
                    'statut'       => 'success',
                ]);
            }
            $this->closeModal_after_edit('modalDetailCotisation');
            $this->detailId = null;

        $this->send_event_at_toast('Paiement validé avec succès', 'success', 'top-end');
    }

    /* ═══════════════════════════════════════════════════════
       SUPPRIMER
    ═══════════════════════════════════════════════════════ */
    public function confirmDelete(int $id): void
    {
        $cot = Cotisation::findOrFail($id);
        $this->sweetAlert_confirm_options_with_button(
            $cot, 'Supprimer cette cotisation ?', 'Cette action est irréversible.',
            'deleteConfirmed', 'warning', 'Oui, supprimer', 'Annuler'
        );
    }

    #[On('deleteConfirmed')]
    public function deleteConfirmed(int $id): void
    {
        $cot = Cotisation::find($id);
        if (! $cot) return;
        $cot->delete();
        if ($this->detailId === $id) {
            $this->detailId = null;
            $this->closeModal_after_edit('modalDetailCotisation');
        }
        $this->send_event_at_toast('Cotisation supprimée', 'success', 'top-end');
    }

    /* ═══════════════════════════════════════════════════════
       RESET FORMULAIRE
    ═══════════════════════════════════════════════════════ */
    protected function resetForm(): void
    {
        $this->editId               = null;
        $this->customerId           = null;
        $this->searchFidele         = '';
        $this->typeCotisationId     = null;
        $this->mois                 = now()->month;
        $this->annee                = now()->year;
        $this->montantPaye          = null;
        $this->modePaiement         = '';
        $this->reference            = '';
        $this->validerImmediatement = true;
        $this->alerteEngagement     = false;
        $this->resetErrorBag();
    }

    /* ═══════════════════════════════════════════════════════
       DONNÉES POUR LA VUE
    ═══════════════════════════════════════════════════════ */
    public function with(): array
    {
        /* ── Cotisations paginées + filtrées ── */
        $cotisations = Cotisation::with(['customer', 'typeCotisation', 'historiques'])
            ->when($this->tabStatut !== 'tous', fn($q) => $q->where('statut', $this->tabStatut))
            ->when($this->filterType !== 'tous', fn($q) => $q->where('type_cotisation_id', $this->filterType))
            ->when($this->filterMois !== 'tous', fn($q) => $q->where('mois', $this->filterMois))
            ->when($this->filterMode !== 'tous', fn($q) =>
                $this->filterMode === 'nd'
                    ? $q->whereNull('mode_paiement')
                    : $q->where('mode_paiement', $this->filterMode)
            )
            ->when($this->search, fn($q) =>
                $q->where(fn($q) =>
                    $q->whereHas('customer', fn($q) =>
                        $q->where('prenom', 'like', "%{$this->search}%")
                          ->orWhere('nom', 'like', "%{$this->search}%")
                    )->orWhereHas('typeCotisation', fn($q) =>
                        $q->where('libelle', 'like', "%{$this->search}%")
                    )
                )
            )
            ->latest()
            ->paginate(15);

        /* ── KPIs globaux ── */
        $kpis = [
            'total'   => Cotisation::count(),
            'ajour'   => Cotisation::where('statut', 'a_jour')->count(),
            'partiel' => Cotisation::where('statut', 'partiel')->count(),
            'retard'  => Cotisation::where('statut', 'en_retard')->count(),
            'montant' => Cotisation::sum('montant_paye'),
        ];

        /* ── Counts tabs (hors filtre tab) ── */
        $base = Cotisation::query()
            ->when($this->filterType !== 'tous', fn($q) => $q->where('type_cotisation_id', $this->filterType))
            ->when($this->filterMois !== 'tous', fn($q) => $q->where('mois', $this->filterMois))
            ->when($this->filterMode !== 'tous', fn($q) =>
                $this->filterMode === 'nd' ? $q->whereNull('mode_paiement') : $q->where('mode_paiement', $this->filterMode)
            );

        $tabCounts = [
            'tous'      => (clone $base)->count(),
            'a_jour'    => (clone $base)->where('statut', 'a_jour')->count(),
            'partiel'   => (clone $base)->where('statut', 'partiel')->count(),
            'en_retard' => (clone $base)->where('statut', 'en_retard')->count(),
        ];

        /* ── Détail ── */
        $detailCotisation = $this->detailId
            ? Cotisation::with(['customer', 'typeCotisation', 'historiques'])->find($this->detailId)
            : null;

        /* ── Formulaire ── */
        $typesCotisation = TypeCotisation::where('status', 'actif')->orderBy('libelle')->get();

        $fidelesSuggeres = $this->searchFidele
            ? Customer::where(fn($q) =>
                $q->where('prenom', 'like', "%{$this->searchFidele}%")
                  ->orWhere('nom',   'like', "%{$this->searchFidele}%")
                  ->orWhere('phone', 'like', "%{$this->searchFidele}%")
              )->limit(8)->get()
            : collect();

        $fideleCourant = $this->customerId
            ? Customer::find($this->customerId)
            : null;

        /* ── Preview report (pour affichage dans le modal) ── */
        $previewReport = $this->_buildPreviewReport($fideleCourant);

        return compact(
            'cotisations', 'kpis', 'tabCounts',
            'detailCotisation', 'typesCotisation',
            'fidelesSuggeres', 'fideleCourant',
            'previewReport'
        );
    }

    /* ── Calcul preview report pour le blade ───────────── */
    private function _buildPreviewReport(?Customer $customer): array
    {
        if (! $customer || ! $this->typeCotisationId || ! $this->montantPaye) {
            return [];
        }

        $tc = TypeCotisation::find($this->typeCotisationId);
        if (! $tc || $tc->type !== 'mensuel' || ! $customer->montant_engagement) {
            return [];
        }

        $engagement = $customer->montant_engagement;
        $budget     = $this->montantPaye;
        $rows       = [];

        /* Mois en retard */
        $enRetard = Cotisation::where('customer_id', $customer->id)
            ->where('type_cotisation_id', $this->typeCotisationId)
            ->whereIn('statut', ['en_retard', 'partiel'])
            ->orderBy('annee')->orderBy('mois')
            ->get();

        foreach ($enRetard as $cot) {
            if ($budget <= 0) break;
            $toCredit       = min($budget, $cot->montant_restant);
            $budget        -= $toCredit;
            $nouveauPaye    = $cot->montant_paye + $toCredit;
            $nouveauRestant = max(0, $cot->montant_du - $nouveauPaye);
            $rows[] = [
                'label'   => Carbon::create($cot->annee, $cot->mois)->translatedFormat('F Y'),
                'montant' => $toCredit,
                'statut'  => $nouveauRestant === 0 ? 'a_jour' : 'partiel',
                'type'    => 'solde',
            ];
        }

        /* Mois suivants */
        $derniere = Cotisation::where('customer_id', $customer->id)
            ->where('type_cotisation_id', $this->typeCotisationId)
            ->orderByDesc('annee')->orderByDesc('mois')
            ->first();

        $prochain = $derniere
            ? Carbon::create($derniere->annee, $derniere->mois)->addMonth()
            : Carbon::create($this->annee, $this->mois);

        while ($budget > 0) {
            $montantCe  = min($budget, $engagement);
            $restantCe  = $engagement - $montantCe;
            $budget    -= $montantCe;
            $rows[] = [
                'label'   => $prochain->translatedFormat('F Y'),
                'montant' => $montantCe,
                'statut'  => $restantCe === 0 ? 'a_jour' : 'partiel',
                'type'    => 'nouveau',
            ];
            if ($restantCe > 0) break;
            $prochain->addMonth();
        }

        return $rows;
    }

    private function _createPaiementTransaction(Cotisation $cotisation, int $montant, string $mode, ?string $reference, bool $valider): void
    {
        $paiement = \App\Models\Paiement::create([
            'customer_id'        => $cotisation->customer_id,
            'type_cotisation_id' => $cotisation->type_cotisation_id,
            'cotisation_id'      => $cotisation->id,
            'montant'            => $montant,
            'mode_paiement'      => $mode,
            'reference'          => $reference ?: null,
            'statut'             => $valider ? 'success' : 'en_attente',
            'date_paiement'      => now(),
        ]);

        \App\Models\Transaction::create([
            'type'             => 'entree',
            'source'           => 'paiement',
            'source_id'        => $paiement->id,
            'status'           => $valider ? 'success' : 'en_attente',
            'montant'          => $montant,
            'libelle'          => "Paiement cotisation – {$cotisation->typeCotisation->libelle}" .
                                ($cotisation->mois ? " – " . \Carbon\Carbon::create($cotisation->annee, $cotisation->mois)->translatedFormat('F Y') : ''),
            'date_transaction' => now(),
        ]);
    }
};
?>