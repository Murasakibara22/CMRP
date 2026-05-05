<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\Paiement;
use App\Models\Cotisation;
use App\Models\Transaction;
use App\Models\DemandeRemboursement;
use App\Models\HistoriqueCotisation;
use App\Traits\UtilsSweetAlert;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

new class extends Component
{
    use WithPagination, UtilsSweetAlert;

    public string $search        = '';
    public string $filterStatut  = 'tous';
    public string $filterMode    = 'tous';
    public string $filterMois    = 'tous';
    public ?int   $detailId      = null;

    /* ── Modal validation multi-cotisations ─────────────── */
    public ?int   $validationPaiementId  = null;
    public array  $validationCotisations = []; // liste des cotisations couvertes

    /* ── Modal annulation ───────────────────────────────── */
    public ?int   $annulationPaiementId  = null;
    public string $annulationCode        = '';
    public string $annulationMotif       = '';
    public string $errorAnnulationCode   = '';
    public string $errorAnnulationMotif  = '';

    /* ── Reset pagination ───────────────────────────────── */
    public function updatedSearch(): void       { $this->resetPage(); }
    public function updatedFilterStatut(): void { $this->resetPage(); }
    public function updatedFilterMode(): void   { $this->resetPage(); }
    public function updatedFilterMois(): void   { $this->resetPage(); }

    /* ── Ouvrir détail ──────────────────────────────────── */
    public function openDetail(int $id): void
    {
        abort_unless(auth()->user()?->hasPermission('PAIEMENT_SHOW_ONE'), 403);

        $this->detailId = $id;
        $this->launch_modal('modalDetailPaiement');
    }

    /* ═══════════════════════════════════════════════════════
       VALIDATION D'UN PAIEMENT

       Règle :
       - Récupérer TOUTES les cotisations liées à ce paiement
         (via paiement_id sur la table cotisations).
       - Si plusieurs cotisations → ouvrir le modal de confirmation
         multi-cotisations (montrer la liste, confirmer).
       - Si une seule cotisation → SweetAlert direct.
       - Après confirmation → valider toutes les cotisations.
    ═══════════════════════════════════════════════════════ */
    public function ouvrirValidation(int $paiementId): void
    {
        abort_unless(auth()->user()?->hasPermission('PAIEMENT_VALIDATE'), 403);

        $pay = Paiement::findOrFail($paiementId);

        if ($pay->statut === 'success') {
            $this->send_event_at_sweet_alert_not_timer(
                'Déjà validé',
                'Ce paiement est déjà validé.',
                'info'
            );
            return;
        }

        /* Cotisations liées à ce paiement */
        $cotisations = Cotisation::with('typeCotisation')
            ->where('paiement_id', $pay->id)
            ->orderBy('annee')->orderBy('mois')
            ->get();

        if ($cotisations->isEmpty()) {
            /* Pas de cotisation liée → valider le paiement seul */
            $this->sweetAlert_confirm_options_with_button(
                $pay,
                'Valider ce paiement ?',
                'Confirmez la réception de ' . number_format($pay->montant, 0, ',', ' ') . ' FCFA.',
                'validerPaiementSeul', 'question', 'Oui, valider', 'Annuler'
            );
            return;
        }

        if ($cotisations->count() === 1) {
            /* Une seule cotisation → SweetAlert direct */
            $cot   = $cotisations->first();
            $label = $cot->typeCotisation?->libelle ?? '—';
            $label .= $cot->mois ? ' — ' . Carbon::create($cot->annee, $cot->mois)->translatedFormat('F Y') : '';

            $this->sweetAlert_confirm_options_with_button(
                $pay,
                'Valider ce paiement ?',
                "Confirmez la réception de " . number_format($pay->montant, 0, ',', ' ') . " FCFA.\nCotisation : {$label}",
                'validerPaiementConfirme', 'question', 'Oui, valider', 'Annuler'
            );
            return;
        }

        /* Plusieurs cotisations → modal de confirmation */
        $this->validationPaiementId  = $paiementId;
        $this->validationCotisations = $cotisations->map(fn($c) => [
            'id'      => $c->id,
            'label'   => ($c->typeCotisation?->libelle ?? '—') . ($c->mois ? ' — ' . Carbon::create($c->annee, $c->mois)->translatedFormat('F Y') : ''),
            'montant' => number_format($c->montant_du ?? $pay->montant / $cotisations->count(), 0, ',', ' '),
            'statut'  => $c->statut,
        ])->toArray();

        $this->closeModal_after_edit('modalDetailPaiement');
        $this->launch_modal('modalValidationMulti');
    }

    /* ── Valider paiement sans cotisation liée ── */
    #[On('validerPaiementSeul')]
    public function validerPaiementSeul(int $id): void
    {
        abort_unless(auth()->user()?->hasPermission('PAIEMENT_VALIDATE'), 403);


        $pay = Paiement::findOrFail($id);
        $pay->update([
            'statut'       => 'success',
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);
        Transaction::create([
            'type'             => 'entree',
            'source'           => 'paiement',
            'source_id'        => $pay->id,
            'status'           => 'success',
            'montant'          => $pay->montant,
            'libelle'          => "Paiement validé #{$pay->id}",
            'date_transaction' => now(),
        ]);
        $this->send_event_at_toast('Paiement validé.', 'success', 'top-end');
    }

    /* ── Valider paiement (1 cotisation — via SweetAlert) ── */
    #[On('validerPaiementConfirme')]
    public function validerPaiementConfirme(int $id): void
    {
        $this->_executerValidation($id);
        $this->send_event_at_toast('Paiement et cotisation validés.', 'success', 'top-end');
    }

    /* ── Valider depuis le modal multi-cotisations ── */
    public function confirmerValidationMulti(): void
    {
        if (! $this->validationPaiementId) return;

        $this->_executerValidation($this->validationPaiementId);

        $this->closeModal_after_edit('modalValidationMulti');
        $this->validationPaiementId  = null;
        $this->validationCotisations = [];
        $this->send_event_at_toast('Paiement et cotisations validés.', 'success', 'top-end');
    }

    /* ─────────────────────────────────────────────────────
       Exécute la validation : Paiement → success,
       toutes cotisations liées → a_jour, Transaction créée.
    ───────────────────────────────────────────────────── */
    private function _executerValidation(int $paiementId): void
    {
        DB::transaction(function () use ($paiementId) {
            $pay = Paiement::with(['cotisation.typeCotisation'])->findOrFail($paiementId);

            /* MAJ Paiement */
            $pay->update([
                'statut'       => 'success',
                'validated_by' => auth()->id(),
                'validated_at' => now(),
            ]);

            /* Toutes les cotisations liées à ce paiement */
            $cotisations = Cotisation::with('typeCotisation')
                ->where('paiement_id', $pay->id)
                ->get();

            foreach ($cotisations as $cot) {
                $cot->update([
                    'statut'          => 'a_jour',
                    'montant_restant' => 0,
                    'validated_by'    => auth()->id(),
                    'validated_at'    => now(),
                ]);
                HistoriqueCotisation::log($cot, 'validation', $cot->montant_paye, 'Validation via paiement #' . $pay->id);
            }

            /* Transaction (si absente) */
            $txExists = Transaction::where('source', 'paiement')
                ->where('source_id', $pay->id)
                ->where('status', 'success')
                ->exists();

            if (! $txExists) {
                $libelle = $cotisations->count() > 1
                    ? "Paiement groupé — {$cotisations->count()} cotisations"
                    : "Cotisation — " . ($cotisations->first()?->typeCotisation?->libelle ?? '—');

                Transaction::create([
                    'type'             => 'entree',
                    'source'           => 'paiement',
                    'source_id'        => $pay->id,
                    'status'           => 'success',
                    'montant'          => $pay->montant,
                    'libelle'          => $libelle,
                    'date_transaction' => now(),
                ]);
            }
        });
    }

    /* ═══════════════════════════════════════════════════════
       ANNULATION D'UN PAIEMENT

       Flux :
       1. Admin clique "Annuler" → ouvre modal avec champ code admin + motif.
       2. Code admin = code PIN stocké dans config('app.admin_cancel_code')
          ou dans User::$cancel_code selon ta config.
       3. Si code OK :
          - Paiement → statut 'annule'
          - Si était 'success' → créer DemandeRemboursement (en_attente)
          - Cotisations liées → statut 'en_retard', montant_paye = 0,
            montant_restant = montant_du, validated_at = null
       4. La validation de la DemandeRemboursement crée la Transaction sortie.
    ═══════════════════════════════════════════════════════ */
    public function ouvrirAnnulation(int $paiementId): void
    {
        abort_unless(auth()->user()?->hasPermission('PAIEMENT_ANNULER'), 403);
 
        $pay = Paiement::findOrFail($paiementId);

        if (in_array($pay->statut, ['annule'])) {
            $this->send_event_at_sweet_alert_not_timer(
                'Déjà annulé',
                'Ce paiement est déjà annulé.',
                'info'
            );
            return;
        }

        $this->annulationPaiementId  = $paiementId;
        $this->annulationCode        = '';
        $this->annulationMotif       = '';
        $this->errorAnnulationCode   = '';
        $this->errorAnnulationMotif  = '';

        $this->closeModal_after_edit('modalDetailPaiement');
        $this->launch_modal('modalAnnulation');
    }

    public function confirmerAnnulation(): void
    {
        $this->errorAnnulationCode  = '';
        $this->errorAnnulationMotif = '';

        if (! trim($this->annulationCode)) {
            $this->errorAnnulationCode = 'Le code administrateur est requis.';
            return;
        }

        if (! trim($this->annulationMotif)) {
            $this->errorAnnulationMotif = 'Veuillez indiquer le motif d\'annulation.';
            return;
        }

        /* Vérification du code admin */
        $codeAttendu = config('app.admin_cancel_code', env('ADMIN_CANCEL_CODE'));

        if ($this->annulationCode !== (string) $codeAttendu) {
            $this->errorAnnulationCode = 'Code administrateur incorrect.';
            return;
        }

        $pay = Paiement::with(['cotisation.typeCotisation'])->findOrFail($this->annulationPaiementId);
        $etaitSuccess = $pay->statut === 'success';

        DB::transaction(function () use ($pay, $etaitSuccess) {

            /* 1. Remettre toutes les cotisations liées en retard */
            $cotisations = Cotisation::where('paiement_id', $pay->id)->get();

            foreach ($cotisations as $cot) {
                $cot->update([
                    'statut'          => 'en_retard',
                    'montant_paye'    => 0,
                    'montant_restant' => $cot->montant_du ?? 0,
                    'validated_by'    => null,
                    'validated_at'    => null,
                ]);
                HistoriqueCotisation::log($cot, 'ajustement', 0, 'Annulation paiement #' . $pay->id);
            }

            

            /* 3. Si le paiement était success → créer une DemandeRemboursement */
            if ($etaitSuccess) {
                DemandeRemboursement::create([
                    'paiement_id'  => $pay->id,
                    'customer_id'  => $pay->customer_id,
                    'montant'      => $pay->montant,
                    'motif'        => trim($this->annulationMotif),
                    'statut'       => 'en_attente',
                    'created_by'   => auth()->id(),
                ]);

                /* 2. Passer le paiement à 'annule' */
                $pay->update([
                    // 'statut'   => 'annule',
                    'metadata' => array_merge($pay->metadata ?? [], [
                        'annule_by'     => auth()->id(),
                        // 'annule_at'     => now()->toDateTimeString(),
                        'annule_motif'  => trim($this->annulationMotif),
                    ]),
                ]);
            }else{
                /* 2. Passer le paiement à 'annule' */
                $pay->update([
                    'statut'   => 'annule',
                    'metadata' => array_merge($pay->metadata ?? [], [
                        'annule_by'     => auth()->id(),
                        'annule_at'     => now()->toDateTimeString(),
                        'annule_motif'  => trim($this->annulationMotif),
                    ]),
                ]);
            }
        });

        $this->closeModal_after_edit('modalAnnulation');
        $this->annulationPaiementId  = null;
        $this->annulationCode        = '';
        $this->annulationMotif       = '';

        $msg = $etaitSuccess
            ? 'Paiement annulé. Une demande de remboursement a été créée.'
            : 'Paiement annulé.';

        $this->send_event_at_toast($msg, 'success', 'top-end');
    }

    public function fermerAnnulation(): void
    {
        $this->closeModal_after_edit('modalAnnulation');
        $this->annulationPaiementId  = null;
        $this->annulationCode        = '';
        $this->annulationMotif       = '';
        $this->errorAnnulationCode   = '';
        $this->errorAnnulationMotif  = '';
    }

    /* ═══════════════════════════════════════════════════════
       EXPORT REÇU
    ═══════════════════════════════════════════════════════ */
    public function exportRecu(int $id): \Symfony\Component\HttpFoundation\Response
    {

        abort_unless(auth()->user()?->hasPermission('PAIEMENT_EXPORT_PDF'), 403);

        $paiement = Paiement::with(['customer', 'cotisation.typeCotisation'])->findOrFail($id);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.recu-paiement', compact('paiement'))
            ->setPaper([0, 0, 420, 595]);
        return response()->streamDownload(
            fn() => print($pdf->output()),
            'recu-' . $paiement->id . '.pdf'
        );
    }

    /* ═══════════════════════════════════════════════════════
       DONNÉES VUE
    ═══════════════════════════════════════════════════════ */
    public function with(): array
    {
        $paiements = Paiement::with(['customer', 'cotisation.typeCotisation'])
            ->when($this->search, fn($q) =>
                $q->where(fn($q) =>
                    $q->whereHas('customer', fn($q) =>
                        $q->where('prenom', 'like', "%{$this->search}%")
                          ->orWhere('nom',   'like', "%{$this->search}%")
                          ->orWhere('phone', 'like', "%{$this->search}%")
                    )->orWhere('reference', 'like', "%{$this->search}%")
                )
            )
            ->when($this->filterStatut !== 'tous', fn($q) => $q->where('statut', $this->filterStatut))
            ->when($this->filterMode   !== 'tous', fn($q) => $q->where('mode_paiement', $this->filterMode))
            ->when($this->filterMois   !== 'tous', fn($q) =>
                $q->whereMonth('created_at', $this->filterMois)->whereYear('created_at', now()->year)
            )
            ->latest()
            ->paginate(15);

        $kpis = [
            'total'   => Paiement::count(),
            'success' => Paiement::where('statut', 'success')->count(),
            'pending' => Paiement::where('statut', 'en_attente')->count(),
            'failed'  => Paiement::whereIn('statut', ['echec', 'annule'])->count(),
            'montant' => Paiement::where('statut', 'success')->sum('montant'),
        ];

        $base = Paiement::query()
            ->when($this->filterMode !== 'tous', fn($q) => $q->where('mode_paiement', $this->filterMode))
            ->when($this->filterMois !== 'tous', fn($q) =>
                $q->whereMonth('created_at', $this->filterMois)->whereYear('created_at', now()->year)
            );

        $tabCounts = [
            'tous'       => (clone $base)->count(),
            'success'    => (clone $base)->where('statut', 'success')->count(),
            'en_attente' => (clone $base)->where('statut', 'en_attente')->count(),
            'echec'      => (clone $base)->whereIn('statut', ['echec', 'annule'])->count(),
        ];

        $detailPaiement = $this->detailId
            ? Paiement::with(['customer', 'cotisation.typeCotisation'])->find($this->detailId)
            : null;

        /* Cotisations liées au paiement en cours de validation */
        $validationPaiement = $this->validationPaiementId
            ? Paiement::with(['customer'])->find($this->validationPaiementId)
            : null;

        /* Paiement en cours d'annulation */
        $annulationPaiement = $this->annulationPaiementId
            ? Paiement::with(['customer'])->find($this->annulationPaiementId)
            : null;

        /* Graphs */
        $moisLabels = $moisMontants = $moisCounts = [];
        for ($m = 1; $m <= 12; $m++) {
            $moisLabels[]   = Carbon::create()->month($m)->translatedFormat('M');
            $moisMontants[] = Paiement::where('statut', 'success')
                ->whereMonth('created_at', $m)->whereYear('created_at', now()->year)->sum('montant');
            $moisCounts[]   = Paiement::where('statut', 'success')
                ->whereMonth('created_at', $m)->whereYear('created_at', now()->year)->count();
        }

        $graphData = [
            'mois_labels'   => $moisLabels,
            'mois_montants' => $moisMontants,
            'mois_counts'   => $moisCounts,
            'modes_labels'  => ['Mobile Money', 'Espèces', 'Virement'],
            'modes_vals'    => [
                Paiement::where('mode_paiement', 'mobile_money')->count(),
                Paiement::where('mode_paiement', 'espece')->count(),
                Paiement::where('mode_paiement', 'virement')->count(),
            ],
        ];

        return compact(
            'paiements', 'kpis', 'tabCounts',
            'detailPaiement', 'graphData',
            'validationPaiement', 'annulationPaiement'
        );
    }
};
?>