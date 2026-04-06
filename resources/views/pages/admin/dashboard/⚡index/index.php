<?php

use Livewire\Component;
use App\Models\Cotisation;
use App\Models\Customer;
use App\Models\Depense;
use App\Models\Paiement;
use App\Models\Transaction;
use App\Models\TypeCotisation;
use Carbon\Carbon;

new  class extends Component
{
    public function with(): array
    {
        $debut = now()->startOfMonth();
        $fin   = now()->endOfMonth();

        /* ── Bannière financière ── */
        $entreesMois   = Transaction::where('type', 'entree')->whereBetween('date_transaction', [$debut, $fin])->sum('montant');
        $sortiesMois   = Transaction::where('type', 'sortie')->whereBetween('date_transaction', [$debut, $fin])->sum('montant');
        $solde         = $entreesMois - $sortiesMois;
        $cotisationMois = Paiement::where('statut', 'success')->whereBetween('date_paiement', [$debut, $fin])->sum('montant');
        $fidelesActifs  = Customer::where('status', 'actif')->count();

        /* ── KPIs ── */
        $fidelesTotal   = Customer::count();
        $fidelesAJour   = Cotisation::where('statut', 'a_jour')
            ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)
            ->distinct('customer_id')->count('customer_id');
        $fidelesPartiel = Cotisation::where('statut', 'partiel')
            ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)
            ->distinct('customer_id')->count('customer_id');
        $fidelesRetard  = Cotisation::where('statut', 'en_retard')
            ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)
            ->distinct('customer_id')->count('customer_id');

        /* ── Trends mois précédent ── */
        $debutPrec = now()->subMonth()->startOfMonth();
        $finPrec   = now()->subMonth()->endOfMonth();
        $entreesPrecedent = Transaction::where('type', 'entree')->whereBetween('date_transaction', [$debutPrec, $finPrec])->sum('montant');
        $sortiesPrecedent = Transaction::where('type', 'sortie')->whereBetween('date_transaction', [$debutPrec, $finPrec])->sum('montant');
        $trendEntrees = $entreesPrecedent > 0 ? round((($entreesMois - $entreesPrecedent) / $entreesPrecedent) * 100, 1) : 0;
        $trendSorties = $sortiesPrecedent > 0 ? round((($sortiesMois - $sortiesPrecedent) / $sortiesPrecedent) * 100, 1) : 0;

        /* ── Graphe flux 12 mois ── */
        $chartLabels = $chartEntrees = $chartSorties = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $chartLabels[]  = $m->translatedFormat('M');
            $chartEntrees[] = (int) Transaction::where('type', 'entree')->whereMonth('date_transaction', $m->month)->whereYear('date_transaction', $m->year)->sum('montant');
            $chartSorties[] = (int) Transaction::where('type', 'sortie')->whereMonth('date_transaction', $m->month)->whereYear('date_transaction', $m->year)->sum('montant');
        }

        /* ── Types cotisation (mapping complet en PHP) ── */
        $iconMap  = [
            'mensuel'     => 'ri-calendar-check-line',
            'jour_precis' => 'ri-hand-heart-line',
            'ordinaire'   => 'ri-gift-line',
            'ramadan'     => 'ri-moon-line',
        ];
        $colorMap = [
            'mensuel'     => '#405189',
            'jour_precis' => '#0ab39c',
            'ordinaire'   => '#f7b84b',
            'ramadan'     => '#d4a843',
        ];
        $bgMap = [
            'mensuel'     => 'rgba(64,81,137,0.10)',
            'jour_precis' => 'rgba(10,179,156,0.10)',
            'ordinaire'   => 'rgba(247,184,75,0.12)',
            'ramadan'     => 'rgba(212,168,67,0.12)',
        ];

        $typesCotisationJs = TypeCotisation::where('status', 'actif')
            ->withCount(['cotisations as nb_contributions' => fn($q) =>
                $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)
            ])
            ->withSum(['cotisations as collecte_mois' => fn($q) =>
                $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)
            ], 'montant_paye')
            ->get()
            ->map(function ($tc) use ($iconMap, $colorMap, $bgMap) {
                return [
                    'nom'        => $tc->libelle,
                    'type'       => $tc->type,
                    'icon'       => $iconMap[$tc->type]  ?? 'ri-file-list-line',
                    'color'      => $colorMap[$tc->type] ?? '#878a99',
                    'bg'         => $bgMap[$tc->type]    ?? 'rgba(135,138,153,0.10)',
                    'collecte'   => (int) ($tc->collecte_mois ?? 0),
                    'objectif'   => $tc->montant_objectif,
                    'nb'         => (int) ($tc->nb_contributions ?? 0),
                    'badge'      => 'Actif',
                    'badgeColor' => 'rgba(10,179,156,0.10)',
                    'badgeText'  => '#0ab39c',
                ];
            })
            ->values()
            ->toArray();

        /* ── Objectif cotisation mensuelle ── */
        $cotisationObj = (int) Customer::where('status', 'actif')->whereNotNull('montant_engagement')->sum('montant_engagement');

        /* ── Collectes en cours (mapping PHP) ── */
        $collectesEnCoursJs = TypeCotisation::where('status', 'actif')
            ->whereNotNull('montant_objectif')
            ->get()
            ->map(function ($tc) {
                return [
                    'nom'       => $tc->libelle,
                    'type'      => ucfirst($tc->type),
                    'collecte'  => (int) $tc->cotisations()->sum('montant_paye'),
                    'objectif'  => (int) $tc->montant_objectif,
                    'color'     => '#405189',
                    'bg'        => 'rgba(64,81,137,0.10)',
                    'typeColor' => '#405189',
                    'typeBg'    => 'rgba(64,81,137,0.10)',
                ];
            })
            ->values()
            ->toArray();

        /* ── Transactions récentes (mapping PHP) ──
         * Le modèle Transaction utilise source/source_id sans relations directes
         * On charge séparément paiements et dépenses pour éviter l'erreur
         */
        $txRaw = Transaction::orderByDesc('date_transaction')->limit(7)->get();

        // Collecter les IDs par source pour un chargement groupé
        $paiementIds = $txRaw->where('source', 'paiement')->pluck('source_id')->unique();
        $depenseIds  = $txRaw->where('source', 'depense')->pluck('source_id')->unique();

        $paiements = Paiement::with('customer')->whereIn('id', $paiementIds)->get()->keyBy('id');
        $depenses  = Depense::with('typeDepense')->whereIn('id', $depenseIds)->get()->keyBy('id');

        $modeLabels = [
            'mobile_money' => 'Mobile Money',
            'espece'       => 'Espèces',
            'virement'     => 'Virement',
        ];

        $transactionsJs = $txRaw->map(function ($tx) use ($paiements, $depenses, $modeLabels) {
            if ($tx->type === 'entree' && $tx->source === 'paiement') {
                $pay = $paiements->get($tx->source_id);
                $nom  = $pay?->customer ? $pay->customer->prenom . ' ' . $pay->customer->nom : $tx->libelle;
                $mode = $modeLabels[$pay?->mode_paiement] ?? '-';
            } elseif ($tx->source === 'depense') {
                $dep  = $depenses->get($tx->source_id);
                $nom  = $dep?->typeDepense?->libelle ?? $tx->libelle;
                $mode = '-';
            } else {
                $nom  = $tx->libelle;
                $mode = '-';
            }

            return [
                'type'    => $tx->type,
                'nom'     => $nom,
                'source'  => $tx->libelle,
                'montant' => (int) $tx->montant,
                'date'    => $tx->date_transaction->diffForHumans(),
                'mode'    => $mode,
            ];
        })->values()->toArray();

        /* ── Dépenses par type mois (mapping PHP) ── */
        $totalDepMois = (int) Depense::whereBetween('date_depense', [$debut, $fin])->sum('montant');

        $depensesParTypeJs = Depense::with('typeDepense')
            ->whereBetween('date_depense', [$debut, $fin])
            ->get()
            ->groupBy('type_depense_id')
            ->map(function ($g) use ($totalDepMois) {
                return [
                    'nom'     => $g->first()->typeDepense?->libelle ?? 'Autres',
                    'montant' => (int) $g->sum('montant'),
                    'pct'     => $totalDepMois > 0 ? (int) round($g->sum('montant') / $totalDepMois * 100) : 0,
                    'icon'    => 'ri-shopping-cart-line',
                    'color'   => '#f06548',
                    'bg'      => 'rgba(240,101,72,0.10)',
                ];
            })
            ->values()
            ->sortByDesc('montant')
            ->take(5)
            ->values()
            ->toArray();

        /* ── Fidèles en retard top 5 (mapping PHP) ── */
        $avatarColors = ['#405189', '#f06548', '#f7b84b', '#299cdb', '#0ab39c'];

        $fidelesEnRetardJs = Cotisation::with('customer')
            ->whereIn('statut', ['en_retard', 'partiel'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->orderByDesc('montant_restant')
            ->limit(5)
            ->get()
            ->map(function ($c) use ($avatarColors) {
                return [
                    'initiales' => $c->customer
                        ? strtoupper(substr($c->customer->prenom, 0, 1) . substr($c->customer->nom, 0, 1))
                        : '??',
                    'couleur'   => $avatarColors[($c->customer_id - 1) % 5],
                    'nom'       => $c->customer ? $c->customer->prenom . ' ' . $c->customer->nom : '—',
                    'phone'     => $c->customer?->phone ?? '—',
                    'statut'    => $c->statut === 'en_retard' ? 'retard' : 'partiel',
                    'moisDu'    => 1,
                    'montantDu' => (int) $c->montant_restant,
                ];
            })
            ->values()
            ->toArray();

        return compact(
            'solde', 'entreesMois', 'sortiesMois', 'cotisationMois',
            'fidelesActifs', 'fidelesTotal', 'fidelesAJour', 'fidelesPartiel', 'fidelesRetard',
            'trendEntrees', 'trendSorties',
            'chartLabels', 'chartEntrees', 'chartSorties',
            'typesCotisationJs', 'cotisationObj',
            'collectesEnCoursJs', 'transactionsJs',
            'depensesParTypeJs', 'totalDepMois', 'fidelesEnRetardJs'
        );
    }
};
?>