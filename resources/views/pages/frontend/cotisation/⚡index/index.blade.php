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

  {{-- ══ BOUTON PAYER EN AVANCE ══════════════════════════════ --}}
  <div style="padding:0 16px 14px;display:flex;justify-content:flex-end">
    <button wire:click="openAvance"
            style="
              display:inline-flex;align-items:center;gap:8px;
              padding:10px 18px;border-radius:12px;
              background:{{ $hasMensuel ? 'linear-gradient(135deg,#2d3a63,#405189)' : '#e9ebec' }};
              color:{{ $hasMensuel ? '#fff' : '#878a99' }};
              border:none;font-size:13px;font-weight:700;
              cursor:{{ $hasMensuel ? 'pointer' : 'not-allowed' }};
              font-family:inherit;transition:all .2s;
            ">
      <i class="ri-calendar-check-2-line" style="font-size:16px"></i>
      Payer en avance
      @if(! $hasMensuel)
      <span style="font-size:10px;font-weight:500;background:rgba(135,138,153,.2);padding:2px 6px;border-radius:6px">Mensuel requis</span>
      @endif
    </button>
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

    /* Paiement en attente = montant_paye > 0 mais pas encore validée */
    $paiementEnAttente = $statut !== 'a_jour'
        && $cot->montant_paye > 0
        && ! $cot->validated_at;
  @endphp

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
        <div class="cot-sub">
          {{ $subLine }}
          @if($paiementEnAttente)
            <span style="display:inline-block;background:rgba(247,184,75,.15);color:#c07a10;font-size:9px;font-weight:700;padding:1px 5px;border-radius:4px;margin-left:4px">En attente validation</span>
          @endif
        </div>
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

  <div style="height:80px"></div>

</main>


{{-- ══════════════════════════════════════════════════════════
     MODAL DÉTAIL COTISATION
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
          'a_jour'  => '<span class="pill" style="background:rgba(255,255,255,.2);color:#fff;font-size:11px"><i class="ri-checkbox-circle-line"></i> À jour</span>',
          'partiel' => '<span class="pill" style="background:rgba(255,255,255,.2);color:#fff;font-size:11px"><i class="ri-error-warning-line"></i> Partiel</span>',
          default   => '<span class="pill" style="background:rgba(255,255,255,.2);color:#fff;font-size:11px"><i class="ri-time-line"></i> En retard</span>',
      };

      /* Payer visible si cotisation non validée */
      $peutPayer = $statut !== 'a_jour' && ! $dc->validated_at;
      /* Paiement déjà en attente = montant_paye > 0 mais pas validé */
      $dejaEnAttente = $statut !== 'a_jour' && $dc->montant_paye > 0 && ! $dc->validated_at;
    @endphp

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

    <div class="cot-modal-body">

      {{-- Alerte paiement en attente --}}
      @if($dejaEnAttente)
      <div style="background:rgba(247,184,75,.08);border:1.5px solid #f7b84b;border-left:4px solid #f7b84b;border-radius:0 10px 10px 0;padding:12px 14px;margin-bottom:14px">
        <div style="font-size:12px;font-weight:700;color:#c07a10;margin-bottom:2px">
          <i class="ri-time-line me-1"></i>Paiement en attente de validation
        </div>
        <div style="font-size:11px;color:#495057">
          Votre paiement de {{ number_format($dc->montant_paye, 0, ',', ' ') }} FCFA a été enregistré et attend la validation de l'administration.
        </div>
      </div>
      @endif

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
          <span class="cot-det-prog-pct" style="color:{{ $progColor }}">{{ $pctDet }}%</span>
        </div>
        <div class="cot-det-prog-track">
          <div class="cot-det-prog-fill" style="width:{{ $pctDet }}%;background:{{ $progColor }}"></div>
        </div>
        <div class="cot-det-prog-footer">
          <span>{{ number_format($dc->montant_paye, 0, ',', ' ') }} FCFA payé</span>
          <span>{{ $dc->montant_du ? number_format($dc->montant_du, 0, ',', ' ').' FCFA dû' : '—' }}</span>
        </div>
      </div>

      {{-- Paiements liés --}}
      <div class="cot-det-section-title">
        <i class="ri-bank-card-line"></i> Paiements liés
      </div>
      <div class="cot-det-pay-list">
        @forelse($detailPaiements as $p)
        <div class="cot-pay-item">
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
        <div class="cot-pay-empty">
          <i class="ri-inbox-line"></i> Aucun paiement enregistré
        </div>
        @endforelse
      </div>

    </div>{{-- /cot-modal-body --}}

    {{-- Footer --}}
    <div class="cot-modal-footer">
      <button class="cot-footer-recla" wire:click="openRecla({{ $dc->id }})">
        <i class="ri-flag-line"></i> Réclamation
      </button>
      {{--
        Bouton Payer :
        - Visible si cotisation non validée
        - Grisé si paiement déjà en attente (montant_paye > 0)
      --}}
      @if($peutPayer)
        @if($dejaEnAttente)
        <button style="
          display:inline-flex;align-items:center;gap:6px;
          padding:10px 18px;border-radius:12px;border:none;
          background:#e9ebec;color:#878a99;
          font-size:13px;font-weight:700;cursor:not-allowed;font-family:inherit;
        " disabled title="Paiement déjà en attente de validation">
          <i class="ri-time-line"></i> En attente
        </button>
        @else
        <button class="cot-footer-pay" wire:click="openPaiement({{ $dc->id }})">
          <i class="ri-bank-card-line"></i> Payer
        </button>
        @endif
      @endif
    </div>

    @endif
  </div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL PAIEMENT SIMPLE
══════════════════════════════════════════════════════════ --}}
<div class="cot-modal-overlay" id="cot-paiement-overlay" wire:ignore.self>
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

      {{-- Montant récapitulatif --}}
      <div style="background:rgba(64,81,137,.05);border-radius:12px;padding:14px 16px;margin-bottom:20px;text-align:center">
        <div style="font-size:11px;color:#878a99;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Montant à payer</div>
        <div style="font-size:28px;font-weight:900;color:#405189;font-family:'JetBrains Mono',monospace">
          {{ number_format($paiementMontant, 0, ',', ' ') }}
          <span style="font-size:14px;font-weight:600">FCFA</span>
        </div>
      </div>

      {{-- Choisir le mode --}}
      <div style="font-size:12px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px">
        Mode de paiement
      </div>

      <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:20px">

        {{-- Espèces --}}
        <div wire:click="selectPaiementMode('espece')"
             style="
               display:flex;align-items:center;gap:12px;
               border:2px solid {{ $paiementMode === 'espece' ? '#405189' : '#e9ebec' }};
               background:{{ $paiementMode === 'espece' ? 'rgba(64,81,137,.06)' : '#fff' }};
               border-radius:12px;padding:14px;cursor:pointer;transition:all .2s;
             ">
          <div style="width:42px;height:42px;border-radius:10px;background:{{ $paiementMode === 'espece' ? 'rgba(64,81,137,.15)' : 'rgba(247,184,75,.1)' }};display:flex;align-items:center;justify-content:center;font-size:20px;color:{{ $paiementMode === 'espece' ? '#405189' : '#d4a843' }};flex-shrink:0">
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

        {{-- Mobile Money --}}
        <div wire:click="selectPaiementMode('mobile_money')"
             style="
               display:flex;align-items:center;gap:12px;
               border:2px solid {{ $paiementMode === 'mobile_money' ? '#405189' : '#e9ebec' }};
               background:{{ $paiementMode === 'mobile_money' ? 'rgba(64,81,137,.06)' : '#fff' }};
               border-radius:12px;padding:14px;cursor:pointer;transition:all .2s;
             ">
          <div style="width:42px;height:42px;border-radius:10px;background:{{ $paiementMode === 'mobile_money' ? 'rgba(64,81,137,.15)' : 'rgba(10,179,156,.1)' }};display:flex;align-items:center;justify-content:center;font-size:20px;color:{{ $paiementMode === 'mobile_money' ? '#405189' : '#0ab39c' }};flex-shrink:0">
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
      <div style="font-size:12px;color:#f06548;font-weight:600;margin-bottom:12px">
        <i class="ri-error-warning-line me-1"></i>{{ $errorPaiement }}
      </div>
      @endif

      {{-- Note espèces --}}
      @if($paiementMode === 'espece')
      <div style="background:rgba(247,184,75,.08);border-left:3px solid #f7b84b;border-radius:0 10px 10px 0;padding:10px 12px;margin-bottom:4px">
        <div style="font-size:11px;color:#c07a10;line-height:1.5">
          <i class="ri-information-line me-1"></i>
          Votre paiement sera enregistré en <strong>attente de validation</strong>.
          Remettez le montant en espèces à l'administration qui le validera.
        </div>
      </div>
      @endif

      @if($paiementMode === 'mobile_money')
      <div style="background:rgba(10,179,156,.06);border-left:3px solid #0ab39c;border-radius:0 10px 10px 0;padding:10px 12px;margin-bottom:4px">
        <div style="font-size:11px;color:#089383;line-height:1.5">
          <i class="ri-information-line me-1"></i>
          Votre paiement sera enregistré en <strong>attente de validation</strong>.
          Effectuez le virement et l'administration confirmera la réception.
        </div>
      </div>
      @endif

    </div>

    <div class="cot-modal-footer" style="border-top:1px solid var(--border)">
      <button class="cot-footer-recla" style="border-color:rgba(135,138,153,.3);color:var(--muted)" wire:click="closePaiement">
        <i class="ri-close-line"></i> Annuler
      </button>
      <button class="cot-footer-pay"
              wire:click="submitPaiement"
              wire:loading.attr="disabled">
        <span wire:loading wire:target="submitPaiement"><div class="spinner"></div></span>
        <span wire:loading.remove wire:target="submitPaiement">
          <i class="ri-check-line"></i> Confirmer
        </span>
      </button>
    </div>

  </div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL PAIEMENT EN AVANCE
══════════════════════════════════════════════════════════ --}}
<div class="cot-modal-overlay" id="cot-avance-overlay" wire:ignore.self>
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
            <div class="cmh-period" style="color:#fff;font-size:14px;font-weight:800">
              Cotisation mensuelle
              @if($customer->typeCotisationMensuel ?? null)
                — {{ $customer->typeCotisationMensuel->libelle }}
              @endif
            </div>
          </div>
        </div>
        <button class="cmh-close" wire:click="closeAvance"><i class="ri-close-line"></i></button>
      </div>
      @if($customer->montant_engagement && count($previewAvance))
      <div class="cmh-amount-row">
        <div class="cmh-amount">
          {{ number_format(count($previewAvance) * $customer->montant_engagement, 0, ',', ' ') }} FCFA
        </div>
        <span class="pill" style="background:rgba(255,255,255,.2);color:#fff;font-size:11px">
          {{ count($previewAvance) }} mois
        </span>
      </div>
      @endif
    </div>

    <div class="cot-modal-body">

      {{-- Nombre de mois --}}
      <div style="margin-bottom:20px">
        <div style="font-size:12px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px">
          Nombre de mois à payer en avance
        </div>
        <div style="display:flex;align-items:center;gap:12px">
          <button wire:click="$set('nbMoisAvance', {{ max(1, $nbMoisAvance - 1) }})"
                  style="width:36px;height:36px;border-radius:50%;border:2px solid #e9ebec;background:#fff;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#405189;flex-shrink:0">
            −
          </button>
          <div style="flex:1;text-align:center">
            <div style="font-size:32px;font-weight:900;color:#405189">{{ $nbMoisAvance }}</div>
            <div style="font-size:11px;color:#878a99">mois</div>
          </div>
          <button wire:click="$set('nbMoisAvance', {{ min(24, $nbMoisAvance + 1) }})"
                  style="width:36px;height:36px;border-radius:50%;border:2px solid #e9ebec;background:#fff;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#405189;flex-shrink:0">
            +
          </button>
        </div>
        <input type="range" wire:model.live="nbMoisAvance" min="1" max="24"
               style="width:100%;margin-top:10px;accent-color:#405189"/>
      </div>

      {{-- Prévisualisation des mois --}}
      @if(count($previewAvance))
      <div style="margin-bottom:20px">
        <div style="font-size:12px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px">
          Mois qui seront créés
        </div>
        <div style="display:flex;flex-direction:column;gap:6px;max-height:180px;overflow-y:auto;padding-right:4px">
          @foreach($previewAvance as $i => $row)
          <div style="display:flex;align-items:center;justify-content:space-between;background:rgba(10,179,156,.05);border:1px solid rgba(10,179,156,.15);border-radius:10px;padding:10px 12px">
            <div style="display:flex;align-items:center;gap:8px">
              <div style="width:28px;height:28px;border-radius:8px;background:rgba(10,179,156,.12);color:#0ab39c;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;flex-shrink:0">
                {{ $i + 1 }}
              </div>
              <div style="font-size:13px;font-weight:600;color:#212529">{{ $row['label'] }}</div>
            </div>
            <div style="font-size:13px;font-weight:800;color:#0ab39c;font-family:'JetBrains Mono',monospace">
              {{ number_format($row['montant'], 0, ',', ' ') }} FCFA
            </div>
          </div>
          @endforeach
        </div>

        {{-- Total --}}
        <div style="background:rgba(64,81,137,.06);border-radius:10px;padding:12px;margin-top:10px;display:flex;justify-content:space-between;align-items:center">
          <span style="font-size:13px;font-weight:700;color:#495057">Total</span>
          <span style="font-size:18px;font-weight:900;color:#405189;font-family:'JetBrains Mono',monospace">
            {{ number_format(count($previewAvance) * ($customer->montant_engagement ?? 0), 0, ',', ' ') }} FCFA
          </span>
        </div>
      </div>
      @endif

      {{-- Mode de paiement --}}
      <div style="font-size:12px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px">
        Mode de paiement
      </div>

      <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:16px">
        <div wire:click="selectAvanceMode('espece')"
             style="display:flex;align-items:center;gap:10px;border:2px solid {{ $avanceMode === 'espece' ? '#405189' : '#e9ebec' }};background:{{ $avanceMode === 'espece' ? 'rgba(64,81,137,.06)' : '#fff' }};border-radius:12px;padding:12px;cursor:pointer;transition:all .2s">
          <div style="width:36px;height:36px;border-radius:9px;background:rgba(247,184,75,.1);display:flex;align-items:center;justify-content:center;font-size:18px;color:#d4a843;flex-shrink:0">
            <i class="ri-money-dollar-circle-line"></i>
          </div>
          <div style="font-size:13px;font-weight:700;color:{{ $avanceMode === 'espece' ? '#405189' : '#212529' }}">Espèces</div>
          <div style="margin-left:auto;width:20px;height:20px;border-radius:50%;border:2px solid {{ $avanceMode === 'espece' ? '#405189' : '#e9ebec' }};background:{{ $avanceMode === 'espece' ? '#405189' : 'transparent' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
            @if($avanceMode === 'espece')<i class="ri-check-line" style="color:#fff;font-size:11px"></i>@endif
          </div>
        </div>

        <div wire:click="selectAvanceMode('mobile_money')"
             style="display:flex;align-items:center;gap:10px;border:2px solid {{ $avanceMode === 'mobile_money' ? '#405189' : '#e9ebec' }};background:{{ $avanceMode === 'mobile_money' ? 'rgba(64,81,137,.06)' : '#fff' }};border-radius:12px;padding:12px;cursor:pointer;transition:all .2s">
          <div style="width:36px;height:36px;border-radius:9px;background:rgba(10,179,156,.1);display:flex;align-items:center;justify-content:center;font-size:18px;color:#0ab39c;flex-shrink:0">
            <i class="ri-smartphone-line"></i>
          </div>
          <div style="font-size:13px;font-weight:700;color:{{ $avanceMode === 'mobile_money' ? '#405189' : '#212529' }}">Mobile Money / Carte</div>
          <div style="margin-left:auto;width:20px;height:20px;border-radius:50%;border:2px solid {{ $avanceMode === 'mobile_money' ? '#405189' : '#e9ebec' }};background:{{ $avanceMode === 'mobile_money' ? '#405189' : 'transparent' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
            @if($avanceMode === 'mobile_money')<i class="ri-check-line" style="color:#fff;font-size:11px"></i>@endif
          </div>
        </div>
      </div>

      @if($errorAvance)
      <div style="font-size:12px;color:#f06548;font-weight:600">
        <i class="ri-error-warning-line me-1"></i>{{ $errorAvance }}
      </div>
      @endif

    </div>

    <div class="cot-modal-footer" style="border-top:1px solid var(--border)">
      <button class="cot-footer-recla" style="border-color:rgba(135,138,153,.3);color:var(--muted)" wire:click="closeAvance">
        <i class="ri-close-line"></i> Annuler
      </button>
      <button class="cot-footer-pay"
              wire:click="submitAvance"
              wire:loading.attr="disabled">
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
<div id="cot-recla-overlay" class="cot-modal-overlay" wire:ignore.self>
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
          <input type="text"
                 class="f-input-sm"
                 wire:model.lazy="reclaTitle"
                 placeholder="ex : Paiement non enregistré"/>
        </div>
        @if($errorReclaTitle)
        <div style="font-size:12px;color:#f06548;margin-top:4px;font-weight:600">{{ $errorReclaTitle }}</div>
        @endif
      </div>

      <div class="f-group">
        <label class="f-label-sm">Message <span style="color:#f06548">*</span></label>
        <textarea class="f-input-sm f-textarea-sm {{ $errorReclaMessage ? 'err' : '' }}"
                  wire:model.lazy="reclaMessage"
                  placeholder="Décrivez le problème en détail…"></textarea>
        @if($errorReclaMessage)
        <div style="font-size:12px;color:#f06548;margin-top:4px;font-weight:600">{{ $errorReclaMessage }}</div>
        @endif
      </div>

    </div>

    <div class="cot-modal-footer" style="border-top:1px solid var(--border)">
      <button class="cot-footer-recla" style="border-color:rgba(135,138,153,.3);color:var(--muted)" wire:click="closeRecla">
        <i class="ri-close-line"></i> Annuler
      </button>
      <button class="cot-footer-pay" wire:click="submitRecla" wire:loading.attr="disabled">
        <span wire:loading wire:target="submitRecla"><div class="spinner"></div></span>
        <span wire:loading.remove wire:target="submitRecla">
          <i class="ri-send-plane-line"></i> Envoyer
        </span>
      </button>
    </div>

  </div>
</div>

</div>{{-- /root div Livewire --}}


@push('scripts')
<script>
/* ══ Filtre statut ══ */
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

/* ══ Overlay helpers ══ */
function openOverlay(id)  { document.getElementById(id)?.classList.add('open');    document.body.style.overflow = 'hidden'; }
function closeOverlay(id) { document.getElementById(id)?.classList.remove('open'); document.body.style.overflow = ''; }

window.addEventListener('OpenDetailCot',    () => openOverlay('cot-detail-overlay'));
window.addEventListener('closeDetailCot',   () => closeOverlay('cot-detail-overlay'));
window.addEventListener('OpenPaiementModal',() => openOverlay('cot-paiement-overlay'));
window.addEventListener('closePaiementModal',()=> closeOverlay('cot-paiement-overlay'));
window.addEventListener('OpenAvanceModal',  () => openOverlay('cot-avance-overlay'));
window.addEventListener('closeAvanceModal', () => closeOverlay('cot-avance-overlay'));
window.addEventListener('OpenReclaModal',   () => openOverlay('cot-recla-overlay'));
window.addEventListener('closeReclaModal',  () => closeOverlay('cot-recla-overlay'));

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
  if (e.key === 'Escape') {
    @this.call('closeDetail');
    @this.call('closePaiement');
    @this.call('closeAvance');
    @this.call('closeRecla');
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

/* ══ SweetAlert simple ══ */
Livewire.on('swal:modalGetInfo_message_not_timer', (payload) => {
  const data = Array.isArray(payload) ? payload[0] : payload;
  if (typeof Swal !== 'undefined') {
    Swal.fire({ title: data.title, text: data.text, icon: data.type });
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
