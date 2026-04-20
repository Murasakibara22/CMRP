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

    /* ── Modal détail cotisation ────────────────────────── */
    public ?int $detailId = null;

    /* ── Modal paiement simple ──────────────────────────── */
    public bool   $showPaiement    = false;
    public ?int   $paiementCotId   = null;
    public string $paiementLabel   = '';
    public int    $paiementMontant = 0;
    public string $paiementMode    = '';
    public string $errorPaiement   = '';

    /* ── Modal paiement en avance ───────────────────────── */
    public bool   $showAvance    = false;
    public int    $nbMoisAvance  = 1;
    public string $avanceMode    = '';
    public string $errorAvance   = '';
    public array  $previewAvance = [];

    /* ── Modal réclamation depuis détail ────────────────── */
    public bool   $showRecla         = false;
    public ?int   $reclaCotId        = null;
    public string $reclaLabel        = '';
    public string $reclaTitle        = '';
    public string $reclaMessage      = '';
    public string $errorReclaTitle   = '';
    public string $errorReclaMessage = '';

    /* ═══════════════════════════════════════════════════════
       MODAL DÉTAIL
    ═══════════════════════════════════════════════════════ */
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

    /* ═══════════════════════════════════════════════════════
       MODAL PAIEMENT SIMPLE
    ═══════════════════════════════════════════════════════ */
    public function openPaiement(int $cotId): void
    {
        $cot = Cotisation::with('typeCotisation')->findOrFail($cotId);

        $this->paiementCotId   = $cotId;
        $this->paiementMontant = $cot->montant_restant > 0
            ? $cot->montant_restant
            : ($cot->montant_du ?? 0);
        $this->paiementMode  = '';
        $this->errorPaiement = '';
        $this->paiementLabel = ($cot->typeCotisation?->libelle ?? '—')
            . ($cot->mois
                ? ' — ' . Carbon::create($cot->annee, $cot->mois)->translatedFormat('F Y')
                : '');

        /* Fermer le détail */
        $this->detailId = null;
        $this->dispatch('closeHomeCotDetail');

        $this->showPaiement = true;
        $this->dispatch('OpenHomePaiement');
    }

    public function closePaiement(): void
    {
        $this->showPaiement    = false;
        $this->paiementCotId   = null;
        $this->paiementMode    = '';
        $this->errorPaiement   = '';
        $this->dispatch('closeHomePaiement');
    }

    public function selectPaiementMode(string $mode): void
    {
        $this->paiementMode  = $mode;
        $this->errorPaiement = '';
    }

    public function submitPaiement(): void
    {
        $this->errorPaiement = '';

        if (! $this->paiementMode) {
            $this->errorPaiement = 'Veuillez choisir un mode de paiement.';
            return;
        }

        $cot     = Cotisation::findOrFail($this->paiementCotId);
        $montant = $cot->montant_restant > 0
            ? $cot->montant_restant
            : ($cot->montant_du ?? 0);

        $paiement = Paiement::create([
            'customer_id'        => auth('customer')->user()->id,
            'type_cotisation_id' => $cot->type_cotisation_id,
            'cotisation_id'      => $cot->id,
            'montant'            => $montant,
            'mode_paiement'      => $this->paiementMode,
            'statut'             => 'en_attente',
            'date_paiement'      => now(),
        ]);

        $cot->update([
            'montant_paye'    => $cot->montant_paye + $montant,
            'montant_restant' => 0,
            'mode_paiement'   => $this->paiementMode,
            'paiement_id'     => $paiement->id,
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
        $this->_buildPreviewAvance();

        $this->showAvance = true;
        $this->dispatch('OpenHomeAvance');
    }

    public function closeAvance(): void
    {
        $this->showAvance    = false;
        $this->nbMoisAvance  = 1;
        $this->avanceMode    = '';
        $this->errorAvance   = '';
        $this->previewAvance = [];
        $this->dispatch('closeHomeAvance');
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
        $this->previewAvance = $rows;
    }

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

        $customer     = auth('customer')->user();
        $engagement   = $customer->montant_engagement;
        $tcId         = $customer->type_cotisation_mensuel_id;
        $totalMontant = count($this->previewAvance) * $engagement;

        $paiement = Paiement::create([
            'customer_id'        => $customer->id,
            'type_cotisation_id' => $tcId,
            'cotisation_id'      => null,
            'montant'            => $totalMontant,
            'mode_paiement'      => $this->avanceMode,
            'statut'             => 'en_attente',
            'date_paiement'      => now(),
        ]);

        $premiereCot = null;
        foreach ($this->previewAvance as $row) {
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
                'statut'             => 'en_retard',
                'mode_paiement'      => $this->avanceMode,
                'paiement_id'        => $paiement->id,
                'validated_by'       => null,
                'validated_at'       => null,
            ]);

            if (! $premiereCot) {
                $premiereCot = $cot;
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
        $this->dispatch('closeHomeCotDetail');
        $this->dispatch('OpenHomeRecla');
    }

    public function closeRecla(): void
    {
        $this->reclaCotId        = null;
        $this->reclaTitle        = '';
        $this->reclaMessage      = '';
        $this->errorReclaTitle   = '';
        $this->errorReclaMessage = '';
        $this->dispatch('closeHomeRecla');
    }

    public function submitRecla(): void
    {
        $this->errorReclaTitle   = '';
        $this->errorReclaMessage = '';
        if (! trim($this->reclaTitle))   $this->errorReclaTitle   = 'Le titre est obligatoire.';
        if (! trim($this->reclaMessage)) $this->errorReclaMessage = 'Le message est obligatoire.';
        if ($this->errorReclaTitle || $this->errorReclaMessage) return;

        $customerId   = auth('customer')->user()->id;
        $reclammation = \App\Models\Reclammation::create([
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
        $this->send_event_at_toast('Réclamation envoyée !', 'success', 'top-end');
        $this->closeRecla();
    }

    /* ═══════════════════════════════════════════════════════
       DONNÉES VUE
    ═══════════════════════════════════════════════════════ */
    public function with(): array
    {
        $customer   = auth('customer')->user();
        $customerId = $customer->id;

        $totalCotise = Paiement::where('customer_id', $customerId)
            ->where('statut', 'success')->sum('montant');

        $totalDu = Cotisation::where('customer_id', $customerId)
            ->whereIn('statut', ['en_retard', 'partiel'])->sum('montant_restant');

        $moisRetard = Cotisation::where('customer_id', $customerId)
            ->where('statut', 'en_retard')->count();

        $retardDetails = Cotisation::where('customer_id', $customerId)
            ->where('statut', 'en_retard')->whereNotNull('mois')
            ->orderBy('annee')->orderBy('mois')->get()
            ->map(fn($c) => Carbon::create($c->annee, $c->mois)->translatedFormat('F Y'));

        $prochainMois    = null;
        $prochainMontant = $customer->montant_engagement ?? 0;
        if ($customer->montant_engagement) {
            $prochainMois = now()->addMonth()->translatedFormat('F Y');
        }

        /* Historique mixte */
        $cotisations = Cotisation::with('typeCotisation')
            ->where('customer_id', $customerId)
            ->orderByDesc('created_at')->limit(6)->get()
            ->map(function ($c) {
                $statut = $c->statut;
                [$iconClass, $iconBg, $iconColor, $pillClass, $pillLabel, $amountColor] = match($statut) {
                    'a_jour'    => ['ri-checkbox-circle-line', 'rgba(10,179,156,.10)', '#0ab39c', 'pill-ok',     'À jour',    '#0ab39c'],
                    'partiel'   => ['ri-error-warning-line',   'rgba(247,184,75,.12)', '#f7b84b', 'pill-warn',   'Partiel',   '#f7b84b'],
                    'en_retard' => ['ri-time-line',            'rgba(240,101,72,.10)', '#f06548', 'pill-danger', 'En retard', '#f06548'],
                    default     => ['ri-calendar-line',        'rgba(64,81,137,.10)',  '#405189', 'pill-info',   'En attente','#405189'],
                };
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
                    'mobile_money' => 'Mobile Money', 'espece' => 'Espèces',
                    'virement' => 'Virement', default => null,
                };
                $dateLabel = $statut === 'en_retard'
                    ? 'Non payé · En retard'
                    : ($c->validated_at
                        ? $c->validated_at->format('d/m/Y') . ($modeLabel ? ' · ' . $modeLabel : '')
                        : $c->created_at->format('d/m/Y') . ($modeLabel ? ' · ' . $modeLabel : ''));
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
                    'montant'     => $montant > 0 ? ($statut === 'a_jour' ? '+' : '-') . number_format($montant, 0, ',', ' ') : null,
                    'amountColor' => $amountColor,
                    'pillClass'   => $pillClass,
                    'pillLabel'   => $pillLabel,
                    'statut'      => $statut,
                ];
            });

        $reclamations = Reclammation::where('customer_id', $customerId)
            ->orderByDesc('created_at')->limit(3)->get()
            ->map(fn($r) => [
                'id'          => $r->id,
                'type'        => 'reclamations',
                'iconClass'   => 'ri-flag-line',
                'iconBg'      => 'rgba(41,156,219,.12)',
                'iconColor'   => '#299cdb',
                'title'       => 'Réclamation — ' . \Str::limit($r->sujet, 30),
                'date'        => $r->created_at->format('d/m/Y') . ' · ' . match($r->status) {
                    'ouverte','en_cours' => 'En cours de traitement',
                    'resolu'  => 'Résolue', 'rejete' => 'Rejetée', default => $r->status,
                },
                'montant'     => null,
                'amountColor' => null,
                'pillClass'   => match($r->status) {
                    'ouverte','en_cours' => 'pill-info', 'resolu' => 'pill-ok',
                    'rejete' => 'pill-danger', default => 'pill-info',
                },
                'pillLabel'   => match($r->status) {
                    'ouverte','en_cours' => 'En cours', 'resolu' => 'Résolu',
                    'rejete' => 'Rejeté', default => '—',
                },
                'statut' => $r->status,
            ]);

        $historique = $cotisations->concat($reclamations)
            ->sortByDesc(fn($item) => $item['id'])
            ->values()->take(8);

        $detailCotisation = $this->detailId
            ? Cotisation::with(['typeCotisation', 'paiements'])->find($this->detailId)
            : null;

        $detailPaiements = [];
        if ($detailCotisation) {
            $detailPaiements = Paiement::where('cotisation_id', $detailCotisation->id)
                ->orderByDesc('date_paiement')->get()
                ->map(fn($p) => [
                    'montant'      => ($p->statut === 'success' ? '+' : '') . number_format($p->montant, 0, ',', ' '),
                    'montantColor' => $p->statut === 'success' ? '#0ab39c' : ($p->statut === 'echec' ? '#f06548' : '#f7b84b'),
                    'date'         => $p->date_paiement->format('d/m/Y'),
                    'mode'         => match($p->mode_paiement){ 'mobile_money'=>'Mobile Money','espece'=>'Espèces','virement'=>'Virement',default=>'—'},
                    'statut'       => $p->statut,
                ])->toArray();
        }

        $hasMensuel = $customer->type_cotisation_mensuel_id && $customer->montant_engagement;

        return compact(
            'customer', 'totalCotise', 'totalDu', 'moisRetard',
            'retardDetails', 'prochainMois', 'prochainMontant',
            'historique', 'detailCotisation', 'detailPaiements',
            'hasMensuel'
        );
    }
};
?>
