<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Cotisation;
use App\Models\Reclammation;
use App\Models\HistoriqueReclammation;
use App\Traits\UtilsSweetAlert;
use Illuminate\Support\Carbon;

new #[Layout('layouts.app-frontend')] class extends Component
{
    use UtilsSweetAlert;

    /* ── Modal ajout ────────────────────────────────────── */
    public ?int   $addCotisationId = null;
    public string $addTitre        = '';
    public string $addMessage      = '';
    public string $errorTitre      = '';
    public string $errorMessage    = '';

    /* ── Modal détail ───────────────────────────────────── */
    public ?int $detailId = null;

    /* ── Ouvrir / fermer modal ajout ────────────────────── */
    public function openAdd(): void
    {
        $this->addCotisationId = null;
        $this->addTitre        = '';
        $this->addMessage      = '';
        $this->errorTitre      = '';
        $this->errorMessage    = '';
        $this->dispatch('OpenAddRecla');
    }

    public function closeAdd(): void
    {
        $this->dispatch('closeAddRecla');
    }

    /* ── Ouvrir / fermer modal détail ───────────────────── */
    public function openDetail(int $id): void
    {
        $this->detailId = $id;
        $this->dispatch('OpenDetailRecla');
    }

    public function closeDetail(): void
    {
        $this->dispatch('closeDetailRecla');
        $this->detailId = null;
    }

    /* ── Soumettre réclamation ──────────────────────────── */
    public function submitRecla(): void
    {
        $this->errorTitre   = '';
        $this->errorMessage = '';

        if (! trim($this->addTitre))   { $this->errorTitre   = 'Le titre est obligatoire.'; }
        if (! trim($this->addMessage)) { $this->errorMessage  = 'Le message est obligatoire.'; }
        if ($this->errorTitre || $this->errorMessage) return;

        $customerId = auth('customer')->user()->id;

        $recla = Reclammation::create([
            'customer_id'   => $customerId,
            'cotisation_id' => $this->addCotisationId ?: null,
            'sujet'         => trim($this->addTitre),
            'description'   => trim($this->addMessage),
            'status'        => 'en_attente',
        ]);

        HistoriqueReclammation::create([
            'reclammation_id'       => $recla->id,
            'description'           => 'Réclamation créée par le fidèle.',
            'status'                => 'en_attente',
            'snapshot_reclammation' => json_encode($recla->toArray()),
        ]);

        $this->dispatch('closeAddRecla');
        $this->addTitre   = '';
        $this->addMessage = '';
        $this->send_event_at_toast('Réclamation envoyée avec succès !', 'success', 'top-end');
    }

    /* ── Données vue ────────────────────────────────────── */
    public function with(): array
    {
        $customerId = auth('customer')->user()->id;

        $reclammations = Reclammation::with([
                'cotisation.typeCotisation',
                'historiqueReclammation',
            ])
            ->where('customer_id', $customerId)
            ->orderByDesc('created_at')
            ->get();

        $cotisations = Cotisation::with('typeCotisation')
            ->where('customer_id', $customerId)
            ->orderByDesc('annee')->orderByDesc('mois')
            ->get()
            ->map(fn($c) => [
                'id'    => $c->id,
                'label' => ($c->typeCotisation?->libelle ?? '—')
                    . ($c->mois && $c->annee
                        ? ' — ' . Carbon::create($c->annee, $c->mois)->translatedFormat('F Y')
                        : ''),
            ]);

        $detailRecla = $this->detailId
            ? Reclammation::with(['cotisation.typeCotisation', 'historiqueReclammation'])
                ->find($this->detailId)
            : null;

        return compact('reclammations', 'cotisations', 'detailRecla');
    }
};
?>
