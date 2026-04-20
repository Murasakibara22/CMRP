<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use App\Models\TypeCotisation;
use App\Traits\UtilsSweetAlert;

new  class extends Component
{
    use UtilsSweetAlert;

    // ─── Filtres & Vue ────────────────────────────────────
    public string $search       = '';
    public string $filterType   = 'tous';
    public string $filterStatus = 'tous';
    public string $vue          = 'grille';

    // ─── Formulaire ───────────────────────────────────────
    public ?int   $editId          = null;
    public string $libelle         = '';
    public string $description     = '';
    public string $type            = '';
    public bool   $isRequired      = false;
    public string $jourRecurrence  = '';
    public string $startAt         = '';
    public string $endAt           = '';
    public ?int   $montantObjectif = null;
    public ?int   $montant_minimum = null;

    // ─── Modal détail ─────────────────────────────────────
    public ?int $detailId = null;

    // ─── Changer vue ──────────────────────────────────────
    public function setVue(string $vue): void
    {
        $this->vue = $vue;
    }

    // ─── Ouvrir modal ajout ───────────────────────────────
    public function openAdd(): void
    {
        $this->resetForm();
        $this->launch_modal('modalTypeCotisation');
    }

    // ─── Ouvrir modal édition ─────────────────────────────
    public function openEdit(int $id): void
    {
        $tc = TypeCotisation::findOrFail($id);

        $this->editId          = $tc->id;
        $this->libelle         = $tc->libelle;
        $this->description     = $tc->description ?? '';
        $this->type            = $tc->type;
        $this->isRequired      = $tc->is_required;
        $this->jourRecurrence  = $tc->jour_recurrence ?? '';
        $this->startAt         = $tc->start_at?->format('Y-m-d') ?? '';
        $this->endAt           = $tc->end_at?->format('Y-m-d') ?? '';
        $this->montantObjectif = $tc->montant_objectif;
        $this->montant_minimum = $tc->montant_minimum;

        $this->launch_modal('modalTypeCotisation');
    }

    // ─── Ouvrir modal détail ──────────────────────────────
    public function openDetail(int $id): void
    {
        $this->detailId = $id;
        $this->launch_modal('modalDetailTC');
    }

    // ─── Sélectionner un type ─────────────────────────────
    public function selectType(string $type): void
    {
        $this->type = $type;

        if ($type === 'mensuel') {
            $this->isRequired     = true;
            $this->jourRecurrence = '';
            $this->startAt        = '';
            $this->endAt          = '';
            $this->montantObjectif = null;
        }

        if ($type === 'ordinaire' || $type === 'ramadan') {
            $this->jourRecurrence = '';
        }

        if ($type === 'jour_precis') {
            $this->startAt = '';
            $this->endAt   = '';
        }
    }

    // ─── Enregistrer ─────────────────────────────────────
    public function save(): void
    {
        $this->validate([
            'libelle' => 'required|string|min:2|max:150',
            'type'    => 'required|in:mensuel,ordinaire,jour_precis,ramadan',
            'startAt' => 'nullable|date',
            'endAt'   => 'nullable|date|after_or_equal:startAt',
            'montantObjectif' => 'nullable|integer|min:1',
        ]);

        $data = [
            'libelle'          => $this->libelle,
            'description'      => $this->description ?: null,
            'type'             => $this->type,
            'is_required'      => $this->isRequired,
            'jour_recurrence'  => $this->jourRecurrence ?: null,
            'montant_objectif' => $this->montantObjectif,
            'montant_minimum'  => $this->montant_minimum,
            'start_at'         => $this->startAt ?: null,
            'end_at'           => $this->endAt ?: null,
            'status'           => 'actif',
        ];

        if ($this->editId) {
            TypeCotisation::findOrFail($this->editId)->update($data);
            $this->send_event_at_toast('Type modifié avec succès', 'success', 'top-end');
        } else {
            TypeCotisation::create($data);
            $this->send_event_at_toast('Type créé avec succès', 'success', 'top-end');
        }

        $this->closeModal_after_edit('modalTypeCotisation');
        $this->resetForm();
    }

    // ─── Toggle statut ────────────────────────────────────
    public function toggleStatus(int $id): void
    {
        $tc = TypeCotisation::findOrFail($id);

        if ($tc->type === 'mensuel' && $tc->is_required && $tc->status === 'actif') {
            $this->send_event_at_sweet_alert_not_timer(
                'Action impossible',
                'Vous ne pouvez pas désactiver le type mensuel obligatoire.',
                'warning'
            );
            return;
        }

        $newStatus = $tc->status === 'actif' ? 'inactif' : 'actif';
        $tc->update(['status' => $newStatus]);
        $label = $newStatus === 'actif' ? 'activé' : 'désactivé';
        $this->send_event_at_toast("« {$tc->libelle} » {$label}", 'success', 'top-end');
    }

    // ─── Confirmation suppression ─────────────────────────
    public function confirmDelete(int $id): void
    {
        $tc = TypeCotisation::findOrFail($id);

        if ($tc->cotisations()->exists()) {
            $this->send_event_at_sweet_alert_not_timer(
                'Suppression impossible',
                "Le type « {$tc->libelle} » possède des cotisations liées. Désactivez-le plutôt.",
                'error'
            );
            return;
        }

        $this->sweetAlert_confirm_options_with_button(
            $tc,
            'Supprimer ce type ?',
            "La suppression de « {$tc->libelle} » est définitive.",
            'deleteConfirmed',
            'warning',
            'Oui, supprimer',
            'Annuler'
        );
    }

    // ─── Suppression confirmée ────────────────────────────
    #[On('deleteConfirmed')]
    public function deleteConfirmed(int $id): void
    {
        $tc = TypeCotisation::find($id);
        if (! $tc) return;

        $libelle = $tc->libelle;
        $tc->delete();

        if ($this->detailId === $id) {
            $this->detailId = null;
            $this->closeModal_after_edit('modalDetailTC');
        }

        $this->send_event_at_toast("« {$libelle} » supprimé", 'success', 'top-end');
    }

    // ─── Reset formulaire ─────────────────────────────────
    protected function resetForm(): void
    {
        $this->editId          = null;
        $this->libelle         = '';
        $this->description     = '';
        $this->type            = '';
        $this->isRequired      = false;
        $this->jourRecurrence  = '';
        $this->startAt         = '';
        $this->endAt           = '';
        $this->montantObjectif = null;
        $this->montant_minimum = null;
        $this->resetErrorBag();
    }

    // ─── Données ──────────────────────────────────────────
    public function with(): array
    {
        $typeCotisations = TypeCotisation::withCount('cotisations')
            ->when($this->search, fn($q) =>
                $q->where('libelle', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
            )
            ->when($this->filterType !== 'tous', fn($q) =>
                $q->where('type', $this->filterType)
            )
            ->when($this->filterStatus !== 'tous', fn($q) =>
                $q->where('status', $this->filterStatus)
            )
            ->latest()
            ->get();

        $kpis = [
            'total'   => TypeCotisation::count(),
            'actifs'  => TypeCotisation::where('status', 'actif')->count(),
            'requis'  => TypeCotisation::where('is_required', true)->count(),
            'enCours' => TypeCotisation::enCours()->count(),
        ];

        $detailTC = $this->detailId
            ? TypeCotisation::withCount('cotisations')
                ->with(['cotisations' => fn($q) => $q->with('customer')->latest()->limit(5)])
                ->find($this->detailId)
            : null;

        return compact('typeCotisations', 'kpis', 'detailTC');
    }
};
?>
