<div>
<main class="page-content">

  {{-- ══ RÉSUMÉ RAPIDE ══════════════════════════════════════ --}}
  <div class="summary-strip">
    <div class="sum-item">
      <div class="sum-val" style="color:#f06548">{{ $summary['retard'] }}</div>
      <div class="sum-label">En retard</div>
    </div>
    <div class="sum-divider"></div>
    <div class="sum-item">
      <div class="sum-val" style="color:#0ab39c">{{ $summary['ajour'] }}</div>
      <div class="sum-label">À jour</div>
    </div>
    <div class="sum-divider"></div>
    <div class="sum-item">
      <div class="sum-val" style="color:#f7b84b">{{ $summary['partiel'] }}</div>
      <div class="sum-label">Partiel</div>
    </div>
    <div class="sum-divider"></div>
    <div class="sum-item">
      <div class="sum-val">{{ $summary['total'] }}</div>
      <div class="sum-label">Total</div>
    </div>
  </div>

  {{-- ══ FILTRES STATUT ═════════════════════════════════════ --}}
  <div class="status-tabs">
    <button class="stab active" data-statut="tous" onclick="filterCot(this)">Tous</button>
    <button class="stab" data-statut="retard" onclick="filterCot(this)">
      <i class="ri-time-line"></i> En retard
      @if($summary['retard'] > 0)
        <span class="stab-count danger">{{ $summary['retard'] }}</span>
      @endif
    </button>
    <button class="stab" data-statut="ajour" onclick="filterCot(this)">
      <i class="ri-checkbox-circle-line"></i> À jour
    </button>
    <button class="stab" data-statut="partiel" onclick="filterCot(this)">
      <i class="ri-error-warning-line"></i> Partiel
    </button>
  </div>

  {{-- ══ LISTE DES COTISATIONS ══════════════════════════════ --}}
  @forelse($grouped as $moisLabel => $items)

  <div class="cot-month-label">{{ $moisLabel }}</div>

  @foreach($items as $cot)
  @php
    $tc     = $cot->typeCotisation;
    $statut = $cot->statut;

    $statutJs = match($statut) {
        'a_jour'    => 'ajour',
        'partiel'   => 'partiel',
        'en_retard' => 'retard',
        default     => 'retard',
    };

    if ($statut === 'en_retard') {
        $displayIcon  = 'ri-time-line';
        $displayBg    = 'rgba(240,101,72,.10)';
        $displayColor = '#f06548';
    } elseif ($statut === 'partiel') {
        $displayIcon  = 'ri-error-warning-line';
        $displayBg    = 'rgba(247,184,75,.12)';
        $displayColor = '#f7b84b';
    } else {
        [$displayIcon, $displayBg, $displayColor] = match($tc?->type) {
            'mensuel'     => ['ri-calendar-check-line', 'rgba(64,81,137,.10)',   '#405189'],
            'jour_precis' => ['ri-hand-heart-line',     'rgba(212,168,67,.12)',  '#d4a843'],
            'ordinaire'   => ['ri-gift-line',           'rgba(10,179,156,.10)',  '#0ab39c'],
            'ramadan'     => ['ri-moon-line',            'rgba(41,156,219,.12)', '#299cdb'],
            default       => ['ri-file-list-3-line',    'rgba(135,138,153,.10)','#878a99'],
        };
    }

    $pct = 0;
    if ($cot->montant_du > 0) $pct = min(round($cot->montant_paye / $cot->montant_du * 100), 100);
    elseif ($cot->montant_paye > 0) $pct = 100;

    $barColor  = $statut === 'a_jour' ? '#0ab39c' : ($statut === 'partiel' ? '#f7b84b' : '#f06548');
    $modeLabel = match($cot->mode_paiement) {
        'mobile_money' => 'Mobile Money',
        'espece'       => 'Espèces',
        'virement'     => 'Virement',
        default        => '—',
    };
    $subLine = match(true) {
        $statut === 'a_jour' && $cot->validated_at !== null
            => 'Payé le ' . $cot->validated_at->format('d/m/Y') . ($cot->mode_paiement ? ' · ' . $modeLabel : ''),
        $statut === 'partiel'
            => 'Payé partiellement · ' . $cot->created_at->format('d/m/Y'),
        $tc?->type === 'mensuel'
            => 'Engagement : ' . ($cot->montant_du ? number_format($cot->montant_du, 0, ',', ' ') . ' FCFA/mois' : '—'),
        default
            => 'Don ponctuel · ' . $cot->created_at->format('d/m/Y'),
    };
  @endphp

  {{--
    IMPORTANT : wire:click sur la div principale.
    On NE met PAS wire:ignore ici car les items doivent
    re-render quand Livewire met à jour la liste.
    L'overlay est en dehors du @forelse donc il ne subit
    pas de re-render partiel qui effacerait la classe CSS.
  --}}
  <div class="cot-item"
       data-statut="{{ $statutJs }}"
       wire:click="showDetail({{ $cot->id }})"
       wire:key="cot-{{ $cot->id }}">
    <div class="cot-left">
      <div class="cot-icon" style="background:{{ $displayBg }};color:{{ $displayColor }}">
        <i class="{{ $displayIcon }}"></i>
      </div>
      <div class="cot-body">
        <div class="cot-name">{{ $tc?->libelle ?? '—' }}</div>
        <div class="cot-sub">{{ $subLine }}</div>
        <div class="cot-progress">
          <div class="cot-fill" style="width:{{ $pct }}%;background:{{ $barColor }}"></div>
        </div>
      </div>
    </div>
    <div class="cot-right">
      @if($statut === 'partiel')
        <div class="cot-amount">{{ number_format($cot->montant_paye, 0, ',', ' ') }}</div>
        <div class="cot-unit">/ {{ number_format($cot->montant_du, 0, ',', ' ') }} FCFA</div>
        <span class="pill pill-warn" style="font-size:10px">Partiel</span>
      @elseif($statut === 'a_jour')
        <div class="cot-amount">{{ number_format($cot->montant_paye, 0, ',', ' ') }}</div>
        <div class="cot-unit">FCFA</div>
        <span class="pill pill-ok" style="font-size:10px">{{ $cot->montant_du ? 'À jour' : 'Reçu' }}</span>
      @else
        <div class="cot-amount">{{ number_format($cot->montant_du ?? 0, 0, ',', ' ') }}</div>
        <div class="cot-unit">FCFA</div>
        <span class="pill pill-danger" style="font-size:10px">En retard</span>
      @endif
    </div>
  </div>

  @endforeach
  @empty
  <div class="empty-state">
    <i class="ri-inbox-line"></i>
    <div class="es-title">Aucune cotisation</div>
    <div class="es-sub">Vous n'avez pas encore de cotisation enregistrée.</div>
  </div>
  @endforelse

  <div class="empty-state" id="cot-empty" style="display:none">
    <i class="ri-inbox-line"></i>
    <div class="es-title">Aucune cotisation</div>
    <div class="es-sub">Aucune cotisation ne correspond à ce filtre.</div>
  </div>

  <div style="height:24px"></div>

</main>


{{-- ══════════════════════════════════════════════════════════
     MODAL DÉTAIL COTISATION
     ──────────────────────────────────────────────────────────
     LA CLÉ DU FIX : la classe "open" est rendue directement
     par Blade via $detailId. Livewire re-render → le HTML
     contient déjà "open" → le CSS l'affiche immédiatement.
     Zéro JS pour l'ouverture/fermeture.
══════════════════════════════════════════════════════════ --}}
<div class="cot-modal-overlay" id="cot-detail-overlay" wire:ignore.self>

  <div class="cot-modal" wire:click.stop>

    @if($detailCotisation)
    @php
      $dc     = $detailCotisation;
      $tc     = $dc->typeCotisation;
      $statut = $dc->statut;

      $headerBg = match($statut) {
          'a_jour'    => 'linear-gradient(135deg,#089383,#0ab39c)',
          'partiel'   => 'linear-gradient(135deg,#c07a10,#f7b84b)',
          'en_retard' => 'linear-gradient(135deg,#c0341a,#f06548)',
          default     => 'linear-gradient(135deg,#2d3a63,#405189)',
      };
      $headerIcon = match($tc?->type) {
          'mensuel'     => 'ri-calendar-check-line',
          'jour_precis' => 'ri-hand-heart-line',
          'ordinaire'   => 'ri-gift-line',
          'ramadan'     => 'ri-moon-line',
          default       => 'ri-file-list-3-line',
      };
      $progColor = match($statut) {
          'a_jour'  => '#0ab39c',
          'partiel' => '#f7b84b',
          default   => '#f06548',
      };
      $pctDet = 0;
      if ($dc->montant_du > 0) $pctDet = min(round($dc->montant_paye / $dc->montant_du * 100), 100);
      elseif ($dc->montant_paye > 0) $pctDet = 100;

      $modeDetLabel = match($dc->mode_paiement) {
          'mobile_money' => 'Mobile Money',
          'espece'       => 'Espèces',
          'virement'     => 'Virement',
          default        => '—',
      };
      $periodeDet = ($dc->mois && $dc->annee)
          ? \Carbon\Carbon::create($dc->annee, $dc->mois)->translatedFormat('F Y')
          : 'Ponctuel';

      $pillHtml = match($statut) {
          'a_jour'    => '<span class="pill" style="background:rgba(255,255,255,.2);color:#fff;font-size:11px"><i class="ri-checkbox-circle-line"></i> À jour</span>',
          'partiel'   => '<span class="pill" style="background:rgba(255,255,255,.2);color:#fff;font-size:11px"><i class="ri-error-warning-line"></i> Partiel</span>',
          default     => '<span class="pill" style="background:rgba(255,255,255,.2);color:#fff;font-size:11px"><i class="ri-time-line"></i> En retard</span>',
      };
    @endphp

    {{-- Header --}}
    <div class="cot-modal-header" id="cot-modal-header" style="background:{{ $headerBg }}">
      <div class="cmh-drag"></div>
      <div class="cmh-top">
        <div class="cmh-left">
          <div class="cmh-icon" id="cmh-icon" style="background:rgba(255,255,255,.18)">
            <i class="{{ $headerIcon }}" style="color:#fff"></i>
          </div>
          <div>
            <div class="cmh-type">{{ $tc?->libelle ?? '—' }}</div>
            <div class="cmh-period">{{ $periodeDet }}</div>
          </div>
        </div>
        <button class="cmh-close" wire:click="closeDetail">
          <i class="ri-close-line"></i>
        </button>
      </div>
      <div class="cmh-amount-row">
        <div class="cmh-amount">
          {{ $dc->montant_du ? number_format($dc->montant_du, 0, ',', ' ').' FCFA' : number_format($dc->montant_paye, 0, ',', ' ').' FCFA' }}
        </div>
        <div>{!! $pillHtml !!}</div>
      </div>
    </div>

    {{-- Corps scrollable --}}
    <div class="cot-modal-body">

      {{-- Grille infos --}}
      <div class="cot-det-grid">
        <div class="cot-det-cell">
          <div class="cot-det-label"><i class="ri-error-warning-line"></i> Montant dû</div>
          <div class="cot-det-val" style="{{ $statut === 'en_retard' ? 'color:#f06548' : '' }}">
            {{ $dc->montant_du ? number_format($dc->montant_du, 0, ',', ' ').' FCFA' : '—' }}
          </div>
        </div>
        <div class="cot-det-cell">
          <div class="cot-det-label"><i class="ri-checkbox-circle-line"></i> Montant payé</div>
          <div class="cot-det-val" style="{{ $dc->montant_paye > 0 ? 'color:#0ab39c' : 'color:var(--muted)' }}">
            {{ number_format($dc->montant_paye, 0, ',', ' ') }} FCFA
          </div>
        </div>
        <div class="cot-det-cell">
          <div class="cot-det-label"><i class="ri-refund-2-line"></i> Restant</div>
          <div class="cot-det-val" style="{{ $dc->montant_restant > 0 ? 'color:#f06548' : 'color:var(--muted)' }}">
            {{ $dc->montant_restant > 0 ? number_format($dc->montant_restant, 0, ',', ' ').' FCFA' : '—' }}
          </div>
        </div>
        <div class="cot-det-cell">
          <div class="cot-det-label"><i class="ri-smartphone-line"></i> Mode paiement</div>
          <div class="cot-det-val">{{ $modeDetLabel }}</div>
        </div>
        <div class="cot-det-cell">
          <div class="cot-det-label"><i class="ri-money-cny-circle-line"></i> Engagement</div>
          <div class="cot-det-val" style="color:var(--p)">
            {{ $dc->montant_du ? number_format($dc->montant_du, 0, ',', ' ').' FCFA/mois' : 'Sans engagement' }}
          </div>
        </div>
        <div class="cot-det-cell">
          <div class="cot-det-label"><i class="ri-calendar-line"></i> Créée le</div>
          <div class="cot-det-val">{{ $dc->created_at->format('d/m/Y') }}</div>
        </div>
      </div>

      {{-- Barre progression --}}
      <div class="cot-det-prog-wrap">
        <div class="cot-det-prog-header">
          <span class="cot-det-prog-label"><i class="ri-bar-chart-line"></i> Progression</span>
          <span class="cot-det-prog-pct" id="det-pct-label" style="color:{{ $progColor }}">{{ $pctDet }}%</span>
        </div>
        <div class="cot-det-prog-track">
          <div class="cot-det-prog-fill" id="det-prog-fill" style="width:{{ $pctDet }}%;background:{{ $progColor }}"></div>
        </div>
        <div class="cot-det-prog-footer">
          <span id="det-prog-paye-lbl">{{ number_format($dc->montant_paye, 0, ',', ' ') }} FCFA payé</span>
          <span id="det-prog-du-lbl">{{ $dc->montant_du ? number_format($dc->montant_du, 0, ',', ' ').' FCFA dû' : '—' }}</span>
        </div>
      </div>

      {{-- Paiements liés --}}
      <div class="cot-det-section-title">
        <i class="ri-bank-card-line"></i> Paiements liés
      </div>
      <div class="cot-det-pay-list" id="det-pay-list">
        @forelse($detailPaiements as $p)
        <div class="cot-pay-row">
          <div class="cot-pay-icon" style="background:{{ $p['iconBg'] }};color:{{ $p['iconColor'] }}">
            <i class="{{ $p['icon'] }}"></i>
          </div>
          <div class="cot-pay-body">
            <div class="cot-pay-title">{{ $p['title'] }}</div>
            <div class="cot-pay-date">{{ $p['date'] }}</div>
          </div>
          <div class="cot-pay-right">
            <span class="cot-pay-amount" style="color:{{ $p['montantColor'] }}">{{ $p['montant'] }}</span>
          </div>
        </div>
        @empty
        <div class="cot-pay-empty" id="det-pay-empty">
          <i class="ri-inbox-line"></i> Aucun paiement enregistré
        </div>
        @endforelse
      </div>

    </div>{{-- /cot-modal-body --}}

    {{-- Footer --}}
    <div class="cot-modal-footer">
      <button class="cot-footer-recla" wire:click="openRecla({{ $dc->id }})">
        <i class="ri-flag-line"></i> Faire une réclamation
      </button>
      @if($statut !== 'a_jour')
      <button class="cot-footer-pay"
              wire:click="closeDetail"
              onclick="window.location.href='{{ route('customer.ajout-cotisations') }}'">
        <i class="ri-add-circle-line"></i> Payer
      </button>
      @endif
    </div>

    @endif
  </div>{{-- /cot-modal --}}
</div>{{-- /cot-modal-overlay détail --}}


{{-- ══════════════════════════════════════════════════════════
     MODAL RÉCLAMATION
     Même principe : classe "open" via $showRecla Blade
══════════════════════════════════════════════════════════ --}}
<div id="cot-recla-overlay" @if($showRecla ) wire:ignore.self @endif class="cot-modal-overlay {{ $showRecla ? 'open' : '' }}"
      >

  <div class="cot-modal cot-modal-sm" wire:click.stop>

    <div class="cot-modal-header" style="background:linear-gradient(135deg,#e8a53a,#f7b84b)">
      <div class="cmh-drag"></div>
      <div class="cmh-top">
        <div class="cmh-left">
          <div class="cmh-icon" style="background:rgba(255,255,255,.2);color:#fff">
            <i class="ri-flag-line"></i>
          </div>
          <div>
            <div class="cmh-type" style="color:rgba(255,255,255,.85);font-size:11px">RÉCLAMATION</div>
            <div class="cmh-period" style="color:#fff;font-size:15px;font-weight:900">Signaler un problème</div>
          </div>
        </div>
        <button class="cmh-close" wire:click="closeRecla">
          <i class="ri-close-line"></i>
        </button>
      </div>
    </div>

    <div class="cot-modal-body">

      <div class="f-group">
        <label class="f-label-sm">Cotisation concernée</label>
        <input type="text" class="f-input-sm" value="{{ $reclaLabel }}" readonly/>
      </div>

      <div class="f-group" wire:ignore>
        <label class="f-label-sm">Titre <span style="color:#f06548">*</span></label>
        <div class="f-input-wrap-sm">
          <i class="ri-text f-ico-sm"></i>
          <input type="text" 
                 class="f-input-sm "
                 wire:model.defer="reclaTitle"
                 placeholder="ex : Paiement non enregistré"/>
        </div>
        @if($errorReclaTitle)
        <div style="font-size:12px;color:#f06548;margin-top:4px;font-weight:600">
          {{ $errorReclaTitle }}
        </div>
        @endif
      </div>

      <div class="f-group" wire:ignore>
        <label class="f-label-sm">Message <span style="color:#f06548">*</span></label>
        <textarea class="f-input-sm f-textarea-sm {{ $errorReclaMessage ? 'err' : '' }}"
                  wire:model.lazy="reclaMessage"
                  placeholder="Décrivez le problème en détail…"></textarea>
        @if($errorReclaMessage)
        <div style="font-size:12px;color:#f06548;margin-top:4px;font-weight:600">
          {{ $errorReclaMessage }}
        </div>
        @endif
      </div>

    </div>

    <div class="cot-modal-footer" style="border-top:1px solid var(--border)">
      <button class="cot-footer-recla"
              style="border-color:rgba(135,138,153,.3);color:var(--muted)"
              wire:click="closeRecla">
        <i class="ri-close-line"></i> Annuler
      </button>
      <button class="cot-footer-pay"
              wire:click="submitRecla"
              wire:loading.attr="disabled">
        <span wire:loading wire:target="submitRecla">
          <div class="spinner"></div>
        </span>
        <span wire:loading.remove wire:target="submitRecla">
          <i class="ri-send-plane-line"></i> Envoyer
        </span>
      </button>
    </div>

  </div>
</div>{{-- /cot-modal-overlay réclamation --}}

</div>{{-- /root div Livewire --}}


@push('scripts')
<script>
/* ══ Filtre statut — JS pur, ne touche pas aux overlays ══ */
function filterCot(btn) {
  document.querySelectorAll('.stab').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  const statut = btn.dataset.statut;
  const items  = document.querySelectorAll('.cot-item');
  const labels = document.querySelectorAll('.cot-month-label');
  const empty  = document.getElementById('cot-empty');
  let visible  = 0;
  items.forEach(item => {
    const show = statut === 'tous' || item.dataset.statut === statut;
    item.style.display = show ? '' : 'none';
    if (show) visible++;
  });
  labels.forEach(label => {
    let sib = label.nextElementSibling, has = false;
    while (sib && !sib.classList.contains('cot-month-label')) {
      if (sib.classList.contains('cot-item') && sib.style.display !== 'none') { has = true; break; }
      sib = sib.nextElementSibling;
    }
    label.style.display = has ? '' : 'none';
  });
  if (empty) empty.style.display = visible === 0 ? 'flex' : 'none';
}


window.addEventListener('OpenDetailCot', () => {
  document.getElementById('cot-detail-overlay')?.classList.add('open');
  document.body.style.overflow = 'hidden';
});
window.addEventListener('closeDetailCot', () => {
  document.getElementById('cot-detail-overlay')?.classList.remove('open');
  document.body.style.overflow = '';
});

window.addEventListener('OpenReclaModal', () => {
  document.getElementById('cot-recla-overlay')?.classList.add('open');
  document.body.style.overflow = 'hidden';
});
window.addEventListener('closeReclaModal', () => {
  document.getElementById('cot-recla-overlay')?.classList.remove('open');
  document.body.style.overflow = '';
});

/* ══ Animations entrée ══ */
document.querySelectorAll('.cot-item').forEach((item, i) => {
  item.style.opacity = '0';
  item.style.transform = 'translateY(10px)';
  setTimeout(() => {
    item.style.transition = 'opacity .3s ease, transform .3s ease';
    item.style.opacity = '1';
    item.style.transform = 'translateY(0)';
  }, 60 + i * 50);
});

/* ══ Escape ══ */
document.addEventListener('keydown', e => {
  if (e.key !== 'Escape') {
    @this.call('closeRecla');
    @this.call('closeDetail');
  }
});

/* ══ Toast ══ */
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
  .spinner { width:18px; height:18px; border:2.5px solid rgba(255,255,255,.3); border-top-color:#fff; border-radius:50%; animation:_spin .7s linear infinite; display:inline-block; }
  @keyframes _spin { to { transform:rotate(360deg); } }
  .feedback-text { width:100%; margin-top:.25rem; font-size:.875em; color:#f06548; }
</style>
@endpush
