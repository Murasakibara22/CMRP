<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Cotisation;
use App\Models\Paiement;
use App\Models\Reclammation;
use Illuminate\Support\Carbon;

new #[Layout('layouts.app-frontend')] class extends Component
{
    /* ── Modal détail cotisation ────────────────────────── */
    public ?int $detailId = null;

    public function openDetail(int $id): void
    {
        $this->detailId = $id;
        $this->dispatch('OpenHomeCotDetail');
    }

    public function closeDetail(): void
    {
        $this->dispatch('closeHomeCotDetail');
        $this->detailId = null;
    }

    public function with(): array
    {
        $customer   = auth('customer')->user();
        $customerId = $customer->id;

        /* ── KPIs ── */
        $totalCotise = Paiement::where('customer_id', $customerId)
            ->where('statut', 'success')->sum('montant');

        $totalDu = Cotisation::where('customer_id', $customerId)
            ->whereIn('statut', ['en_retard', 'partiel'])->sum('montant_restant');

        $moisRetard = Cotisation::where('customer_id', $customerId)
            ->where('statut', 'en_retard')->count();

        /* Mois en retard labels */
        $retardDetails = Cotisation::where('customer_id', $customerId)
            ->where('statut', 'en_retard')
            ->whereNotNull('mois')
            ->orderBy('annee')->orderBy('mois')
            ->get()
            ->map(fn($c) => Carbon::create($c->annee, $c->mois)->translatedFormat('F Y'));

        /* Prochain mois à payer */
        $prochainMois    = null;
        $prochainMontant = $customer->montant_engagement ?? 0;
        if ($customer->montant_engagement) {
            $m = now()->addMonth();
            $prochainMois = $m->translatedFormat('F Y');
        }

        /* ── Historique mixte (cotisations + réclamations) 8 derniers ── */
        $cotisations = Cotisation::with('typeCotisation')
            ->where('customer_id', $customerId)
            ->orderByDesc('created_at')
            ->limit(6)
            ->get()
            ->map(function ($c) {
                $statut = $c->statut;
                [$iconClass, $iconBg, $iconColor, $pillClass, $pillLabel, $amountColor] = match($statut) {
                    'a_jour'    => ['ri-checkbox-circle-line', 'rgba(10,179,156,.10)', '#0ab39c', 'pill-ok',     'À jour',     '#0ab39c'],
                    'partiel'   => ['ri-error-warning-line',   'rgba(247,184,75,.12)', '#f7b84b', 'pill-warn',   'Partiel',    '#f7b84b'],
                    'en_retard' => ['ri-time-line',            'rgba(240,101,72,.10)', '#f06548', 'pill-danger', 'En retard',  '#f06548'],
                    default     => ['ri-calendar-line',        'rgba(64,81,137,.10)',  '#405189', 'pill-info',   'En attente', '#405189'],
                };
                // Icône type cotisation si à jour
                if ($statut === 'a_jour') {
                    $iconClass = match($c->typeCotisation?->type) {
                        'jour_precis' => 'ri-hand-heart-line',
                        'ordinaire'   => 'ri-gift-line',
                        'ramadan'     => 'ri-moon-line',
                        default       => 'ri-checkbox-circle-line',
                    };
                    [$iconBg, $iconColor] = match($c->typeCotisation?->type) {
                        'jour_precis' => ['rgba(212,168,67,.12)', '#d4a843'],
                        'ordinaire'   => ['rgba(10,179,156,.10)', '#0ab39c'],
                        'ramadan'     => ['rgba(41,156,219,.12)', '#299cdb'],
                        default       => ['rgba(10,179,156,.10)', '#0ab39c'],
                    };
                }
                $modeLabel = match($c->mode_paiement) {
                    'mobile_money' => 'Mobile Money',
                    'espece'       => 'Espèces',
                    'virement'     => 'Virement',
                    default        => null,
                };
                $dateLabel = $statut === 'en_retard'
                    ? 'Non payé · En retard'
                    : ($c->validated_at
                        ? $c->validated_at->format('d/m/Y') . ($modeLabel ? ' · ' . $modeLabel : '')
                        : $c->created_at->format('d/m/Y') . ($modeLabel ? ' · ' . $modeLabel : ''));

                $prefix  = $statut === 'a_jour' ? '+' : '-';
                $montant = $c->montant_paye > 0 ? $c->montant_paye : ($c->montant_du ?? 0);

                $periodeLabel = ($c->mois && $c->annee)
                    ? Carbon::create($c->annee, $c->mois)->translatedFormat('M Y')
                    : $c->created_at->translatedFormat('M Y');

                return [
                    'id'          => $c->id,
                    'type'        => 'cotisations',
                    'iconClass'   => $iconClass,
                    'iconBg'      => $iconBg,
                    'iconColor'   => $iconColor,
                    'title'       => ($c->typeCotisation?->libelle ?? '—') . ' — ' . $periodeLabel,
                    'date'        => $dateLabel,
                    'montant'     => $montant > 0 ? $prefix . number_format($montant, 0, ',', ' ') : null,
                    'amountColor' => $amountColor,
                    'pillClass'   => $pillClass,
                    'pillLabel'   => $pillLabel,
                    'statut'      => $statut,
                ];
            });

        $reclamations = Reclammation::where('customer_id', $customerId)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->map(fn($r) => [
                'id'        => $r->id,
                'type'      => 'reclamations',
                'iconClass' => 'ri-flag-line',
                'iconBg'    => 'rgba(41,156,219,.12)',
                'iconColor' => '#299cdb',
                'title'     => 'Réclamation — ' . \Str::limit($r->sujet, 30),
                'date'      => $r->created_at->format('d/m/Y') . ' · ' . match($r->status) {
                    'ouverte','en_cours' => 'En cours de traitement',
                    'resolu'  => 'Résolue',
                    'rejete'  => 'Rejetée',
                    default   => $r->status,
                },
                'montant'     => null,
                'amountColor' => null,
                'pillClass'   => match($r->status) {
                    'ouverte','en_cours' => 'pill-info',
                    'resolu'  => 'pill-ok',
                    'rejete'  => 'pill-danger',
                    default   => 'pill-info',
                },
                'pillLabel'   => match($r->status) {
                    'ouverte','en_cours' => 'En cours',
                    'resolu'  => 'Résolu',
                    'rejete'  => 'Rejeté',
                    default   => '—',
                },
                'statut' => $r->status,
            ]);

        // Mélanger et trier par date décroissante
        $historique = $cotisations->concat($reclamations)
            ->sortByDesc(fn($item) => $item['id'])
            ->values()
            ->take(8);

        /* Détail cotisation sélectionnée */
        $detailCotisation = $this->detailId
            ? Cotisation::with(['typeCotisation', 'paiements'])->find($this->detailId)
            : null;

        $detailPaiements = [];
        if ($detailCotisation) {
            $detailPaiements = Paiement::where('cotisation_id', $detailCotisation->id)
                ->orderByDesc('date_paiement')->get()
                ->map(fn($p) => [
                    'montant'      => ($p->statut === 'success' ? '+' : '') . number_format($p->montant, 0, ',', ' '),
                    'montantColor' => $p->statut === 'success' ? '#0ab39c' : ($p->statut === 'echec' ? '#f06548' : 'var(--muted)'),
                    'date'         => $p->date_paiement->format('d/m/Y'),
                    'mode'         => match($p->mode_paiement){ 'mobile_money'=>'Mobile Money','espece'=>'Espèces','virement'=>'Virement',default=>'—'},
                    'statut'       => $p->statut,
                ])->toArray();
        }

        return compact(
            'customer', 'totalCotise', 'totalDu', 'moisRetard',
            'retardDetails', 'prochainMois', 'prochainMontant',
            'historique', 'detailCotisation', 'detailPaiements'
        );
    }
};
?>