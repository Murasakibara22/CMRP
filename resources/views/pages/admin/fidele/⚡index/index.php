<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\CoutEngagement;
use App\Models\Customer;
use App\Models\Cotisation;
use App\Models\TypeCotisation;
use App\Models\HistoriqueCotisation;
use App\Traits\UtilsSweetAlert;
use App\Models\DemandeChangeCotisationMensuel;
use Carbon\Carbon;

new class extends Component
{
    use WithPagination, UtilsSweetAlert;

    /* ── Filtres & Vue ──────────────────────────────────── */
    public string $search       = '';
    public string $filterStatut = 'tous';
    public string $filterMois   = '';
    public string $filterAnnee  = '';
    public string $vue          = 'table';

    /* ── Formulaire ajout / édition ─────────────────────── */
    public ?int   $customerId              = null;
    public int    $formStep                = 1;
    public string $prenom                  = '';
    public string $nom                     = '';
    public string $dialCode                = '+225';
    public string $telephone               = '';
    public string $adresse                 = '';
    public string $dateAdhesion            = '';
    public ?int   $montantEngagement       = null;

    /*
     * Step 2 — Type de cotisation mensuel (optionnel)
     * Si le fidèle choisit un type mensuel is_required différent
     * de son type actuel → on affiche une confirmation.
     */
    public ?int   $typeCotisationMensuelId      = null;
    public bool   $showConfirmChangementType     = false;
    public string $confirmChangementMessage      = '';
    public ?int   $nouvelEngagement              = null;
    public string $errorNouvelEngagement         = '';

    /* ── Export PDF ─────────────────────────────────────── */
    public string $exportDebut = '';
    public string $exportFin   = '';
    public bool   $exportTout  = false;

    /* ── Modal détail ───────────────────────────────────── */
    public ?int $detailCustomerId = null;

    public ?int   $avanceCustomerId  = null;
    public int    $avanceNbMois      = 1;
    public string $avanceMode        = '';
    public string $avanceErrorMode   = '';
    public array  $avancePreview     = [];

    public bool   $supprimerRetardsChangement = false;
    public int    $nbRetardsAncienType        = 0;

    /* ── Reset pagination ───────────────────────────────── */
    public function updatedSearch(): void       { $this->resetPage(); }
    public function updatedFilterStatut(): void { $this->resetPage(); }

    /* ── Modaux ─────────────────────────────────────────── */
    public function openAdd(): void
    {
        $this->resetForm();
        $this->dateAdhesion = now()->format('Y-m-d');
        $this->launch_modal('modalAddFidele');
    }

    public function openEdit(int $id): void
    {
        $customer = Customer::findOrFail($id);

        $this->customerId             = $customer->id;
        $this->prenom                 = $customer->prenom;
        $this->nom                    = $customer->nom;
        $this->dialCode               = $customer->dial_code;
        $this->telephone              = $customer->phone;
        $this->adresse                = $customer->adresse ?? '';
        $this->dateAdhesion           = $customer->date_adhesion->format('Y-m-d');
        $this->montantEngagement      = $customer->montant_engagement;
        $this->typeCotisationMensuelId = $customer->type_cotisation_mensuel_id;
        $this->formStep               = 1;

        $this->launch_modal('modalAddFidele');
    }

    public function openDetail(int $id): void
    {
        $this->detailCustomerId = $id;
        $this->launch_modal('modalDetailFidele');
    }

    /* ── Navigation formulaire ──────────────────────────── */
    public function nextStep(): void
    {
        $this->validateStep1();
        $this->formStep = 2;
    }

    public function prevStep(): void
    {
        $this->formStep = 1;
        $this->showConfirmChangementType = false;
        $this->errorNouvelEngagement     = '';
    }

    protected function validateStep1(): void
    {
        $this->validate([
            'prenom'       => 'required|string|min:2|max:100',
            'nom'          => 'required|string|min:2|max:100',
            'dialCode'     => 'required|string',
            'telephone'    => 'required|string|min:8|max:20',
            'dateAdhesion' => 'required|date',
        ]);
    }

    /* ── Sélection type cotisation mensuel ──────────────── */
    public function selectTypeMensuel(?int $id): void
    {
        /* Désélection si même type cliqué */
        if ($this->typeCotisationMensuelId === $id) {
            $this->typeCotisationMensuelId   = null;
            $this->showConfirmChangementType = false;
            $this->confirmChangementMessage  = '';
            $this->nouvelEngagement          = null;
            $this->errorNouvelEngagement     = '';
            return;
        }

        $this->typeCotisationMensuelId   = $id;
        $this->showConfirmChangementType = false;
        $this->confirmChangementMessage  = '';
        $this->nouvelEngagement          = null;
        $this->errorNouvelEngagement     = '';
    }

    /* ── Sélection montant engagement ───────────────────── */
    public function selectEngagement(?int $montant): void
    {
        $this->montantEngagement = ($this->montantEngagement === $montant) ? null : $montant;
    }

    /* ═══════════════════════════════════════════════════════
       SAVE
    ═══════════════════════════════════════════════════════ */
    public function save(): void
    {
        $this->validateStep1();

        $customer = $this->customerId ? Customer::findOrFail($this->customerId) : null;

        $tcNouveau = $this->typeCotisationMensuelId
            ? TypeCotisation::find($this->typeCotisationMensuelId)
            : null;

        $isMensuelObligatoire = $tcNouveau && $tcNouveau->type === 'mensuel' && $tcNouveau->is_required;

        /*
         * Vérifier montant_engagement ≥ montant_minimum du type choisi
         */
        if (
            $isMensuelObligatoire &&
            $tcNouveau->montant_minimum &&
            $this->montantEngagement &&
            $this->montantEngagement < $tcNouveau->montant_minimum
        ) {
            $this->send_event_at_sweet_alert_not_timer(
                'Engagement insuffisant',
                "Le montant d'engagement (" .
                number_format($this->montantEngagement, 0, ',', ' ') .
                " FCFA) est inférieur au minimum requis pour « {$tcNouveau->libelle} » (" .
                number_format($tcNouveau->montant_minimum, 0, ',', ' ') . " FCFA).",
                'warning'
            );
            return;
        }

        /*
         * Détecter changement de type mensuel obligatoire :
         * si le fidèle avait déjà un type actif et qu'on en choisit un autre
         * → demander confirmation + nouveau montant d'engagement
         */
        $ancienTypeId = $customer?->type_cotisation_mensuel_id;
        $estChangement = $isMensuelObligatoire
            && $ancienTypeId
            && $ancienTypeId !== $this->typeCotisationMensuelId;

        if ($estChangement && ! $this->showConfirmChangementType) {
            $ancienType = TypeCotisation::find($ancienTypeId);
            $minLabel   = $tcNouveau->montant_minimum
                ? ' (minimum ' . number_format($tcNouveau->montant_minimum, 0, ',', ' ') . ' FCFA/mois)'
                : '';

            $this->showConfirmChangementType = true;
            $this->nouvelEngagement          = null;
            $this->supprimerRetardsChangement  = false;   // ← reset
            $this->nbRetardsAncienType         = Cotisation::where('customer_id', $customer->id)
                ->where('type_cotisation_id', $ancienTypeId)
                ->where('statut', 'en_retard')
                ->count();                                 // ← compter les retards
            $this->errorNouvelEngagement     = '';
            $this->confirmChangementMessage  =
                "Ce fidèle est actuellement en « {$ancienType?->libelle} » avec " .
                number_format($customer->montant_engagement ?? 0, 0, ',', ' ') .
                " FCFA/mois. Vous le migrez vers « {$tcNouveau->libelle} »{$minLabel}. " .
                "Renseignez son nouveau montant d'engagement mensuel.";
            return;
        }

        /* Appliquer nouvelEngagement si changement confirmé */
        if ($estChangement && $this->showConfirmChangementType) {
            $this->montantEngagement = $this->nouvelEngagement;
        }

        $this->_persistSave($customer, $tcNouveau, $estChangement);
    }

    /*
     * Appelé depuis le blade quand l'admin confirme le changement.
     */
    public function confirmerChangementType(): void
    {
        $this->errorNouvelEngagement = '';
        $tc = TypeCotisation::find($this->typeCotisationMensuelId);

        if (! $this->nouvelEngagement || $this->nouvelEngagement < 1) {
            $this->errorNouvelEngagement = "Veuillez saisir un montant d'engagement valide.";
            return;
        }
        if ($tc?->montant_minimum && $this->nouvelEngagement < $tc->montant_minimum) {
            $this->errorNouvelEngagement =
                "Le montant doit être ≥ " .
                number_format($tc->montant_minimum, 0, ',', ' ') .
                " FCFA (minimum de « {$tc->libelle} »).";
            return;
        }

        $this->montantEngagement         = $this->nouvelEngagement;
        $this->showConfirmChangementType = false;
        $this->confirmChangementMessage  = '';
        $this->errorNouvelEngagement     = '';

        $customer     = $this->customerId ? Customer::findOrFail($this->customerId) : null;
        $ancienTypeId = $customer?->type_cotisation_mensuel_id;

        \DB::transaction(function () use ($customer, $tc, $ancienTypeId) {

            /* 1. Créer la DemandeChangeCotisationMensuel (validée directement par l'admin) */
            DemandeChangeCotisationMensuel::create([
                'customer_id'                  => $customer->id,
                'created_by'                   => auth()->id(),
                'type_demande'                 => 'changement',
                'ancien_type_cotisation_id'    => $ancienTypeId,
                'ancien_montant_engagement'    => $customer->montant_engagement,
                'nouveau_type_cotisation_id'   => $this->typeCotisationMensuelId,
                'nouveau_montant_engagement'   => $this->montantEngagement,
                'supprimer_cotisations_retard' => $this->supprimerRetardsChangement,
                'motif'                        => 'Modification directe par l\'administrateur',
                'statut'                       => 'validee',
                'validated_by'                 => auth()->id(),
                'validated_at'                 => now(),
            ]);

            /* 2. Supprimer cotisations en retard de l'ANCIEN type si demandé */
            if ($this->supprimerRetardsChangement && $ancienTypeId) {
                Cotisation::where('customer_id', $customer->id)
                    ->where('type_cotisation_id', $ancienTypeId)
                    ->where('statut', 'en_retard')
                    ->delete();
            }

            /* 3. Appliquer le changement sur le customer */
            $this->_persistSave($customer, $tc, true);
        });
    }

    public function annulerChangementType(): void
    {
        $this->showConfirmChangementType = false;
        $this->confirmChangementMessage  = '';
        $this->nouvelEngagement          = null;
        $this->errorNouvelEngagement     = '';
        /* Remettre l'ancien type */
        $customer = $this->customerId ? Customer::find($this->customerId) : null;
        $this->typeCotisationMensuelId = $customer?->type_cotisation_mensuel_id;
    }

    /* ── Persistance en DB ──────────────────────────────── */
    private function _persistSave(?Customer $customer, ?TypeCotisation $tc, bool $estChangement): void
    {
        $data = [
            'prenom'                     => $this->prenom,
            'nom'                        => $this->nom,
            'dial_code'                  => $this->dialCode,
            'phone'                      => $this->telephone,
            'adresse'                    => $this->adresse ?: null,
            'date_adhesion'              => $this->dateAdhesion,
            'montant_engagement'         => $this->montantEngagement ?: null,
            'status'                     => 'actif',
            'type_cotisation_mensuel_id' => $this->typeCotisationMensuelId,
        ];

        if ($customer) {
            /* ── MODIFICATION ── */
            $ancienTypeId     = $customer->type_cotisation_mensuel_id;
            $ancienEngagement = $customer->montant_engagement;
            $estArret         = $ancienTypeId && ! $this->typeCotisationMensuelId;

            /* Créer une DemandeChangeCotisationMensuel si arrêt de type mensuel */
            if ($estArret) {
                DemandeChangeCotisationMensuel::create([
                    'customer_id'                  => $customer->id,
                    'created_by'                   => auth()->id(),
                    'type_demande'                 => 'arret',
                    'ancien_type_cotisation_id'    => $ancienTypeId,
                    'ancien_montant_engagement'    => $ancienEngagement,
                    'nouveau_type_cotisation_id'   => null,
                    'nouveau_montant_engagement'   => null,
                    'supprimer_cotisations_retard' => $this->supprimerRetardsChangement,
                    'motif'                        => 'Arrêt direct par l\'administrateur',
                    'statut'                       => 'validee',
                    'validated_by'                 => auth()->id(),
                    'validated_at'                 => now(),
                ]);

                if ($this->supprimerRetardsChangement && $ancienTypeId) {
                    Cotisation::where('customer_id', $customer->id)
                        ->where('type_cotisation_id', $ancienTypeId)
                        ->where('statut', 'en_retard')
                        ->delete();
                }
            }
            
            $customer->update($data);

            /**
             * Nous creons une cotisation innitiale du mois suivant la derniere cotisation du customer mais avec le nouveau type_cotisation_mensuel et le nouveau montant_engagement
             */
            $this->createCotisation($customer, $tc);


            $this->send_event_at_toast('Fidèle modifié avec succès', 'success', 'top-end');
        } else {
            $customer = Customer::create($data);

            /*
             * Nous créons une cotisation initiale pour ce nouveau fidèle avec le type mensuel choisi (s'il y en a un) et le montant d'engagement renseigné
             */

            $this->createCotisation($customer, $tc);

            $this->send_event_at_toast('Fidèle ajouté avec succès', 'success', 'top-end');
        }

        $this->closeModal_after_edit('modalAddFidele');
        $this->resetForm();
    }

    public function createCotisation(Customer $customer, ?TypeCotisation $tc): void
    {
        if ($tc && $tc->type === 'mensuel' && $tc->is_required) {
            $derniereCotisation = $customer->derniereCotisationMensuelle();

            $mois = $derniereCotisation
                ? ($derniereCotisation->mois % 12) + 1
                : now()->month;
            $annee = $derniereCotisation
                ? ($derniereCotisation->mois === 12 ? $derniereCotisation->annee + 1 : $derniereCotisation->annee)
                : now()->year;

            Cotisation::create([
                'customer_id'         => $customer->id,
                'type_cotisation_id' => $tc->id,
                'mois'               => $mois,
                'annee'              => $annee,
                'montant_du'         => $customer->montant_engagement,
                'montant_paye'       => 0,
                'montant_restant'   => $customer->montant_engagement,
                'statut'             => 'en_retard',
            ]);
        }
    }

    /* ── Suppression ────────────────────────────────────── */
    public function confirmDelete(int $id): void
    {
        $customer = Customer::findOrFail($id);
        $this->sweetAlert_confirm_options_with_button(
            $customer,
            'Supprimer ce fidèle ?',
            "La suppression de {$customer->prenom} {$customer->nom} est irréversible.",
            'deleteConfirmed', 'warning', 'Oui, supprimer', 'Annuler'
        );
    }

    #[On('deleteConfirmed')]
    public function deleteConfirmed(int $id): void
    {
        $customer = Customer::find($id);
        if (! $customer) {
            $this->send_event_at_toast('Fidèle introuvable', 'error', 'top-end');
            return;
        }
        $nom = "{$customer->prenom} {$customer->nom}";
        $customer->delete();

        if ($this->detailCustomerId === $id) {
            $this->detailCustomerId = null;
            $this->closeModal_after_edit('modalDetailFidele');
        }
        $this->send_event_at_toast("{$nom} supprimé", 'success', 'top-end');
    }

    /* ── Export PDF ─────────────────────────────────────── */
    public function openExportFidele(): void
    {
        $this->closeModal_after_edit('modalDetailFidele');
        $this->exportDebut = now()->startOfYear()->format('Y-m-d');
        $this->exportFin   = now()->format('Y-m-d');
        $this->exportTout  = false;
        $this->launch_modal('modalExportFidele');
    }

    public function exportBilanFidele()
    {
        $customer = Customer::with(['cotisations.typeCotisation', 'paiements'])
            ->findOrFail($this->detailCustomerId);

        $debut = $this->exportTout ? null : Carbon::parse($this->exportDebut)->startOfDay();
        $fin   = $this->exportTout ? null : Carbon::parse($this->exportFin)->endOfDay();

        $cotisations = $customer->cotisations()
            ->with('typeCotisation')
            ->when(! $this->exportTout, fn($q) => $q->whereBetween('created_at', [$debut, $fin]))
            ->orderByDesc('annee')->orderByDesc('mois')
            ->get();

        $paiements = $customer->paiements()
            ->where('statut', 'success')
            ->when(! $this->exportTout, fn($q) => $q->whereBetween('date_paiement', [$debut, $fin]))
            ->orderByDesc('date_paiement')
            ->get();

        $totalPaye    = $paiements->sum('montant');
        $totalDu      = $cotisations->sum('montant_du');
        $totalRestant = $cotisations->sum('montant_restant');
        $periode      = $this->exportTout
            ? "Tout l'historique"
            : Carbon::parse($this->exportDebut)->translatedFormat('d F Y') . ' au ' .
              Carbon::parse($this->exportFin)->translatedFormat('d F Y');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.bilan-fidele', compact(
            'customer', 'cotisations', 'paiements',
            'totalPaye', 'totalDu', 'totalRestant', 'periode'
        ))->setPaper('a4');

        $this->closeModal_after_edit('modalExportFidele');

        return response()->streamDownload(
            fn() => print($pdf->output()),
            "bilan-{$customer->nom}-{$customer->prenom}-" . now()->format('Ymd') . ".pdf"
        );
    }

    /* ── Divers ─────────────────────────────────────────── */
    public function setVue(string $vue): void { $this->vue = $vue; }

    protected function resetForm(): void
    {
        $this->customerId                = null;
        $this->formStep                  = 1;
        $this->prenom                    = '';
        $this->nom                       = '';
        $this->dialCode                  = '+225';
        $this->telephone                 = '';
        $this->adresse                   = '';
        $this->dateAdhesion              = '';
        $this->montantEngagement         = null;
        $this->typeCotisationMensuelId   = null;
        $this->showConfirmChangementType = false;
        $this->confirmChangementMessage  = '';
        $this->nouvelEngagement          = null;
        $this->errorNouvelEngagement     = '';
        $this->resetErrorBag();
    }


    /**Avance paiements */
    public function openAvance(int $customerId): void
    {
        $customer = Customer::with('typeCotisationMensuel')->findOrFail($customerId);
    
        if (! $customer->type_cotisation_mensuel_id || ! $customer->montant_engagement) {
            $this->send_event_at_sweet_alert_not_timer(
                'Impossible',
                "Ce fidèle n'a pas de type de cotisation mensuel ou de montant d'engagement défini.",
                'warning'
            );
            return;
        }
    
        $this->avanceCustomerId = $customerId;
        $this->avanceNbMois     = 1;
        $this->avanceMode       = '';
        $this->avanceErrorMode  = '';
        $this->_buildAvancePreview($customer);
    
        /* Fermer le modal détail et ouvrir le modal avance */
        $this->closeModal_after_edit('modalDetailFidele');
        $this->launch_modal('modalAvanceFidele');
    }
    
    public function updatedAvanceNbMois(): void
    {
        if (! $this->avanceCustomerId) return;
        $customer = Customer::find($this->avanceCustomerId);
        if ($customer) $this->_buildAvancePreview($customer);
    }
    
    public function selectAvanceMode(string $mode): void
    {
        $this->avanceMode      = $mode;
        $this->avanceErrorMode = '';
    }
    
    public function submitAvance(): void
    {
        $this->avanceErrorMode = '';
    
        if (! $this->avanceMode) {
            $this->avanceErrorMode = 'Veuillez choisir un mode de paiement.';
            return;
        }
    
        if (empty($this->avancePreview)) {
            $this->avanceErrorMode = 'Aucun mois à créer.';
            return;
        }
    
        $customer   = Customer::findOrFail($this->avanceCustomerId);
        $engagement = $customer->montant_engagement;
        $tcId       = $customer->type_cotisation_mensuel_id;
        $total      = count($this->avancePreview) * $engagement;
    
        /* Créer un Paiement global en_attente */
        $paiement = \App\Models\Paiement::create([
            'customer_id'        => $customer->id,
            'type_cotisation_id' => $tcId,
            'cotisation_id'      => null,
            'montant'            => $total,
            'mode_paiement'      => $this->avanceMode,
            'statut'             => 'en_attente',
            'date_paiement'      => now(),
        ]);
    
        $premiereCot = null;
    
        foreach ($this->avancePreview as $row) {
            $exists = Cotisation::where('customer_id', $customer->id)
                ->where('type_cotisation_id', $tcId)
                ->where('mois',  $row['mois'])
                ->where('annee', $row['annee'])
                ->exists();
    
            if ($exists) continue;
    
            $cot = Cotisation::create([
                'customer_id'        => $customer->id,
                'type_cotisation_id' => $tcId,
                'mois'               => $row['mois'],
                'annee'              => $row['annee'],
                'montant_du'         => $engagement,
                'montant_paye'       => $engagement,
                'montant_restant'    => 0,
                'statut'             => 'en_retard', // validé par le BO après réception
                'mode_paiement'      => $this->avanceMode,
                'paiement_id'        => $paiement->id,
                'validated_by'       => null,
                'validated_at'       => null,
            ]);
    
            HistoriqueCotisation::log($cot, 'creation', $engagement,
                "Paiement en avance BO — {$row['label']}");
    
            if (! $premiereCot) {
                $premiereCot = $cot;
                $paiement->update(['cotisation_id' => $cot->id]);
            }
        }
    
        $this->closeModal_after_edit('modalAvanceFidele');
        $this->avanceCustomerId = null;
        $this->avancePreview    = [];
    
        $this->send_event_at_toast(
            count($this->avancePreview) . ' cotisations créées en avance. Paiement en attente de validation.',
            'success', 'top-end'
        );
    }
    
    /* ── Helper preview ── */
    private function _buildAvancePreview(Customer $customer): void
    {
        $this->avancePreview = [];
        $nb         = max(1, min((int) $this->avanceNbMois, 24));
        $engagement = $customer->montant_engagement;
        $tcId       = $customer->type_cotisation_mensuel_id;
    
        $derniere = Cotisation::where('customer_id', $customer->id)
            ->where('type_cotisation_id', $tcId)
            ->orderByDesc('annee')->orderByDesc('mois')
            ->first();
    
        $prochain = $derniere
            ? Carbon::create($derniere->annee, $derniere->mois)->addMonth()
            : Carbon::now()->startOfMonth();
    
        $rows = [];
        for ($i = 0; $i < $nb; $i++) {
            $rows[] = [
                'label'   => $prochain->copy()->translatedFormat('F Y'),
                'montant' => $engagement,
                'mois'    => $prochain->month,
                'annee'   => $prochain->year,
            ];
            $prochain->addMonth();
        }
        $this->avancePreview = $rows;
    }

    /* ── Données vue ────────────────────────────────────── */
    public function with(): array
    {
        $mois  = $this->filterMois  ?: now()->month;
        $annee = $this->filterAnnee ?: now()->year;

        $customers = Customer::with(['cotisations' => function ($q) use ($mois, $annee) {
            $q->whereHas('typeCotisation', fn($q) => $q->where('type', 'mensuel'))
              ->where('mois', $mois)
              ->where('annee', $annee);
        }])
        ->when($this->search, fn($q) =>
            $q->where(fn($q) =>
                $q->where('nom', 'like', "%{$this->search}%")
                  ->orWhere('prenom', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%")
            )
        )
        ->when($this->filterStatut !== 'tous', function ($q) {
            match ($this->filterStatut) {
                'ajour'   => $q->whereHas('cotisations', fn($q) => $q->where('statut', 'a_jour')),
                'retard'  => $q->whereHas('cotisations', fn($q) => $q->where('statut', 'en_retard')),
                'partiel' => $q->whereHas('cotisations', fn($q) => $q->where('statut', 'partiel')),
                'libre'   => $q->whereNull('montant_engagement'),
                default   => null,
            };
        })
        ->latest()
        ->paginate(15);

        /* KPIs */
        $total          = Customer::count();
        $sansEngagement = Customer::whereNull('montant_engagement')->count();
        $ajour          = 0;
        $enRetard       = 0;
        Customer::whereNotNull('montant_engagement')->get()->each(function ($c) use (&$ajour, &$enRetard) {
            $c->statutGlobal() === 'a_jour' ? $ajour++ : $enRetard++;
        });
        $kpis = compact('total', 'ajour', 'enRetard', 'sansEngagement');

        /* Détail fidèle */
        $detailCustomer = $this->detailCustomerId
            ? Customer::with(['cotisations.typeCotisation', 'paiements', 'documents'])
                ->find($this->detailCustomerId)
            : null;

        $statut  = $detailCustomer?->statutGlobal() ?? 'sans_engagement';
        $totalDu = $detailCustomer
            ? $detailCustomer->cotisations->whereIn('statut', ['en_retard', 'partiel'])->sum('montant_restant')
            : 0;

        /* Types cotisation mensuels actifs pour le step 2 */
        $typesMensuels   = TypeCotisation::where('type', 'mensuel')
            ->where('status', 'actif')
            ->orderBy('libelle')
            ->get();

        $coutEngagements = CoutEngagement::actif()->orderBy('montant')->get();

        $avanceCustomer = $this->avanceCustomerId
        ? Customer::with('typeCotisationMensuel')->find($this->avanceCustomerId)
        : null;

        return compact(
            'customers', 'kpis', 'detailCustomer',
            'statut', 'totalDu',
            'coutEngagements', 'typesMensuels',
            'avanceCustomer'
        );
    }
};
?>
