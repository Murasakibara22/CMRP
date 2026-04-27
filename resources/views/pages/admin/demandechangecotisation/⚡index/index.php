<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\DemandeChangeCotisationMensuel;
use App\Models\Customer;
use App\Models\Cotisation;
use App\Models\TypeCotisation;
use App\Models\CoutEngagement;
use App\Traits\UtilsSweetAlert;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

new class extends Component
{
    use WithPagination, UtilsSweetAlert;

    /* ── Filtres ── */
    public string $search       = '';
    public string $filterStatut = 'tous';
    public string $filterType   = 'tous'; // changement | arret | tous

    /* ── Modal détail / édition ── */
    public ?int   $detailId     = null;
    public bool   $modeEdit     = false;

    /* ── Champs édition ── */
    public string $editTypeDemande             = '';
    public ?int   $editNouveauTypeId           = null;
    public ?int   $editNouvelEngagement        = null;
    public bool   $editSupprimerRetard         = false;
    public string $editMotif                   = '';
    public string $errorEditNouveauType        = '';
    public string $errorEditNouvelEngagement   = '';

    /* ── Modal rejet ── */
    public ?int   $rejetId     = null;
    public string $rejetMotif  = '';
    public string $errorRejet  = '';

    /* ── Reset pagination ── */
    public function updatedSearch(): void       { $this->resetPage(); }
    public function updatedFilterStatut(): void { $this->resetPage(); }
    public function updatedFilterType(): void   { $this->resetPage(); }

    /* ═══════════════════════════════════════════════════════
       MODAL DÉTAIL
    ═══════════════════════════════════════════════════════ */
    public function openDetail(int $id): void
    {
        $this->detailId  = $id;
        $this->modeEdit  = false;
        $this->_resetEditFields();
        $this->launch_modal('modalDetailDemande');
    }

    public function activerEdition(): void
    {
        $demande = DemandeChangeCotisationMensuel::findOrFail($this->detailId);
        $this->modeEdit              = true;
        $this->editTypeDemande       = $demande->type_demande;
        $this->editNouveauTypeId     = $demande->nouveau_type_cotisation_id;
        $this->editNouvelEngagement  = $demande->nouveau_montant_engagement;
        $this->editSupprimerRetard   = $demande->supprimer_cotisations_retard;
        $this->editMotif             = $demande->motif ?? '';
        $this->errorEditNouveauType      = '';
        $this->errorEditNouvelEngagement = '';
    }

    public function annulerEdition(): void
    {
        $this->modeEdit = false;
        $this->_resetEditFields();
    }

    public function selectEditNouveauType(?int $id): void
    {
        $this->editNouveauTypeId       = $id;
        $this->editNouvelEngagement    = null;
        $this->errorEditNouveauType    = '';
        $this->errorEditNouvelEngagement = '';
    }

    public function selectEditNouvelEngagement(?int $montant): void
    {
        $this->editNouvelEngagement      = $montant;
        $this->errorEditNouvelEngagement = '';
    }

    /* ═══════════════════════════════════════════════════════
       SAUVEGARDER LES MODIFICATIONS
    ═══════════════════════════════════════════════════════ */
    public function sauvegarderEdition(): void
    {
        $this->errorEditNouveauType      = '';
        $this->errorEditNouvelEngagement = '';

        $demande  = DemandeChangeCotisationMensuel::with(['customer'])->findOrFail($this->detailId);
        $customer = $demande->customer;

        if ($this->editTypeDemande === 'changement') {

            if (! $this->editNouveauTypeId) {
                $this->errorEditNouveauType = 'Veuillez sélectionner le nouveau type.';
                return;
            }

            /* Même type que l'actuel du customer */
            if ($this->editNouveauTypeId === $customer->type_cotisation_mensuel_id) {
                $this->errorEditNouveauType = 'Ce type est identique au type actuel du fidèle. Choisissez-en un autre.';
                return;
            }

            $tcNouveau = TypeCotisation::find($this->editNouveauTypeId);

            if (! $this->editNouvelEngagement || $this->editNouvelEngagement < 1) {
                $this->errorEditNouvelEngagement = "Veuillez renseigner le nouveau montant d'engagement.";
                return;
            }
            if ($tcNouveau?->montant_minimum && $this->editNouvelEngagement < $tcNouveau->montant_minimum) {
                $this->errorEditNouvelEngagement = "Le minimum pour « {$tcNouveau->libelle} » est " .
                    number_format($tcNouveau->montant_minimum, 0, ',', ' ') . " FCFA/mois.";
                return;
            }
        }

        $demande->update([
            'type_demande'                 => $this->editTypeDemande,
            'nouveau_type_cotisation_id'   => $this->editTypeDemande === 'changement' ? $this->editNouveauTypeId   : null,
            'nouveau_montant_engagement'   => $this->editTypeDemande === 'changement' ? $this->editNouvelEngagement : null,
            'supprimer_cotisations_retard' => $this->editSupprimerRetard,
            'motif'                        => trim($this->editMotif) ?: null,
        ]);

        $this->modeEdit = false;
        $this->_resetEditFields();
        $this->send_event_at_toast('Demande modifiée.', 'success', 'top-end');
    }

    /* ═══════════════════════════════════════════════════════
       VALIDER UNE DEMANDE

       Process selon type_demande :

       CHANGEMENT :
         1. customer.type_cotisation_mensuel_id = nouveau_type_cotisation_id
         2. customer.montant_engagement         = nouveau_montant_engagement
         3. Si supprimer_cotisations_retard → delete cotisations en_retard de l'ANCIEN type
         4. Créer cotisation du mois courant pour le NOUVEAU type (si absente)
         5. Demande → validee

       ARRÊT :
         1. customer.type_cotisation_mensuel_id = null
         2. customer.montant_engagement         = null
         3. Si supprimer_cotisations_retard → delete cotisations en_retard de l'ANCIEN type
         4. Demande → validee
    ═══════════════════════════════════════════════════════ */
    public function confirmerValidation(int $id): void
    {
        $demande = DemandeChangeCotisationMensuel::with(['customer', 'ancienType', 'nouveauType'])
            ->findOrFail($id);

        $label = $demande->isChangement()
            ? "Appliquer le changement vers « {$demande->nouveauType?->libelle} » pour {$demande->customer?->prenom} {$demande->customer?->nom} ?"
            : "Confirmer l'arrêt de la cotisation mensuelle pour {$demande->customer?->prenom} {$demande->customer?->nom} ?";

        $this->sweetAlert_confirm_options_with_button(
            $demande,
            'Valider la demande ?',
            $label,
            'validerDemande',
            'question',
            'Oui, valider',
            'Annuler'
        );
    }

    #[On('validerDemande')]
    public function validerDemande(int $id): void
    {
        $demande = DemandeChangeCotisationMensuel::with(['customer', 'ancienType', 'nouveauType'])
            ->findOrFail($id);

        if ($demande->statut !== 'en_attente') {
            $this->send_event_at_sweet_alert_not_timer('Action impossible', 'Cette demande a déjà été traitée.', 'warning');
            return;
        }

        DB::transaction(function () use ($demande) {
            $customer       = $demande->customer;
            $ancienTypeId   = $demande->ancien_type_cotisation_id;

            if ($demande->isChangement()) {
                /* ── CHANGEMENT DE TYPE ── */

                /* 1. MAJ customer */
                $customer->update([
                    'type_cotisation_mensuel_id' => $demande->nouveau_type_cotisation_id,
                    'montant_engagement'         => $demande->nouveau_montant_engagement,
                ]);

                /* 2. Supprimer cotisations en retard de l'ANCIEN type si demandé */
                if ($demande->supprimer_cotisations_retard && $ancienTypeId) {
                    Cotisation::where('customer_id', $customer->id)
                        ->where('type_cotisation_id', $ancienTypeId)
                        ->where('statut', 'en_retard')
                        ->delete();
                }

                /* 3. Créer cotisation du mois courant pour le NOUVEAU type */
                $existeDejaCeMois = Cotisation::where('customer_id', $customer->id)
                    ->where('type_cotisation_id', $demande->nouveau_type_cotisation_id)
                    ->where('mois', now()->month)
                    ->where('annee', now()->year)
                    ->exists();

                if (! $existeDejaCeMois && $demande->nouveau_montant_engagement) {
                    Cotisation::create([
                        'customer_id'        => $customer->id,
                        'type_cotisation_id' => $demande->nouveau_type_cotisation_id,
                        'mois'               => now()->month,
                        'annee'              => now()->year,
                        'montant_du'         => $demande->nouveau_montant_engagement,
                        'montant_paye'       => 0,
                        'montant_restant'    => $demande->nouveau_montant_engagement,
                        'statut'             => 'en_retard',
                        'mode_paiement'      => null,
                        'validated_by'       => null,
                        'validated_at'       => null,
                    ]);
                }

            } else {
                /* ── ARRÊT ── */

                /* 1. Supprimer cotisations en retard si demandé */
                if ($demande->supprimer_cotisations_retard && $ancienTypeId) {
                    Cotisation::where('customer_id', $customer->id)
                        ->where('type_cotisation_id', $ancienTypeId)
                        ->where('statut', 'en_retard')
                        ->delete();
                }

                /* 2. Supprimer le type mensuel du customer */
                $customer->update([
                    'type_cotisation_mensuel_id' => null,
                    'montant_engagement'         => null,
                ]);
            }

            /* 4. Marquer la demande comme validée */
            $demande->update([
                'statut'       => 'validee',
                'validated_by' => auth()->id(),
                'validated_at' => now(),
            ]);
        });

        $this->closeModal_after_edit('modalDetailDemande');
        $this->detailId = null;
        $this->send_event_at_toast('Demande validée et appliquée.', 'success', 'top-end');
    }

    /* ═══════════════════════════════════════════════════════
       REJETER UNE DEMANDE
    ═══════════════════════════════════════════════════════ */
    public function ouvrirRejet(int $id): void
    {
        $this->rejetId    = $id;
        $this->rejetMotif = '';
        $this->errorRejet = '';
        $this->closeModal_after_edit('modalDetailDemande');
        $this->launch_modal('modalRejet');
    }

    public function fermerRejet(): void
    {
        $this->rejetId    = null;
        $this->rejetMotif = '';
        $this->errorRejet = '';
        $this->closeModal_after_edit('modalRejet');
    }

    public function confirmerRejet(): void
    {
        $this->errorRejet = '';

        if (! trim($this->rejetMotif)) {
            $this->errorRejet = 'Veuillez indiquer le motif du rejet.';
            return;
        }

        $demande = DemandeChangeCotisationMensuel::findOrFail($this->rejetId);

        if ($demande->statut !== 'en_attente') {
            $this->send_event_at_sweet_alert_not_timer('Action impossible', 'Cette demande a déjà été traitée.', 'warning');
            return;
        }

        $demande->update([
            'statut'       => 'rejetee',
            'validated_by' => auth()->id(),
            'validated_at' => now(),
            'motif_rejet'  => trim($this->rejetMotif),
        ]);

        $this->fermerRejet();
        $this->send_event_at_toast('Demande rejetée.', 'success', 'top-end');
    }

    /* ── Helpers ── */
    private function _resetEditFields(): void
    {
        $this->editTypeDemande           = '';
        $this->editNouveauTypeId         = null;
        $this->editNouvelEngagement      = null;
        $this->editSupprimerRetard       = false;
        $this->editMotif                 = '';
        $this->errorEditNouveauType      = '';
        $this->errorEditNouvelEngagement = '';
    }

    /* ── Données vue ── */
    public function with(): array
    {
        $demandes = DemandeChangeCotisationMensuel::with([
                'customer', 'ancienType', 'nouveauType', 'createdBy', 'validatedBy',
            ])
            ->when($this->search, fn($q) =>
                $q->whereHas('customer', fn($q) =>
                    $q->where('prenom', 'like', "%{$this->search}%")
                      ->orWhere('nom',   'like', "%{$this->search}%")
                      ->orWhere('phone', 'like', "%{$this->search}%")
                )
            )
            ->when($this->filterStatut !== 'tous', fn($q) => $q->where('statut', $this->filterStatut))
            ->when($this->filterType   !== 'tous', fn($q) => $q->where('type_demande', $this->filterType))
            ->latest()
            ->paginate(15);

        $kpis = [
            'total'       => DemandeChangeCotisationMensuel::count(),
            'attente'     => DemandeChangeCotisationMensuel::where('statut', 'en_attente')->count(),
            'validees'    => DemandeChangeCotisationMensuel::where('statut', 'validee')->count(),
            'rejetees'    => DemandeChangeCotisationMensuel::where('statut', 'rejetee')->count(),
            'changements' => DemandeChangeCotisationMensuel::where('type_demande', 'changement')->count(),
            'arrets'      => DemandeChangeCotisationMensuel::where('type_demande', 'arret')->count(),
        ];

        $base = DemandeChangeCotisationMensuel::query()
            ->when($this->filterType !== 'tous', fn($q) => $q->where('type_demande', $this->filterType));

        $tabCounts = [
            'tous'       => (clone $base)->count(),
            'en_attente' => (clone $base)->where('statut', 'en_attente')->count(),
            'validee'    => (clone $base)->where('statut', 'validee')->count(),
            'rejetee'    => (clone $base)->where('statut', 'rejetee')->count(),
        ];

        $detailDemande = $this->detailId
            ? DemandeChangeCotisationMensuel::with([
                'customer.typeCotisationMensuel',
                'ancienType', 'nouveauType',
                'createdBy', 'validatedBy',
              ])->find($this->detailId)
            : null;

        /* Nb cotisations en retard de l'ancien type pour le fidèle concerné */
        $nbRetardAncienType = 0;
        if ($detailDemande?->customer && $detailDemande?->ancien_type_cotisation_id) {
            $nbRetardAncienType = Cotisation::where('customer_id', $detailDemande->customer_id)
                ->where('type_cotisation_id', $detailDemande->ancien_type_cotisation_id)
                ->where('statut', 'en_retard')
                ->count();
        }

        $typesMensuels   = TypeCotisation::where('type', 'mensuel')->where('status', 'actif')->orderBy('libelle')->get();
        $coutEngagements = CoutEngagement::actif()->orderBy('montant')->get();

        return compact(
            'demandes', 'kpis', 'tabCounts',
            'detailDemande', 'nbRetardAncienType',
            'typesMensuels', 'coutEngagements'
        );
    }
};
?>