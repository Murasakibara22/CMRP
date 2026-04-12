<div>
<main class="page-content">

  {{-- ══ EN-TÊTE ══════════════════════════════════════════ --}}
  <div class="doc-page-header">
    <div>
      <div class="page-title">Mes Documents</div>
      <div class="page-sub">Gérez vos pièces justificatives.</div>
    </div>
    <button class="btn-add-doc" wire:click="openAdd">
      <i class="ri-upload-2-line"></i> Ajouter
    </button>
  </div>

  {{-- ══ KPI STRIP ══════════════════════════════════════════ --}}
  <div class="doc-kpi-strip">
    <div class="doc-kpi">
      <div class="doc-kpi-icon" style="background:rgba(64,81,137,.10);color:#405189"><i class="ri-file-list-3-line"></i></div>
      <div class="doc-kpi-val">{{ $stats['total'] }}</div>
      <div class="doc-kpi-label">Total</div>
    </div>
    <div class="doc-kpi">
      <div class="doc-kpi-icon" style="background:rgba(10,179,156,.10);color:#0ab39c"><i class="ri-checkbox-circle-line"></i></div>
      <div class="doc-kpi-val">{{ $stats['valide'] }}</div>
      <div class="doc-kpi-label">Validés</div>
    </div>
    <div class="doc-kpi">
      <div class="doc-kpi-icon" style="background:rgba(247,184,75,.12);color:#f7b84b"><i class="ri-time-line"></i></div>
      <div class="doc-kpi-val">{{ $stats['attente'] }}</div>
      <div class="doc-kpi-label">En attente</div>
    </div>
    <div class="doc-kpi">
      <div class="doc-kpi-icon" style="background:rgba(240,101,72,.10);color:#f06548"><i class="ri-close-circle-line"></i></div>
      <div class="doc-kpi-val">{{ $stats['rejete'] }}</div>
      <div class="doc-kpi-label">Rejetés</div>
    </div>
  </div>

  {{-- ══ LISTE DOCUMENTS ═════════════════════════════════ --}}
  @forelse($documents as $doc)
  @php
    [$pillClass, $pillLabel, $statIcn, $statColor] = match($doc->status) {
        'valide'     => ['pill-ok',     'Validé',     'ri-shield-check-line',   '#0ab39c'],
        'en_attente' => ['pill-warn',   'En attente', 'ri-time-line',            '#f7b84b'],
        'rejete'     => ['pill-danger', 'Rejeté',     'ri-close-circle-line',    '#f06548'],
        default      => ['pill-info',   '—',          'ri-file-line',            '#878a99'],
    };
    $extIcon = match(strtolower($doc->extension ?? '')) {
        'pdf'  => 'ri-file-pdf-line',
        'jpg','jpeg','png','webp' => 'ri-image-line',
        'doc','docx' => 'ri-file-word-line',
        default => 'ri-file-line',
    };
    $tailleFmt = $doc->taille
        ? ($doc->taille >= 1048576
            ? round($doc->taille / 1048576, 1).' Mo'
            : round($doc->taille / 1024).' Ko')
        : null;
  @endphp

  <div class="doc-item card"
       wire:key="doc-{{ $doc->id }}"
       wire:click="openDetail({{ $doc->id }})"
       style="cursor:pointer">
    <div class="doc-item-left">
      <div class="doc-icon">
        <i class="{{ $extIcon }}"></i>
      </div>
      <div class="doc-body">
        <div class="doc-name">{{ $doc->nom }}</div>
        <div class="doc-meta">
          <span class="doc-type-badge">{{ ucfirst($doc->type) }}</span>
          @if($tailleFmt)<span>{{ $tailleFmt }}</span>@endif
          <span>{{ $doc->created_at->format('d/m/Y') }}</span>
        </div>
      </div>
    </div>
    <div class="doc-item-right">
      <span class="pill {{ $pillClass }}">
        <i class="{{ $statIcn }}"></i> {{ $pillLabel }}
      </span>
      <button class="doc-delete-btn"
              wire:click.stop="deleteDoc({{ $doc->id }})"
              onclick="event.stopPropagation()"
              title="Supprimer">
        <i class="ri-delete-bin-line"></i>
      </button>
    </div>
  </div>

  @empty
  <div class="doc-empty">
    <div class="doc-empty-icon"><i class="ri-file-list-3-line"></i></div>
    <div class="doc-empty-title">Aucun document</div>
    <div class="doc-empty-sub">Soumettez vos pièces justificatives pour compléter votre dossier.</div>
    <button class="btn-add-doc" wire:click="openAdd" style="margin-top:16px">
      <i class="ri-upload-2-line"></i> Ajouter un document
    </button>
  </div>
  @endforelse

  {{-- Note --}}
  @if($documents->count() > 0)
  <div class="doc-info-note">
    <i class="ri-information-line"></i>
    Les documents soumis sont vérifiés manuellement par un administrateur. La validation peut prendre 24–48h.
  </div>
  @endif

  <div style="height:24px"></div>

</main>


{{-- ══ MODAL UPLOAD DOCUMENT ═══════════════════════════════ --}}
<div class="pwa-modal-overlay" id="add-doc-overlay" wire:ignore.self>
  <div class="pwa-modal" wire:click.stop>
    <div class="pwa-modal-header">
      <div class="pwa-modal-drag"></div>
      <div class="pwa-modal-title-row">
        <div class="pwa-modal-title"><i class="ri-upload-2-line"></i> Ajouter un document</div>
        <button class="pwa-modal-close" wire:click="closeAdd"><i class="ri-close-line"></i></button>
      </div>
    </div>
    <div class="pwa-modal-body">

      <div class="f-group">
        <label class="f-label">Type de document <span class="req">*</span></label>
        <div class="f-input-wrap">
          <i class="ri-tag-line f-input-icon"></i>
          <select class="f-input {{ $errorType ? 'f-input-err' : '' }}" wire:model="typeDoc">
            <option value="">— Choisir —</option>
            @foreach($typesDoc as $val => $label)
              <option value="{{ $val }}">{{ $label }}</option>
            @endforeach
          </select>
        </div>
        @if($errorType)<div class="f-err">{{ $errorType }}</div>@endif
      </div>

      <div class="f-group">
        <label class="f-label">Fichier <span class="req">*</span></label>
        <div class="doc-upload-zone {{ $errorFichier ? 'upload-err' : '' }}"
             onclick="document.getElementById('doc-file-input').click()">
          @if($fichier)
          <div class="doc-upload-preview">
            <i class="ri-file-check-line" style="color:#0ab39c;font-size:28px"></i>
            <div class="doc-upload-filename">{{ $fichier->getClientOriginalName() }}</div>
            <div class="doc-upload-size">{{ round($fichier->getSize() / 1024) }} Ko</div>
          </div>
          @else
          <div class="doc-upload-placeholder">
            <i class="ri-upload-cloud-2-line"></i>
            <div class="doc-upload-text">Appuyez pour choisir un fichier</div>
            <div class="doc-upload-hint">PDF, JPG, PNG · max 5 Mo</div>
          </div>
          @endif
          <input type="file" id="doc-file-input" wire:model="fichier"
                 accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx"
                 style="display:none"/>
        </div>
        @if($errorFichier)<div class="f-err">{{ $errorFichier }}</div>@endif
        <div wire:loading wire:target="fichier">
          <div class="doc-upload-loading"><div class="spinner-sm"></div> Chargement…</div>
        </div>
      </div>

      <div class="recla-info-note">
        <i class="ri-information-line"></i>
        Votre document sera vérifié par un administrateur dans les 24–48h.
      </div>

    </div>
    <div class="pwa-modal-footer">
      <button class="btn-outline" style="height:46px;font-size:14px" wire:click="closeAdd">
        <i class="ri-close-line"></i> Annuler
      </button>
      <button class="btn-main" style="height:46px;font-size:14px"
              wire:click="saveDoc" wire:loading.attr="disabled">
        <span wire:loading wire:target="saveDoc"><div class="spinner"></div></span>
        <span wire:loading.remove wire:target="saveDoc">
          <i class="ri-upload-2-line"></i> Soumettre
        </span>
      </button>
    </div>
  </div>
</div>


{{-- ══ MODAL DÉTAIL DOCUMENT ════════════════════════════════ --}}
<div class="pwa-modal-overlay" id="detail-doc-overlay" wire:ignore.self>
  <div class="pwa-modal pwa-modal-sm" wire:click.stop>
    <div class="pwa-modal-header">
      <div class="pwa-modal-drag"></div>
      <div class="pwa-modal-title-row">
        <div class="pwa-modal-title"><i class="ri-file-info-line"></i> Détail du document</div>
        <button class="pwa-modal-close" wire:click="closeDetail"><i class="ri-close-line"></i></button>
      </div>
    </div>

    @if($detailDoc)
    @php
      [$pillClass, $pillLabel] = match($detailDoc->status) {
          'valide'     => ['pill-ok',     'Validé'],
          'en_attente' => ['pill-warn',   'En attente de validation'],
          'rejete'     => ['pill-danger', 'Rejeté'],
          default      => ['pill-info',   '—'],
      };
      $extIcon = match(strtolower($detailDoc->extension ?? '')) {
          'pdf'  => 'ri-file-pdf-line',
          'jpg','jpeg','png','webp' => 'ri-image-line',
          'doc','docx' => 'ri-file-word-line',
          default => 'ri-file-line',
      };
    @endphp
    <div class="pwa-modal-body">

      <div class="doc-detail-icon">
        <i class="{{ $extIcon }}"></i>
      </div>

      <div class="doc-detail-name">{{ $detailDoc->nom }}</div>
      <div style="text-align:center;margin-bottom:16px">
        <span class="pill {{ $pillClass }}">{{ $pillLabel }}</span>
      </div>

      <div class="doc-detail-grid">
        <div class="doc-detail-item">
          <div class="doc-di-label">Type</div>
          <div class="doc-di-val">{{ ucfirst($detailDoc->type) }}</div>
        </div>
        <div class="doc-detail-item">
          <div class="doc-di-label">Format</div>
          <div class="doc-di-val">{{ strtoupper($detailDoc->extension ?? '—') }}</div>
        </div>
        <div class="doc-detail-item">
          <div class="doc-di-label">Taille</div>
          <div class="doc-di-val">
            {{ $detailDoc->taille ? round($detailDoc->taille / 1024).' Ko' : '—' }}
          </div>
        </div>
        <div class="doc-detail-item">
          <div class="doc-di-label">Soumis le</div>
          <div class="doc-di-val">{{ $detailDoc->created_at->format('d/m/Y') }}</div>
        </div>
      </div>

      @if($detailDoc->status === 'rejete' && $detailDoc->motif_rejet)
      <div style="background:rgba(240,101,72,.06);border-left:3px solid #f06548;border-radius:0 8px 8px 0;padding:10px 14px;margin-top:12px">
        <div style="font-size:11px;font-weight:700;color:#f06548;margin-bottom:4px">MOTIF DU REJET</div>
        <div style="font-size:13px;color:var(--text)">{{ $detailDoc->motif_rejet }}</div>
      </div>
      @endif

      @if($detailDoc->status === 'en_attente')
      <div style="font-size:13px;color:var(--muted);text-align:center;margin-top:12px;font-style:italic">
        <i class="ri-time-line"></i> En cours de vérification par l'administration…
      </div>
      @endif

    </div>
    <div class="pwa-modal-footer">
      @if($detailDoc->chemin)
      <a href="{{ asset('storage/'.$detailDoc->chemin) }}" target="_blank"
         class="btn-outline" style="height:46px;font-size:14px;display:flex;align-items:center;gap:6px">
        <i class="ri-eye-line"></i> Voir
      </a>
      @endif
      <button class="btn-main" style="height:46px;font-size:14px;flex:1" wire:click="closeDetail">
        <i class="ri-check-line"></i> Fermer
      </button>
    </div>
    @endif
  </div>
</div>

</div>{{-- /root Livewire --}}


@push('scripts')
<script>
window.addEventListener('OpenAddDoc',     () => { document.getElementById('add-doc-overlay')?.classList.add('open');     document.body.style.overflow = 'hidden'; });
window.addEventListener('closeAddDoc',    () => { document.getElementById('add-doc-overlay')?.classList.remove('open');  document.body.style.overflow = ''; });
window.addEventListener('OpenDetailDoc',  () => { document.getElementById('detail-doc-overlay')?.classList.add('open');  document.body.style.overflow = 'hidden'; });
window.addEventListener('closeDetailDoc', () => {
  document.getElementById('detail-doc-overlay')?.classList.remove('open');
  document.body.style.overflow = '';
  @this.set('detailId', null);
});

Livewire.on('modalShowmessageToast', (payload) => {
  const data = Array.isArray(payload) ? payload[0] : payload;
  if (typeof Swal !== 'undefined') {
    Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true })
        .fire({ icon: data.type, title: data.title });
  }
});
</script>

@push('styles')
<link href="{{ asset('frontend/css/document.css') }}" rel="stylesheet" type="text/css" />
<style>
  .spinner { width:18px;height:18px;border:2.5px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:_spin .7s linear infinite;display:inline-block; }
  .spinner-sm { width:14px;height:14px;border:2px solid rgba(64,81,137,.2);border-top-color:#405189;border-radius:50%;animation:_spin .7s linear infinite;display:inline-block; }
  @keyframes _spin { to { transform:rotate(360deg); } }
  .f-err { font-size:12px;color:#f06548;margin-top:4px;font-weight:600; }
  .f-input-err { border-color:#f06548 !important; }
</style>
@endpush

@endpush