<div>
<div class="page-content">
<div class="container-fluid">

  {{-- ══ HEADER ════════════════════════════════════════════ --}}
  <div class="dep-page-header fu fu-1">
    <div>
      <h4>Dépenses</h4>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Dépenses</li>
        </ol>
      </nav>
    </div>
    <div class="d-flex gap-2">
      <button class="btn-dep-primary" wire:click="openAdd()">
        <i class="ri-add-circle-line"></i> Nouvelle dépense
      </button>
      <button class="btn btn-soft-success btn-sm waves-effect">
        <i class="ri-file-excel-2-line me-1"></i> Exporter
      </button>
    </div>
  </div>

  {{-- ══ KPI STRIP ══════════════════════════════════════════ --}}
  <div class="dep-kpi-strip">
    <div class="dep-kpi dk-total fu fu-1">
      <div class="dki-icon" style="background:rgba(64,81,137,.10);color:#405189"><i class="ri-receipt-line"></i></div>
      <div>
        <div class="dki-label">Total dépenses</div>
        <div class="dki-val">{{ $kpis['total'] }}</div>
        <div class="dki-sub">Toutes périodes</div>
      </div>
    </div>
    <div class="dep-kpi dk-mois fu fu-2">
      <div class="dki-icon" style="background:rgba(41,156,219,.10);color:#299cdb"><i class="ri-calendar-line"></i></div>
      <div>
        <div class="dki-label">Ce mois</div>
        <div class="dki-val">{{ $kpis['ce_mois'] }}</div>
        <div class="dki-sub">{{ now()->translatedFormat('F Y') }}</div>
      </div>
    </div>
    <div class="dep-kpi dk-montant fu fu-3">
      <div class="dki-icon" style="background:rgba(240,101,72,.10);color:#f06548"><i class="ri-money-cny-circle-line"></i></div>
      <div>
        <div class="dki-label">Total engagé</div>
        <div class="dki-val" style="font-size:14px">{{ number_format($kpis['montant_total'], 0, ',', ' ') }} F</div>
        <div class="dki-sub">Toutes périodes</div>
      </div>
    </div>
    <div class="dep-kpi dk-mois-montant fu fu-4">
      <div class="dki-icon" style="background:rgba(247,184,75,.12);color:#f7b84b"><i class="ri-calendar-2-line"></i></div>
      <div>
        <div class="dki-label">Ce mois</div>
        <div class="dki-val" style="font-size:14px">{{ number_format($kpis['montant_mois'], 0, ',', ' ') }} F</div>
        <div class="dki-sub">{{ now()->translatedFormat('F Y') }}</div>
      </div>
    </div>
  </div>

  {{-- ══ GRAPHE + TOOLBAR ══════════════════════════════════ --}}
  <div style="display:grid;grid-template-columns:1fr 340px;gap:16px;margin-bottom:16px" class="fu fu-2">

    {{-- Toolbar --}}
    <div class="dep-toolbar">
      <div class="sw">
        <i class="ri-search-line"></i>
        <input type="text" wire:model.live.debounce.400ms="search" placeholder="Rechercher une dépense…">
      </div>
      <select class="dep-sel" wire:model.live="filterType">
        <option value="tous">Toutes catégories</option>
        @foreach($typesDepense as $td)
          <option value="{{ $td->id }}">{{ $td->libelle }}</option>
        @endforeach
      </select>
      <select class="dep-sel" wire:model.live="filterMois" style="min-width:120px">
        <option value="tous">Tous mois</option>
        @foreach(range(1,12) as $m)
          <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
        @endforeach
      </select>
    </div>

    {{-- Mini graphe 6 mois --}}
    <div class="dep-mini-graph">
      <div style="font-size:11px;font-weight:700;color:var(--dep-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px">6 derniers mois</div>
      <canvas id="chartDepMois" height="70"></canvas>
    </div>

  </div>

  {{-- ══ TABLE ══════════════════════════════════════════════ --}}
  <div class="dep-table-card fu fu-3" wire:loading.class="opacity-50">
    <div class="table-responsive">
      <table class="dep-table">
        <thead>
          <tr>
            <th>Catégorie</th>
            <th>Libellé</th>
            <th>Montant</th>
            <th>Date</th>
            <th>Note</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($depenses as $dep)
          <tr wire:click="openDetail({{ $dep->id }})">
            <td>
              <div style="display:flex;align-items:center;gap:8px">
                <div style="width:32px;height:32px;border-radius:9px;background:rgba(240,101,72,.10);color:#f06548;display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0">
                  <i class="ri-shopping-cart-line"></i>
                </div>
                <span style="font-size:12px;font-weight:700;color:#212529">{{ $dep->typeDepense?->libelle ?? '—' }}</span>
              </div>
            </td>
            <td>
              <span style="font-size:12px;color:var(--dep-text)">{{ $dep->libelle ?? '—' }}</span>
            </td>
            <td>
              <span class="dep-montant">{{ number_format($dep->montant, 0, ',', ' ') }} FCFA</span>
            </td>
            <td>
              <span style="font-size:12px;color:var(--dep-text)">{{ $dep->date_depense->format('d M Y') }}</span>
            </td>
            <td>
              <span style="font-size:11px;color:var(--dep-muted)">{{ $dep->note ? \Str::limit($dep->note, 40) : '—' }}</span>
            </td>
            <td wire:click.stop="">
              <div class="dep-actions">
                <button class="btn btn-soft-primary waves-effect" wire:click="openDetail({{ $dep->id }})" title="Détails">
                  <i class="ri-eye-line"></i>
                </button>
                <button class="btn btn-soft-info waves-effect" wire:click="openEdit({{ $dep->id }})" title="Modifier">
                  <i class="ri-edit-line"></i>
                </button>
                <button class="btn btn-soft-danger waves-effect" wire:click="confirmDelete({{ $dep->id }})" title="Supprimer">
                  <i class="ri-delete-bin-line"></i>
                </button>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6">
              <div class="dep-empty">
                <i class="ri-receipt-line"></i>
                <p>Aucune dépense trouvée</p>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="dep-pagination">
      <span class="dep-pag-info">{{ $depenses->firstItem() ?? 0 }}–{{ $depenses->lastItem() ?? 0 }} sur {{ $depenses->total() }} dépense(s)</span>
      <div>{{ $depenses->links('livewire::bootstrap') }}</div>
    </div>
  </div>

</div>
</div>


{{-- ══ MODAL DÉTAIL DÉPENSE ════════════════════════════════ --}}
<div class="modal fade" id="modalDetailDepense" tabindex="-1" aria-hidden="true" wire:ignore.self>
  <div class="modal-dialog" style="max-width:540px">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;">

      @if($detailDepense)
      @php $dep = $detailDepense; @endphp

      <div class="dep-modal-header">
        <button class="dep-modal-close" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>
        <div class="dep-mh-inner">
          <div class="dep-mh-icon"><i class="ri-shopping-cart-line"></i></div>
          <div>
            <div class="dep-mh-title">{{ $dep->typeDepense?->libelle ?? 'Dépense' }}</div>
            <div class="dep-mh-meta">
              <span><i class="ri-calendar-line"></i>{{ $dep->date_depense->format('d M Y') }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="dep-modal-stats">
        <div class="dep-ms-box">
          <div class="dmsb-v" style="color:#f06548">-{{ number_format($dep->montant, 0, ',', ' ') }} FCFA</div>
          <div class="dmsb-l">Montant</div>
        </div>
        <div class="dep-ms-box">
          <div class="dmsb-v" style="font-size:12px">{{ $dep->typeDepense?->libelle ?? '—' }}</div>
          <div class="dmsb-l">Catégorie</div>
        </div>
        <div class="dep-ms-box">
          <div class="dmsb-v" style="font-size:11px">{{ $dep->date_depense->format('d M Y') }}</div>
          <div class="dmsb-l">Date</div>
        </div>
      </div>

      <div style="overflow-y:auto;max-height:calc(90vh - 230px);">
        <div style="padding:0 22px 22px">

          <div class="dep-section-title">Détails</div>
          <div class="dep-detail-grid">
            <div class="dep-detail-item">
              <div class="dep-di-l"><i class="ri-tag-line me-1"></i>Catégorie</div>
              <div class="dep-di-v">{{ $dep->typeDepense?->libelle ?? '—' }}</div>
            </div>
            <div class="dep-detail-item">
              <div class="dep-di-l"><i class="ri-money-cny-circle-line me-1"></i>Montant</div>
              <div class="dep-di-v" style="color:#f06548;font-family:var(--dep-mono)">{{ number_format($dep->montant, 0, ',', ' ') }} FCFA</div>
            </div>
            <div class="dep-detail-item">
              <div class="dep-di-l"><i class="ri-calendar-line me-1"></i>Date</div>
              <div class="dep-di-v">{{ $dep->date_depense->format('d M Y') }}</div>
            </div>
            <div class="dep-detail-item">
              <div class="dep-di-l"><i class="ri-calendar-event-line me-1"></i>Enregistré le</div>
              <div class="dep-di-v">{{ $dep->created_at->format('d M Y H:i') }}</div>
            </div>
            @if($dep->libelle)
            <div class="dep-detail-item" style="grid-column:1/-1">
              <div class="dep-di-l"><i class="ri-file-text-line me-1"></i>Libellé</div>
              <div class="dep-di-v" style="font-weight:500;color:var(--dep-text)">{{ $dep->libelle }}</div>
            </div>
            @endif
            @if($dep->note)
            <div class="dep-detail-item" style="grid-column:1/-1">
              <div class="dep-di-l"><i class="ri-sticky-note-line me-1"></i>Note</div>
              <div class="dep-di-v" style="font-weight:500;color:var(--dep-text)">{{ $dep->note }}</div>
            </div>
            @endif
          </div>

          <div class="d-flex gap-2">
            <button class="btn btn-soft-info waves-effect btn-sm"
                    wire:click="openEdit({{ $dep->id }})" data-bs-dismiss="modal">
              <i class="ri-edit-line me-1"></i>Modifier
            </button>
            <button class="btn btn-soft-danger waves-effect btn-sm"
                    wire:click="confirmDelete({{ $dep->id }})" data-bs-dismiss="modal">
              <i class="ri-delete-bin-line me-1"></i>Supprimer
            </button>
          </div>

        </div>
      </div>

      <div class="dep-modal-footer">
        <button class="btn-dep-secondary" data-bs-dismiss="modal"><i class="ri-close-line me-1"></i> Fermer</button>
      </div>

      @endif
    </div>
  </div>
</div>


{{-- ══ MODAL FORMULAIRE DÉPENSE ════════════════════════════ --}}
<div class="modal fade" id="modalFormDepense" tabindex="-1" aria-hidden="true" wire:ignore.self>
  <div class="modal-dialog" style="max-width:520px">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;">

      <div class="dep-modal-header">
        <button class="dep-modal-close" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>
        <div class="dep-mh-inner">
          <div class="dep-mh-icon"><i class="{{ $editId ? 'ri-edit-line' : 'ri-add-circle-line' }}"></i></div>
          <div>
            <div class="dep-mh-title">{{ $editId ? 'Modifier la dépense' : 'Nouvelle dépense' }}</div>
            <div class="dep-mh-meta">
              <span><i class="ri-information-line"></i>{{ $editId ? 'Modification — transaction mise à jour auto' : 'Une transaction de sortie sera créée' }}</span>
            </div>
          </div>
        </div>
      </div>

      <div style="overflow-y:auto;max-height:calc(90vh - 142px);">
        <div style="padding:20px 22px 0">

          @if($errors->any())
          <div style="background:rgba(240,101,72,.06);border:1px solid rgba(240,101,72,.25);border-left:3px solid #f06548;border-radius:0 10px 10px 0;padding:10px 14px;margin-bottom:14px;">
            @foreach($errors->all() as $err)
              <div style="font-size:12px;color:#c44a2e;">• {{ $err }}</div>
            @endforeach
          </div>
          @endif

          <div class="dep-section-title">Informations</div>

          <div class="mb-3">
            <label class="form-label-dep">Catégorie <span class="req">*</span></label>
            <div class="dep-input-wrap">
              <i class="ri-tag-line dep-iw-icon"></i>
              <select class="dep-input {{ $errors->has('typeDepenseId') ? 'is-err' : '' }}"
                      wire:model="typeDepenseId" style="padding-left:38px;cursor:pointer">
                <option value="">— Choisir une catégorie —</option>
                @foreach($typesDepense as $td)
                  <option value="{{ $td->id }}">{{ $td->libelle }}</option>
                @endforeach
              </select>
            </div>
            @error('typeDepenseId') <div class="dep-err show">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label-dep">Libellé <span style="font-size:10px;color:var(--dep-muted);font-weight:500;text-transform:none">(optionnel)</span></label>
            <div class="dep-input-wrap">
              <i class="ri-file-text-line dep-iw-icon"></i>
              <input type="text" class="dep-input" wire:model="libelle"
                     placeholder="ex : Facture CIE mars 2025, Achat matériel…">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label-dep">Montant <span class="req">*</span></label>
            <div class="dep-input-wrap">
              <i class="ri-money-cny-circle-line dep-iw-icon"></i>
              <input type="number" class="dep-input dep-has-sfx {{ $errors->has('montant') ? 'is-err' : '' }}"
                     wire:model="montant" placeholder="ex : 25000" min="1">
              <span class="dep-iw-suffix">FCFA</span>
            </div>
            @error('montant') <div class="dep-err show">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label-dep">Date de la dépense <span class="req">*</span></label>
            <div class="dep-input-wrap">
              <i class="ri-calendar-line dep-iw-icon"></i>
              <input type="date" class="dep-input {{ $errors->has('dateDepense') ? 'is-err' : '' }}"
                     wire:model="dateDepense">
            </div>
            @error('dateDepense') <div class="dep-err show">{{ $message }}</div> @enderror
          </div>

          <div class="mb-4">
            <label class="form-label-dep">Note <span style="font-size:10px;color:var(--dep-muted);font-weight:500;text-transform:none">(optionnel)</span></label>
            <textarea class="dep-input" wire:model="note" rows="3"
                      placeholder="Informations complémentaires…"
                      style="height:auto;padding:10px 14px;resize:vertical;min-height:80px"></textarea>
          </div>

        </div>
      </div>

      <div class="dep-modal-footer">
        <button class="btn-dep-secondary" data-bs-dismiss="modal"><i class="ri-close-line me-1"></i> Annuler</button>
        <button class="btn-dep-primary" wire:click="save" wire:loading.attr="disabled">
          <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1"></span>
          <i class="ri-save-line" wire:loading.remove wire:target="save"></i>
          <span wire:loading.remove wire:target="save"> {{ $editId ? 'Enregistrer' : 'Créer la dépense' }}</span>
          <span wire:loading wire:target="save">Enregistrement…</span>
        </button>
      </div>

    </div>
  </div>
</div>

</div>


@push('styles')
<link href="{{ asset('assets/css/depense.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const DEP_GRAPH = @json($graphData);
(function() {
  const ctx = document.getElementById('chartDepMois');
  if (!ctx) return;
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: DEP_GRAPH.map(d => d.label),
      datasets: [{
        data: DEP_GRAPH.map(d => d.montant),
        backgroundColor: 'rgba(240,101,72,.18)',
        borderColor: '#f06548', borderWidth: 2,
        borderRadius: 5, borderSkipped: false,
      }]
    },
    options: {
      responsive: true,
      plugins: { legend:{ display:false }, tooltip:{ callbacks:{ label: ctx => new Intl.NumberFormat('fr-FR').format(ctx.raw)+' FCFA' } } },
      scales: {
        y: { ticks:{ font:{size:9}, callback: v => v>=1000?(v/1000)+'k':v }, grid:{ color:'rgba(0,0,0,.04)' } },
        x: { ticks:{ font:{size:9} }, grid:{ display:false } },
      }
    }
  });
})();

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

