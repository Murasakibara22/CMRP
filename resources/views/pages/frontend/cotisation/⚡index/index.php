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

    /* ── Modaux ─────────────────────────────────────────── */
    public ?int $detailId  = null;
    public bool $showRecla = false;

    /* ── Modal paiement simple (pour une cotisation) ────── */
    public bool   $showPaiement     = false;
    public ?int   $paiementCotId    = null;
    public string $paiementLabel    = '';
    public int    $paiementMontant  = 0;
    public string $paiementMode     = '';   // espece | mobile_money
    public string $errorPaiement    = '';

    /* ── Modal paiement en avance ───────────────────────── */
    public bool   $showAvance       = false;
    public int    $nbMoisAvance     = 1;
    public string $avanceMode       = '';
    public string $errorAvance      = '';
    public array  $previewAvance    = [];   // lignes de preview

    /* ── Modal réclamation ──────────────────────────────── */
    public ?int   $reclaCotId        = null;
    public string $reclaLabel        = '';
    public string $reclaTitle        = '';
    public string $reclaMessage      = '';
    public string $errorReclaTitle   = '';
    public string $errorReclaMessage = '';

    /* ═══════════════════════════════════════════════════════
       MODAL DÉTAIL
    ═══════════════════════════════════════════════════════ */
    public function showDetail(int $id): void
    {
        $this->detailId = $id;
        $this->dispatch('OpenDetailCot');
    }

    public function closeDetail(): void
    {
        $this->dispatch('closeDetailCot');
        $this->detailId = null;
    }

    /* ═══════════════════════════════════════════════════════
       MODAL PAIEMENT SIMPLE
       Ouvert depuis le modal détail quand la cotisation
       n'est pas encore validée (statut en_retard ou partiel).
    ═══════════════════════════════════════════════════════ */
    public function openPaiement(int $cotId): void
    {
        $cot = Cotisation::with('typeCotisation')->findOrFail($cotId);

        $this->paiementCotId   = $cotId;
        $this->paiementMontant = $cot->montant_restant > 0 ? $cot->montant_restant : ($cot->montant_du ?? 0);
        $this->paiementMode    = '';
        $this->errorPaiement   = '';
        $this->paiementLabel   = ($cot->typeCotisation?->libelle ?? '—')
            . ($cot->mois ? ' — ' . Carbon::create($cot->annee, $cot->mois)->translatedFormat('F Y') : '');

        /* Fermer le modal détail avant d'ouvrir paiement */
        $this->detailId = null;
        $this->dispatch('closeDetailCot');
        $this->showPaiement = true;
        $this->dispatch('OpenPaiementModal');
    }

    public function closePaiement(): void
    {
        $this->showPaiement    = false;
        $this->paiementCotId   = null;
        $this->paiementMode    = '';
        $this->errorPaiement   = '';
        $this->dispatch('closePaiementModal');
    }

    public function selectPaiementMode(string $mode): void
    {
        $this->paiementMode  = $mode;
        $this->errorPaiement = '';
    }

    /*
     * Soumettre le paiement d'une cotisation.
     *
     * Règles :
     * - Espèces → Paiement créé en_attente (validé par le BO).
     *   La cotisation reste en_retard jusqu'à la validation BO.
     * - Mobile Money → même logique (traitement externe).
     *
     * Dans les 2 cas : montant_paye est mis à jour sur la cotisation
     * pour refléter le versement, mais validated_at reste null.
     * La cotisation passe à a_jour uniquement quand le BO valide.
     */
    public function submitPaiement(): void
    {
        $this->errorPaiement = '';

        if (! $this->paiementMode) {
            $this->errorPaiement = 'Veuillez choisir un mode de paiement.';
            return;
        }

        $cot = Cotisation::findOrFail($this->paiementCotId);
        $montant = $cot->montant_restant > 0 ? $cot->montant_restant : ($cot->montant_du ?? 0);

        /* Créer le paiement en_attente */
        $paiement = Paiement::create([
            'customer_id'        => auth('customer')->user()->id,
            'type_cotisation_id' => $cot->type_cotisation_id,
            'cotisation_id'      => $cot->id,
            'montant'            => $montant,
            'mode_paiement'      => $this->paiementMode,
            'statut'             => 'en_attente',
            'date_paiement'      => now(),
        ]);

        /* MAJ cotisation : on enregistre le montant reçu */
        $cot->update([
            'montant_paye'    => $cot->montant_paye + $montant,
            'montant_restant' => 0,
            'mode_paiement'   => $this->paiementMode,
            'paiement_id'     => $paiement->id,
            /* statut reste en_retard — BO validera */
        ]);

        $this->closePaiement();
        $this->send_event_at_toast('Paiement enregistré ! En attente de validation.', 'success', 'top-end');
    }

    /* ═══════════════════════════════════════════════════════
       MODAL PAIEMENT EN AVANCE
    ═══════════════════════════════════════════════════════ */
    public function openAvance(): void
    {
        $customer = auth('customer')->user();

        /* Griser si pas de type mensuel */
        if (! $customer->type_cotisation_mensuel_id || ! $customer->montant_engagement) {
            $this->send_event_at_sweet_alert_not_timer(
                'Réservé aux cotisations mensuelles',
                'Le paiement en avance est réservé aux fidèles ayant souscrit à une cotisation mensuelle obligatoire.',
                'info'
            );
            return;
        }

        $this->nbMoisAvance  = 1;
        $this->avanceMode    = '';
        $this->errorAvance   = '';
        $this->previewAvance = [];

        $this->_buildPreviewAvance();

        $this->showAvance = true;
        $this->dispatch('OpenAvanceModal');
    }

    public function closeAvance(): void
    {
        $this->showAvance    = false;
        $this->nbMoisAvance  = 1;
        $this->avanceMode    = '';
        $this->errorAvance   = '';
        $this->previewAvance = [];
        $this->dispatch('closeAvanceModal');
    }

    public function updatedNbMoisAvance(): void
    {
        $this->_buildPreviewAvance();
    }

    public function selectAvanceMode(string $mode): void
    {
        $this->avanceMode  = $mode;
        $this->errorAvance = '';
    }

    /*
     * Calcule les mois qui seront créés en avance.
     * Part du mois suivant la dernière cotisation du type mensuel.
     */
    private function _buildPreviewAvance(): void
    {
        $this->previewAvance = [];
        $customer = auth('customer')->user();

        if (! $customer->type_cotisation_mensuel_id || ! $customer->montant_engagement) return;

        $nb         = max(1, min((int) $this->nbMoisAvance, 24));
        $engagement = $customer->montant_engagement;
        $tcId       = $customer->type_cotisation_mensuel_id;

        $derniere = Cotisation::where('customer_id', $customer->id)
            ->where('type_cotisation_id', $tcId)
            ->orderByDesc('annee')
            ->orderByDesc('mois')
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

        $this->previewAvance = $rows;
    }

    /*
     * Valide et crée les cotisations en avance.
     *
     * Structure :
     * - Un seul Paiement global créé (en_attente), lié à la
     *   première cotisation créée (cotisation_id = id 1re cot).
     * - Chaque cotisation créée stocke paiement_id = id du paiement.
     */
    public function submitAvance(): void
    {
        $this->errorAvance = '';

        if (! $this->avanceMode) {
            $this->errorAvance = 'Veuillez choisir un mode de paiement.';
            return;
        }

        if (empty($this->previewAvance)) {
            $this->errorAvance = 'Aucun mois à créer.';
            return;
        }

        $customer   = auth('customer')->user();
        $engagement = $customer->montant_engagement;
        $tcId       = $customer->type_cotisation_mensuel_id;
        $totalMontant = count($this->previewAvance) * $engagement;

        /* Créer le Paiement global (cotisation_id sera mis à jour après) */
        $paiement = Paiement::create([
            'customer_id'        => $customer->id,
            'type_cotisation_id' => $tcId,
            'cotisation_id'      => null, // mis à jour après 1re cot
            'montant'            => $totalMontant,
            'mode_paiement'      => $this->avanceMode,
            'statut'             => 'en_attente',
            'date_paiement'      => now(),
        ]);

        $premiereCot = null;

        foreach ($this->previewAvance as $row) {
            /* Sauter si ce mois existe déjà */
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
                'statut'             => 'en_retard', // validé par BO
                'mode_paiement'      => $this->avanceMode,
                'paiement_id'        => $paiement->id,
                'validated_by'       => null,
                'validated_at'       => null,
            ]);

            if (! $premiereCot) {
                $premiereCot = $cot;
                /* Lier le paiement à la 1re cotisation */
                $paiement->update(['cotisation_id' => $cot->id]);
            }
        }

        $this->closeAvance();
        $this->send_event_at_toast(
            count($this->previewAvance) . ' mois enregistrés en avance ! En attente de validation.',
            'success', 'top-end'
        );
    }

    /* ═══════════════════════════════════════════════════════
       MODAL RÉCLAMATION
    ═══════════════════════════════════════════════════════ */
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

        $this->detailId = null;
        $this->dispatch('OpenReclaModal');
    }

    public function closeRecla(): void
    {
        $this->reclaCotId        = null;
        $this->reclaLabel        = '';
        $this->reclaTitle        = '';
        $this->reclaMessage      = '';
        $this->errorReclaTitle   = '';
        $this->errorReclaMessage = '';
        $this->dispatch('closeReclaModal');
    }

    public function submitRecla(): void
    {
        $this->errorReclaTitle   = '';
        $this->errorReclaMessage = '';

        if (! trim($this->reclaTitle))   $this->errorReclaTitle   = 'Le titre est obligatoire.';
        if (! trim($this->reclaMessage)) $this->errorReclaMessage = 'Le message est obligatoire.';
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

    /* ═══════════════════════════════════════════════════════
       DONNÉES VUE
    ═══════════════════════════════════════════════════════ */
    public function with(): array
    {
        $customerId = auth('customer')->user()->id;
        $customer   = auth('customer')->user();

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
                            'en_attente' => 'En attente de validation',
                            'refund'     => 'Remboursé',
                            default      => 'Paiement',
                        },
                        'date'         => $p->date_paiement->format('d/m/Y') . ' · ' . $modeLabel,
                        'montant'      => match($p->statut) {
                            'success' => '+' . number_format($p->montant, 0, ',', ' '),
                            'echec'   => '-' . number_format($p->montant, 0, ',', ' '),
                            default   => number_format($p->montant, 0, ',', ' '),
                        },
                        'montantColor' => match($p->statut) {
                            'success' => '#0ab39c',
                            'echec'   => '#f06548',
                            default   => '#f7b84b',
                        },
                    ];
                })->toArray();
        }

        /* Pour le bouton "Payer en avance" */
        $hasMensuel = $customer->type_cotisation_mensuel_id && $customer->montant_engagement;

        return compact(
            'cotisations', 'grouped', 'summary',
            'detailCotisation', 'detailPaiements',
            'hasMensuel', 'customer'
        );
    }
};
?>
