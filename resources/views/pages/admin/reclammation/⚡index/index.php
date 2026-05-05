<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Reclammation;
use App\Models\HistoriqueReclammation;
use App\Models\User;
use App\Traits\UtilsSweetAlert;
use Illuminate\Support\Carbon;

new class extends Component
{
    use WithPagination, UtilsSweetAlert;

    /* ── Filtres ────────────────────────────────────────── */
    public string $search     = '';
    public string $filterStatut = 'tous';

    /* ── Modal détail / réponse ─────────────────────────── */
    public ?int   $detailId    = null;
    public string $reponse     = '';
    public string $newStatut   = '';
    public string $errorReponse = '';

    public function updatingSearch(): void   { $this->resetPage(); }
    public function updatingFilterStatut(): void { $this->resetPage(); }

    /* ── Ouvrir modal détail ────────────────────────────── */
    public function openDetail(int $id): void
    {
        abort_unless(auth()->user()?->hasPermission('RECLAMATION_SHOW_ONE'), 403);

        $this->detailId     = $id;
        $this->reponse      = '';
        $this->newStatut    = '';
        $this->errorReponse = '';
        $this->launch_modal('modalDetailRecla');
    }

    public function closeDetail(): void
    {
        $this->detailId = null;
        $this->closeModal_after_edit('modalDetailRecla');
    }

    /* ── Envoyer une réponse + changer statut ───────────── */
    public function repondre(): void
    {
        abort_unless(auth()->user()?->hasPermission('RECLAMATION_EDIT'), 403);

        $this->errorReponse = '';

        if (! trim($this->reponse)) {
            $this->errorReponse = 'La réponse est obligatoire.';
            return;
        }
        if (! $this->newStatut) {
            $this->errorReponse = 'Veuillez choisir un statut.';
            return;
        }

        $recla = Reclammation::find($this->detailId);
        if (! $recla) return;

        $recla->update([
            'status'          => $this->newStatut,
            'reponse'         => trim($this->reponse),
            'user_charged_id' => auth()->id(),
            'resolved_at'     => in_array($this->newStatut, ['resolu', 'rejete']) ? now() : null,
        ]);

        HistoriqueReclammation::create([
            'reclammation_id'       => $recla->id,
            'description'           => trim($this->reponse),
            'status'                => $this->newStatut,
            'user_charged_id'       => auth()->id(),
            'snapshot_reclammation' => json_encode($recla->fresh()->toArray()),
        ]);

        $this->closeModal_after_edit('modalDetailRecla');
        $this->detailId = null;
        $this->send_event_at_toast('Réponse envoyée avec succès !', 'success', 'top-end');
    }

    /* ── Changer statut rapide depuis la table ──────────── */
    public function changerStatut(int $id, string $statut): void
    {
        abort_unless(auth()->user()?->hasPermission('RECLAMATION_EDIT'), 403);

        $recla = Reclammation::find($id);
        if (! $recla) return;

        $recla->update([
            'status'      => $statut,
            'resolved_at' => in_array($statut, ['resolu', 'rejete']) ? now() : null,
        ]);

        $this->send_event_at_toast('Statut mis à jour.', 'success', 'top-end');
    }

    /* ── Supprimer ──────────────────────────────────────── */
    public function confirmDelete(int $id): void
    {
        abort_unless(auth()->user()?->hasPermission('RECLAMATION_DELETE'), 403);

        $recla = Reclammation::find($id);
        if ($recla) {
            $this->sweetAlert_confirm_options_with_button(
                $recla, 'Supprimer cette réclamation ?',
                'Cette action est irréversible.',
                'doDelete', 'warning',
                'Supprimer', 'Annuler'
            );
        }
    }

    #[\Livewire\Attributes\On('doDelete')]
    public function doDelete(int $id): void
    {
        Reclammation::find($id)?->delete();
        $this->send_event_at_toast('Réclamation supprimée.', 'success', 'top-end');
    }

    /* ── Données vue ────────────────────────────────────── */
    public function with(): array
    {
        $query = Reclammation::with(['customer', 'cotisation.typeCotisation', 'userCharged'])
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('sujet', 'like', '%'.$this->search.'%')
                  ->orWhereHas('customer', fn($q) =>
                      $q->where('nom', 'like', '%'.$this->search.'%')
                        ->orWhere('prenom', 'like', '%'.$this->search.'%')
                  );
            }))
            ->when($this->filterStatut !== 'tous', fn($q) => $q->where('status', $this->filterStatut))
            ->orderByDesc('created_at');

        $reclammations = $query->paginate(15);

        $kpis = [
            'total'    => Reclammation::count(),
            'ouverte'  => Reclammation::whereIn('status', ['ouverte', 'en_cours'])->count(),
            'resolu'   => Reclammation::where('status', 'resolu')->count(),
            'rejete'   => Reclammation::where('status', 'rejete')->count(),
        ];

        $detailRecla = $this->detailId
            ? Reclammation::with([
                'customer',
                'cotisation.typeCotisation',
                'historiqueReclammation',
                'userCharged',
              ])->find($this->detailId)
            : null;

        return compact('reclammations', 'kpis', 'detailRecla');
    }
};
?>
