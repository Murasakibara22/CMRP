<div>
<div class="page-content">
<div class="container-fluid">

  {{-- ══ HEADER ════════════════════════════════════════════ --}}
  <div class="ce-page-header fu fu-1">
    <div>
      <h4>Paliers d'engagement</h4>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Coûts d'engagement</li>
        </ol>
      </nav>
    </div>
    @if(auth()->user()?->hasPermission('COUT_ENGAGEMENT_CREATE'))
    <button class="btn-ce-primary" wire:click="openAdd">
      <i class="ri-add-circle-line"></i> Nouveau palier
    </button>
    @endif
  </div>

  {{-- ══ KPI STRIP ══════════════════════════════════════════ --}}
  <div class="ce-kpi-strip">
    <div class="ce-kpi ck-total fu fu-1">
      <div class="cki-icon" style="background:rgba(64,81,137,.10);color:#405189"><i class="ri-list-ordered"></i></div>
      <div>
        <div class="cki-label">Total paliers</div>
        <div class="cki-val">{{ $kpis['total'] }}</div>
        <div class="cki-sub">Configurés</div>
      </div>
    </div>
    <div class="ce-kpi ck-actif fu fu-2">
      <div class="cki-icon" style="background:rgba(10,179,156,.10);color:#0ab39c"><i class="ri-checkbox-circle-line"></i></div>
      <div>
        <div class="cki-label">Actifs</div>
        <div class="cki-val">{{ $kpis['actifs'] }}</div>
        <div class="cki-sub">Disponibles</div>
      </div>
    </div>
    <div class="ce-kpi ck-min fu fu-3">
      <div class="cki-icon" style="background:rgba(247,184,75,.12);color:#f7b84b"><i class="ri-arrow-down-circle-line"></i></div>
      <div>
        <div class="cki-label">Palier min</div>
        <div class="cki-val" style="font-size:15px">{{ number_format($kpis['min'], 0, ',', ' ') }}</div>
        <div class="cki-sub">FCFA</div>
      </div>
    </div>
    <div class="ce-kpi ck-max fu fu-4">
      <div class="cki-icon" style="background:rgba(64,81,137,.10);color:#405189"><i class="ri-arrow-up-circle-line"></i></div>
      <div>
        <div class="cki-label">Palier max</div>
        <div class="cki-val" style="font-size:15px">{{ number_format($kpis['max'], 0, ',', ' ') }}</div>
        <div class="cki-sub">FCFA</div>
      </div>
    </div>
  </div>

  {{-- ══ TOOLBAR ════════════════════════════════════════════ --}}
  <div class="ce-toolbar fu fu-2">
    <div class="sw">
      <i class="ri-search-line"></i>
      <input type="text" wire:model.live.debounce.300ms="search"
             placeholder="Rechercher un palier…">
    </div>
  </div>

  {{-- ══ TABLE ══════════════════════════════════════════════ --}}
  <div class="ce-table-card fu fu-3" wire:loading.class="opacity-50">
    <div class="table-responsive">
      <table class="ce-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Montant</th>
            <th>Libellé</th>
            <th>Utilisation</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($couts as $i => $ce)
          <tr wire:key="ce-{{ $ce->id }}">
            <td style="font-family:var(--ce-mono);font-size:11px;color:var(--ce-muted)">
              #{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
            </td>
            <td>
              <div style="display:flex;align-items:center;gap:10px">
                <div style="width:38px;height:38px;border-radius:10px;background:rgba(64,81,137,.10);color:#405189;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0">
                  <i class="ri-money-cny-circle-line"></i>
                </div>
                <span style="font-size:15px;font-weight:900;color:#212529;font-family:var(--ce-mono)">
                  {{ number_format($ce->montant, 0, ',', ' ') }}
                  <span style="font-size:11px;font-weight:600;color:var(--ce-muted)"> FCFA</span>
                </span>
              </div>
            </td>
            <td>
              <span style="font-size:12px;color:var(--ce-muted)">{{ $ce->libelle ?? '—' }}</span>
            </td>
            <td>
              @php $nbCustomers = \App\Models\Customer::where('montant_engagement', $ce->montant)->count(); @endphp
              <span style="font-size:13px;font-weight:700;color:#405189">{{ $nbCustomers }}</span>
              <span style="font-size:10px;color:var(--ce-muted);margin-left:3px">fidèle(s)</span>
            </td>
            <td>
              @if($ce->status ?? 'actif' === 'actif')
                <span class="ce-pill cp-actif"><i class="ri-checkbox-circle-line"></i> Actif</span>
              @else
                <span class="ce-pill cp-inactif"><i class="ri-forbid-line"></i> Inactif</span>
              @endif
            </td>
            <td>
              <div class="ce-actions">
                @if(auth()->user()?->hasPermission('COUT_ENGAGEMENT_EDIT'))
                  <button class="btn btn-soft-primary waves-effect"
                          wire:click="openEdit({{ $ce->id }})" title="Modifier">
                    <i class="ri-edit-line"></i>
                  </button>
                @endif
                <button class="btn {{ ($ce->status ?? 'actif') === 'actif' ? 'btn-soft-warning' : 'btn-soft-success' }} waves-effect"
                        wire:click="toggleActif({{ $ce->id }})"
                        title="{{ ($ce->status ?? 'actif') === 'actif' ? 'Désactiver' : 'Activer' }}">
                  <i class="{{ ($ce->status ?? 'actif') === 'actif' ? 'ri-eye-off-line' : 'ri-eye-line' }}"></i>
                </button>

                @if(auth()->user()?->hasPermission('COUT_ENGAGEMENT_DELETE'))
                <button class="btn btn-soft-danger waves-effect"
                        wire:click="confirmDelete({{ $ce->id }})" title="Supprimer">
                  <i class="ri-delete-bin-line"></i>
                </button>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6">
              <div class="ce-empty">
                <i class="ri-list-ordered"></i>
                <p>Aucun palier configuré</p>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="ce-pagination">
      <span class="ce-pag-info">
        {{ $couts->firstItem() ?? 0 }}–{{ $couts->lastItem() ?? 0 }} sur {{ $couts->total() }}
      </span>
      <div>{{ $couts->links('livewire::bootstrap') }}</div>
    </div>
  </div>

</div>
</div>


{{-- ══ MODAL AJOUT / MODIFICATION ═════════════════════════ --}}
<div class="modal fade" id="modalCoutEngagement" tabindex="-1" aria-hidden="true" wire:ignore.self>
  <div class="modal-dialog" style="max-width:460px">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;display:flex;flex-direction:column;max-height:90vh">

      <div class="ce-modal-header" style="flex-shrink:0">
        <div class="cemh-left">
          <div class="cemh-icon">
            <i class="{{ $editId ? 'ri-edit-line' : 'ri-add-circle-line' }}"></i>
          </div>
          <div>
            <p class="cemh-title">{{ $editId ? 'Modifier le palier' : 'Nouveau palier d\'engagement' }}</p>
            <p class="cemh-sub">Montant mensuel proposé aux fidèles</p>
          </div>
        </div>
        <button class="ce-modal-close" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>
      </div>

      <div style="overflow-y:auto;flex:1;padding:24px 24px 0">

        <div class="mb-3">
          <label class="form-label-ce">Montant (FCFA) <span class="req">*</span></label>
          <div class="input-ce-wrap">
            <i class="ri-money-cny-circle-line itw-icon"></i>
            <input type="number"
                   class="input-ce {{ $errorMontant ? 'is-err' : '' }}"
                   wire:model="montant"
                   placeholder="ex : 10000"
                   min="100" inputmode="numeric"/>
          </div>
          @if($errorMontant)<div class="err-ce">{{ $errorMontant }}</div>@endif
          <div style="font-size:11px;color:var(--ce-muted);margin-top:4px">Montant minimum : 100 FCFA</div>
        </div>

        <div class="mb-3">
          <label class="form-label-ce">Libellé <span style="font-size:10px;color:var(--ce-muted);font-weight:500;text-transform:none">(optionnel)</span></label>
          <div class="input-ce-wrap">
            <i class="ri-text itw-icon"></i>
            <input type="text"
                   class="input-ce {{ $errorLibelle ? 'is-err' : '' }}"
                   wire:model="libelle"
                   placeholder="ex : Palier Bronze, Standard…"/>
          </div>
          @if($errorLibelle)<div class="err-ce">{{ $errorLibelle }}</div>@endif
        </div>

        <div class="mb-4">
          <label class="form-label-ce">Statut</label>
          <div style="display:flex;gap:10px">
            <button type="button"
                    class="ce-status-btn {{ $isActif ? 'selected-actif' : '' }}"
                    wire:click="$set('isActif',true)">
              <i class="ri-checkbox-circle-line" style="color:#0ab39c"></i> Actif
            </button>
            <button type="button"
                    class="ce-status-btn {{ ! $isActif ? 'selected-inactif' : '' }}"
                    wire:click="$set('isActif',false)">
              <i class="ri-forbid-line" style="color:#878a99"></i> Inactif
            </button>
          </div>
        </div>

      </div>

      <div class="ce-modal-footer" style="flex-shrink:0">
        <button class="btn-ce-secondary" data-bs-dismiss="modal">
          <i class="ri-close-line me-1"></i> Annuler
        </button>
        @if(auth()->user()?->hasPermission($editId ? 'COUT_ENGAGEMENT_EDIT' : 'COUT_ENGAGEMENT_CREATE'))
        <button class="btn-ce-primary" wire:click="save" wire:loading.attr="disabled">
          <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1"></span>
          <i class="ri-save-line" wire:loading.remove wire:target="save"></i>
          <span wire:loading.remove wire:target="save"> {{ $editId ? 'Enregistrer' : 'Créer' }}</span>
          <span wire:loading wire:target="save">Enregistrement…</span>
        </button>
        @endif
      </div>

    </div>
  </div>
</div>

</div>


@push('styles')
<link href="{{ asset('assets/css/cout-engagement.css') }}" rel="stylesheet" type="text/css" />
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

