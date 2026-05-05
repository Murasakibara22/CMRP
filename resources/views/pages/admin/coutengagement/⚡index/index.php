<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\CoutEngagement;
use App\Traits\UtilsSweetAlert;

new class extends Component
{
    use WithPagination;
    use UtilsSweetAlert;

    /* ── Formulaire ─────────────────────────────────────── */
    public ?int   $editId   = null;
    public string $montant  = '';
    public string $libelle  = '';  // label optionnel ex: "Palier Bronze"
    public string   $isActif  = 'actif';
    public string $search   = '';

    public string $errorMontant = '';
    public string $errorLibelle = '';

    public function updatingSearch(): void { $this->resetPage(); }

    /* ── Ouvrir modal ajout ─────────────────────────────── */
    public function openAdd(): void
    {
        abort_unless(auth()->user()?->hasPermission('COUT_ENGAGEMENT_CREATE'), 403);
        

        $this->editId   = null;
        $this->montant  = '';
        $this->libelle  = '';
        $this->isActif  = 'actif';
        $this->errorMontant = '';
        $this->errorLibelle = '';
        $this->launch_modal('modalCoutEngagement');
    }

    /* ── Ouvrir modal édition ───────────────────────────── */
    public function openEdit(int $id): void
    {
        abort_unless(auth()->user()?->hasPermission('COUT_ENGAGEMENT_EDIT'), 403);

        $ce = CoutEngagement::findOrFail($id);
        $this->editId  = $ce->id;
        $this->montant = (string) $ce->montant;
        $this->libelle = $ce->libelle ?? '';
        $this->isActif = $ce->status ?? 'actif';
        $this->errorMontant = '';
        $this->errorLibelle = '';
        $this->launch_modal('modalCoutEngagement');
    }

    public function closeModal(): void
    {
        $this->closeModal_after_edit('modalCoutEngagement');
    }

    /* ── Sauvegarder ────────────────────────────────────── */
    public function save(): void
    {
        abort_unless(
            auth()->user()?->hasPermission('COUT_ENGAGEMENT_CREATE') ||
            auth()->user()?->hasPermission('COUT_ENGAGEMENT_EDIT'),
            403
        );

        $this->errorMontant = '';
        $this->errorLibelle = '';

        $montantInt = (int) str_replace([' ', '.', ','], '', $this->montant);
        if (! $montantInt || $montantInt < 100) {
            $this->errorMontant = 'Le montant doit être un entier positif (min. 100 FCFA).';
            return;
        }

        // Anti-doublon
        $exists = CoutEngagement::where('montant', $montantInt)
            ->when($this->editId, fn($q) => $q->where('id', '!=', $this->editId))
            ->exists();
        if ($exists) {
            $this->errorMontant = 'Ce montant existe déjà.';
            return;
        }

        $data = [
            'montant'  => $montantInt,
            'libelle'  => trim($this->libelle) ?: null,
            'status' => $this->isActif,
        ];

        if ($this->editId) {
            CoutEngagement::find($this->editId)->update($data);
            $msg = 'Palier modifié avec succès !';
        } else {
            CoutEngagement::create($data);
            $msg = 'Palier créé avec succès !';
        }

        $this->closeModal_after_edit('modalCoutEngagement');
        $this->send_event_at_toast($msg, 'success', 'top-end');
    }

    /* ── Supprimer ──────────────────────────────────────── */
    public function confirmDelete(int $id): void
    {
        abort_unless(auth()->user()?->hasPermission('COUT_ENGAGEMENT_DELETE'), 403);

        $ce = CoutEngagement::find($id);
        if (! $ce) return;
        $this->sweetAlert_confirm_options_with_button(
            $ce, 'Supprimer ce palier ?',
            'Les clients avec cet engagement ne seront pas affectés.',
            'doDelete', 'warning', 'Supprimer', 'Annuler'
        );
    }

    #[\Livewire\Attributes\On('doDelete')]
    public function doDelete(int $id): void
    {
        CoutEngagement::find($id)?->delete();
        $this->send_event_at_toast('Palier supprimé.', 'success', 'top-end');
    }

    /* ── Toggle actif ───────────────────────────────────── */
    public function toggleActif(int $id): void
    {
        $ce = CoutEngagement::find($id);
        if (! $ce) return;
        $ce->update(['status' => $ce->isActif() ? 'inactif' : 'actif' ]);
        $this->send_event_at_toast(
            $ce->status ? 'Palier activé.' : 'Palier désactivé.',
            'success', 'top-end'
        );
    }

    public function with(): array
    {
        $couts = CoutEngagement::when($this->search, fn($q) =>
                $q->where('montant', 'like', '%'.$this->search.'%')
                  ->orWhere('libelle', 'like', '%'.$this->search.'%')
            )
            ->orderBy('montant')
            ->paginate(15);

        $kpis = [
            'total'  => CoutEngagement::count(),
            'actifs' => CoutEngagement::where('status', 'actif')->count(),
            'min'    => CoutEngagement::min('montant') ?? 0,
            'max'    => CoutEngagement::max('montant') ?? 0,
        ];

        return compact('couts', 'kpis');
    }
};
?>
