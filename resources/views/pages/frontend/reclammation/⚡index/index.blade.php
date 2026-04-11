<div>
<main class="page-content">

  {{-- ══ EN-TÊTE ══════════════════════════════════════════ --}}
  <div class="recla-page-header">
    <div>
      <div class="page-title">Mes Réclamations</div>
      <div class="page-sub" style="margin-bottom:0">Signalez un problème lié à vos cotisations ou à votre compte.</div>
    </div>
    <button class="btn-add-recla" wire:click="openAdd">
      <i class="ri-add-line"></i> Nouvelle
    </button>
  </div>

  {{-- ══ FILTRES ═════════════════════════════════════════ --}}
  <div class="recla-filters">
    <button class="recla-filter active" data-filter="tous" onclick="filterRecla(this)">
      <i class="ri-list-check"></i> Toutes
    </button>
    <button class="recla-filter" data-filter="en_cours" onclick="filterRecla(this)">
      <i class="ri-time-line"></i> En cours
    </button>
    <button class="recla-filter" data-filter="resolu" onclick="filterRecla(this)">
      <i class="ri-checkbox-circle-line"></i> Résolus
    </button>
    <button class="recla-filter" data-filter="rejete" onclick="filterRecla(this)">
      <i class="ri-close-circle-line"></i> Rejetés
    </button>
  </div>

  {{-- ══ LISTE ═══════════════════════════════════════════ --}}
  <div class="recla-list">

    @forelse($reclammations as $r)
    @php
      $statutJs = match($r->status) {
          'ouverte', 'en_cours' => 'en_cours',
          'resolu'              => 'resolu',
          'rejete'              => 'rejete',
          default               => 'en_cours',
      };
      [$iconClass, $iconBg, $iconColor, $pillClass, $pillLabel] = match($r->status) {
          'ouverte','en_cours' => ['ri-flag-line',         'rgba(41,156,219,.12)', '#299cdb', 'pill-info',   'En cours'],
          'resolu'             => ['ri-check-double-line', 'rgba(10,179,156,.10)', '#0ab39c', 'pill-ok',     'Résolu'],
          'rejete'             => ['ri-close-circle-line', 'rgba(240,101,72,.10)', '#f06548', 'pill-danger', 'Rejeté'],
          default              => ['ri-flag-line',         'rgba(41,156,219,.12)', '#299cdb', 'pill-info',   'En cours'],
      };
      $cotLabel = $r->cotisation
          ? ($r->cotisation->typeCotisation?->libelle ?? '—')
            . ($r->cotisation->mois && $r->cotisation->annee
                ? ' — ' . \Carbon\Carbon::create($r->cotisation->annee, $r->cotisation->mois)->translatedFormat('F Y')
                : '')
          : 'Sans cotisation liée';

      $dernierHisto = $r->historiqueReclammation->last();
    @endphp

    <div class="recla-item"
         data-statut="{{ $statutJs }}"
         wire:click="openDetail({{ $r->id }})"
         wire:key="recla-{{ $r->id }}"
         style="cursor:pointer">
      <div class="ri-left">
        <div class="ri-icon" style="background:{{ $iconBg }};color:{{ $iconColor }}">
          <i class="{{ $iconClass }}"></i>
        </div>
      </div>
      <div class="ri-body">
        <div class="ri-header">
          <div class="ri-title">{{ $r->sujet }}</div>
          <span class="pill {{ $pillClass }}">{{ $pillLabel }}</span>
        </div>
        <div class="ri-cot">
          <i class="ri-calendar-line"></i> {{ $cotLabel }}
        </div>
        <div class="ri-msg">{{ \Str::limit($r->description, 100) }}</div>
        <div class="ri-date">
          <i class="ri-time-line"></i> Soumise le {{ $r->created_at->format('d/m/Y') }}
          @if(in_array($r->status, ['resolu','rejete']) && $dernierHisto)
            · {{ $r->status === 'resolu' ? 'Résolue' : 'Rejetée' }} le {{ $dernierHisto->created_at->format('d/m/Y') }}
          @endif
        </div>
      </div>
    </div>

    @empty
    <div class="empty-state">
      <i class="ri-flag-line"></i>
      <div class="es-title">Aucune réclamation</div>
      <div class="es-sub">Vous n'avez pas encore soumis de réclamation.</div>
    </div>
    @endforelse

    <div class="empty-state" id="recla-empty" style="display:none">
      <i class="ri-flag-line"></i>
      <div class="es-title">Aucune réclamation</div>
      <div class="es-sub">Aucune réclamation ne correspond à ce filtre.</div>
    </div>

  </div>

  <div style="height:24px"></div>

</main>


{{-- ══ MODAL NOUVELLE RÉCLAMATION ═════════════════════════ --}}
<div class="pwa-modal-overlay" id="add-overlay" wire:ignore.self>
  <div class="pwa-modal" wire:click.stop>
    <div class="pwa-modal-header">
      <div class="pwa-modal-drag"></div>
      <div class="pwa-modal-title-row">
        <div class="pwa-modal-title"><i class="ri-flag-line"></i> Nouvelle réclamation</div>
        <button class="pwa-modal-close" wire:click="closeAdd"><i class="ri-close-line"></i></button>
      </div>
    </div>
    <div class="pwa-modal-body">

      <div class="f-group">
        <label class="f-label">Cotisation concernée <span class="opt">(optionnel)</span></label>
        <div class="f-input-wrap">
          <i class="ri-calendar-line f-input-icon"></i>
          <select class="f-input" wire:model="addCotisationId">
            <option value="">— Aucune cotisation spécifique —</option>
            @foreach($cotisations as $cot)
              <option value="{{ $cot['id'] }}">{{ $cot['label'] }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="f-group">
        <label class="f-label">Titre <span class="req">*</span></label>
        <div class="f-input-wrap">
          <i class="ri-text f-input-icon"></i>
          <input type="text"
                 class="f-input {{ $errorTitre ? 'f-input-err' : '' }}"
                 wire:model.lazy="addTitre"
                 placeholder="ex : Paiement non enregistré"/>
        </div>
        @if($errorTitre)<div class="f-err">{{ $errorTitre }}</div>@endif
      </div>

      <div class="f-group">
        <label class="f-label">Description du problème <span class="req">*</span></label>
        <textarea class="f-input {{ $errorMessage ? 'f-input-err' : '' }}"
                  wire:model.lazy="addMessage"
                  style="min-height:100px;height:auto;padding:10px 14px;resize:vertical"
                  placeholder="Décrivez votre problème en détail…"></textarea>
        @if($errorMessage)<div class="f-err">{{ $errorMessage }}</div>@endif
      </div>

      <div class="recla-info-note">
        <i class="ri-information-line"></i>
        Votre réclamation sera traitée par un administrateur dans les plus brefs délais.
        Vous recevrez une notification dès qu'une réponse sera disponible.
      </div>

    </div>
    <div class="pwa-modal-footer">
      <button class="btn-outline" style="height:46px;font-size:14px" wire:click="closeAdd">
        <i class="ri-close-line"></i> Annuler
      </button>
      <button class="btn-main" style="height:46px;font-size:14px"
              wire:click="submitRecla" wire:loading.attr="disabled">
        <span wire:loading wire:target="submitRecla"><div class="spinner"></div></span>
        <span wire:loading.remove wire:target="submitRecla">
          <i class="ri-send-plane-line"></i> Envoyer
        </span>
      </button>
    </div>
  </div>
</div>


{{-- ══ MODAL DÉTAIL RÉCLAMATION ════════════════════════════ --}}
<div class="pwa-modal-overlay" id="detail-overlay" wire:ignore.self>
  <div class="pwa-modal" wire:click.stop>
    <div class="pwa-modal-header">
      <div class="pwa-modal-drag"></div>
      <div class="pwa-modal-title-row">
        <div class="pwa-modal-title"><i class="ri-eye-line"></i> Détail</div>
        <button class="pwa-modal-close" wire:click="closeDetail"><i class="ri-close-line"></i></button>
      </div>
    </div>

    @if($detailRecla)
    @php
      $dr = $detailRecla;
      [$pillClass, $pillLabel] = match($dr->status) {
          'ouverte','en_cours' => ['pill-info',   'En cours'],
          'resolu'             => ['pill-ok',     'Résolu'],
          'rejete'             => ['pill-danger', 'Rejeté'],
          default              => ['pill-info',   'En cours'],
      };
      $cotLabel = $dr->cotisation
          ? ($dr->cotisation->typeCotisation?->libelle ?? '—')
            . ($dr->cotisation->mois && $dr->cotisation->annee
                ? ' — ' . \Carbon\Carbon::create($dr->cotisation->annee, $dr->cotisation->mois)->translatedFormat('F Y')
                : '')
          : null;
      $reponseAdmin = $dr->historiqueReclammation
          ->where('description', '!=', 'Réclamation créée par le fidèle.')
          ->last();
    @endphp

    <div class="pwa-modal-body">

      <div class="recla-detail-meta">
        <span class="recla-detail-id">REC-{{ str_pad($dr->id, 3, '0', STR_PAD_LEFT) }}</span>
        <span class="pill {{ $pillClass }}">{{ $pillLabel }}</span>
      </div>

      <div class="recla-detail-title">{{ $dr->sujet }}</div>

      @if($cotLabel)
      <div class="recla-detail-cot">
        <i class="ri-calendar-line"></i>
        <span>{{ $cotLabel }}</span>
      </div>
      @endif

      <div class="recla-detail-date">
        <i class="ri-time-line"></i> Soumise le {{ $dr->created_at->format('d/m/Y à H:i') }}
      </div>

      <div class="recla-section-label">Votre message</div>
      <div class="recla-detail-msg">{{ $dr->description }}</div>

      @if($reponseAdmin)
      <div class="recla-section-label">Réponse de l'administration</div>
      <div class="recla-reponse-box">{{ $reponseAdmin->description }}</div>
      @elseif(in_array($dr->status, ['ouverte', 'en_cours']))
      <div style="font-size:13px;color:var(--muted);font-style:italic;margin-top:16px;text-align:center">
        <i class="ri-time-line"></i> En attente de réponse de l'administration…
      </div>
      @endif

    </div>

    <div class="pwa-modal-footer">
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
/* ── Pattern dispatch → window.addEventListener ── */
window.addEventListener('OpenAddRecla',     () => { document.getElementById('add-overlay')?.classList.add('open');     document.body.style.overflow = 'hidden'; });
window.addEventListener('closeAddRecla',    () => { document.getElementById('add-overlay')?.classList.remove('open');  document.body.style.overflow = ''; });
window.addEventListener('OpenDetailRecla',  () => { document.getElementById('detail-overlay')?.classList.add('open');  document.body.style.overflow = 'hidden'; });
window.addEventListener('closeDetailRecla', () => {
  document.getElementById('detail-overlay')?.classList.remove('open');
  document.body.style.overflow = '';
  @this.set('detailId', null);
});

/* ── Filtre ── */
function filterRecla(btn) {
  document.querySelectorAll('.recla-filter').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  const f = btn.dataset.filter;
  const items = document.querySelectorAll('.recla-item');
  const empty = document.getElementById('recla-empty');
  let visible = 0;
  items.forEach(item => {
    const show = f === 'tous' || item.dataset.statut === f;
    item.style.display = show ? '' : 'none';
    if (show) visible++;
  });
  if (empty) empty.style.display = visible === 0 ? 'flex' : 'none';
}

/* ── Toast ── */
Livewire.on('modalShowmessageToast', (payload) => {
  const data = Array.isArray(payload) ? payload[0] : payload;
  if (typeof Swal !== 'undefined') {
    Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true })
        .fire({ icon: data.type, title: data.title });
  }
});
</script>
@endpush


@push('styles')
<style>
  .f-err { font-size:12px; color:#f06548; margin-top:4px; font-weight:600; }
  .f-input-err { border-color:#f06548 !important; }
  .spinner { width:18px;height:18px;border:2.5px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:_spin .7s linear infinite;display:inline-block; }
  @keyframes _spin { to { transform:rotate(360deg); } }
</style>
@endpush
