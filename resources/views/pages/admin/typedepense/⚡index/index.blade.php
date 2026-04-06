<div>
<div class="page-content">
<div class="container-fluid">

  {{-- ══ HEADER ════════════════════════════════════════════ --}}
  <div class="td-page-header fu fu-1">
    <div>
      <h4>Types de Dépense</h4>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Types de dépense</li>
        </ol>
      </nav>
    </div>
    <button class="btn-td-primary" wire:click="openAdd()">
      <i class="ri-add-circle-line"></i> Nouveau type
    </button>
  </div>

  {{-- ══ KPI STRIP ══════════════════════════════════════════ --}}
  <div class="td-kpi-strip">
    <div class="td-kpi tk-total fu fu-1">
      <div class="tki-icon" style="background:rgba(64,81,137,.10);color:#405189"><i class="ri-list-check-2"></i></div>
      <div>
        <div class="tki-label">Total types</div>
        <div class="tki-val">{{ $kpis['total'] }}</div>
        <div class="tki-sub">Configurés</div>
      </div>
    </div>
    <div class="td-kpi tk-actif fu fu-2">
      <div class="tki-icon" style="background:rgba(10,179,156,.10);color:#0ab39c"><i class="ri-checkbox-circle-line"></i></div>
      <div>
        <div class="tki-label">Actifs</div>
        <div class="tki-val">{{ $kpis['actifs'] }}</div>
        <div class="tki-sub">En cours d'utilisation</div>
      </div>
    </div>
    <div class="td-kpi tk-inactif fu fu-3">
      <div class="tki-icon" style="background:rgba(135,138,153,.10);color:#878a99"><i class="ri-forbid-line"></i></div>
      <div>
        <div class="tki-label">Inactifs</div>
        <div class="tki-val">{{ $kpis['inactifs'] }}</div>
        <div class="tki-sub">Désactivés</div>
      </div>
    </div>
    <div class="td-kpi tk-montant fu fu-4">
      <div class="tki-icon" style="background:rgba(240,101,72,.10);color:#f06548"><i class="ri-money-cny-circle-line"></i></div>
      <div>
        <div class="tki-label">Total dépensé</div>
        <div class="tki-val" style="font-size:14px">{{ number_format($kpis['montant'], 0, ',', ' ') }} F</div>
        <div class="tki-sub">Toutes catégories</div>
      </div>
    </div>
  </div>

  {{-- ══ TOOLBAR ════════════════════════════════════════════ --}}
  <div class="td-toolbar fu fu-2">
    <div class="sw">
      <i class="ri-search-line"></i>
      <input type="text" wire:model.live.debounce.400ms="search" placeholder="Rechercher un type de dépense…">
    </div>
  </div>

  {{-- ══ TABLE ══════════════════════════════════════════════ --}}
  <div class="td-table-card fu fu-3" wire:loading.class="opacity-50">
    <div class="table-responsive">
      <table class="td-table">
        <thead>
          <tr>
            <th>Libellé</th>
            <th>Description</th>
            <th>Nb dépenses</th>
            <th>Total engagé</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($typeDepenses as $td)
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:10px">
                <div style="width:36px;height:36px;border-radius:10px;background:rgba(240,101,72,.10);color:#f06548;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0">
                  <i class="ri-shopping-cart-line"></i>
                </div>
                <span style="font-size:13px;font-weight:700;color:#212529">{{ $td->libelle }}</span>
              </div>
            </td>
            <td><span style="font-size:12px;color:var(--td-muted)">{{ $td->description ?? '—' }}</span></td>
            <td>
              <span style="font-size:13px;font-weight:700;color:#405189">{{ $td->depenses_count }}</span>
              <span style="font-size:10px;color:var(--td-muted);margin-left:4px">opération(s)</span>
            </td>
            <td>
              <span style="font-family:var(--td-mono);font-size:13px;font-weight:800;color:#f06548">
                {{ number_format($td->depenses_sum_montant ?? 0, 0, ',', ' ') }} FCFA
              </span>
            </td>
            <td>
              @if($td->status === 'actif')
                <span class="td-pill tp-actif"><i class="ri-checkbox-circle-line"></i>Actif</span>
              @else
                <span class="td-pill tp-inactif"><i class="ri-forbid-line"></i>Inactif</span>
              @endif
            </td>
            <td>
              <div class="td-actions">
                <button class="btn btn-soft-primary waves-effect" wire:click="openEdit({{ $td->id }})" title="Modifier">
                  <i class="ri-edit-line"></i>
                </button>
                <button class="btn {{ $td->status === 'actif' ? 'btn-soft-warning' : 'btn-soft-success' }} waves-effect"
                        wire:click="toggleStatus({{ $td->id }})"
                        title="{{ $td->status === 'actif' ? 'Désactiver' : 'Activer' }}">
                  <i class="{{ $td->status === 'actif' ? 'ri-eye-off-line' : 'ri-eye-line' }}"></i>
                </button>
                <button class="btn btn-soft-danger waves-effect" wire:click="confirmDelete({{ $td->id }})" title="Supprimer">
                  <i class="ri-delete-bin-line"></i>
                </button>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6">
              <div class="td-empty">
                <i class="ri-list-check-2"></i>
                <p>Aucun type de dépense trouvé</p>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="td-pagination">
      <span class="td-pag-info">{{ $typeDepenses->firstItem() ?? 0 }}–{{ $typeDepenses->lastItem() ?? 0 }} sur {{ $typeDepenses->total() }}</span>
      <div>{{ $typeDepenses->links('livewire::bootstrap') }}</div>
    </div>
  </div>

</div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL AJOUT / MODIFICATION TYPE DÉPENSE
     Pattern scroll : div(overflow-y:auto) + footer HORS du div
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalTypeDepense" tabindex="-1" aria-hidden="true" wire:ignore.self>
  <div class="modal-dialog" style="max-width:520px">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;display:flex;flex-direction:column;max-height:90vh">

      {{-- Header fixe --}}
      <div class="td-modal-header" style="flex-shrink:0">
        <div class="tdmh-left">
          <div class="tdmh-icon">
            <i class="{{ $editId ? 'ri-edit-line' : 'ri-add-circle-line' }}"></i>
          </div>
          <div>
            <p class="tdmh-title">{{ $editId ? 'Modifier le type' : 'Nouveau type de dépense' }}</p>
            <p class="tdmh-sub">Catégoriser vos dépenses de la mosquée</p>
          </div>
        </div>
        <button class="td-modal-close" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>
      </div>

      {{-- Zone scrollable SEULEMENT --}}
      <div style="overflow-y:auto;flex:1;padding:24px 24px 0">

        @if($errors->any())
        <div style="background:rgba(240,101,72,.06);border:1px solid rgba(240,101,72,.25);border-left:3px solid #f06548;border-radius:0 10px 10px 0;padding:10px 14px;margin-bottom:16px;">
          @foreach($errors->all() as $err)
            <div style="font-size:12px;color:#c44a2e;">• {{ $err }}</div>
          @endforeach
        </div>
        @endif

        <div class="mb-3">
          <label class="form-label-td">Libellé <span class="req">*</span></label>
          <div class="input-td-wrap">
            <i class="ri-text itw-icon"></i>
            <input type="text"
                   class="input-td {{ $errors->has('libelle') ? 'is-err' : '' }}"
                   wire:model="libelle"
                   placeholder="ex : Électricité, Entretien, Salaires…">
          </div>
          @error('libelle') <div class="err-td show">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label-td">Description</label>
          <textarea class="input-td"
                    wire:model="description"
                    rows="3"
                    placeholder="Description optionnelle de ce type de dépense…"
                    style="height:auto;padding:10px 14px;resize:vertical;min-height:80px"></textarea>
        </div>

        <div class="mb-3" style="padding-bottom:8px">
          <label class="form-label-td">Statut</label>
          <div style="display:flex;gap:10px">
            <button type="button"
                    class="td-status-btn {{ $status === 'actif' ? 'selected-actif' : '' }}"
                    wire:click="$set('status','actif')">
              <i class="ri-checkbox-circle-line" style="color:#0ab39c"></i> Actif
            </button>
            <button type="button"
                    class="td-status-btn {{ $status === 'inactif' ? 'selected-inactif' : '' }}"
                    wire:click="$set('status','inactif')">
              <i class="ri-forbid-line" style="color:#878a99"></i> Inactif
            </button>
          </div>
        </div>

      </div>

      {{-- Footer HORS du scrollable — toujours visible --}}
      <div class="td-modal-footer" style="flex-shrink:0">
        <button class="btn-td-secondary" data-bs-dismiss="modal">
          <i class="ri-close-line me-1"></i> Annuler
        </button>
        <button class="btn-td-primary" wire:click="save" wire:loading.attr="disabled">
          <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1"></span>
          <i class="ri-save-line" wire:loading.remove wire:target="save"></i>
          <span wire:loading.remove wire:target="save">{{ $editId ? 'Enregistrer' : 'Créer' }}</span>
          <span wire:loading wire:target="save">Enregistrement…</span>
        </button>
      </div>

    </div>
  </div>
</div>

</div>


@push('styles')
<link href="{{ asset('assets/css/type-depense.css') }}" rel="stylesheet" type="text/css" />
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
Livewire.on('swal:modalGetInfo_message_not_timer', (payload) => {
    const data = Array.isArray(payload) ? payload[0] : payload;
    Swal.fire({ title: data.title, text: data.text, icon: data.type });
});
</script>
@endpush


@push('styles')
<style>
    .feedback-text{ width:100%; margin-top:.25rem; font-size:.875em; color:#f06548; }
</style>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
@endpush
