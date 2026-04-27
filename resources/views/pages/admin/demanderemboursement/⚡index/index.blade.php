<div>
<div class="page-content">
<div class="container-fluid">

  {{-- ══ PAGE HEADER ══════════════════════════════════════ --}}
  <div class="page-header-cust fade-up">
    <div>
      <h4>Demandes de remboursement</h4>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Remboursements</li>
        </ol>
      </nav>
    </div>
    @if($kpis['attente'] > 0)
    <span style="background:rgba(240,101,72,.1);color:#f06548;font-size:12px;font-weight:700;padding:6px 14px;border-radius:20px;display:inline-flex;align-items:center;gap:6px">
      <i class="ri-alarm-warning-line"></i> {{ $kpis['attente'] }} en attente de traitement
    </span>
    @endif
  </div>

  {{-- ══ KPI STRIP ══════════════════════════════════════ --}}
  <div class="cust-kpi-bar">
    <div class="ckpi fade-up fu-1">
      <div class="ckpi-icon" style="background:rgba(64,81,137,.10);color:#405189"><i class="ri-refund-2-line"></i></div>
      <div>
        <div class="ckpi-label">Total</div>
        <div class="ckpi-value">{{ $kpis['total'] }}</div>
        <div class="ckpi-sub">Toutes demandes</div>
      </div>
    </div>
    <div class="ckpi fade-up fu-2">
      <div class="ckpi-icon" style="background:rgba(247,184,75,.12);color:#f7b84b"><i class="ri-time-line"></i></div>
      <div>
        <div class="ckpi-label">En attente</div>
        <div class="ckpi-value">{{ $kpis['attente'] }}</div>
        <div class="ckpi-sub">À traiter</div>
      </div>
    </div>
    <div class="ckpi fade-up fu-3">
      <div class="ckpi-icon" style="background:rgba(10,179,156,.10);color:#0ab39c"><i class="ri-checkbox-circle-line"></i></div>
      <div>
        <div class="ckpi-label">Validées</div>
        <div class="ckpi-value">{{ $kpis['validees'] }}</div>
        <div class="ckpi-sub">Remboursées</div>
      </div>
    </div>
    <div class="ckpi fade-up fu-4">
      <div class="ckpi-icon" style="background:rgba(240,101,72,.10);color:#f06548"><i class="ri-close-circle-line"></i></div>
      <div>
        <div class="ckpi-label">Rejetées</div>
        <div class="ckpi-value">{{ $kpis['rejetees'] }}</div>
        <div class="ckpi-sub">Refusées</div>
      </div>
    </div>
    <div class="ckpi fade-up fu-5">
      <div class="ckpi-icon" style="background:rgba(212,168,67,.12);color:#d4a843"><i class="ri-money-cny-circle-line"></i></div>
      <div>
        <div class="ckpi-label">Montant remboursé</div>
        <div class="ckpi-value" style="font-size:14px">{{ number_format($kpis['montant'], 0, ',', ' ') }} FCFA</div>
        <div class="ckpi-sub">Sorties de caisse</div>
      </div>
    </div>
  </div>

  {{-- ══ TABS ═════════════════════════════════════════════ --}}
  <div class="pay-tabs fade-up fu-2">
    <span class="tab-label"><i class="ri-filter-3-line me-1"></i>Statut :</span>
    <button class="pay-tab {{ $filterStatut === 'tous' ? 'active' : '' }}" wire:click="$set('filterStatut','tous')">
      Tous <span class="tab-count">{{ $tabCounts['tous'] }}</span>
    </button>
    <button class="pay-tab tab-pending {{ $filterStatut === 'en_attente' ? 'active' : '' }}" wire:click="$set('filterStatut','en_attente')">
      <i class="ri-time-line"></i>En attente <span class="tab-count">{{ $tabCounts['en_attente'] }}</span>
    </button>
    <button class="pay-tab tab-success {{ $filterStatut === 'validee' ? 'active' : '' }}" wire:click="$set('filterStatut','validee')">
      <i class="ri-checkbox-circle-line"></i>Validées <span class="tab-count">{{ $tabCounts['validee'] }}</span>
    </button>
    <button class="pay-tab tab-failed {{ $filterStatut === 'rejetee' ? 'active' : '' }}" wire:click="$set('filterStatut','rejetee')">
      <i class="ri-close-circle-line"></i>Rejetées <span class="tab-count">{{ $tabCounts['rejetee'] }}</span>
    </button>
  </div>

  {{-- ══ TOOLBAR ══════════════════════════════════════════ --}}
  <div class="cust-toolbar fade-up fu-3">
    <div class="search-wrap">
      <i class="ri-search-line"></i>
      <input type="text" wire:model.live.debounce.400ms="search" placeholder="Rechercher un fidèle…">
    </div>
    <select class="filter-select" wire:model.live="filterMois">
      <option value="tous">Tous les mois</option>
      @foreach(range(1,12) as $m)
        <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
      @endforeach
    </select>
  </div>

  {{-- ══ TABLE ════════════════════════════════════════════ --}}
  <div class="pay-table-card fade-up fu-4" wire:loading.class="opacity-50">
    <div class="table-responsive">
      <table class="pay-table">
        <thead>
          <tr>
            <th>Fidèle</th>
            <th>Paiement lié</th>
            <th>Motif</th>
            <th>Montant</th>
            <th>Date demande</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($demandes as $dr)
          @php
            $ac = ['#405189','#0ab39c','#f06548','#f7b84b','#299cdb','#d4a843'];
            $col = $ac[($dr->customer_id - 1) % count($ac)];
            $ini = $dr->customer ? strtoupper(substr($dr->customer->prenom,0,1).substr($dr->customer->nom,0,1)) : '??';
            [$pillCls, $pillIcon, $pillLabel] = match($dr->statut) {
                'en_attente' => ['pp-pending', 'ri-time-line',            'En attente'],
                'validee'    => ['pp-success', 'ri-checkbox-circle-line', 'Validée'],
                'rejetee'    => ['pp-failed',  'ri-close-circle-line',    'Rejetée'],
                default      => ['pp-pending', 'ri-question-line',        $dr->statut],
            };
            $ref = $dr->paiement?->reference ?? 'PAY-' . str_pad($dr->paiement_id, 5, '0', STR_PAD_LEFT);
            $typeLabel = $dr->paiement?->cotisation?->typeCotisation?->libelle ?? '—';
          @endphp

          <tr wire:click="openDetail({{ $dr->id }})" style="cursor:pointer"
              class="{{ $dr->statut === 'en_attente' ? 'row-pending' : ($dr->statut === 'validee' ? 'row-success' : 'row-failed') }}">

            <td>
              <div style="display:flex;align-items:center;gap:9px">
                <div class="pay-avatar" style="background:{{ $col }}">{{ $ini }}</div>
                <div>
                  <div class="pay-fidele-name">{{ $dr->customer?->prenom }} {{ $dr->customer?->nom }}</div>
                  <div class="pay-fidele-phone">{{ $dr->customer?->dial_code }} {{ $dr->customer?->phone }}</div>
                </div>
              </div>
            </td>

            <td>
              <span class="pay-ref" style="font-size:11px">{{ $ref }}</span>
              <div style="font-size:10px;color:var(--pay-muted);margin-top:2px">{{ $typeLabel }}</div>
            </td>

            <td>
              <span style="font-size:12px;color:var(--pay-text)">{{ \Str::limit($dr->motif, 40) }}</span>
            </td>

            <td>
              <span style="font-size:14px;font-weight:800;color:#f06548">
                {{ number_format($dr->montant, 0, ',', ' ') }} FCFA
              </span>
            </td>

            <td>
              <div style="font-size:12px;color:var(--pay-text)">{{ $dr->created_at->format('d M Y') }}</div>
              <div style="font-size:10px;color:var(--pay-muted)">{{ $dr->created_at->format('H:i') }}</div>
            </td>

            <td>
              <span class="pay-pill {{ $pillCls }}">
                <i class="{{ $pillIcon }}"></i>{{ $pillLabel }}
              </span>
            </td>

            <td wire:click.stop="">
              <div class="pay-actions">
                <button class="btn btn-soft-primary waves-effect" wire:click="openDetail({{ $dr->id }})" title="Voir">
                  <i class="ri-eye-line"></i>
                </button>
                @if($dr->statut === 'en_attente')
                <button class="btn btn-soft-success waves-effect" wire:click="confirmerValidation({{ $dr->id }})" title="Valider">
                  <i class="ri-checkbox-circle-line"></i>
                </button>
                <button class="btn btn-soft-danger waves-effect" wire:click="confirmerRejet({{ $dr->id }})" title="Rejeter">
                  <i class="ri-close-circle-line"></i>
                </button>
                @endif
              </div>
            </td>

          </tr>
          @empty
          <tr>
            <td colspan="7">
              <div class="pay-empty">
                <i class="ri-refund-2-line"></i>
                <p>Aucune demande de remboursement</p>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="pay-pagination">
      <span class="pay-pag-info">
        {{ $demandes->firstItem() ?? 0 }}–{{ $demandes->lastItem() ?? 0 }} sur {{ $demandes->total() }} demande(s)
      </span>
      <div>{{ $demandes->links('livewire::bootstrap') }}</div>
    </div>
  </div>

</div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL DÉTAIL DEMANDE
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalDetailDemande" tabindex="-1" aria-hidden="true" wire:ignore.self>
  <div class="modal-dialog" style="max-width:600px">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden">

      @if($detailDemande)
      @php
        $dd = $detailDemande;
        $headerGrad = match($dd->statut) {
            'validee'    => 'linear-gradient(130deg,#089383,#0ab39c)',
            'rejetee'    => 'linear-gradient(130deg,#c0341a,#f06548)',
            default      => 'linear-gradient(130deg,#a07c10,#d4a843)',
        };
        $ref = $dd->paiement?->reference ?? 'PAY-' . str_pad($dd->paiement_id, 5, '0', STR_PAD_LEFT);
        $typeLabel = $dd->paiement?->cotisation?->typeCotisation?->libelle ?? '—';
        $periodeLabel = ($dd->paiement?->cotisation?->mois && $dd->paiement?->cotisation?->annee)
            ? \Carbon\Carbon::create($dd->paiement->cotisation->annee, $dd->paiement->cotisation->mois)->translatedFormat('F Y')
            : '—';
        [$pillCls, $pillIcon, $pillLabel] = match($dd->statut) {
            'en_attente' => ['pp-pending', 'ri-time-line',            'En attente'],
            'validee'    => ['pp-success', 'ri-checkbox-circle-line', 'Validée'],
            'rejetee'    => ['pp-failed',  'ri-close-circle-line',    'Rejetée'],
            default      => ['pp-pending', 'ri-question-line',        $dd->statut],
        };
      @endphp

      {{-- Header --}}
      <div class="pay-modal-header" style="background:{{ $headerGrad }}">
        <button class="pmh-close" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>
        <div class="pmh-inner">
          <div class="pmh-icon"><i class="ri-refund-2-line"></i></div>
          <div>
            <div class="pmh-name">{{ $dd->customer?->prenom }} {{ $dd->customer?->nom }}</div>
            <div class="pmh-meta">
              <span><i class="ri-hashtag"></i>{{ $ref }}</span>
              <span><i class="ri-calendar-line"></i>{{ $dd->created_at->format('d M Y') }}</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Stats --}}
      <div class="pay-modal-stats">
        <div class="pay-ms-box">
          <div class="pmsb-v" style="color:#f06548">{{ number_format($dd->montant, 0, ',', ' ') }} FCFA</div>
          <div class="pmsb-l">Montant à rembourser</div>
        </div>
        <div class="pay-ms-box">
          <div class="pmsb-v"><span class="pay-pill {{ $pillCls }}" style="font-size:11px"><i class="{{ $pillIcon }}"></i>{{ $pillLabel }}</span></div>
          <div class="pmsb-l">Statut</div>
        </div>
        <div class="pay-ms-box">
          <div class="pmsb-v">{{ $dd->created_at->translatedFormat('d M Y') }}</div>
          <div class="pmsb-l">Date demande</div>
        </div>
      </div>

      <div style="overflow-y:auto;max-height:calc(90vh - 240px)">
        <div class="pay-modal-body">

          {{-- Motif --}}
          <div class="pay-section-title">Motif d'annulation</div>
          <div style="background:rgba(247,184,75,.06);border:1px solid rgba(247,184,75,.2);border-left:4px solid #f7b84b;border-radius:0 10px 10px 0;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#495057;line-height:1.6">
            {{ $dd->motif ?? '—' }}
          </div>

          {{-- Détails --}}
          <div class="pay-section-title">Paiement concerné</div>
          <div class="pay-detail-grid">
            <div class="pay-detail-item">
              <div class="pdi-l"><i class="ri-user-line me-1"></i>Fidèle</div>
              <div class="pdi-v">{{ $dd->customer?->prenom }} {{ $dd->customer?->nom }}</div>
            </div>
            <div class="pay-detail-item">
              <div class="pdi-l"><i class="ri-hashtag me-1"></i>Référence paiement</div>
              <div class="pdi-v" style="font-family:monospace;color:#405189">{{ $ref }}</div>
            </div>
            <div class="pay-detail-item">
              <div class="pdi-l"><i class="ri-tag-line me-1"></i>Type de cotisation</div>
              <div class="pdi-v">{{ $typeLabel }}</div>
            </div>
            <div class="pay-detail-item">
              <div class="pdi-l"><i class="ri-calendar-line me-1"></i>Période</div>
              <div class="pdi-v">{{ $periodeLabel }}</div>
            </div>
            <div class="pay-detail-item">
              <div class="pdi-l"><i class="ri-money-cny-circle-line me-1"></i>Montant à rembourser</div>
              <div class="pdi-v" style="color:#f06548;font-weight:800;font-size:15px">{{ number_format($dd->montant, 0, ',', ' ') }} FCFA</div>
            </div>
            <div class="pay-detail-item">
              <div class="pdi-l"><i class="ri-user-settings-line me-1"></i>Annulé par</div>
              <div class="pdi-v">{{ $dd->createdBy?->name ?? 'Admin' }}</div>
            </div>
            @if($dd->validatedBy)
            <div class="pay-detail-item">
              <div class="pdi-l"><i class="ri-shield-check-line me-1"></i>Traité par</div>
              <div class="pdi-v">{{ $dd->validatedBy->name }}</div>
            </div>
            <div class="pay-detail-item">
              <div class="pdi-l"><i class="ri-time-line me-1"></i>Traité le</div>
              <div class="pdi-v">{{ $dd->validated_at?->format('d M Y H:i') }}</div>
            </div>
            @endif
          </div>

          {{-- Note si validée --}}
          @if($dd->statut === 'validee')
          <div style="background:rgba(10,179,156,.06);border:1px solid rgba(10,179,156,.2);border-radius:10px;padding:12px 14px;margin-top:14px">
            <div style="font-size:12px;color:#089383;font-weight:700;margin-bottom:3px">
              <i class="ri-checkbox-circle-line me-1"></i>Remboursement validé
            </div>
            <div style="font-size:11px;color:#495057">
              Une transaction de sortie de {{ number_format($dd->montant, 0, ',', ' ') }} FCFA a été créée dans le bilan financier.
            </div>
          </div>
          @endif

          {{-- Actions --}}
          @if($dd->statut === 'en_attente')
          <div class="d-flex gap-2 mt-4 flex-wrap">
            <button class="btn btn-success waves-effect"
                    wire:click="confirmerValidation({{ $dd->id }})"
                    data-bs-dismiss="modal">
              <i class="ri-checkbox-circle-line me-1"></i>Valider le remboursement
            </button>
            <button class="btn btn-soft-danger waves-effect"
                    wire:click="confirmerRejet({{ $dd->id }})"
                    data-bs-dismiss="modal">
              <i class="ri-close-circle-line me-1"></i>Rejeter
            </button>
          </div>
          @endif

        </div>
      </div>

      <div class="pay-modal-footer">
        <button class="btn-pay-secondary" data-bs-dismiss="modal">
          <i class="ri-close-line me-1"></i>Fermer
        </button>
      </div>

      @endif
    </div>
  </div>
</div>

</div>

@push('styles')
<link href="{{ asset('assets/css/paiements.css') }}" rel="stylesheet" type="text/css" />
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