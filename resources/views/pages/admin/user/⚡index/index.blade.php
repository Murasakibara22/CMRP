<div>
<div class="page-content">
    <div class="container-fluid">
        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm" style="border-radius: 15px; border-left: 4px solid #20379b;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 text-uppercase" style="font-size: 11px; font-weight: 600;">Total</p>
                                <h3 class="mb-0 fw-bold" style="color: #20379b;">{{ $stats['total'] }}</h3>
                                <small class="text-muted">Utilisateurs</small>
                            </div>
                            <div class="avatar-lg">
                                <div class="avatar-title rounded-circle" style="background: linear-gradient(135deg, #20379b 0%, #764ba2 100%);">
                                    <i class="ri-user-line text-white fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm" style="border-radius: 15px; border-left: 4px solid #92193f;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 text-uppercase" style="font-size: 11px; font-weight: 600;">Super Admins</p>
                                <h3 class="mb-0 fw-bold" style="color: #92193f;">{{ $stats['superadmins'] }}</h3>
                                <small class="text-muted">Administrateurs</small>
                            </div>
                            <div class="avatar-lg">
                                <div class="avatar-title rounded-circle" style="background: linear-gradient(135deg, #92193f 0%, #f5576c 100%);">
                                    <i class="ri-shield-star-line text-white fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm" style="border-radius: 15px; border-left: 4px solid #28a745;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 text-uppercase" style="font-size: 11px; font-weight: 600;">Admins</p>
                                <h3 class="mb-0 fw-bold" style="color: #28a745;">{{ $stats['admins'] }}</h3>
                                <small class="text-muted">Gestionnaires</small>
                            </div>
                            <div class="avatar-lg">
                                <div class="avatar-title rounded-circle" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                                    <i class="ri-admin-line text-white fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm" style="border-radius: 15px; border-left: 4px solid #17a2b8;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 text-uppercase" style="font-size: 11px; font-weight: 600;">Actifs</p>
                                <h3 class="mb-0 fw-bold" style="color: #17a2b8;">{{ $stats['active'] }}</h3>
                                <small class="text-muted">En ligne</small>
                            </div>
                            <div class="avatar-lg">
                                <div class="avatar-title rounded-circle" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                                    <i class="ri-check-double-line text-white fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <div class="position-relative">
                                    <i class="ri-search-line position-absolute" style="left: 18px; top: 50%; transform: translateY(-50%); color: #20379b; font-size: 18px;"></i>
                                    <input type="text"
                                        class="form-control ps-5"
                                        wire:model.live.debounce.300ms="search"
                                        placeholder="🔍 Rechercher un utilisateur..."
                                        style="border-radius: 12px; padding: 12px 12px 12px 45px; border: 2px solid #e9ecef;">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select"
                                        wire:model.live="filterType"
                                        style="border-radius: 12px; padding: 12px; border: 2px solid #e9ecef;">
                                    <option value="all">👤 Tous les types</option>
                                    <option value="SUPERADMIN">🔱 Super Admin</option>
                                    <option value="ADMIN">⚙️ Admin</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select"
                                        wire:model.live="filterStatus"
                                        style="border-radius: 12px; padding: 12px; border: 2px solid #e9ecef;">
                                    <option value="all">📊 Tous les statuts</option>
                                    <option value="actif">✅ Actifs</option>
                                    <option value="inactif">❌ Désactivés</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button wire:click="openCreateModal"
                                        class="btn w-100 text-white"
                                        style="background: linear-gradient(135deg, #20379b 0%, #764ba2 100%); border: none; border-radius: 12px; padding: 12px;">
                                    <i class="ri-add-line me-1"></i>Nouveau
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Grid -->
        <div class="row g-4">
            @forelse($users as $user)
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100"
                    style="border-radius: 15px; transition: all 0.3s;"
                    onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 15px 35px rgba(0,0,0,0.15)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='';">

                    <!-- User Avatar -->
                    <div class="text-center pt-4">
                        <div class="avatar-xl mx-auto mb-3">
                            <div class="avatar-title rounded-circle" style="background: linear-gradient(135deg, #20379b 0%, #764ba2 100%); width: 80px; height: 80px; font-size: 32px; font-weight: 600; box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);">
                                {{ strtoupper(substr($user->nom . ' ' . $user->prenom, 0, 2)) }}
                            </div>
                        </div>
                        <h5 class="mb-1 fw-bold">{{ $user->nom . ' ' . $user->prenom }}</h5>
                        <div class="mb-3">
                            @if($user->role->code === 'SUPER_ADMIN')
                                <span class="badge" style="background: linear-gradient(135deg, #92193f 0%, #f5576c 100%); color: white; padding: 5px 15px; border-radius: 12px;">
                                    <i class="ri-shield-star-line me-1"></i>Super Admin
                                </span>
                            @else
                                <span class="badge" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 5px 15px; border-radius: 12px;">
                                    <i class="ri-admin-line me-1"></i>Admin
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body px-4 pb-4">
                        <!-- Contact Info -->
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2 p-2" style="background: #f8f9fa; border-radius: 10px;">
                                <i class="ri-mail-line me-2" style="color: #20379b; font-size: 18px;"></i>
                                <small class="text-truncate">{{ $user->email }}</small>
                            </div>
                            <div class="d-flex align-items-center p-2" style="background: #f8f9fa; border-radius: 10px;">
                                <i class="ri-phone-line me-2" style="color: #28a745; font-size: 18px;"></i>
                                <small>{{ $user->phone }}</small>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Statut</small>
                                @if($user->status === 'actif')
                                    <span class="badge badge-soft-success">
                                        <i class="ri-checkbox-circle-line me-1"></i>Actif
                                    </span>
                                @else
                                    <span class="badge badge-soft-danger">
                                        <i class="ri-close-circle-line me-1"></i>Désactivé
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Date -->
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="ri-calendar-line me-1"></i>Créé le {{ $user->created_at->format('d/m/Y') }}
                            </small>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex gap-2">
                            <button wire:click="openEditModal({{ $user->id }})"
                                    class="btn btn-sm flex-fill"
                                    style="background: linear-gradient(135deg, #20379b 0%, #764ba2 100%); color: white; border: none; border-radius: 10px; padding: 8px;">
                                <i class="ri-pencil-line me-1"></i>Modifier
                            </button>
                            <button wire:click="confirmToggleStatus({{ $user->id }})"
                                    class="btn btn-sm"
                                    style="background: {{ $user->status === 'actif' ? '#ffc107' : '#28a745' }}; color: white; border: none; border-radius: 10px; padding: 8px 12px;"
                                    title="{{ $user->status === 'actif' ? 'Désactiver' : 'Activer' }}">
                                <i class="ri-{{ $user->status === 'actif' ? 'close' : 'check' }}-line"></i>
                            </button>
                            @if($user->id !== auth()->id())
                            <button wire:click="confirmDelete({{ $user->id }})"
                                    class="btn btn-sm"
                                    style="background: #dc3545; color: white; border: none; border-radius: 10px; padding: 8px 12px;">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-body text-center py-5">
                        <div class="mb-4" style="font-size: 80px;">👥</div>
                        <h3 class="mb-3">Aucun utilisateur trouvé</h3>
                        <p class="text-muted mb-4">Créez votre premier utilisateur pour commencer</p>
                        <button wire:click="openCreateModal"
                                class="btn text-white"
                                style="background: linear-gradient(135deg, #20379b 0%, #764ba2 100%); border: none; border-radius: 12px; padding: 12px 30px;">
                            <i class="ri-add-line me-1"></i>Créer un Utilisateur
                        </button>
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $users->links() }}
        </div>

    </div>
</div>

    <!-- Modals -->
    @include('pages.admin.user.partials.create-edit-modal')
    @include('pages.admin.user.partials.delete-modal')
</div>



@push('styles')
<style>
    .feedback-text{
        width: 100%;
        margin-top: .25rem;
        font-size: .875em;
        color: #f06548;
    }
</style>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
@endpush
