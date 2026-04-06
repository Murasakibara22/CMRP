<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\Paiement;
use App\Models\Transaction;
use App\Models\HistoriqueCotisation;
use App\Traits\UtilsSweetAlert;
use Carbon\Carbon;

new  class extends Component
{
    use WithPagination, UtilsSweetAlert;

    public string $search        = '';
    public string $filterStatut  = 'tous';
    public string $filterMode    = 'tous';
    public string $filterMois    = 'tous';
    public ?int   $detailId      = null;

    public function updatedSearch(): void       { $this->resetPage(); }
    public function updatedFilterStatut(): void { $this->resetPage(); }
    public function updatedFilterMode(): void   { $this->resetPage(); }
    public function updatedFilterMois(): void   { $this->resetPage(); }

    /* ── Ouvrir détail ──────────────────────────────────── */
    public function openDetail(int $id): void
    {
        $this->detailId = $id;
        $this->launch_modal('modalDetailPaiement');
    }

    /* ── Valider paiement espèces ───────────────────────── */
    public function confirmerValidation(int $id): void
    {
        $pay = Paiement::findOrFail($id);
        $this->sweetAlert_confirm_options_with_button(
            $pay,
            'Valider ce paiement ?',
            'Vous confirmez la réception de ' . number_format($pay->montant, 0, ',', ' ') . ' FCFA en espèces.',
            'validerPaiement', 'question', 'Oui, valider', 'Annuler'
        );
    }

    #[On('validerPaiement')]
    public function validerPaiement(int $id): void
    {
        $pay = Paiement::with(['cotisation.typeCotisation', 'transaction'])->findOrFail($id);

        // 1. Valider le paiement
        $pay->update([
            'statut'       => 'success',
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        // 2. Mettre à jour la cotisation liée
        if ($pay->cotisation) {
            $cot            = $pay->cotisation;
            $nouveauPaye    = $cot->montant_paye + $pay->montant;
            $nouveauRestant = $cot->montant_du ? max(0, $cot->montant_du - $nouveauPaye) : 0;
            $nouveauStatut  = $cot->montant_du
                ? ($nouveauPaye >= $cot->montant_du ? 'a_jour' : ($nouveauPaye > 0 ? 'partiel' : 'en_retard'))
                : 'a_jour';

            $cot->update([
                'montant_paye'    => $nouveauPaye,
                'montant_restant' => $nouveauRestant,
                'statut'          => $nouveauStatut,
                'validated_by'    => auth()->id(),
                'validated_at'    => now(),
            ]);

            HistoriqueCotisation::log($cot, 'validation', $pay->montant, 'Validation admin espèces');
        }

        // 3. Transaction : màj si existante, sinon créer
        if ($pay->transaction) {
            $pay->transaction->update(['status' => 'success']);
        } else {
            $libelle = $pay->cotisation
                ? "Paiement cotisation – {$pay->cotisation->typeCotisation->libelle}"
                : "Paiement #{$pay->id}";

            Transaction::create([
                'type'             => 'entree',
                'source'           => 'paiement',
                'source_id'        => $pay->id,
                'montant'          => $pay->montant,
                'libelle'          => $libelle,
                'date_transaction' => now(),
            ]);
        }

        $this->send_event_at_toast('Paiement validé avec succès', 'success', 'top-end');
    }

    /* ── Rembourser ─────────────────────────────────────── */
    public function rembourserPaiement(int $id): void
    {
        $pay = Paiement::findOrFail($id);
        $this->sweetAlert_confirm_options_with_button(
            $pay,
            'Rembourser ce paiement ?',
            'Le statut passera à Remboursé et la cotisation sera ajustée.',
            'rembourserConfirme', 'warning', 'Oui, rembourser', 'Annuler'
        );
    }

    #[On('rembourserConfirme')]
    public function rembourserConfirme(int $id): void
    {
        $pay = Paiement::with(['cotisation', 'transaction'])->findOrFail($id);
        $pay->update(['statut' => 'refund']);

        // Ajuster la cotisation
        if ($pay->cotisation) {
            $cot            = $pay->cotisation;
            $nouveauPaye    = max(0, $cot->montant_paye - $pay->montant);
            $nouveauRestant = $cot->montant_du ? max(0, $cot->montant_du - $nouveauPaye) : 0;
            $nouveauStatut  = $cot->montant_du
                ? ($nouveauPaye >= $cot->montant_du ? 'a_jour' : ($nouveauPaye > 0 ? 'partiel' : 'en_retard'))
                : 'a_jour';

            $cot->update([
                'montant_paye'    => $nouveauPaye,
                'montant_restant' => $nouveauRestant,
                'statut'          => $nouveauStatut,
            ]);

            HistoriqueCotisation::log($cot, 'ajustement', $pay->montant, 'Remboursement paiement #' . $id);
        }

        // Créer une transaction de sortie pour le remboursement
        Transaction::create([
            'type'             => 'sortie',
            'source'           => 'paiement',
            'source_id'        => $pay->id,
            'montant'          => $pay->montant,
            'libelle'          => 'Remboursement paiement #' . $id,
            'date_transaction' => now(),
        ]);

        $this->send_event_at_toast('Paiement remboursé', 'success', 'top-end');
    }

    /* ── Données vue ────────────────────────────────────── */
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
                $q->whereMonth('created_at', $this->filterMois)
                  ->whereYear('created_at', now()->year)
            )
            ->latest()
            ->paginate(15);

        $kpis = [
            'total'   => Paiement::count(),
            'success' => Paiement::where('statut', 'success')->count(),
            'pending' => Paiement::where('statut', 'en_attente')->count(),
            'failed'  => Paiement::where('statut', 'echec')->count(),
            'montant' => Paiement::where('statut', 'success')->sum('montant'),
        ];

        // Counts tabs (hors filtre tab)
        $base = Paiement::query()
            ->when($this->filterMode !== 'tous', fn($q) => $q->where('mode_paiement', $this->filterMode))
            ->when($this->filterMois !== 'tous', fn($q) =>
                $q->whereMonth('created_at', $this->filterMois)->whereYear('created_at', now()->year)
            );

        $tabCounts = [
            'tous'       => (clone $base)->count(),
            'success'    => (clone $base)->where('statut', 'success')->count(),
            'en_attente' => (clone $base)->where('statut', 'en_attente')->count(),
            'echec'      => (clone $base)->where('statut', 'echec')->count(),
        ];

        $detailPaiement = $this->detailId
            ? Paiement::with(['customer', 'cotisation.typeCotisation'])->find($this->detailId)
            : null;

        // Graphs
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

        return compact('paiements', 'kpis', 'tabCounts', 'detailPaiement', 'graphData');
    }
};
?>