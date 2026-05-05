<div>
<div class="page-content">
<div class="container-fluid">

  {{-- ══ HEADER ════════════════════════════════════════════ --}}
  <div class="rp-page-header fu fu-1">
    <div>
      <h4>Rôles &amp; Permissions</h4>
      <p style="font-size:13px;color:var(--rp-muted);margin:2px 0 0">
        {{ $stats['total'] }} rôles disponibles sur la plateforme
      </p>
    </div>
    @if(auth()->user()?->hasPermission('ROLE_CREATE'))
    <button class="btn-rp-primary" wire:click="openAdd">
      <i class="ri-add-line"></i> Nouveau rôle
    </button>
    @endif
  </div>

  {{-- ══ STATS STRIP ════════════════════════════════════════ --}}
  <div class="rp-stats-strip fu fu-1">
    <div class="rp-stat">
      <i class="ri-shield-line" style="color:#405189;font-size:20px"></i>
      <div>
        <div class="rps-val">{{ $stats['total'] }}</div>
        <div class="rps-label">Total Admins</div>
      </div>
    </div>
    <div class="rp-stat-div"></div>
    <div class="rp-stat">
      <i class="ri-checkbox-circle-line" style="color:#0ab39c;font-size:20px"></i>
      <div>
        <div class="rps-val">{{ $stats['users'] }}</div>
        <div class="rps-label">Actifs</div>
      </div>
    </div>
    <div class="rp-stat-div"></div>
    <div class="rp-stat">
      <i class="ri-close-circle-line" style="color:#f06548;font-size:20px"></i>
      <div>
        <div class="rps-val">0</div>
        <div class="rps-label">Suspendus</div>
      </div>
    </div>
    <div class="rp-stat-div"></div>
    <div class="rp-stat">
      <i class="ri-pulse-line" style="color:#f7b84b;font-size:20px"></i>
      <div>
        <div class="rps-val" style="color:#f7b84b">{{ $stats['permissions'] }}</div>
        <div class="rps-label">Permissions (24h)</div>
      </div>
    </div>
  </div>


  {{-- ══ GRILLE RÔLES ════════════════════════════════════════ --}}
  <div class="rp-roles-grid fu fu-3">

    @forelse($roles as $role)
    @php
      $nbPerms = $role->permissions->count();
      $topPerms = $role->permissions->take(3);
      $reste = $nbPerms - 3;
    @endphp

    <div class="rp-role-card" wire:key="role-{{ $role->id }}">
      <div class="rrc-header">
        <div class="rrc-icon">
          <i class="ri-shield-line"></i>
        </div>
        <div>
          <div class="rrc-title">{{ $role->libelle }}</div>
          <div class="rrc-users">
            <i class="ri-user-line" style="font-size:11px"></i>
            {{ $role->users_count }} utilisateur{{ $role->users_count > 1 ? 's' : '' }}
          </div>
        </div>
      </div>

      <div class="rrc-desc">
        {{ $role->description ?? 'Aucune description' }}
      </div>

      @if($nbPerms > 0)
      <div class="rrc-perms-title">Permissions principales :</div>
      <div class="rrc-perms-list">
        @foreach($topPerms as $perm)
        <div class="rrc-perm-item">
          <i class="ri-checkbox-circle-fill" style="color:#0ab39c;font-size:13px;flex-shrink:0"></i>
          <span>{{ $perm->libelle }}</span>
        </div>
        @endforeach
        @if($reste > 0)
        <div style="font-size:12px;color:#405189;font-weight:700;margin-top:4px;cursor:pointer"
             wire:click="openEdit({{ $role->id }})">
          +{{ $reste }} autres
        </div>
        @endif
      </div>
      @else
      <div class="rrc-no-perm">Aucune permission assignée</div>
      @endif

      <div class="rrc-actions">
        @if(auth()->user()?->hasPermission('ROLE_EDIT'))
        <button class="rrc-btn-modifier" wire:click="openEdit({{ $role->id }})">
          Modifier
        </button>
        @endif
        @if(auth()->user()?->hasPermission('ROLE_DELETE'))
        <button class="rrc-btn-supprimer" wire:click="confirmDelete({{ $role->id }})">
          <i class="ri-delete-bin-line"></i> Supprimer
        </button>
        @endif
      </div>
    </div>

    @empty
    <div style="grid-column:1/-1;text-align:center;padding:60px;color:var(--rp-muted)">
      <i class="ri-shield-line" style="font-size:48px;opacity:.3;display:block;margin-bottom:12px"></i>
      <div style="font-weight:700">Aucun rôle configuré</div>
    </div>
    @endforelse

  </div>

</div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL CRÉER / MODIFIER UN RÔLE
     Design fidèle aux captures — pleine page style sheet
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalRole" tabindex="-1" aria-hidden="true" wire:ignore.self>
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden">

      {{-- Header --}}
      <div class="modal-header" style="border:none;padding:28px 32px 0;background:#fff">
        <div>
          <div style="font-size:22px;font-weight:900;color:#212529">
            {{ $editId ? 'Modifier un rôle' : 'Créer un rôle' }}
          </div>
          <div style="font-size:13px;color:var(--rp-muted);margin-top:2px">
            {{ $editId ? 'Modifiez les informations et les permissions du rôle' : 'Définissez le rôle et sélectionnez ses permissions' }}
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" style="padding:24px 32px">

        {{-- Erreurs --}}
        @if($errorLibelle || $errorCode)
        <div style="background:rgba(240,101,72,.06);border:1px solid rgba(240,101,72,.25);border-left:3px solid #f06548;border-radius:0 10px 10px 0;padding:10px 14px;margin-bottom:16px">
          @if($errorLibelle)<div style="font-size:12px;color:#c44a2e">• {{ $errorLibelle }}</div>@endif
          @if($errorCode)<div style="font-size:12px;color:#c44a2e">• {{ $errorCode }}</div>@endif
        </div>
        @endif

        {{-- Section infos --}}
        <div class="rp-form-section">
          <div class="rp-form-section-title">Informations du rôle</div>
          <div class="row g-3">
            <div class="col-md-5">
              <label class="rp-form-label">Libellé <span class="req">*</span></label>
              <div class="rp-input-wrap">
                <i class="ri-shield-line rp-input-icon"></i>
                <input type="text"
                       class="rp-input {{ $errorLibelle ? 'rp-input-err' : '' }}"
                       wire:model.live="libelle"
                       placeholder="Ex : Admin Finance"/>
              </div>
            </div>
            <div class="col-md-4">
              <label class="rp-form-label">Code <span class="req">*</span></label>
              <div class="rp-input-wrap">
                <i class="ri-code-s-slash-line rp-input-icon"></i>
                <input type="text"
                       class="rp-input rp-input-mono {{ $errorCode ? 'rp-input-err' : '' }}"
                       wire:model="code"
                       placeholder="ADMIN_FINANCE"/>
              </div>
            </div>
            <div class="col-md-12">
              <label class="rp-form-label">Description</label>
              <div class="rp-input-wrap">
                <i class="ri-align-left rp-input-icon" style="top:14px;transform:none"></i>
                <textarea class="rp-input" wire:model="description" rows="2"
                          style="height:auto;padding:10px 14px 10px 38px;resize:none"
                          placeholder="Description du rôle…"></textarea>
              </div>
            </div>
          </div>
        </div>

        {{-- Section permissions --}}
        @if(auth()->user()?->hasPermission('PERMISSION_ATTRIBUATE'))
        <div class="rp-form-section">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="rp-form-section-title" style="margin-bottom:0">Permissions du rôle</div>
            <div style="font-size:13px;color:var(--rp-muted);font-weight:600">
              {{ count($permissionIds) }} / {{ collect($allGrouped)->flatten(1)->count() }} permissions
            </div>
          </div>

          @foreach($allGrouped as $code => $perms)
          @php
            $permGroupIds = $perms->pluck('id')->map(fn($i)=>(string)$i)->toArray();
            $allChecked   = empty(array_diff($permGroupIds, $permissionIds));
          @endphp

          <div class="rp-perm-group">
            <div class="rp-perm-group-header">
              <div style="font-size:14px;font-weight:800;color:#212529">{{ $code }}</div>
              <button type="button"
                      class="rp-toggle-all"
                      wire:click="toggleGroupe('{{ $code }}', {{ json_encode($perms->pluck('id')->toArray()) }})">
                {{ $allChecked ? 'Tout désélectionner' : 'Tout sélectionner' }}
              </button>
            </div>

            <div class="rp-perm-grid">
              @foreach($perms as $perm)
              @php $checked = in_array((string)$perm['id'], $permissionIds); @endphp
              <label class="rp-perm-item {{ $checked ? 'checked' : '' }}"
                     wire:click="togglePermission({{ $perm['id'] }})">
                <div class="rp-perm-checkbox {{ $checked ? 'rp-checked' : '' }}">
                  @if($checked)<i class="ri-check-line"></i>@endif
                </div>
                <span class="rp-perm-label">{{ $perm['libelle'] }}</span>
              </label>
              @endforeach
            </div>
          </div>
          @endforeach

        </div>{{-- /permissions --}}
        @endif

      </div>{{-- /modal-body --}}

      {{-- Footer --}}
      <div class="modal-footer" style="border:none;padding:16px 32px 24px;background:#fff;justify-content:space-between">
        <button class="rp-btn-cancel" data-bs-dismiss="modal">Annuler</button>
        @if(auth()->user()?->hasPermission($editId ? 'ROLE_EDIT' : 'ROLE_CREATE'))
        <button class="rp-btn-save" wire:click="save" wire:loading.attr="disabled">
          <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1"></span>
          <i class="ri-shield-check-line" wire:loading.remove wire:target="save"></i>
          <span wire:loading.remove wire:target="save">
            {{ $editId ? ' Enregistrer les modifications' : ' Créer le rôle' }}
          </span>
          <span wire:loading wire:target="save">Enregistrement…</span>
        </button>
        @endif
      </div>

    </div>
  </div>
</div>

</div>


@push('styles')
<link href="{{ asset('assets/css/roles-permissions.css') }}" rel="stylesheet" type="text/css" />
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


@push('styles')
<style>
  .feedback-text{ width:100%; margin-top:.25rem; font-size:.875em; color:#f06548; }
</style>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
@endpush
