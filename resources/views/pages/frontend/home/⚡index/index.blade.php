<div>
<main class="page-content">

  {{-- ══ GREETING ═════════════════════════════════════════ --}}
  <div class="greeting-section">
    <div class="greeting-left">
      <div class="greeting-hello">Bonjour, {{ $customer->prenom }} 👋</div>
      <div class="greeting-sub">Voici votre situation en un coup d'œil.</div>
    </div>
    <span class="pill {{ $customer->status === 'actif' ? 'pill-ok' : 'pill-warn' }} greeting-badge">
      <i class="{{ $customer->status === 'actif' ? 'ri-shield-check-line' : 'ri-time-line' }}"></i>
      {{ $customer->status === 'actif' ? 'Validé' : 'En attente' }}
    </span>
  </div>

  {{-- ══ ALERTE RETARD ══════════════════════════════════════ --}}
  @if($moisRetard > 0)
  <div class="alert-card">
    <div class="alert-card-left">
      <div class="alert-icon"><i class="ri-alarm-warning-line"></i></div>
      <div>
        <div class="alert-title">Vous avez {{ $moisRetard }} mois en retard</div>
        <div class="alert-sub">
          {{ $retardDetails->join(', ') }}
          — {{ number_format($totalDu, 0, ',', ' ') }} FCFA dus
        </div>
      </div>
    </div>
    <a href="{{ route('customer.cotisations') }}" class="alert-btn">
      Régulariser <i class="ri-arrow-right-s-line"></i>
    </a>
  </div>
  @endif

  {{-- ══ KPIs ═════════════════════════════════════════════ --}}
  <div class="kpi-grid">
    <div class="kpi-card">
      <div class="kpi-icon" style="background:rgba(10,179,156,.10);color:#0ab39c"><i class="ri-money-cny-circle-line"></i></div>
      <div class="kpi-label">Total cotisé</div>
      <div class="kpi-value">{{ number_format($totalCotise, 0, ',', ' ') }}</div>
      <div class="kpi-unit">FCFA</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon" style="background:rgba(240,101,72,.10);color:#f06548"><i class="ri-error-warning-line"></i></div>
      <div class="kpi-label">Montant dû</div>
      <div class="kpi-value" style="{{ $totalDu > 0 ? 'color:#f06548' : 'color:#0ab39c' }}">
        {{ $totalDu > 0 ? number_format($totalDu, 0, ',', ' ') : '0' }}
      </div>
      <div class="kpi-unit">{{ $totalDu > 0 ? 'FCFA restant' : 'À jour ✓' }}</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon" style="background:rgba(247,184,75,.12);color:#f7b84b"><i class="ri-time-line"></i></div>
      <div class="kpi-label">Mois en retard</div>
      <div class="kpi-value" style="{{ $moisRetard > 0 ? 'color:#f7b84b' : 'color:#0ab39c' }}">{{ $moisRetard }}</div>
      <div class="kpi-unit">Derniers mois</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon" style="background:rgba(64,81,137,.10);color:#405189"><i class="ri-calendar-event-line"></i></div>
      <div class="kpi-label">Prochain paiement</div>
      <div class="kpi-value kpi-value-sm">{{ $prochainMois ?? '—' }}</div>
      <div class="kpi-unit">
        @if($prochainMontant) {{ number_format($prochainMontant, 0, ',', ' ') }} FCFA @else — @endif
      </div>
    </div>
  </div>

  {{-- ══ DESKTOP 2 COLONNES ══════════════════════════════ --}}
  <div class="desktop-cols">

    {{-- Colonne gauche : historique --}}
    <div>
      <div class="hist-header">
        <div class="hist-title">Historique</div>
      </div>
      <div class="hist-filters">
        <button class="hf-btn active" data-filter="tous" onclick="filterHist(this)">
          <i class="ri-list-check"></i> Tous
        </button>
        <button class="hf-btn" data-filter="cotisations" onclick="filterHist(this)">
          <i class="ri-calendar-check-line"></i> Cotisations
        </button>
        <button class="hf-btn" data-filter="reclamations" onclick="filterHist(this)">
          <i class="ri-flag-line"></i> Réclamations
        </button>
      </div>

      <div class="hist-list card">
        @forelse($historique as $item)
        <div class="hist-item"
             data-type="{{ $item['type'] }}"
             wire:key="hist-{{ $item['type'] }}-{{ $item['id'] }}"
             @if($item['type'] === 'cotisations')
             wire:click="openDetail({{ $item['id'] }})"
             style="cursor:pointer"
             @endif>
          <div class="hi-icon" style="background:{{ $item['iconBg'] }};color:{{ $item['iconColor'] }}">
            <i class="{{ $item['iconClass'] }}"></i>
          </div>
          <div class="hi-body">
            <div class="hi-title">{{ $item['title'] }}</div>
            <div class="hi-date">{{ $item['date'] }}</div>
          </div>
          <div class="hi-right">
            @if($item['montant'])
            <div class="hi-amount" style="color:{{ $item['amountColor'] }}">{{ $item['montant'] }}</div>
            @endif
            <span class="pill {{ $item['pillClass'] }}" style="font-size:10px">{{ $item['pillLabel'] }}</span>
          </div>
        </div>
        @empty
        <div style="text-align:center;padding:32px;color:var(--muted)">
          <i class="ri-inbox-line" style="font-size:32px;display:block;margin-bottom:8px;opacity:.4"></i>
          <div style="font-size:13px;font-weight:600">Aucune activité récente</div>
        </div>
        @endforelse
      </div>
    </div>

    {{-- Colonne droite desktop --}}
    <div class="desktop-right">

      {{-- Rappel paiement dû --}}
      @if($prochainMois && $prochainMontant)
      <div class="reminder-card">
        <div class="reminder-card-title">
          <i class="ri-alarm-warning-line"></i> Prochain rappel
        </div>
        <div class="reminder-due">
          <div class="reminder-due-label">Cotisation due</div>
          <div class="reminder-due-amount">{{ number_format($prochainMontant, 0, ',', ' ') }} FCFA</div>
          <div class="reminder-due-period">{{ $prochainMois }}</div>
          <a href="{{ route('customer.ajout-cotisations') }}" class="reminder-due-btn">
            <i class="ri-money-cny-circle-line"></i> Payer maintenant
          </a>
        </div>
      </div>
      @endif

      {{-- Raccourcis --}}
      <div class="reminder-card">
        <div class="reminder-card-title">
          <i class="ri-flashlight-line"></i> Accès rapide
        </div>
        <div class="quick-links">
          <a class="quick-link" href="{{ route('customer.cotisations') }}">
            <i class="ri-calendar-check-line"></i>
            <span>Mes cotisations</span>
            <i class="ri-arrow-right-s-line ql-arrow"></i>
          </a>
          <a class="quick-link" href="{{ route('customer.paiements') }}">
            <i class="ri-bank-card-line"></i>
            <span>Mes paiements</span>
            <i class="ri-arrow-right-s-line ql-arrow"></i>
          </a>
          <a class="quick-link" href="{{ route('customer.documents') }}">
            <i class="ri-file-list-3-line"></i>
            <span>Mes documents</span>
            <i class="ri-arrow-right-s-line ql-arrow"></i>
          </a>
          <a class="quick-link" href="{{ route('customer.reclamations') }}">
            <i class="ri-flag-line"></i>
            <span>Mes réclamations</span>
            <i class="ri-arrow-right-s-line ql-arrow"></i>
          </a>
        </div>
      </div>

    </div>

  </div>{{-- /desktop-cols --}}

  <div style="height:24px"></div>

</main>


{{-- ══ MODAL DÉTAIL COTISATION (même pattern que partout) ══ --}}
<div class="cot-modal-overlay" id="home-cot-detail-overlay" wire:ignore.self>
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
      $progColor = match($statut) { 'a_jour'=>'#0ab39c', 'partiel'=>'#f7b84b', default=>'#f06548' };
      $pct = 0;
      if ($dc->montant_du > 0) $pct = min(round($dc->montant_paye / $dc->montant_du * 100), 100);
      elseif ($dc->montant_paye > 0) $pct = 100;
      $pillHtml = match($statut) {
          'a_jour'    => '<span class="pill" style="background:rgba(255,255,255,.2);color:#fff;font-size:11px"><i class="ri-checkbox-circle-line"></i> À jour</span>',
          'partiel'   => '<span class="pill" style="background:rgba(255,255,255,.2);color:#fff;font-size:11px"><i class="ri-error-warning-line"></i> Partiel</span>',
          default     => '<span class="pill" style="background:rgba(255,255,255,.2);color:#fff;font-size:11px"><i class="ri-time-line"></i> En retard</span>',
      };
      $modeLabel = match($dc->mode_paiement){ 'mobile_money'=>'Mobile Money','espece'=>'Espèces','virement'=>'Virement',default=>'—'};
      $periodeLabel = ($dc->mois && $dc->annee)
          ? \Carbon\Carbon::create($dc->annee, $dc->mois)->translatedFormat('F Y')
          : 'Ponctuel';
    @endphp

    <div class="cot-modal-header" style="background:{{ $headerBg }}">
      <div class="cmh-drag"></div>
      <div class="cmh-top">
        <div class="cmh-left">
          <div class="cmh-icon" style="background:rgba(255,255,255,.18)">
            <i class="{{ $headerIcon }}" style="color:#fff"></i>
          </div>
          <div>
            <div class="cmh-type">{{ $tc?->libelle ?? '—' }}</div>
            <div class="cmh-period">{{ $periodeLabel }}</div>
          </div>
        </div>
        <button class="cmh-close" wire:click="closeDetail"><i class="ri-close-line"></i></button>
      </div>
      <div class="cmh-amount-row">
        <div class="cmh-amount">
          {{ $dc->montant_du ? number_format($dc->montant_du, 0, ',', ' ').' FCFA' : number_format($dc->montant_paye, 0, ',', ' ').' FCFA' }}
        </div>
        <div>{!! $pillHtml !!}</div>
      </div>
    </div>

    <div class="cot-modal-body">
      <div class="cot-det-grid">
        <div class="cot-det-cell">
          <div class="cot-det-label"><i class="ri-error-warning-line"></i> Montant dû</div>
          <div class="cot-det-val" style="{{ $statut==='en_retard' ? 'color:#f06548' : '' }}">
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
          <div class="cot-det-label"><i class="ri-smartphone-line"></i> Mode</div>
          <div class="cot-det-val">{{ $modeLabel }}</div>
        </div>
      </div>

      <div class="cot-det-prog-wrap">
        <div class="cot-det-prog-header">
          <span class="cot-det-prog-label"><i class="ri-bar-chart-line"></i> Progression</span>
          <span style="color:{{ $progColor }};font-weight:800;font-size:13px">{{ $pct }}%</span>
        </div>
        <div class="cot-det-prog-track">
          <div class="cot-det-prog-fill" style="width:{{ $pct }}%;background:{{ $progColor }}"></div>
        </div>
      </div>

      @if(count($detailPaiements))
      <div class="cot-det-section-title"><i class="ri-bank-card-line"></i> Paiements liés</div>
      <div class="cot-det-pay-list">
        @foreach($detailPaiements as $p)
        <div class="cot-pay-row">
          <div class="cot-pay-body">
            <div class="cot-pay-title">{{ $p['date'] }} · {{ $p['mode'] }}</div>
          </div>
          <div class="cot-pay-right">
            <span class="cot-pay-amount" style="color:{{ $p['montantColor'] }}">{{ $p['montant'] }}</span>
          </div>
        </div>
        @endforeach
      </div>
      @endif
    </div>

    <div class="cot-modal-footer">
      @if($statut !== 'a_jour')
      <a href="{{ route('customer.ajout-cotisations') }}" class="cot-footer-pay" wire:click="closeDetail">
        <i class="ri-add-circle-line"></i> Payer
      </a>
      @endif
      <a href="{{ route('customer.cotisations') }}" class="cot-footer-recla" wire:click="closeDetail">
        <i class="ri-list-check"></i> Voir toutes
      </a>
    </div>

    @endif
  </div>
</div>

</div>{{-- /root Livewire --}}


@push('scripts')
<script>
/* ── Modal cotisation home ── */
window.addEventListener('OpenHomeCotDetail',   () => { document.getElementById('home-cot-detail-overlay')?.classList.add('open');    document.body.style.overflow = 'hidden'; });
window.addEventListener('closeHomeCotDetail',  () => {
  document.getElementById('home-cot-detail-overlay')?.classList.remove('open');
  document.body.style.overflow = '';
  @this.set('detailId', null);
});

/* ── Filtre historique ── */
function filterHist(btn) {
  document.querySelectorAll('.hf-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  const f = btn.dataset.filter;
  document.querySelectorAll('.hist-item').forEach(item => {
    item.style.display = (f === 'tous' || item.dataset.type === f) ? '' : 'none';
  });
}
</script>
@endpush