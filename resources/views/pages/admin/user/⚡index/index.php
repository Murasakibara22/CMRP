<?php

use App\Models\Role;
use App\Models\User;
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

    public $search = '';
    public $filterType = 'all'; // all, SUPERADMIN, ADMIN
    public $filterStatus = 'all'; // all, true, false

    // Form fields
    public $userId;
    public $nom, $prenom, $email, $phone, $password, $password_confirmation, $role_id;
    public $type = 'ADMIN';
    public $status = true;
    public $photo;
    public $isEditMode = false;

    // Delete confirmation
    public $userToDelete;
    public $confirmPassword;
    public $newStatus;

    protected $listeners = ['deleteConfirmed'];

    public function rules()
    {
        $rules = [
            'nom' => 'required|string|min:3|max:255',
            'prenom' => 'required|string|min:3|max:255',
            'phone' => 'required|string|max:20',
            'role_id' => 'required|exists:roles,id',
        ];

        if ($this->isEditMode) {
            $rules['email'] = 'required|email|max:255|unique:users,email,' . $this->userId;
            $rules['password'] = 'nullable|min:6|confirmed';
        } else {
            $rules['email'] = 'required|email|max:255|unique:users,email';
            $rules['password'] = 'required|min:6|confirmed';
        }

        return $rules;
    }

    protected $messages = [
        'nom.required' => 'Le nom est obligatoire',
        'nom.min' => 'Le nom doit contenir au minimum 3 caractères',
        'nom.max' => 'Le nom ne doit pas dépasser 255 caractères',
        'prenom' => 'Le prénom est obligatoire',
        'prenom.min' => 'Le prénom doit contenir au minimum 3 caractères',
        'prenom.max' => 'Le prénom ne doit pas dépasser 255 caractères',
        'email.required' => 'L\'email est obligatoire',
        'email.email' => 'Ce champ doit être une adresse email valide',
        'email.unique' => 'Cet email existe déjà',
        'phone.required' => 'Le numéro de téléphone est obligatoire',
        'password.required' => 'Le mot de passe est obligatoire',
        'password.min' => 'Le mot de passe doit contenir au minimum 6 caractères',
        'password.confirmed' => 'Les mots de passe ne correspondent pas',
        'type.required' => 'Le type d\'utilisateur est obligatoire',
    ];

    public function updated($propertynom)
    {
        $this->validateOnly($propertynom);
    }

    public function with()
    {
        $stats = [
            'total' => User::count(),
            'superadmins' => User::whereHas('role',function($q) { $q->where('code', 'SUPERADMIN'); })->count(),
            'admins' => User::whereHas('role',function($q) { $q->where('code', 'ADMIN'); })->count(),
            'active' => User::where('status', 'actif')->count(),
            'deactivated' => User::where('status', 'inactif')->count(),
        ];

        $users = User::query()
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('nom', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterType !== 'all', function($query) {
                $query->where('type', $this->filterType);
            })
            ->when($this->filterStatus !== 'all', function($query) {
                $query->where('status', $this->filterStatus);
            })
            ->latest()
            ->paginate(12);

        $roles = Role::all();

        return [
            'users' => $users,
            'stats' => $stats,
            'roles' => $roles,
        ];
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->launch_modal('createEditModal');
    }

    public function openEditModal($userId)
    {
        $user = User::findOrFail($userId);

        $this->userId = $user->id;
        $this->nom = $user->nom;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->type = $user->type;
        $this->status = $user->status;

        $this->isEditMode = true;
        $this->launch_modal('createEditModal');
    }

    public function store()
    {
        $this->validate();

        try {
            $user= User::create([
                'nom' => ucwords(strtolower($this->nom)),
                'prenom' => ucwords(strtolower($this->prenom)),
                'email' => strtolower($this->email),
                'phone' => $this->phone,
                'password' => Hash::make($this->password),
                'role_id' => $this->role_id,
                'status' => 'actif',
                // 'changed_first_password' => false,
            ]);

            Mail::to($user->email)->send(new \App\Mail\AdminCredentialsMail($user, $this->password));

            // Log
            // ActivityLog("Enregistrement d'un nouvel utilisateur : " . $this->nom, "User");

            $this->closeModal_after_edit('createEditModal');
            $this->send_event_at_toast('Utilisateur créé avec succès', 'success', 'top-end');
            $this->resetForm();
        } catch (\Exception $e) {
            $this->send_event_at_toast('Erreur : ' . $e->getMessage(), 'error', 'top-end');
        }
    }

    public function update()
    {
        $this->validate();

        try {
            $user = User::findOrFail($this->userId);

            $updateData = [
                'nom' => ucwords(strtolower($this->nom)),
                'prenom' => ucwords(strtolower($this->prenom)),
                'email' => strtolower($this->email),
                'phone' => $this->phone,
                'role_id' => $this->role_id,
            ];

            if ($this->password) {
                $updateData['password'] = Hash::make($this->password);
            }

            $user->update($updateData);

            // Log
            // ActivityLog("Modification d'un utilisateur : " . $user->nom, "User");

            $this->closeModal_after_edit('createEditModal');
            $this->send_event_at_toast('Utilisateur modifié avec succès', 'success', 'top-end');
            $this->resetForm();
        } catch (\Exception $e) {
            $this->send_event_at_toast('Erreur : ' . $e->getMessage(), 'error', 'top-end');
        }
    }

    public function confirmToggleStatus($userId)
    {
        $user = User::findOrFail($userId);

        $newStatus = $user->status == 'actif'  ? 'inactif' : 'actif';
        $action = $newStatus == 'actif' ? 'activer' : 'désactiver';

        $this->userId = $userId;
        $this->newStatus = $newStatus;

        $this->sweetAlert_confirm_options_with_button(
            $user,
            'Confirmer l\'action',
            "Voulez-vous vraiment {$action} cet utilisateur ?",
            'toggleStatusConfirmed',
            'question',
            'Oui, confirmer',
            'Annuler'
        );
    }

    #[On('toggleStatusConfirmed')]
    public function toggleStatusConfirmed()
    {
        $user = User::findOrFail($this->userId);
        $user->update(['status' => $this->newStatus]);

        $statusLabel = $this->newStatus == 'actif' ? 'activé' : 'désactivé';
        $this->send_event_at_toast("Utilisateur {$statusLabel} avec succès", 'success', 'top-end');
    }

    public function confirmDelete($userId)
    {
        $user = User::findOrFail($userId);

        if ($user->id === auth()->id()) {
            $this->send_event_at_toast('Vous ne pouvez pas supprimer votre propre compte', 'error', 'top-end');
            return;
        }

        $this->userToDelete = $user;
        $this->launch_modal('deleteModal');
    }

    public function deleteUser()
    {
        if (!Hash::check($this->confirmPassword, auth()->user()->password)) {
            $this->send_event_at_toast('Mot de passe incorrect', 'error', 'top-end');
            return;
        }

        try {
            $user = User::findOrFail($this->userToDelete->id);
            $usernom = $user->nom;

            $user->delete();

            // Log
            // ActivityLog("Suppression d'un utilisateur : " . $usernom, "User");

            $this->closeModal_after_edit('deleteModal');
            $this->send_event_at_toast('Utilisateur supprimé avec succès', 'success', 'top-end');
            $this->reset(['userToDelete', 'confirmPassword']);
        } catch (\Exception $e) {
            $this->send_event_at_toast('Erreur : ' . $e->getMessage(), 'error', 'top-end');
        }
    }

    public function generatePassword()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%';
        $pass = [];
        $alphaLength = strlen($alphabet) - 1;

        for ($i = 0; $i < 10; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        $this->password = implode($pass);
        $this->password_confirmation = $this->password;
    }

    public function resetForm()
    {
        $this->reset([
            'userId', 'nom', 'email', 'phone', 'password',
            'password_confirmation', 'photo', 'type', 'isEditMode'
        ]);
        $this->type = 'ADMIN';
        $this->status = True;
        $this->resetValidation();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }
};