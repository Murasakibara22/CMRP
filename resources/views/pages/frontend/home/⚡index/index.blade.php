<div>
<main class="page-content">

  {{-- ══ GREETING ═════════════════════════════════════════ --}}
  <div class="greeting-section">
    <div class="greeting-left">
      <div class="greeting-hello">Bonjour, {{ $customer->prenom }} 👋</div>
      <div class="greeting-sub">Voici votre situation en un coup d'œil.</div>
    </div>
    <div style="display:flex;align-items:center;gap:8px">
      {{-- Bouton Payer en avance --}}
      <button wire:click="openAvance"
              style="
                display:inline-flex;align-items:center;gap:6px;
                padding:8px 14px;border-radius:10px;border:none;
                background:{{ $hasMensuel ? 'linear-gradient(135deg,#2d3a63,#405189)' : '#e9ebec' }};
                color:{{ $hasMensuel ? '#fff' : '#878a99' }};
                font-size:12px;font-weight:700;cursor:{{ $hasMensuel ? 'pointer' : 'not-allowed' }};
                font-family:inherit;
              ">
        <i class="ri-calendar-check-2-line"></i>
        Avance
      </button>
      <span class="pill {{ $customer->status === 'actif' ? 'pill-ok' : 'pill-warn' }} greeting-badge">
        <i class="{{ $customer->status === 'actif' ? 'ri-shield-check-line' : 'ri-time-line' }}"></i>
        {{ $customer->status === 'actif' ? 'Validé' : 'En attente' }}
      </span>
    </div>
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

    {{-- Historique --}}
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
      @if($prochainMois && $prochainMontant)
      <div class="reminder-card">
        <div class="reminder-card-title">
          <i class="ri-alarm-warning-line"></i> Prochain rappel
        </div>
        <div class="reminder-due">
          <div class="reminder-due-label">Cotisation due</div>
          <div class="reminder-due-amount">{{ number_format($prochainMontant, 0, ',', ' ') }} FCFA</div>
          <div class="reminder-due-period">{{ $prochainMois }}</div>
          <a href="{{ route('customer.cotisations') }}" class="reminder-due-btn">
            <i class="ri-money-cny-circle-line"></i> Payer maintenant
          </a>
        </div>
      </div>
      @endif

      <div class="reminder-card">
        <div class="reminder-card-title">
          <i class="ri-flashlight-line"></i> Accès rapide
        </div>
        <div class="quick-links">
          <a class="quick-link" href="{{ route('customer.cotisations') }}">
            <i class="ri-calendar-check-line"></i><span>Mes cotisations</span>
            <i class="ri-arrow-right-s-line ql-arrow"></i>
          </a>
          <a class="quick-link" href="{{ route('customer.paiements') }}">
            <i class="ri-bank-card-line"></i><span>Mes paiements</span>
            <i class="ri-arrow-right-s-line ql-arrow"></i>
          </a>
          <a class="quick-link" href="{{ route('customer.documents') }}">
            <i class="ri-file-list-3-line"></i><span>Mes documents</span>
            <i class="ri-arrow-right-s-line ql-arrow"></i>
          </a>
          <a class="quick-link" href="{{ route('customer.reclamations') }}">
            <i class="ri-flag-line"></i><span>Mes réclamations</span>
            <i class="ri-arrow-right-s-line ql-arrow"></i>
          </a>
        </div>
      </div>
    </div>

  </div>

  <div style="height:24px"></div>

</main>


{{-- ══════════════════════════════════════════════════════════
     MODAL DÉTAIL COTISATION
══════════════════════════════════════════════════════════ --}}
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
          'mensuel'=>'ri-calendar-check-line','jour_precis'=>'ri-hand-heart-line',
          'ordinaire'=>'ri-gift-line','ramadan'=>'ri-moon-line',default=>'ri-file-list-3-line',
      };
      $progColor = match($statut){ 'a_jour'=>'#0ab39c','partiel'=>'#f7b84b',default=>'#f06548' };
      $pct = 0;
      if ($dc->montant_du > 0) $pct = min(round($dc->montant_paye / $dc->montant_du * 100), 100);
      elseif ($dc->montant_paye > 0) $pct = 100;
      $pillHtml = match($statut) {
          'a_jour'  => '<span class="pill" style="background:rgba(255,255,255,.2);color:#fff;font-size:11px"><i class="ri-checkbox-circle-line"></i> À jour</span>',
          'partiel' => '<span class="pill" style="background:rgba(255,255,255,.2);color:#fff;font-size:11px"><i class="ri-error-warning-line"></i> Partiel</span>',
          default   => '<span class="pill" style="background:rgba(255,255,255,.2);color:#fff;font-size:11px"><i class="ri-time-line"></i> En retard</span>',
      };
      $modeLabel   = match($dc->mode_paiement){ 'mobile_money'=>'Mobile Money','espece'=>'Espèces','virement'=>'Virement',default=>'—'};
      $periodeLabel = ($dc->mois && $dc->annee)
          ? \Carbon\Carbon::create($dc->annee, $dc->mois)->translatedFormat('F Y') : 'Ponctuel';
      $peutPayer     = $statut !== 'a_jour' && ! $dc->validated_at;
      $dejaEnAttente = $statut !== 'a_jour' && $dc->montant_paye > 0 && ! $dc->validated_at;
    @endphp

    <div class="cot-modal-header" style="background:{{ $headerBg }}">
      <div class="cmh-drag"></div>
      <div class="cmh-top">
        <div class="cmh-left">
          <div class="cmh-icon" style="background:rgba(255,255,255,.18)">
            <i class="{{ $headerIcon }}" style="color:#fff"></i>
          </div>
          <div>
            <div class="cmh-type">{{ $dc->libelle ?? $tc?->libelle }}</div>
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

      {{-- Alerte paiement en attente --}}
      @if($dejaEnAttente)
      <div style="background:rgba(247,184,75,.08);border:1.5px solid #f7b84b;border-left:4px solid #f7b84b;border-radius:0 10px 10px 0;padding:12px 14px;margin-bottom:14px">
        <div style="font-size:12px;font-weight:700;color:#c07a10;margin-bottom:2px">
          <i class="ri-time-line me-1"></i>Paiement en attente de validation
        </div>
        <div style="font-size:11px;color:#495057">
          Votre paiement de {{ number_format($dc->montant_paye, 0, ',', ' ') }} FCFA attend la validation de l'administration.
        </div>
      </div>
      @endif

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
      <button class="cot-footer-recla" wire:click="openRecla({{ $dc->id }})">
        <i class="ri-flag-line"></i> Réclamation
      </button>
      @if($peutPayer)
        @if($dejaEnAttente)
        <button style="display:inline-flex;align-items:center;gap:6px;padding:10px 18px;border-radius:12px;border:none;background:#e9ebec;color:#878a99;font-size:13px;font-weight:700;cursor:not-allowed;font-family:inherit" disabled>
          <i class="ri-time-line"></i> En attente
        </button>
        @else
        <button class="cot-footer-pay" wire:click="openPaiement({{ $dc->id }})">
          <i class="ri-bank-card-line"></i> Payer
        </button>
        @endif
      @else
      <a href="{{ route('customer.cotisations') }}" class="cot-footer-recla" wire:click="closeDetail">
        <i class="ri-list-check"></i> Voir toutes
      </a>
      @endif
    </div>

    @endif
  </div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL PAIEMENT SIMPLE
══════════════════════════════════════════════════════════ --}}
<div class="cot-modal-overlay" id="home-paiement-overlay" wire:ignore.self>
  <div class="cot-modal cot-modal-sm" wire:click.stop>

    <div class="cot-modal-header" style="background:linear-gradient(135deg,#2d3a63,#405189)">
      <div class="cmh-drag"></div>
      <div class="cmh-top">
        <div class="cmh-left">
          <div class="cmh-icon" style="background:rgba(255,255,255,.18)">
            <i class="ri-bank-card-line" style="color:#fff"></i>
          </div>
          <div>
            <div class="cmh-type" style="color:rgba(255,255,255,.8);font-size:11px">PAIEMENT</div>
            <div class="cmh-period" style="color:#fff;font-size:14px;font-weight:800">{{ $paiementLabel }}</div>
          </div>
        </div>
        <button class="cmh-close" wire:click="closePaiement"><i class="ri-close-line"></i></button>
      </div>
      <div class="cmh-amount-row">
        <div class="cmh-amount">{{ number_format($paiementMontant, 0, ',', ' ') }} FCFA</div>
        <span class="pill" style="background:rgba(255,255,255,.2);color:#fff;font-size:11px">À régler</span>
      </div>
    </div>

    <div class="cot-modal-body">

      <div style="background:rgba(64,81,137,.05);border-radius:12px;padding:14px 16px;margin-bottom:20px;text-align:center">
        <div style="font-size:11px;color:#878a99;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Montant à payer</div>
        <div style="font-size:28px;font-weight:900;color:#405189;font-family:'JetBrains Mono',monospace">
          {{ number_format($paiementMontant, 0, ',', ' ') }}
          <span style="font-size:14px;font-weight:600">FCFA</span>
        </div>
      </div>

      <div style="font-size:12px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px">
        Mode de paiement
      </div>

      <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:16px">

        <div wire:click="selectPaiementMode('espece')"
             style="display:flex;align-items:center;gap:12px;border:2px solid {{ $paiementMode === 'espece' ? '#405189' : '#e9ebec' }};background:{{ $paiementMode === 'espece' ? 'rgba(64,81,137,.06)' : '#fff' }};border-radius:12px;padding:14px;cursor:pointer;transition:all .2s">
          <div style="width:42px;height:42px;border-radius:10px;background:rgba(247,184,75,.1);display:flex;align-items:center;justify-content:center;font-size:20px;color:#d4a843;flex-shrink:0">
            <i class="ri-money-dollar-circle-line"></i>
          </div>
          <div>
            <div style="font-size:13px;font-weight:700;color:{{ $paiementMode === 'espece' ? '#405189' : '#212529' }}">Espèces</div>
            <div style="font-size:11px;color:#878a99;margin-top:2px">Remise en main propre à l'administration</div>
          </div>
          <div style="margin-left:auto;width:22px;height:22px;border-radius:50%;border:2px solid {{ $paiementMode === 'espece' ? '#405189' : '#e9ebec' }};background:{{ $paiementMode === 'espece' ? '#405189' : 'transparent' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
            @if($paiementMode === 'espece')<i class="ri-check-line" style="color:#fff;font-size:12px"></i>@endif
          </div>
        </div>

        <div wire:click="selectPaiementMode('mobile_money')"
             style="display:flex;align-items:center;gap:12px;border:2px solid {{ $paiementMode === 'mobile_money' ? '#405189' : '#e9ebec' }};background:{{ $paiementMode === 'mobile_money' ? 'rgba(64,81,137,.06)' : '#fff' }};border-radius:12px;padding:14px;cursor:pointer;transition:all .2s">
          <div style="width:42px;height:42px;border-radius:10px;background:rgba(10,179,156,.1);display:flex;align-items:center;justify-content:center;font-size:20px;color:#0ab39c;flex-shrink:0">
            <i class="ri-smartphone-line"></i>
          </div>
          <div>
            <div style="font-size:13px;font-weight:700;color:{{ $paiementMode === 'mobile_money' ? '#405189' : '#212529' }}">Mobile Money / Carte</div>
            <div style="font-size:11px;color:#878a99;margin-top:2px">Orange Money, MTN MoMo, Wave, carte visa</div>
          </div>
          <div style="margin-left:auto;width:22px;height:22px;border-radius:50%;border:2px solid {{ $paiementMode === 'mobile_money' ? '#405189' : '#e9ebec' }};background:{{ $paiementMode === 'mobile_money' ? '#405189' : 'transparent' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
            @if($paiementMode === 'mobile_money')<i class="ri-check-line" style="color:#fff;font-size:12px"></i>@endif
          </div>
        </div>

      </div>

      @if($errorPaiement)
      <div style="font-size:12px;color:#f06548;font-weight:600;margin-bottom:8px">
        <i class="ri-error-warning-line me-1"></i>{{ $errorPaiement }}
      </div>
      @endif

      @if($paiementMode === 'espece')
      <div style="background:rgba(247,184,75,.08);border-left:3px solid #f7b84b;border-radius:0 10px 10px 0;padding:10px 12px">
        <div style="font-size:11px;color:#c07a10;line-height:1.5">
          <i class="ri-information-line me-1"></i>
          Paiement enregistré en <strong>attente de validation</strong>. Remettez le montant à l'administration.
        </div>
      </div>
      @endif
      @if($paiementMode === 'mobile_money')
      <div style="background:rgba(10,179,156,.06);border-left:3px solid #0ab39c;border-radius:0 10px 10px 0;padding:10px 12px">
        <div style="font-size:11px;color:#089383;line-height:1.5">
          <i class="ri-information-line me-1"></i>
          Paiement enregistré en <strong>attente de validation</strong>. Effectuez le virement puis attendez la confirmation.
        </div>
      </div>
      @endif

    </div>

    <div class="cot-modal-footer" style="border-top:1px solid var(--border)">
      <button class="cot-footer-recla" style="border-color:rgba(135,138,153,.3);color:var(--muted)" wire:click="closePaiement">
        <i class="ri-close-line"></i> Annuler
      </button>
      <button class="cot-footer-pay" wire:click="submitPaiement" wire:loading.attr="disabled">
        <span wire:loading wire:target="submitPaiement"><div class="spinner"></div></span>
        <span wire:loading.remove wire:target="submitPaiement"><i class="ri-check-line"></i> Confirmer</span>
      </button>
    </div>

  </div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL PAIEMENT EN AVANCE
══════════════════════════════════════════════════════════ --}}
<div class="cot-modal-overlay" id="home-avance-overlay" wire:ignore.self>
  <div class="cot-modal" wire:click.stop>

    <div class="cot-modal-header" style="background:linear-gradient(135deg,#0a5a50,#0ab39c)">
      <div class="cmh-drag"></div>
      <div class="cmh-top">
        <div class="cmh-left">
          <div class="cmh-icon" style="background:rgba(255,255,255,.18)">
            <i class="ri-calendar-check-2-line" style="color:#fff"></i>
          </div>
          <div>
            <div class="cmh-type" style="color:rgba(255,255,255,.8);font-size:11px">PAIEMENT EN AVANCE</div>
            <div class="cmh-period" style="color:#fff;font-size:14px;font-weight:800">Cotisation mensuelle</div>
          </div>
        </div>
        <button class="cmh-close" wire:click="closeAvance"><i class="ri-close-line"></i></button>
      </div>
      @if($customer->montant_engagement && count($previewAvance))
      <div class="cmh-amount-row">
        <div class="cmh-amount">{{ number_format(count($previewAvance) * $customer->montant_engagement, 0, ',', ' ') }} FCFA</div>
        <span class="pill" style="background:rgba(255,255,255,.2);color:#fff;font-size:11px">{{ count($previewAvance) }} mois</span>
      </div>
      @endif
    </div>

    <div class="cot-modal-body">

      <div style="margin-bottom:20px">
        <div style="font-size:12px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px">
          Nombre de mois à payer en avance
        </div>
        <div style="display:flex;align-items:center;gap:12px">
          <button wire:click="$set('nbMoisAvance', {{ max(1, $nbMoisAvance - 1) }})"
                  style="width:36px;height:36px;border-radius:50%;border:2px solid #e9ebec;background:#fff;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#405189;flex-shrink:0">−</button>
          <div style="flex:1;text-align:center">
            <div style="font-size:32px;font-weight:900;color:#405189">{{ $nbMoisAvance }}</div>
            <div style="font-size:11px;color:#878a99">mois</div>
          </div>
          <button wire:click="$set('nbMoisAvance', {{ min(24, $nbMoisAvance + 1) }})"
                  style="width:36px;height:36px;border-radius:50%;border:2px solid #e9ebec;background:#fff;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#405189;flex-shrink:0">+</button>
        </div>
        <input type="range" wire:model.live="nbMoisAvance" min="1" max="24"
               style="width:100%;margin-top:10px;accent-color:#405189"/>
      </div>

      @if(count($previewAvance))
      <div style="margin-bottom:20px">
        <div style="font-size:12px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px">
          Mois qui seront créés
        </div>
        <div style="display:flex;flex-direction:column;gap:6px;max-height:160px;overflow-y:auto">
          @foreach($previewAvance as $i => $row)
          <div style="display:flex;align-items:center;justify-content:space-between;background:rgba(10,179,156,.05);border:1px solid rgba(10,179,156,.15);border-radius:10px;padding:10px 12px">
            <div style="display:flex;align-items:center;gap:8px">
              <div style="width:26px;height:26px;border-radius:7px;background:rgba(10,179,156,.12);color:#0ab39c;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;flex-shrink:0">{{ $i + 1 }}</div>
              <div style="font-size:13px;font-weight:600;color:#212529">{{ $row['label'] }}</div>
            </div>
            <div style="font-size:13px;font-weight:800;color:#0ab39c">{{ number_format($row['montant'], 0, ',', ' ') }} FCFA</div>
          </div>
          @endforeach
        </div>
        <div style="background:rgba(64,81,137,.06);border-radius:10px;padding:12px;margin-top:10px;display:flex;justify-content:space-between;align-items:center">
          <span style="font-size:13px;font-weight:700;color:#495057">Total</span>
          <span style="font-size:18px;font-weight:900;color:#405189">
            {{ number_format(count($previewAvance) * ($customer->montant_engagement ?? 0), 0, ',', ' ') }} FCFA
          </span>
        </div>
      </div>
      @endif

      <div style="font-size:12px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px">Mode de paiement</div>
      <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:16px">
        <div wire:click="selectAvanceMode('espece')"
             style="display:flex;align-items:center;gap:10px;border:2px solid {{ $avanceMode === 'espece' ? '#405189' : '#e9ebec' }};background:{{ $avanceMode === 'espece' ? 'rgba(64,81,137,.06)' : '#fff' }};border-radius:12px;padding:12px;cursor:pointer;transition:all .2s">
          <div style="width:36px;height:36px;border-radius:9px;background:rgba(247,184,75,.1);display:flex;align-items:center;justify-content:center;font-size:18px;color:#d4a843;flex-shrink:0"><i class="ri-money-dollar-circle-line"></i></div>
          <div style="font-size:13px;font-weight:700;color:{{ $avanceMode === 'espece' ? '#405189' : '#212529' }}">Espèces</div>
          <div style="margin-left:auto;width:20px;height:20px;border-radius:50%;border:2px solid {{ $avanceMode === 'espece' ? '#405189' : '#e9ebec' }};background:{{ $avanceMode === 'espece' ? '#405189' : 'transparent' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
            @if($avanceMode === 'espece')<i class="ri-check-line" style="color:#fff;font-size:11px"></i>@endif
          </div>
        </div>
        <div wire:click="selectAvanceMode('mobile_money')"
             style="display:flex;align-items:center;gap:10px;border:2px solid {{ $avanceMode === 'mobile_money' ? '#405189' : '#e9ebec' }};background:{{ $avanceMode === 'mobile_money' ? 'rgba(64,81,137,.06)' : '#fff' }};border-radius:12px;padding:12px;cursor:pointer;transition:all .2s">
          <div style="width:36px;height:36px;border-radius:9px;background:rgba(10,179,156,.1);display:flex;align-items:center;justify-content:center;font-size:18px;color:#0ab39c;flex-shrink:0"><i class="ri-smartphone-line"></i></div>
          <div style="font-size:13px;font-weight:700;color:{{ $avanceMode === 'mobile_money' ? '#405189' : '#212529' }}">Mobile Money / Carte</div>
          <div style="margin-left:auto;width:20px;height:20px;border-radius:50%;border:2px solid {{ $avanceMode === 'mobile_money' ? '#405189' : '#e9ebec' }};background:{{ $avanceMode === 'mobile_money' ? '#405189' : 'transparent' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
            @if($avanceMode === 'mobile_money')<i class="ri-check-line" style="color:#fff;font-size:11px"></i>@endif
          </div>
        </div>
      </div>

      @if($errorAvance)
      <div style="font-size:12px;color:#f06548;font-weight:600"><i class="ri-error-warning-line me-1"></i>{{ $errorAvance }}</div>
      @endif

    </div>

    <div class="cot-modal-footer" style="border-top:1px solid var(--border)">
      <button class="cot-footer-recla" style="border-color:rgba(135,138,153,.3);color:var(--muted)" wire:click="closeAvance">
        <i class="ri-close-line"></i> Annuler
      </button>
      <button class="cot-footer-pay" wire:click="submitAvance" wire:loading.attr="disabled">
        <span wire:loading wire:target="submitAvance"><div class="spinner"></div></span>
        <span wire:loading.remove wire:target="submitAvance">
          <i class="ri-check-double-line"></i> Confirmer {{ count($previewAvance) }} mois
        </span>
      </button>
    </div>

  </div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL RÉCLAMATION
══════════════════════════════════════════════════════════ --}}
<div class="cot-modal-overlay" id="home-recla-overlay" wire:ignore.self>
  <div class="cot-modal cot-modal-sm" wire:click.stop>

    <div class="cot-modal-header" style="background:linear-gradient(135deg,#e8a53a,#f7b84b)">
      <div class="cmh-drag"></div>
      <div class="cmh-top">
        <div class="cmh-left">
          <div class="cmh-icon" style="background:rgba(255,255,255,.2);color:#fff"><i class="ri-flag-line"></i></div>
          <div>
            <div class="cmh-type" style="color:rgba(255,255,255,.85);font-size:11px">RÉCLAMATION</div>
            <div class="cmh-period" style="color:#fff;font-size:15px;font-weight:900">Signaler un problème</div>
          </div>
        </div>
        <button class="cmh-close" wire:click="closeRecla"><i class="ri-close-line"></i></button>
      </div>
    </div>

    <div class="cot-modal-body">
      <div class="f-group">
        <label class="f-label-sm">Cotisation concernée</label>
        <input type="text" class="f-input-sm" value="{{ $reclaLabel }}" readonly/>
      </div>
      <div class="f-group">
        <label class="f-label-sm">Titre <span style="color:#f06548">*</span></label>
        <div class="f-input-wrap-sm">
          <i class="ri-text f-ico-sm"></i>
          <input type="text" class="f-input-sm" wire:model.lazy="reclaTitle" placeholder="ex : Paiement non enregistré"/>
        </div>
        @if($errorReclaTitle)<div style="font-size:12px;color:#f06548;margin-top:4px;font-weight:600">{{ $errorReclaTitle }}</div>@endif
      </div>
      <div class="f-group">
        <label class="f-label-sm">Message <span style="color:#f06548">*</span></label>
        <textarea class="f-input-sm f-textarea-sm" wire:model.lazy="reclaMessage" placeholder="Décrivez le problème…"></textarea>
        @if($errorReclaMessage)<div style="font-size:12px;color:#f06548;margin-top:4px;font-weight:600">{{ $errorReclaMessage }}</div>@endif
      </div>
    </div>

    <div class="cot-modal-footer" style="border-top:1px solid var(--border)">
      <button class="cot-footer-recla" style="border-color:rgba(135,138,153,.3);color:var(--muted)" wire:click="closeRecla">
        <i class="ri-close-line"></i> Annuler
      </button>
      <button class="cot-footer-pay" wire:click="submitRecla" wire:loading.attr="disabled">
        <span wire:loading wire:target="submitRecla"><div class="spinner"></div></span>
        <span wire:loading.remove wire:target="submitRecla"><i class="ri-send-plane-line"></i> Envoyer</span>
      </button>
    </div>

  </div>
</div>

</div>{{-- /root Livewire --}}


@push('scripts')
<script>
function openOverlay(id)  { document.getElementById(id)?.classList.add('open');    document.body.style.overflow = 'hidden'; }
function closeOverlay(id) { document.getElementById(id)?.classList.remove('open'); document.body.style.overflow = ''; }

window.addEventListener('OpenHomeCotDetail',  () => openOverlay('home-cot-detail-overlay'));
window.addEventListener('closeHomeCotDetail', () => { closeOverlay('home-cot-detail-overlay'); @this.set('detailId', null); });
window.addEventListener('OpenHomePaiement',   () => openOverlay('home-paiement-overlay'));
window.addEventListener('closeHomePaiement',  () => closeOverlay('home-paiement-overlay'));
window.addEventListener('OpenHomeAvance',     () => openOverlay('home-avance-overlay'));
window.addEventListener('closeHomeAvance',    () => closeOverlay('home-avance-overlay'));
window.addEventListener('OpenHomeRecla',      () => openOverlay('home-recla-overlay'));
window.addEventListener('closeHomeRecla',     () => closeOverlay('home-recla-overlay'));

function filterHist(btn) {
  document.querySelectorAll('.hf-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  const f = btn.dataset.filter;
  document.querySelectorAll('.hist-item').forEach(item => {
    item.style.display = (f === 'tous' || item.dataset.type === f) ? '' : 'none';
  });
}

document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    @this.call('closeDetail');
    @this.call('closePaiement');
    @this.call('closeAvance');
    @this.call('closeRecla');
  }
});

Livewire.on('modalShowmessageToast', (payload) => {
  const data = Array.isArray(payload) ? payload[0] : payload;
  if (typeof Swal !== 'undefined') {
    Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true })
        .fire({ icon: data.type, title: data.title });
  }
});
Livewire.on('swal:modalGetInfo_message_not_timer', (payload) => {
  const data = Array.isArray(payload) ? payload[0] : payload;
  if (typeof Swal !== 'undefined') Swal.fire({ title: data.title, text: data.text, icon: data.type });
});
</script>
@endpush

@push('styles')
<style>
  .spinner { width:18px;height:18px;border:2.5px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:_spin .7s linear infinite;display:inline-block; }
  @keyframes _spin { to { transform:rotate(360deg); } }
</style>
@endpush
