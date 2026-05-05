<div>
<div class="page-content">
    <div class="container-fluid">

        {{-- Stats Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm" style="border-radius:15px;border-left:4px solid #20379b">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 text-uppercase" style="font-size:11px;font-weight:600">Total</p>
                                <h3 class="mb-0 fw-bold" style="color:#20379b">{{ $stats['total'] }}</h3>
                                <small class="text-muted">Utilisateurs</small>
                            </div>
                            <div class="avatar-lg">
                                <div class="avatar-title rounded-circle" style="background:linear-gradient(135deg,#20379b,#764ba2)">
                                    <i class="ri-user-line text-white fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm" style="border-radius:15px;border-left:4px solid #92193f">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 text-uppercase" style="font-size:11px;font-weight:600">Super Admins</p>
                                <h3 class="mb-0 fw-bold" style="color:#92193f">{{ $stats['superadmins'] }}</h3>
                                <small class="text-muted">Administrateurs</small>
                            </div>
                            <div class="avatar-lg">
                                <div class="avatar-title rounded-circle" style="background:linear-gradient(135deg,#92193f,#f5576c)">
                                    <i class="ri-shield-star-line text-white fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm" style="border-radius:15px;border-left:4px solid #28a745">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 text-uppercase" style="font-size:11px;font-weight:600">Admins</p>
                                <h3 class="mb-0 fw-bold" style="color:#28a745">{{ $stats['admins'] }}</h3>
                                <small class="text-muted">Gestionnaires</small>
                            </div>
                            <div class="avatar-lg">
                                <div class="avatar-title rounded-circle" style="background:linear-gradient(135deg,#28a745,#20c997)">
                                    <i class="ri-admin-line text-white fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm" style="border-radius:15px;border-left:4px solid #17a2b8">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 text-uppercase" style="font-size:11px;font-weight:600">Actifs</p>
                                <h3 class="mb-0 fw-bold" style="color:#17a2b8">{{ $stats['active'] }}</h3>
                                <small class="text-muted">En ligne</small>
                            </div>
                            <div class="avatar-lg">
                                <div class="avatar-title rounded-circle" style="background:linear-gradient(135deg,#17a2b8,#138496)">
                                    <i class="ri-check-double-line text-white fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters & Actions --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius:15px">
                    <div class="card-body p-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <div class="position-relative">
                                    <i class="ri-search-line position-absolute" style="left:18px;top:50%;transform:translateY(-50%);color:#20379b;font-size:18px"></i>
                                    <input type="text" class="form-control ps-5"
                                           wire:model.live.debounce.300ms="search"
                                           placeholder="🔍 Rechercher un utilisateur..."
                                           style="border-radius:12px;padding:12px 12px 12px 45px;border:2px solid #e9ecef">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" wire:model.live="filterType"
                                        style="border-radius:12px;padding:12px;border:2px solid #e9ecef">
                                    <option value="all">👤 Tous les rôles</option>
                                    @foreach(\App\Models\Role::all() as $role)
                                    <option value="{{ $role->code }}">{{ $role->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" wire:model.live="filterStatus"
                                        style="border-radius:12px;padding:12px;border:2px solid #e9ecef">
                                    <option value="all">📊 Tous les statuts</option>
                                    <option value="actif">✅ Actifs</option>
                                    <option value="inactif">❌ Désactivés</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button wire:click="openCreateModal" class="btn w-100 text-white"
                                        style="background:linear-gradient(135deg,#20379b,#764ba2);border:none;border-radius:12px;padding:12px">
                                    <i class="ri-add-line me-1"></i>Nouveau
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Users Grid --}}
        <div class="row g-4">
            @forelse($users as $user)
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100"
                     style="border-radius:15px;transition:all .3s"
                     onmouseover="this.style.transform='translateY(-8px)';this.style.boxShadow='0 15px 35px rgba(0,0,0,.15)'"
                     onmouseout="this.style.transform='translateY(0)';this.style.boxShadow=''">

                    <div class="text-center pt-4">
                        <div class="avatar-xl mx-auto mb-3">
                            <div class="avatar-title rounded-circle"
                                 style="background:linear-gradient(135deg,#20379b,#764ba2);width:80px;height:80px;font-size:28px;font-weight:700;box-shadow:0 8px 20px rgba(102,126,234,.3)">
                                {{ strtoupper(substr(($user->prenom ?? '').' '.($user->nom ?? ''), 0, 2)) }}
                            </div>
                        </div>
                        <h5 class="mb-1 fw-bold">{{ $user->prenom }} {{ $user->nom }}</h5>

                        {{-- Badge rôle --}}
                        <div class="mb-2">
                            <span class="badge px-3 py-1" style="border-radius:10px;background:linear-gradient(135deg,{{ $user->role?->code === 'SUPER_ADMIN' ? '#92193f,#f5576c' : '#28a745,#20c997' }});font-size:11px">
                                <i class="ri-{{ $user->role?->code === 'SUPER_ADMIN' ? 'shield-star' : 'shield' }}-line me-1"></i>
                                {{ $user->role?->libelle ?? '—' }}
                            </span>
                        </div>

                        {{-- Nb permissions --}}
                        <div style="font-size:11px;color:#878a99;margin-bottom:8px">
                            <i class="ri-key-line me-1"></i>
                            {{ $user->permissions->count() }} permission(s)
                        </div>
                    </div>

                    <div class="card-body px-4 pb-4">
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2 p-2" style="background:#f8f9fa;border-radius:10px">
                                <i class="ri-mail-line me-2" style="color:#20379b;font-size:18px"></i>
                                <small class="text-truncate">{{ $user->email }}</small>
                            </div>
                            <div class="d-flex align-items-center p-2" style="background:#f8f9fa;border-radius:10px">
                                <i class="ri-phone-line me-2" style="color:#28a745;font-size:18px"></i>
                                <small>{{ $user->phone }}</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Statut</small>
                                @if($user->status === 'actif')
                                    <span class="badge badge-soft-success"><i class="ri-checkbox-circle-line me-1"></i>Actif</span>
                                @else
                                    <span class="badge badge-soft-danger"><i class="ri-close-circle-line me-1"></i>Désactivé</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted"><i class="ri-calendar-line me-1"></i>Créé le {{ $user->created_at->format('d/m/Y') }}</small>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex gap-2 mb-2">
                            @if(auth()->user()?->hasPermission('ADMIN_EDIT'))
                            <button wire:click="openEditModal({{ $user->id }})" class="btn btn-sm flex-fill text-white"
                                    style="background:linear-gradient(135deg,#20379b,#764ba2);border:none;border-radius:10px;padding:7px">
                                <i class="ri-pencil-line me-1"></i>Modifier
                            </button>
                            @endif

                            @if(auth()->user()?->hasPermission('ADMIN_ACTIVATE'))
                            <button wire:click="confirmToggleStatus({{ $user->id }})" class="btn btn-sm"
                                    style="background:{{ $user->status==='actif'?'#ffc107':'#28a745' }};color:white;border:none;border-radius:10px;padding:7px 10px"
                                    title="{{ $user->status==='actif'?'Désactiver':'Activer' }}">
                                <i class="ri-{{ $user->status==='actif'?'close':'check' }}-line"></i>
                            </button>
                            @endif

                            @if(auth()->user()?->hasPermission('ADMIN_DELETE'))
                            @if($user->id !== auth()->id())
                            <button wire:click="confirmDelete({{ $user->id }})" class="btn btn-sm"
                                    style="background:#dc3545;color:white;border:none;border-radius:10px;padding:7px 10px">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                            @endif
                            @endif
                        </div>

                        {{-- Bouton permissions individuelles --}}
                        @if(auth()->user()?->hasPermission('ADMIN_MANAGE_PERMISSION'))
                        <button wire:click="openPermissions({{ $user->id }})"
                                class="btn btn-sm w-100"
                                style="border:1.5px solid #405189;color:#405189;border-radius:10px;padding:7px;font-size:12px;font-weight:700;background:rgba(64,81,137,.05)">
                            <i class="ri-key-2-line me-1"></i>Gérer ses permissions
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius:15px">
                    <div class="card-body text-center py-5">
                        <div class="mb-4" style="font-size:80px">👥</div>
                        <h3 class="mb-3">Aucun utilisateur trouvé</h3>
                        @if(auth()->user()?->hasPermission('ADMIN_CREATE'))
                        <button wire:click="openCreateModal" class="btn text-white"
                                style="background:linear-gradient(135deg,#20379b,#764ba2);border:none;border-radius:12px;padding:12px 30px">
                            <i class="ri-add-line me-1"></i>Créer un Utilisateur
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        <div class="mt-4">{{ $users->links() }}</div>

    </div>
</div>

    {{-- Modals création/édition et suppression --}}
    @include('pages.admin.user.partials.create-edit-modal')
    @include('pages.admin.user.partials.delete-modal')


{{-- ══════════════════════════════════════════════════════════
     MODAL PERMISSIONS INDIVIDUELLES
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalUserPerms" tabindex="-1" aria-hidden="true" wire:ignore.self>
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden">

      <div class="modal-header" style="border:none;padding:24px 32px 0;background:#fff">
        @if($permUser)
        <div>
          <div style="font-size:20px;font-weight:900;color:#212529">
            Permissions de {{ $permUser->prenom }} {{ $permUser->nom }}
          </div>
          <div style="font-size:13px;color:#878a99;margin-top:3px">
            Rôle : <strong style="color:#405189">{{ $permUser->role?->libelle }}</strong> ·
            {{ count($userPermIds) }} / {{ collect($permGrouped)->flatten(1)->count() }} permissions actives
          </div>
        </div>
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" style="padding:20px 32px">

        @if($permUser)
        <div class="alert border-0 mb-4" style="background:rgba(64,81,137,.05);border-radius:12px;font-size:13px">
          <i class="ri-information-line me-2" style="color:#405189"></i>
          Les permissions ci-dessous sont propres à cet utilisateur. Elles peuvent différer de celles de son rôle.
        </div>
        @endif

        @foreach($permGrouped as $module => $perms)
        @php
          $groupIds = $perms->pluck('id')->map(fn($i)=>(string)$i)->toArray();
          $allChecked = empty(array_diff($groupIds, $userPermIds));
        @endphp
        <div class="rp-perm-group mb-3">
          <div class="rp-perm-group-header">
            <div style="font-size:14px;font-weight:800;color:#212529">{{ $module }}</div>
            <button type="button"
                    class="rp-toggle-all"
                    wire:click="toggleUserGroupe({{ json_encode($perms->pluck('id')->toArray()) }})">
              {{ $allChecked ? 'Tout désélectionner' : 'Tout sélectionner' }}
            </button>
          </div>
          <div class="rp-perm-grid">
            @foreach($perms as $perm)
            @php $checked = in_array((string)$perm['id'], $userPermIds); @endphp
            <label class="rp-perm-item {{ $checked ? 'checked' : '' }}"
                   wire:click="toggleUserPermission({{ $perm['id'] }})">
              <div class="rp-perm-checkbox {{ $checked ? 'rp-checked' : '' }}">
                @if($checked)<i class="ri-check-line"></i>@endif
              </div>
              <span class="rp-perm-label">{{ $perm['libelle'] }}</span>
            </label>
            @endforeach
          </div>
        </div>
        @endforeach

      </div>

      <div class="modal-footer" style="border:none;padding:14px 32px 24px;background:#fff;justify-content:space-between">
        <button class="rp-btn-cancel" data-bs-dismiss="modal">Annuler</button>
        <button class="rp-btn-save" wire:click="saveUserPermissions" wire:loading.attr="disabled">
          <span wire:loading wire:target="saveUserPermissions" class="spinner-border spinner-border-sm me-1"></span>
          <i class="ri-save-line" wire:loading.remove wire:target="saveUserPermissions"></i>
          <span wire:loading.remove wire:target="saveUserPermissions"> Enregistrer les permissions</span>
          <span wire:loading wire:target="saveUserPermissions">Enregistrement…</span>
        </button>
      </div>

    </div>
  </div>
</div>

</div>


@push('styles')
<link href="{{ asset('assets/css/roles-permissions.css') }}" rel="stylesheet" type="text/css" />
<style>
  .feedback-text{ width:100%; margin-top:.25rem; font-size:.875em; color:#f06548; }
</style>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
@endpush

@push('scripts')
<script>
Livewire.on('OpenModalModilEdit', ({ name_modal }) => {
  const el = document.getElementById(name_modal);
  if (el) bootstrap.Modal.getOrCreateInstance(el).show();
});
Livewire.on('closeModalModilEdit', ({ name_modal }) => {
  const el = document.getElementById(name_modal);
  if (el) bootstrap.Modal.getOrCreateInstance(el).hide();
});
Livewire.on('swal:modalDeleteOptionsWithButton', (payload) => {
  const data = Array.isArray(payload) ? payload[0] : payload;
  Swal.fire({
    title: data.title, text: data.text, icon: data.type,
    showCancelButton: true,
    confirmButtonText: data.succesButton ?? 'Confirmer',
    cancelButtonText:  data.cancelButton ?? 'Annuler',
    confirmButtonColor: '#f06548', cancelButtonColor: '#878a99',
  }).then(r => { if (r.isConfirmed) Livewire.dispatch(data.eventRetour, { id: data.id }); });
});
Livewire.on('modalShowmessageToast', (payload) => {
  const data = Array.isArray(payload) ? payload[0] : payload;
  Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true })
      .fire({ icon: data.type, title: data.title });
});
</script>
@endpush
