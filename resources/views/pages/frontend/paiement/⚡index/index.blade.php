<div>
<main class="page-content">

  <div class="page-title">Mes Paiements</div>
  <div class="page-sub">Historique de tous vos paiements enregistrés.</div>

  {{-- ══ KPIs ═══════════════════════════════════════════════ --}}
  <div class="pay-kpi-strip">
    <div class="pay-kpi">
      <div class="pay-kpi-icon" style="background:rgba(10,179,156,.10);color:#0ab39c"><i class="ri-checkbox-circle-line"></i></div>
      <div class="pay-kpi-val">{{ $kpis['success'] }}</div>
      <div class="pay-kpi-label">Succès</div>
    </div>
    <div class="pay-kpi">
      <div class="pay-kpi-icon" style="background:rgba(247,184,75,.12);color:#f7b84b"><i class="ri-time-line"></i></div>
      <div class="pay-kpi-val">{{ $kpis['attente'] }}</div>
      <div class="pay-kpi-label">En attente</div>
    </div>
    <div class="pay-kpi">
      <div class="pay-kpi-icon" style="background:rgba(240,101,72,.10);color:#f06548"><i class="ri-close-circle-line"></i></div>
      <div class="pay-kpi-val">{{ $kpis['echec'] }}</div>
      <div class="pay-kpi-label">Échoué</div>
    </div>
    <div class="pay-kpi">
      <div class="pay-kpi-icon" style="background:rgba(64,81,137,.10);color:#405189"><i class="ri-money-cny-circle-line"></i></div>
      <div class="pay-kpi-val" style="font-size:14px">{{ number_format($kpis['total'], 0, ',', ' ') }}</div>
      <div class="pay-kpi-label">FCFA total</div>
    </div>
  </div>

  {{-- ══ FILTRES ═════════════════════════════════════════════ --}}
  <div class="pay-filters">
    <button class="pay-filter active" data-filter="tous" onclick="filterPay(this)">
      <i class="ri-list-check"></i> Tous
    </button>
    <button class="pay-filter" data-filter="success" onclick="filterPay(this)">
      <i class="ri-checkbox-circle-line"></i> Succès
    </button>
    <button class="pay-filter" data-filter="attente" onclick="filterPay(this)">
      <i class="ri-time-line"></i> En attente
    </button>
    <button class="pay-filter" data-filter="echec" onclick="filterPay(this)">
      <i class="ri-close-circle-line"></i> Échoué
    </button>
  </div>

  {{-- ══ LISTE ═══════════════════════════════════════════════ --}}
  <div class="pay-list card">

    @forelse($paiements as $p)
    @php
      $statutJs = match($p->statut) {
          'success'    => 'success',
          'en_attente' => 'attente',
          'echec'      => 'echec',
          'refund'     => 'echec',
          default      => 'attente',
      };
      [$iconClass, $iconBg, $iconColor, $amountColor] = match($p->statut) {
          'success'    => ['ri-checkbox-circle-line', 'rgba(10,179,156,.10)', '#0ab39c', '#0ab39c'],
          'en_attente' => ['ri-time-line',            'rgba(247,184,75,.12)', '#f7b84b', '#f7b84b'],
          'echec'      => ['ri-close-circle-line',    'rgba(240,101,72,.10)', '#f06548', '#f06548'],
          'refund'     => ['ri-refund-2-line',        'rgba(41,156,219,.12)', '#299cdb', '#299cdb'],
          default      => ['ri-time-line',            'rgba(247,184,75,.12)', '#f7b84b', '#f7b84b'],
      };
      $modeLabel = match($p->mode_paiement) {
          'mobile_money' => 'Mobile Money',
          'espece'       => 'Espèces',
          'virement'     => 'Virement',
          default        => '—',
      };
      $typeLabel   = $p->cotisation?->libelle ?? $p->cotisation?->typeCotisation?->libelle;
      $periodeLabel = ($p->cotisation?->mois && $p->cotisation?->annee)
          ? \Carbon\Carbon::create($p->cotisation->annee, $p->cotisation->mois)->translatedFormat('F Y')
          : $p->date_paiement->translatedFormat('F Y');
      $ref = $p->reference ?? 'PAY-' . str_pad($p->id, 6, '0', STR_PAD_LEFT);
      $pillClass = match($p->statut) {
          'success'    => 'pill-ok',
          'en_attente' => 'pill-warn',
          default      => 'pill-danger',
      };
      $pillLabel = match($p->statut) {
          'success'    => 'Succès',
          'en_attente' => 'En attente',
          'echec'      => 'Échoué',
          'refund'     => 'Remboursé',
          default      => '—',
      };
      $prefix = $p->statut === 'success' ? '+' : '';
    @endphp

    <div class="pay-item"
         data-statut="{{ $statutJs }}"
         wire:click="showDetail({{ $p->id }})"
         wire:key="pay-{{ $p->id }}"
         style="cursor:pointer">
      <div class="pay-icon" style="background:{{ $iconBg }};color:{{ $iconColor }}">
        <i class="{{ $iconClass }}"></i>
      </div>
      <div class="pay-body">
        <div class="pay-title">{{ $typeLabel }}{{ $periodeLabel !== '—' ? ' — ' . $periodeLabel : '' }}</div>
        <div class="pay-meta">
          {{ $p->date_paiement->format('d/m/Y H:i') }} · {{ $modeLabel }} ·
          <span class="pay-ref">{{ $ref }}</span>
        </div>
      </div>
      <div class="pay-right">
        <div class="pay-amount" style="color:{{ $amountColor }}">
          {{ $prefix }}{{ number_format($p->montant, 0, ',', ' ') }}
        </div>
        <span class="pill {{ $pillClass }} pay-pill">{{ $pillLabel }}</span>
      </div>
    </div>

    @empty
    <div style="text-align:center;padding:40px 20px;color:var(--muted)">
      <i class="ri-inbox-line" style="font-size:36px;display:block;margin-bottom:10px;opacity:.4"></i>
      <div style="font-size:14px;font-weight:600">Aucun paiement enregistré</div>
    </div>
    @endforelse

  </div>

  <div style="height:24px"></div>

</main>


{{-- ══════════════════════════════════════════════════════════
     MODAL DÉTAIL PAIEMENT
     Classe "open" rendue par Blade → zéro JS pour l'overlay
══════════════════════════════════════════════════════════ --}}
<div class="pay-modal-overlay {{ $detailId ? 'open' : '' }}" @if($detailId) wire:ignore.self @endif>

  <div class="pay-modal" wire:click.stop>

    @if($detailPaiement)
    @php
      $dp = $detailPaiement;

      $headerBg = match($dp->statut) {
          'success'    => 'linear-gradient(135deg,#089383,#0ab39c)',
          'en_attente' => 'linear-gradient(135deg,#c07a10,#f7b84b)',
          'echec'      => 'linear-gradient(135deg,#c0341a,#f06548)',
          'refund'     => 'linear-gradient(135deg,#1a6080,#299cdb)',
          default      => 'linear-gradient(135deg,#2d3a63,#405189)',
      };
      $headerIcon = match($dp->mode_paiement) {
          'mobile_money' => 'ri-smartphone-line',
          'espece'       => 'ri-money-dollar-circle-line',
          'virement'     => 'ri-bank-line',
          default        => 'ri-bank-card-line',
      };
      $statutLabel = match($dp->statut) {
          'success'    => 'Succès',
          'en_attente' => 'En attente',
          'echec'      => 'Échoué',
          'refund'     => 'Remboursé',
          default      => '—',
      };
      $amountColor = match($dp->statut) {
          'success' => '#0ab39c',
          'echec'   => '#f06548',
          'refund'  => '#299cdb',
          default   => '#f7b84b',
      };
      $statutPillCls = match($dp->statut) {
          'success'    => 'pill-ok',
          'en_attente' => 'pill-warn',
          default      => 'pill-danger',
      };
      $modeLabel = match($dp->mode_paiement) {
          'mobile_money' => 'Mobile Money',
          'espece'       => 'Espèces',
          'virement'     => 'Virement',
          default        => '—',
      };
      $typeLabel    = $dp->cotisation?->libelle ?? $dp->cotisation?->typeCotisation?->libelle ?? '—';
      $periodeLabel = ($dp->cotisation?->mois && $dp->cotisation?->annee)
          ? \Carbon\Carbon::create($dp->cotisation->annee, $dp->cotisation->mois)->translatedFormat('F Y')
          : '—';
      $ref = $dp->reference ?? 'PAY-' . str_pad($dp->id, 6, '0', STR_PAD_LEFT);
      $prefix = $dp->statut === 'success' ? '+' : '';

      // Validated by
      if ($dp->validated_by) {
          $validatedBy = 'Admin #' . $dp->validated_by;
          $validatedAt = $dp->validated_at?->format('d/m/Y H:i') ?? '—';
      } elseif ($dp->statut === 'success') {
          $validatedBy = 'Système automatique';
          $validatedAt = $dp->date_paiement->format('d/m/Y H:i');
      } else {
          $validatedBy = '—';
          $validatedAt = '—';
      }

      $metadata = $dp->metadata ? json_decode($dp->metadata, true) : [];
      $operateur = $metadata['operateur'] ?? '—';
      $note      = $metadata['note'] ?? ($dp->statut === 'en_attente' ? 'En attente de validation par un administrateur.' : '');
    @endphp

    {{-- Header --}}
    <div class="pay-modal-header" style="background:{{ $headerBg }}">
      <div class="pmh-inner">
        <div class="pmh-icon"><i class="{{ $headerIcon }}"></i></div>
        <div>
          <div class="pmh-title">Détail du paiement</div>
          <div class="pmh-ref">{{ $ref }}</div>
        </div>
      </div>
      <button class="pmh-close" wire:click="closeDetail">
        <i class="ri-close-line"></i>
      </button>
    </div>

    {{-- Corps --}}
    <div class="pay-modal-body">

      {{-- Montant central --}}
      <div class="pmd-amount-wrap">
        <div class="pmd-amount" style="color:{{ $amountColor }}">
          {{ $prefix }}{{ number_format($dp->montant, 0, ',', ' ') }} FCFA
        </div>
        <div class="pmd-statut">
          <span class="pill {{ $statutPillCls }}">{{ $statutLabel }}</span>
        </div>
      </div>

      {{-- Grille infos --}}
      <div class="pmd-grid">
        <div class="pmd-item">
          <div class="pmd-label"><i class="ri-tag-line"></i> Type</div>
          <div class="pmd-value">{{ $typeLabel }}</div>
        </div>
        <div class="pmd-item">
          <div class="pmd-label"><i class="ri-calendar-line"></i> Période</div>
          <div class="pmd-value">{{ $periodeLabel }}</div>
        </div>
        <div class="pmd-item">
          <div class="pmd-label"><i class="ri-smartphone-line"></i> Mode</div>
          <div class="pmd-value">{{ $modeLabel }}</div>
        </div>
        <div class="pmd-item">
          <div class="pmd-label"><i class="ri-bank-line"></i> Opérateur</div>
          <div class="pmd-value">{{ $operateur }}</div>
        </div>
        <div class="pmd-item pmd-full">
          <div class="pmd-label"><i class="ri-hashtag"></i> Référence</div>
          <div class="pmd-value pmd-mono">{{ $ref }}</div>
        </div>
        <div class="pmd-item">
          <div class="pmd-label"><i class="ri-time-line"></i> Date paiement</div>
          <div class="pmd-value">{{ $dp->date_paiement->format('d/m/Y H:i') }}</div>
        </div>
        <div class="pmd-item">
          <div class="pmd-label"><i class="ri-shield-check-line"></i> Validé par</div>
          <div class="pmd-value">{{ $validatedBy }}</div>
        </div>
        <div class="pmd-item">
          <div class="pmd-label"><i class="ri-calendar-check-line"></i> Validé le</div>
          <div class="pmd-value">{{ $validatedAt }}</div>
        </div>
        @if($note)
        <div class="pmd-item pmd-full">
          <div class="pmd-label"><i class="ri-information-line"></i> Note</div>
          <div class="pmd-value pmd-note">{{ $note }}</div>
        </div>
        @endif
      </div>

    </div>

    {{-- Footer --}}
    <div class="pay-modal-footer">
      <button class="btn-outline" style="height:46px;font-size:14px"
              wire:click="closeDetail">
        <i class="ri-close-line"></i> Fermer
      </button>
      @if($detailPaiement->statut === 'success')
      <button class="btn-main" style="height:46px;font-size:14px"
              wire:click="telechargerRecu({{ $detailPaiement->id }})"
              wire:loading.attr="disabled"
              wire:target="telechargerRecu">
        <span wire:loading wire:target="telechargerRecu" class="spinner-border spinner-border-sm me-1"></span>
        <i class="ri-download-2-line" wire:loading.remove wire:target="telechargerRecu"></i>
        <span wire:loading.remove wire:target="telechargerRecu"> Télécharger le reçu</span>
        <span wire:loading wire:target="telechargerRecu"> Génération…</span>
      </button>
      @else
      <button class="btn-main" style="height:46px;font-size:14px;opacity:.5;cursor:not-allowed" disabled
              title="Reçu disponible uniquement pour les paiements validés">
        <i class="ri-download-2-line"></i> Reçu indisponible
      </button>
      @endif
    </div>

    @endif
  </div>
</div>

{{-- Zone impression supprimée — le PDF est généré côté serveur via DomPDF --}}

</div>{{-- /root Livewire --}}


@push('scripts')
<script>
/* ── Filtre statut JS ── */
function filterPay(btn) {
  document.querySelectorAll('.pay-filter').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  const f = btn.dataset.filter;
  document.querySelectorAll('.pay-item').forEach(item => {
    item.style.display = (f === 'tous' || item.dataset.statut === f) ? '' : 'none';
  });
}

// une fonction pour closeDetatil qui vas ecouter un event de livewire et fermer le modal selon son id
window.addEventListener('closePayDetail', event => {
        //pas de fonction ph mais plutot une fermeture ici en js ex un display none sur le modal
        const modalOverlay = document.querySelector('.pay-modal-overlay');
        if (modalOverlay) {
                modalOverlay.classList.remove('open');
                @this.set('detailId', null); // reset detailId pour éviter les problèmes à l'ouverture suivante
        }

});


window.addEventListener('OpenPayDetail', event => {
        //pas de fonction ph mais plutot une fermeture ici en js ex un display none sur le modal
        const modalOverlay = document.querySelector('.pay-modal-overlay');
        if (modalOverlay) {
                modalOverlay.classList.add('open');
        }

});

/* ── Impression retirée — PDF généré via DomPDF (téléchargerRecu) ── */
</script>
@endpush
