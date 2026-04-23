<?php

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Traits\UtilsSweetAlert;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

new class extends Component
{
    use UtilsSweetAlert, WithFileUploads, WithPagination;

    protected $paginationTheme = 'bootstrap';

    /* ── Filtres ─────────────────────────────────────────── */
    public string $search       = '';
    public string $filterType   = 'all';
    public string $filterStatus = 'all';

    /* ── Formulaire création / édition ──────────────────── */
    public ?int   $userId    = null;
    public string $nom       = '';
    public string $prenom    = '';
    public string $email     = '';
    public string $phone     = '';
    public string $password  = '';
    public string $password_confirmation = '';
    public ?int   $role_id   = null;
    public bool   $isEditMode = false;

    /* ── Suppression ────────────────────────────────────── */
    public $userToDelete;
    public string $confirmPassword = '';
    public string $newStatus       = '';

    /* ── Modal permissions individuelles ────────────────── */
    public ?int  $permUserId      = null;  // user en cours de modif permissions
    public array $userPermIds     = [];    // permissions actuelles de ce user

    /* ── Validation ─────────────────────────────────────── */
    public function rules(): array
    {
        $rules = [
            'nom'     => 'required|string|min:2|max:255',
            'prenom'  => 'required|string|min:2|max:255',
            'phone'   => 'required|string|max:20',
            'role_id' => 'required|exists:roles,id',
        ];
        if ($this->isEditMode) {
            $rules['email']    = 'required|email|max:255|unique:users,email,'.$this->userId;
            $rules['password'] = 'nullable|min:6|confirmed';
        } else {
            $rules['email']    = 'required|email|max:255|unique:users,email';
            $rules['password'] = 'required|min:6|confirmed';
        }
        return $rules;
    }

    protected array $messages = [
        'nom.required'       => 'Le nom est obligatoire',
        'prenom.required'    => 'Le prénom est obligatoire',
        'email.required'     => "L'email est obligatoire",
        'email.email'        => 'Email invalide',
        'email.unique'       => 'Cet email existe déjà',
        'phone.required'     => 'Le téléphone est obligatoire',
        'password.required'  => 'Le mot de passe est obligatoire',
        'password.min'       => '6 caractères minimum',
        'password.confirmed' => 'Les mots de passe ne correspondent pas',
        'role_id.required'   => 'Le rôle est obligatoire',
    ];

    public function updated(string $prop): void { $this->validateOnly($prop); }

    /* ── Ouvrir modal création ──────────────────────────── */
    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->launch_modal('createEditModal');
    }

    /* ── Ouvrir modal édition ───────────────────────────── */
    public function openEditModal(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->userId   = $user->id;
        $this->nom      = $user->nom;
        $this->prenom   = $user->prenom ?? '';
        $this->email    = $user->email;
        $this->phone    = $user->phone;
        $this->role_id  = $user->role_id;
        $this->password = '';
        $this->password_confirmation = '';
        $this->isEditMode = true;
        $this->launch_modal('createEditModal');
    }

    /* ── Créer utilisateur ──────────────────────────────── */
    public function store(): void
    {
        $this->validate();
        try {
            $role = Role::with('permissions')->findOrFail($this->role_id);

            $user = User::create([
                'nom'      => ucwords(strtolower($this->nom)),
                'prenom'   => ucwords(strtolower($this->prenom)),
                'email'    => strtolower($this->email),
                'phone'    => $this->phone,
                'password' => Hash::make($this->password),
                'role_id'  => $this->role_id,
                'status'   => 'actif',
            ]);

            // Associer les permissions du rôle au user
            $user->permissions()->sync($role->permissions->pluck('id'));

            Mail::to($user->email)->send(new \App\Mail\AdminCredentialsMail($user, $this->password));

            $this->closeModal_after_edit('createEditModal');
            $this->send_event_at_toast('Utilisateur créé avec succès', 'success', 'top-end');
            $this->resetForm();
        } catch (\Exception $e) {
            $this->send_event_at_toast('Erreur : '.$e->getMessage(), 'error', 'top-end');
        }
    }

    /* ── Modifier utilisateur ───────────────────────────── */
    public function update(): void
    {
        $this->validate();
        try {
            $user = User::findOrFail($this->userId);
            $oldRoleId = $user->role_id;

            $data = [
                'nom'     => ucwords(strtolower($this->nom)),
                'prenom'  => ucwords(strtolower($this->prenom)),
                'email'   => strtolower($this->email),
                'phone'   => $this->phone,
                'role_id' => $this->role_id,
            ];
            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }
            $user->update($data);

            // Si le rôle change → re-sync les permissions du nouveau rôle
            if ($oldRoleId !== $this->role_id) {
                $role = Role::with('permissions')->find($this->role_id);
                $user->permissions()->sync($role->permissions->pluck('id'));
            }

            $this->closeModal_after_edit('createEditModal');
            $this->send_event_at_toast('Utilisateur modifié avec succès', 'success', 'top-end');
            $this->resetForm();
        } catch (\Exception $e) {
            $this->send_event_at_toast('Erreur : '.$e->getMessage(), 'error', 'top-end');
        }
    }

    /* ── Générer mot de passe ───────────────────────────── */
    public function generatePassword(): void
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%';
        $pwd = '';
        for ($i = 0; $i < 10; $i++) $pwd .= $chars[rand(0, strlen($chars) - 1)];
        $this->password = $pwd;
        $this->password_confirmation = $pwd;
    }

    /* ── Toggle statut ──────────────────────────────────── */
    public function confirmToggleStatus(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->userId    = $userId;
        $this->newStatus = $user->status === 'actif' ? 'inactif' : 'actif';
        $action = $this->newStatus === 'actif' ? 'activer' : 'désactiver';
        $this->sweetAlert_confirm_options_with_button(
            $user, 'Confirmer l\'action',
            "Voulez-vous vraiment {$action} cet utilisateur ?",
            'toggleStatusConfirmed', 'question', 'Oui, confirmer', 'Annuler'
        );
    }

    #[On('toggleStatusConfirmed')]
    public function toggleStatusConfirmed(): void
    {
        User::findOrFail($this->userId)->update(['status' => $this->newStatus]);
        $label = $this->newStatus === 'actif' ? 'activé' : 'désactivé';
        $this->send_event_at_toast("Utilisateur {$label} avec succès", 'success', 'top-end');
    }

    /* ── Suppression ────────────────────────────────────── */
    public function confirmDelete(int $userId): void
    {
        $user = User::findOrFail($userId);
        if ($user->id === auth()->id()) {
            $this->send_event_at_toast('Vous ne pouvez pas supprimer votre propre compte', 'error', 'top-end');
            return;
        }
        $this->userToDelete = $user;
        $this->launch_modal('deleteModal');
    }

    public function deleteUser(): void
    {
        if (! Hash::check($this->confirmPassword, auth()->user()->password)) {
            $this->send_event_at_toast('Mot de passe incorrect', 'error', 'top-end');
            return;
        }
        try {
            $user = User::findOrFail($this->userToDelete->id);
            $user->permissions()->detach();
            $user->delete();
            $this->closeModal_after_edit('deleteModal');
            $this->send_event_at_toast('Utilisateur supprimé avec succès', 'success', 'top-end');
            $this->reset(['userToDelete', 'confirmPassword']);
        } catch (\Exception $e) {
            $this->send_event_at_toast('Erreur : '.$e->getMessage(), 'error', 'top-end');
        }
    }

    /* ══ PERMISSIONS INDIVIDUELLES ══════════════════════════ */

    /* Ouvrir modal permissions d'un user */
    public function openPermissions(int $userId): void
    {
        $user = User::with('permissions')->findOrFail($userId);
        $this->permUserId  = $userId;
        $this->userPermIds = $user->permissions->pluck('id')->map(fn($i) => (string)$i)->toArray();
        $this->launch_modal('modalUserPerms');
    }

    /* Toggle une permission pour le user */
    public function toggleUserPermission(int $permId): void
    {
        $key = (string) $permId;
        if (in_array($key, $this->userPermIds)) {
            $this->userPermIds = array_values(array_filter($this->userPermIds, fn($p) => $p !== $key));
        } else {
            $this->userPermIds[] = $key;
        }
    }

    /* Tout sélectionner / désélectionner un groupe pour le user */
    public function toggleUserGroupe(array $permIds): void
    {
        $ids = array_map('strval', $permIds);
        $allSelected = empty(array_diff($ids, $this->userPermIds));
        if ($allSelected) {
            $this->userPermIds = array_values(array_diff($this->userPermIds, $ids));
        } else {
            $this->userPermIds = array_values(array_unique(array_merge($this->userPermIds, $ids)));
        }
    }

    /* Sauvegarder les permissions du user */
    public function saveUserPermissions(): void
    {
        $user = User::find($this->permUserId);
        if (! $user) return;
        $user->permissions()->sync($this->userPermIds);
        $this->closeModal_after_edit('modalUserPerms');
        $this->send_event_at_toast('Permissions mises à jour !', 'success', 'top-end');
    }

    /* ── Reset du formulaire ─────────────────────────────── */
    public function resetForm(): void
    {
        $this->reset(['userId','nom','prenom','email','phone','password','password_confirmation','role_id','isEditMode']);
        $this->resetValidation();
    }

    /* ── Données vue ─────────────────────────────────────── */
    public function with(): array
    {
        $stats = [
            'total'      => User::count(),
            'superadmins'=> User::whereHas('role', fn($q) => $q->where('code', 'SUPERADMIN'))->count(),
            'admins'     => User::whereHas('role', fn($q) => $q->where('code', 'ADMIN'))->count(),
            'active'     => User::where('status', 'actif')->count(),
        ];

        $users = User::with(['role', 'permissions'])
            ->when($this->search, fn($q) =>
                $q->where('nom', 'like', '%'.$this->search.'%')
                  ->orWhere('email', 'like', '%'.$this->search.'%')
                  ->orWhere('phone', 'like', '%'.$this->search.'%')
            )
            ->when($this->filterType !== 'all', fn($q) =>
                $q->whereHas('role', fn($r) => $r->where('code', $this->filterType))
            )
            ->when($this->filterStatus !== 'all', fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(12);

        $roles = Role::all();

        /* Permissions groupées pour le modal user permissions */
        $allPermissions = Permission::orderBy('code')->orderBy('libelle')->get();
        $permGrouped = $allPermissions->groupBy('code')->map(fn($perms) =>
            $perms->map(fn($p) => [
                'id'      => $p->id,
                'libelle' => $p->libelle,
            ])
        );

        /* Pour le modal user perm — user sélectionné */
        $permUser = $this->permUserId ? User::with(['role','permissions'])->find($this->permUserId) : null;

        return compact('users', 'stats', 'roles', 'permGrouped', 'permUser');
    }

    public function updatingSearch(): void       { $this->resetPage(); }
    public function updatingFilterType(): void   { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }
};
