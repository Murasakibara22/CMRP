<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Cotisation;
use App\Models\Paiement;
use App\Models\Reclammation;
use App\Models\HistoriqueReclammation;
use App\Traits\UtilsSweetAlert;
use Illuminate\Support\Carbon;

new #[Layout('layouts.app-frontend')] class extends Component
{
    use UtilsSweetAlert;

    /* ── Modaux gérés par propriétés Livewire ───────────── */
    public ?int $detailId  = null;   // null = fermé
    public bool $showRecla = false;  // true = modal réclamation ouvert

    /* ── Modal réclamation ──────────────────────────────── */
    public ?int   $reclaCotId        = null;
    public string $reclaLabel        = '';
    public string $reclaTitle        = '';
    public string $reclaMessage      = '';
    public string $errorReclaTitle   = '';
    public string $errorReclaMessage = '';

    /* ── Ouvrir / fermer modal détail ───────────────────── */
    public function showDetail(int $id): void
    {
        $this->detailId  = $id;
        $this->dispatch('OpenDetailCot');
    }

    public function closeDetail(): void
    {
        $this->dispatch('closeDetailCot');
        $this->detailId  = null;

    }

    /* ── Ouvrir modal réclamation depuis le détail ──────── */
    public function openRecla(int $cotId): void
    {
        $this->reclaCotId        = $cotId;
        $this->reclaTitle        = '';
        $this->reclaMessage      = '';
        $this->errorReclaTitle   = '';
        $this->errorReclaMessage = '';

        $cot = Cotisation::with('typeCotisation')->find($cotId);
        $this->reclaLabel = $cot
            ? ($cot->typeCotisation?->libelle ?? '—')
              . ($cot->mois ? ' — ' . Carbon::create($cot->annee, $cot->mois)->translatedFormat('F Y') : '')
            : '—';


        $this->detailId  = null;  // ferme le détail

        $this->dispatch('OpenReclaModal');
        // $this->showRecla = true;  // ouvre la réclamation
    }

    public function closeRecla(): void
    {
        // $this->dispatch('closeReclaModal'); // pour fermer la modale via JS (ex: Escape)
        // $this->showRecla     = false;
        // $this->reclaCotId    = null;
        // $this->reclaLabel    = '';
        // // $this->reclaTitle    = '';
        // $this->reclaMessage  = '';
        // $this->errorReclaTitle   = '';
        // $this->errorReclaMessage = '';
    }

    /* ── Soumettre réclamation ──────────────────────────── */
    public function submitRecla()
    {
        $this->errorReclaTitle   = '';
        $this->errorReclaMessage = '';

        if (! trim($this->reclaTitle)) {
            $this->errorReclaTitle = 'Le titre est obligatoire.';
        }
        if (! trim($this->reclaMessage)) {
            $this->errorReclaMessage = 'Le message est obligatoire.';
        }
        if ($this->errorReclaTitle || $this->errorReclaMessage) return;

        $customerId = auth('customer')->user()->id;

        $reclammation = Reclammation::create([
            'customer_id'   => $customerId,
            'cotisation_id' => $this->reclaCotId,
            'sujet'         => trim($this->reclaTitle),
            'description'   => trim($this->reclaMessage),
            'status'        => 'en_attente',
        ]);

        HistoriqueReclammation::create([
            'reclammation_id'       => $reclammation->id,
            'description'           => 'Réclamation créée par le fidèle.',
            'status'                => 'ouverte',
            'snapshot_reclammation' => json_encode($reclammation->toArray()),
        ]);
        $this->send_event_at_toast('Réclamation envoyée avec succès !', 'success', 'top-end');


        $this->closeRecla();
    }

    /* ── Données pour la vue ────────────────────────────── */
    public function with(): array
    {
        $customerId = auth('customer')->user()->id;

        $cotisations = Cotisation::with('typeCotisation')
            ->where('customer_id', $customerId)
            ->orderByDesc('annee')
            ->orderByDesc('mois')
            ->orderByDesc('created_at')
            ->get();

        $summary = [
            'retard'  => $cotisations->where('statut', 'en_retard')->count(),
            'ajour'   => $cotisations->where('statut', 'a_jour')->count(),
            'partiel' => $cotisations->where('statut', 'partiel')->count(),
            'total'   => $cotisations->count(),
        ];

        $grouped = $cotisations->groupBy(function ($c) {
            if ($c->mois && $c->annee) {
                return Carbon::create($c->annee, $c->mois)->translatedFormat('F Y');
            }
            return $c->created_at->translatedFormat('F Y');
        });

        /* Détail — chargé uniquement si modal ouvert */
        $detailCotisation = $this->detailId
            ? Cotisation::with('typeCotisation')->find($this->detailId)
            : null;

        $detailPaiements = [];
        if ($detailCotisation) {
            $detailPaiements = Paiement::where('cotisation_id', $detailCotisation->id)
                ->orderByDesc('date_paiement')
                ->get()
                ->map(function ($p) {
                    $modeLabel = match($p->mode_paiement) {
                        'mobile_money' => 'Mobile Money',
                        'espece'       => 'Espèces',
                        'virement'     => 'Virement',
                        default        => '—',
                    };
                    [$icon, $iconColor, $iconBg] = match($p->statut) {
                        'success'    => ['ri-checkbox-circle-line', '#0ab39c', 'rgba(10,179,156,.10)'],
                        'echec'      => ['ri-close-circle-line',    '#f06548', 'rgba(240,101,72,.10)'],
                        'en_attente' => ['ri-time-line',            '#f7b84b', 'rgba(247,184,75,.12)'],
                        'refund'     => ['ri-refund-2-line',        '#299cdb', 'rgba(41,156,219,.12)'],
                        default      => ['ri-add-circle-line',      '#405189', 'rgba(64,81,137,.10)'],
                    };
                    return [
                        'icon'         => $icon,
                        'iconColor'    => $iconColor,
                        'iconBg'       => $iconBg,
                        'title'        => match($p->statut) {
                            'success'    => 'Paiement reçu',
                            'echec'      => 'Paiement échoué',
                            'en_attente' => 'En attente',
                            'refund'     => 'Remboursé',
                            default      => 'Paiement',
                        },
                        'date'         => $p->date_paiement->format('d/m/Y') . ' · ' . $modeLabel,
                        'montant'      => match($p->statut) {
                            'success' => '+' . number_format($p->montant, 0, ',', ' '),
                            'echec'   => '-' . number_format($p->montant, 0, ',', ' '),
                            default   => '—',
                        },
                        'montantColor' => match($p->statut) {
                            'success' => '#0ab39c',
                            'echec'   => '#f06548',
                            default   => 'var(--muted)',
                        },
                    ];
                })->toArray();
        }

        return compact(
            'cotisations', 'grouped', 'summary',
            'detailCotisation', 'detailPaiements'
        );
    }
};
?>
