<?php

use Livewire\Component;
use App\Models\Cotisation;
use App\Models\Paiement;
use App\Models\Transaction;
use App\Models\Depense;
use App\Models\Customer;
use App\Models\TypeDepense;
use Carbon\Carbon;

new  class extends Component
{
    /* ── Filtres période ──────────────────────────────────── */
    public string $periode    = 'mois';   // mois | trimestre | annee | custom
    public string $dateDebut  = '';
    public string $dateFin    = '';

    public function mount(): void
    {
        $this->dateDebut = now()->startOfMonth()->format('Y-m-d');
        $this->dateFin   = now()->endOfMonth()->format('Y-m-d');
    }

    public function setPeriode(string $periode): void
    {
        $this->periode = $periode;
        match ($periode) {
            'mois'      => [$this->dateDebut, $this->dateFin] = [now()->startOfMonth()->format('Y-m-d'),   now()->endOfMonth()->format('Y-m-d')],
            'trimestre' => [$this->dateDebut, $this->dateFin] = [now()->startOfQuarter()->format('Y-m-d'), now()->endOfQuarter()->format('Y-m-d')],
            'annee'     => [$this->dateDebut, $this->dateFin] = [now()->startOfYear()->format('Y-m-d'),    now()->endOfYear()->format('Y-m-d')],
            default     => null,
        };
    }

    public function appliquerCustom(): void
    {
        $this->periode = 'custom';
    }

    /* ── Modal détail transaction ─────────────────────────── */
    public ?int    $detailTxId    = null;
    public ?int    $detailDepId   = null;

    public function openDetailTx(int $id): void
    {
        $this->detailTxId  = $id;
        $this->detailDepId = null;
        $this->launch_modal('modalDetailBilan');
    }

    public function openDetailDep(int $id): void
    {
        $this->detailDepId = $id;
        $this->detailTxId  = null;
        $this->launch_modal('modalDetailBilan');
    }

    /* ── Helper scope période ─────────────────────────────── */
    private function scopePeriode($query, string $col = 'created_at')
    {
        return $query->whereBetween($col, [
            Carbon::parse($this->dateDebut)->startOfDay(),
            Carbon::parse($this->dateFin)->endOfDay(),
        ]);
    }

    public function exportBilanPdf()
    {
        // Réutilise toutes les données déjà calculées dans with()
        $data = $this->with();

        $periode = match($this->periode) {
            'mois'      => now()->translatedFormat('F Y'),
            'trimestre' => 'Trimestre '.now()->quarter.' '.now()->year,
            'annee'     => 'Année '.now()->year,
            default     => \Carbon\Carbon::parse($this->dateDebut)->translatedFormat('d M Y').
                        ' au '.
                        \Carbon\Carbon::parse($this->dateFin)->translatedFormat('d M Y'),
        };

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.bilan-general', array_merge($data, [
            'periode'     => $periode,
            'dateDebut'   => $this->dateDebut,
            'dateFin'     => $this->dateFin,
            'genereLe'    => now()->translatedFormat('d F Y à H:i'),
        ]))->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn() => print($pdf->output()),
            'bilan-general-'.now()->format('Ymd').'.pdf'
        );
    }

    /* ── Données vue ──────────────────────────────────────── */
    public function with(): array
    {
        $debut = Carbon::parse($this->dateDebut)->startOfDay();
        $fin   = Carbon::parse($this->dateFin)->endOfDay();

        /* ── Transactions période ── */
        $txQuery = fn() => Transaction::whereBetween('date_transaction', [$debut, $fin]);

        $totalEntrees  = (clone $txQuery())->where('type', 'entree')->sum('montant');
        $totalSorties  = (clone $txQuery())->where('type', 'sortie')->sum('montant');
        $soldeNet      = $totalEntrees - $totalSorties;

        /* ── KPIs ── */
        $nbCotisations  = Cotisation::whereBetween('created_at', [$debut, $fin])->count();
        $nbPaiements    = Paiement::where('statut', 'success')->whereBetween('date_paiement', [$debut, $fin])->count();
        $totalDepenses  = Depense::whereBetween('date_depense', [$debut, $fin])->sum('montant');
        $nbFidelesActif = Customer::where('status', 'actif')
            ->whereHas('cotisations', fn($q) => $q->whereBetween('created_at', [$debut, $fin]))
            ->count();

        /* ── Taux recouvrement par type de cotisation ── */
        $tauxRecouvrement = Cotisation::with('typeCotisation')
            ->whereBetween('created_at', [$debut, $fin])
            ->get()
            ->groupBy('type_cotisation_id')
            ->map(function ($group) {
                $tc      = $group->first()->typeCotisation;
                $du      = $group->sum('montant_du');
                $paye    = $group->sum('montant_paye');
                $taux    = $du > 0 ? round(($paye / $du) * 100) : 100;
                return [
                    'libelle' => $tc?->libelle ?? '—',
                    'type'    => $tc?->type ?? '',
                    'du'      => $du,
                    'paye'    => $paye,
                    'taux'    => $taux,
                    'count'   => $group->count(),
                    'ajour'   => $group->where('statut', 'a_jour')->count(),
                    'retard'  => $group->where('statut', 'en_retard')->count(),
                    'partiel' => $group->where('statut', 'partiel')->count(),
                ];
            })->values();

        /* ── Transactions récentes ── */
        $transactions = (clone $txQuery())
            ->orderByDesc('date_transaction')
            ->limit(10)
            ->get();

        /* ── Dépenses par type ── */
        $depensesParType = Depense::with('typeDepense')
            ->whereBetween('date_depense', [$debut, $fin])
            ->get()
            ->groupBy('type_depense_id')
            ->map(fn($g) => [
                'libelle' => $g->first()->typeDepense?->libelle ?? '—',
                'total'   => $g->sum('montant'),
                'count'   => $g->count(),
            ])->values()
            ->sortByDesc('total');

        /* ── Dépenses récentes ── */
        $depensesRecentes = Depense::with('typeDepense')
            ->whereBetween('date_depense', [$debut, $fin])
            ->orderByDesc('date_depense')
            ->limit(8)
            ->get();

        /* ── Graphe évolution mensuelle (12 mois glissants) ── */
        $graphLabels = $graphEntrees = $graphSorties = [];
        for ($i = 11; $i >= 0; $i--) {
            $mois  = now()->subMonths($i);
            $graphLabels[]  = $mois->translatedFormat('M Y');
            $graphEntrees[] = Transaction::where('type', 'entree')
                ->whereMonth('date_transaction', $mois->month)
                ->whereYear('date_transaction',  $mois->year)
                ->sum('montant');
            $graphSorties[] = Transaction::where('type', 'sortie')
                ->whereMonth('date_transaction', $mois->month)
                ->whereYear('date_transaction',  $mois->year)
                ->sum('montant');
        }

        /* ── Graphe répartition dépenses ── */
        $depTypes = TypeDepense::withSum(
            ['depenses' => fn($q) => $q->whereBetween('date_depense', [$debut, $fin])],
            'montant'
        )->having('depenses_sum_montant', '>', 0)->get();

        $graphData = [
            'evolution' => [
                'labels'  => $graphLabels,
                'entrees' => $graphEntrees,
                'sorties' => $graphSorties,
                'nets'    => array_map(fn($e, $s) => $e - $s, $graphEntrees, $graphSorties),
            ],
            'depenses_types' => [
                'labels' => $depTypes->pluck('libelle')->toArray(),
                'vals'   => $depTypes->pluck('depenses_sum_montant')->toArray(),
            ],
            'statuts_cotisation' => [
                'labels' => ['À jour', 'Partiel', 'En retard'],
                'vals'   => [
                    Cotisation::where('statut', 'a_jour')->whereBetween('created_at', [$debut, $fin])->count(),
                    Cotisation::where('statut', 'partiel')->whereBetween('created_at', [$debut, $fin])->count(),
                    Cotisation::where('statut', 'en_retard')->whereBetween('created_at', [$debut, $fin])->count(),
                ],
            ],
        ];

        /* ── Détail modal ── */
        $detailTx  = $this->detailTxId  ? Transaction::find($this->detailTxId)  : null;
        $detailDep = $this->detailDepId ? Depense::with('typeDepense')->find($this->detailDepId) : null;

        return compact(
            'totalEntrees', 'totalSorties', 'soldeNet',
            'nbCotisations', 'nbPaiements', 'totalDepenses', 'nbFidelesActif',
            'tauxRecouvrement', 'transactions', 'depensesParType', 'depensesRecentes',
            'graphData', 'detailTx', 'detailDep'
        );
    }
};
?>
