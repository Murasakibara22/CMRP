<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\TypeDepense;
use App\Traits\UtilsSweetAlert;

new  class extends Component
{
    use WithPagination, UtilsSweetAlert;

    /* ── Liste ─────────────────────────────────────────── */
    public string $search = '';

    /* ── Formulaire ────────────────────────────────────── */
    public ?int   $editId      = null;
    public string $libelle     = '';
    public string $description = '';
    public string $status      = 'actif';

    public function updatedSearch(): void { $this->resetPage(); }

    /* ── Ouvrir modal ajout ─────────────────────────────── */
    public function openAdd(): void
    {
        abort_unless(auth()->user()?->hasPermission('TYPE_DEPENSE_CREATE'), 403);

        $this->resetForm();
        $this->launch_modal('modalTypeDepense');
    }

    /* ── Ouvrir modal modification ──────────────────────── */
    public function openEdit(int $id): void
    {
        abort_unless(auth()->user()?->hasPermission('TYPE_DEPENSE_EDIT'), 403);

        $td = TypeDepense::findOrFail($id);
        $this->resetForm();
        $this->editId      = $id;
        $this->libelle     = $td->libelle;
        $this->description = $td->description ?? '';
        $this->status      = $td->status;
        $this->launch_modal('modalTypeDepense');
    }

    /* ── Save ───────────────────────────────────────────── */
    public function save(): void
    {
        abort_unless(auth()->user()?->hasPermission('TYPE_DEPENSE_CREATE') || auth()->user()?->hasPermission('TYPE_DEPENSE_EDIT'), 403);

        $this->validate([
            'libelle' => 'required|string|max:120',
            'status'  => 'required|in:actif,inactif',
        ]);

        if ($this->editId) {
            TypeDepense::findOrFail($this->editId)->update([
                'libelle'     => $this->libelle,
                'description' => $this->description ?: null,
                'status'      => $this->status,
            ]);
            $msg = 'Type de dépense modifié avec succès';
        } else {
            TypeDepense::create([
                'libelle'     => $this->libelle,
                'description' => $this->description ?: null,
                'status'      => $this->status,
            ]);
            $msg = 'Type de dépense créé avec succès';
        }

        $this->closeModal_after_edit('modalTypeDepense');
        $this->resetForm();
        $this->send_event_at_toast($msg, 'success', 'top-end');
    }

    /* ── Toggle statut rapide ───────────────────────────── */
    public function toggleStatus(int $id): void
    {
        abort_unless(auth()->user()?->hasPermission('TYPE_DEPENSE_ACTIVATE'), 403);

        $td = TypeDepense::findOrFail($id);
        $td->update(['status' => $td->status === 'actif' ? 'inactif' : 'actif']);
        $this->send_event_at_toast('Statut mis à jour', 'success', 'top-end');
    }

    /* ── Supprimer ──────────────────────────────────────── */
    public function confirmDelete(int $id): void
    {
        abort_unless(auth()->user()?->hasPermission('TYPE_DEPENSE_DELETE'), 403);

        $td = TypeDepense::findOrFail($id);
        $this->sweetAlert_confirm_options_with_button(
            $td,
            'Supprimer ce type de dépense ?',
            $td->depenses()->count() > 0
                ? "Attention : {$td->depenses()->count()} dépense(s) liée(s). Elles seront désassociées."
                : 'Cette action est irréversible.',
            'deleteConfirmed', 'warning', 'Oui, supprimer', 'Annuler'
        );
    }

    #[On('deleteConfirmed')]
    public function deleteConfirmed(int $id): void
    {
        TypeDepense::find($id)?->delete();
        $this->send_event_at_toast('Type de dépense supprimé', 'success', 'top-end');
    }

    protected function resetForm(): void
    {
        $this->editId      = null;
        $this->libelle     = '';
        $this->description = '';
        $this->status      = 'actif';
        $this->resetErrorBag();
    }

    public function with(): array
    {
        $typeDepenses = TypeDepense::withCount('depenses')
            ->withSum('depenses', 'montant')
            ->when($this->search, fn($q) =>
                $q->where('libelle', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
            )
            ->orderBy('libelle')
            ->paginate(15);

        $kpis = [
            'total'   => TypeDepense::count(),
            'actifs'  => TypeDepense::where('status', 'actif')->count(),
            'inactifs'=> TypeDepense::where('status', 'inactif')->count(),
            'montant' => TypeDepense::join('depense', 'type_depense.id', '=', 'depense.type_depense_id')
                            ->sum('depense.montant'),
        ];

        return compact('typeDepenses', 'kpis');
    }
};
?>
