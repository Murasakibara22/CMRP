<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Role;
use App\Models\Permission;
use App\Traits\UtilsSweetAlert;

new class extends Component
{
    use UtilsSweetAlert;

    /* ── Formulaire rôle ─────────────────────────────────── */
    public ?int   $editId      = null;
    public string $libelle     = '';
    public string $code        = '';
    public string $description = '';
    public array  $permissionIds = [];   // IDs cochés

    public string $errorLibelle = '';
    public string $errorCode    = '';

    /* ── Stats BO ────────────────────────────────────────── */
    public function updatedLibelle(): void
    {
        if (! $this->editId) {
            // Auto-générer le code depuis le libellé
            $this->code = strtoupper(preg_replace('/[^A-Z0-9]/i', '_', $this->libelle));
        }
    }

    /* ── Ouvrir modal ajout ─────────────────────────────── */
    public function openAdd(): void
    {
        abort_unless(auth()->user()?->hasPermission('ROLE_CREATE'), 403);

        $this->editId        = null;
        $this->libelle       = '';
        $this->code          = '';
        $this->description   = '';
        $this->permissionIds = [];
        $this->errorLibelle  = '';
        $this->errorCode     = '';
        $this->launch_modal('modalRole');
    }

    /* ── Ouvrir modal édition ───────────────────────────── */
    public function openEdit(int $id): void
    {
        abort_unless(auth()->user()?->hasPermission('ROLE_EDIT'), 403);
        
        $role = Role::with('permissions')->findOrFail($id);
        $this->editId        = $role->id;
        $this->libelle       = $role->libelle;
        $this->code          = $role->code;
        $this->description   = $role->description ?? '';
        $this->permissionIds = $role->permissions->pluck('id')->map(fn($i) => (string)$i)->toArray();
        $this->errorLibelle  = '';
        $this->errorCode     = '';
        $this->launch_modal('modalRole');
    }

    public function closeModal(): void
    {
        $this->closeModal_after_edit('modalRole');
    }

    /* ── Toggle permission dans le formulaire ───────────── */
    public function togglePermission(int $permId): void
    {
        $key = (string) $permId;
        if (in_array($key, $this->permissionIds)) {
            $this->permissionIds = array_values(array_filter($this->permissionIds, fn($p) => $p !== $key));
        } else {
            $this->permissionIds[] = $key;
        }
    }

    /* ── Tout sélectionner / déselectionner par groupe ── */
    public function toggleGroupe(string $groupe, array $permIds): void
    {
        $ids = array_map('strval', $permIds);
        $allSelected = empty(array_diff($ids, $this->permissionIds));

        if ($allSelected) {
            $this->permissionIds = array_values(array_diff($this->permissionIds, $ids));
        } else {
            $this->permissionIds = array_values(array_unique(array_merge($this->permissionIds, $ids)));
        }
    }

    /* ── Sauvegarder rôle ───────────────────────────────── */
    public function save(): void
    {
        abort_unless(
            auth()->user()?->hasPermission('ROLE_CREATE') ||
            auth()->user()?->hasPermission('ROLE_EDIT'),
            403
        );

        $this->errorLibelle = '';
        $this->errorCode    = '';

        if (! trim($this->libelle)) { $this->errorLibelle = 'Le libellé est obligatoire.'; }
        if (! trim($this->code))    { $this->errorCode    = 'Le code est obligatoire.'; }
        if ($this->errorLibelle || $this->errorCode) return;

        $codeClean = strtoupper(preg_replace('/[^A-Z0-9_]/i', '_', trim($this->code)));

        // Anti-doublon code
        $existsCode = Role::where('code', $codeClean)
            ->when($this->editId, fn($q) => $q->where('id', '!=', $this->editId))
            ->exists();
        if ($existsCode) { $this->errorCode = 'Ce code existe déjà.'; return; }

        $data = [
            'libelle'     => trim($this->libelle),
            'code'        => $codeClean,
            'description' => trim($this->description) ?: null,
        ];

        if ($this->editId) {
            $role = Role::find($this->editId);
            $role->update($data);
        } else {
            $role = Role::create($data);
        }

        // Sync permissions via table role_permission
        $role->permissions()->sync($this->permissionIds);

        $this->closeModal_after_edit('modalRole');
        $this->send_event_at_toast(
            $this->editId ? 'Rôle modifié avec succès !' : 'Rôle créé avec succès !',
            'success', 'top-end'
        );
    }

    /* ── Supprimer rôle ─────────────────────────────────── */
    public function confirmDelete(int $id): void
    {
        abort_unless(auth()->user()?->hasPermission('ROLE_DELETE'), 403);

        $role = Role::find($id);
        if (! $role) return;
        $this->sweetAlert_confirm_options_with_button(
            $role, 'Supprimer ce rôle ?',
            'Les utilisateurs ayant ce rôle seront affectés.',
            'doDelete', 'warning', 'Supprimer', 'Annuler'
        );
    }

    #[\Livewire\Attributes\On('doDelete')]
    public function doDelete(int $id): void
    {
        $role = Role::find($id);
        if ($role) {
            $role->permissions()->detach();
            $role->delete();
        }
        $this->send_event_at_toast('Rôle supprimé.', 'success', 'top-end');
    }

    /* ── Données vue ────────────────────────────────────── */
    public function with(): array
    {
        $roles = Role::withCount('users')
            ->with('permissions')
            ->orderBy('libelle')
            ->get();

        /* Grouper les permissions par module */
        $permissions = Permission::orderBy('code')->orderBy('libelle')->get();
        $grouped = $permissions->groupBy('code');

        $stats = [
            'total'       => $roles->count(),
            'actifs'      => \App\Models\User::distinct('role_id')->count('role_id'),
            'permissions' => $permissions->count(),
            'users'       => \App\Models\User::count(),
        ];

        /* Pour le formulaire : permissions déjà cochées */
        $allGrouped = $grouped->map(fn($perms) => $perms->map(fn($p) => [
            'id'      => $p->id,
            'libelle' => $p->libelle,
            'checked' => in_array((string)$p->id, $this->permissionIds),
        ]));

        return compact('roles', 'grouped', 'allGrouped', 'stats');
    }
};
?>
