<div>
<div class="page-content">
<div class="container-fluid">

  {{-- ══ PAGE HEADER ══════════════════════════════════════ --}}
  <div class="bl-page-header fu fu-1">
    <div>
      <h4><i class="ri-bar-chart-grouped-line me-2" style="color:var(--bl-primary)"></i>Bilan Financier</h4>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Bilan</li>
        </ol>
      </nav>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-soft-success btn-sm waves-effect">
        <i class="ri-file-excel-2-line me-1"></i> Exporter PDF
      </button>
    </div>
  </div>

  {{-- ══ FILTRE PÉRIODE ════════════════════════════════════ --}}
  <div class="bl-period-bar fu fu-1">
    <span class="bl-period-label"><i class="ri-calendar-line me-1"></i>Période :</span>
    <button class="bl-period-btn {{ $periode === 'mois' ? 'active' : '' }}" wire:click="setPeriode('mois')">Ce mois</button>
    <button class="bl-period-btn {{ $periode === 'trimestre' ? 'active' : '' }}" wire:click="setPeriode('trimestre')">Ce trimestre</button>
    <button class="bl-period-btn {{ $periode === 'annee' ? 'active' : '' }}" wire:click="setPeriode('annee')">Cette année</button>
    <div class="bl-period-sep"></div>
    <div class="bl-period-inputs">
      <input type="date" wire:model="dateDebut" max="{{ $dateFin }}">
      <span style="font-size:12px;color:var(--bl-muted)">→</span>
      <input type="date" wire:model="dateFin" min="{{ $dateDebut }}">
      <button class="bl-btn-apply" wire:click="appliquerCustom()">
        <i class="ri-filter-line"></i> Appliquer
      </button>
    </div>
    <div style="margin-left:auto;font-size:11px;color:var(--bl-muted)">
      <i class="ri-information-line me-1"></i>
      {{ \Carbon\Carbon::parse($dateDebut)->format('d M Y') }} au {{ \Carbon\Carbon::parse($dateFin)->format('d M Y') }}
    </div>
  </div>

  {{-- ══ RÉSUMÉ FINANCIER PRINCIPAL ═══════════════════════ --}}
  <div class="bl-summary-strip fu fu-2">

    <div class="bl-summary-card sc-solde">
      <i class="ri-scales-line sc-icon"></i>
      <div class="sc-inner">
        <div class="sc-label"><i class="ri-scales-line"></i>Solde net de la période</div>
        <div class="sc-amount">{{ number_format($soldeNet, 0, ',', ' ') }} FCFA</div>
        <div class="sc-sub">
          @if($soldeNet >= 0)
            <i class="ri-arrow-up-line"></i> Excédent
          @else
            <i class="ri-arrow-down-line"></i> Déficit
          @endif
          · Entrées – Sorties
        </div>
      </div>
    </div>

    <div class="bl-summary-card sc-entrees">
      <i class="ri-arrow-down-circle-line sc-icon"></i>
      <div class="sc-inner">
        <div class="sc-label"><i class="ri-arrow-down-circle-line"></i>Total entrées</div>
        <div class="sc-amount">{{ number_format($totalEntrees, 0, ',', ' ') }} FCFA</div>
        <div class="sc-sub">Paiements validés + autres recettes</div>
      </div>
    </div>

    <div class="bl-summary-card sc-sorties">
      <i class="ri-arrow-up-circle-line sc-icon"></i>
      <div class="sc-inner">
        <div class="sc-label"><i class="ri-arrow-up-circle-line"></i>Total sorties</div>
        <div class="sc-amount">{{ number_format($totalSorties, 0, ',', ' ') }} FCFA</div>
        <div class="sc-sub">Dépenses + remboursements</div>
      </div>
    </div>

  </div>

  {{-- ══ KPI GRID ═══════════════════════════════════════════ --}}
  <div class="bl-kpi-grid fu fu-3">
    <div class="bl-kpi bk-cotisation">
      <div class="bki-icon" style="background:rgba(64,81,137,.10);color:#405189"><i class="ri-file-list-3-line"></i></div>
      <div>
        <div class="bki-label">Cotisations</div>
        <div class="bki-val">{{ $nbCotisations }}</div>
        <div class="bki-sub">Sur la période</div>
      </div>
    </div>
    <div class="bl-kpi bk-paiement">
      <div class="bki-icon" style="background:rgba(10,179,156,.10);color:#0ab39c"><i class="ri-bank-card-line"></i></div>
      <div>
        <div class="bki-label">Paiements validés</div>
        <div class="bki-val">{{ $nbPaiements }}</div>
        <div class="bki-sub">Sur la période</div>
      </div>
    </div>
    <div class="bl-kpi bk-depense">
      <div class="bki-icon" style="background:rgba(240,101,72,.10);color:#f06548"><i class="ri-shopping-cart-line"></i></div>
      <div>
        <div class="bki-label">Dépenses</div>
        <div class="bki-val" style="font-size:15px">{{ number_format($totalDepenses, 0, ',', ' ') }} F</div>
        <div class="bki-sub">Sur la période</div>
      </div>
    </div>
    <div class="bl-kpi bk-fidele">
      <div class="bki-icon" style="background:rgba(101,89,204,.10);color:#6559cc"><i class="ri-user-heart-line"></i></div>
      <div>
        <div class="bki-label">Fidèles actifs</div>
        <div class="bki-val">{{ $nbFidelesActif }}</div>
        <div class="bki-sub">Avec cotisation</div>
      </div>
    </div>
  </div>

  {{-- ══ GRAPHE ÉVOLUTION 12 MOIS (pleine largeur) ══════════ --}}
  <div class="bl-chart-full fu fu-4">
    <div class="bl-chart-full-header">
      <div>
        <p class="bl-chart-full-title">Évolution financière — 12 derniers mois</p>
        <p class="bl-chart-full-sub">Entrées, sorties et solde net mensuel</p>
      </div>
      <div class="bl-chart-legend">
        <span class="bl-legend-entree">Entrées</span>
        <span class="bl-legend-sortie">Sorties</span>
        <span class="bl-legend-net">Solde net</span>
      </div>
    </div>
    <canvas id="chartEvolution" height="100"></canvas>
  </div>

  {{-- ══ GRILLE PRINCIPALE ══════════════════════════════════ --}}
  <div class="bl-main-grid fu fu-5">

    {{-- ── Taux de recouvrement par type ──────────────────── --}}
    <div class="bl-card">
      <div class="bl-card-header">
        <div>
          <p class="bch-title">Recouvrement par type de cotisation</p>
          <p class="bch-sub">Montants dus vs perçus sur la période</p>
        </div>
        <span class="bch-badge" style="background:rgba(64,81,137,.08);color:#405189">Période</span>
      </div>
      <div class="bl-card-body">
        @if($tauxRecouvrement->count())
        <div class="bl-progress-list">
          @foreach($tauxRecouvrement as $item)
          @php
            $color = $item['taux'] >= 80 ? '#0ab39c' : ($item['taux'] >= 50 ? '#f7b84b' : '#f06548');
          @endphp
          <div class="bl-progress-item">
            <div class="bpi-header">
              <div>
                <span class="bpi-label">{{ $item['libelle'] }}</span>
                <span style="font-size:10px;color:var(--bl-muted);margin-left:6px">{{ $item['count'] }} cotisations</span>
              </div>
              <span class="bpi-pct" style="color:{{ $color }}">{{ $item['taux'] }}%</span>
            </div>
            <div class="bpi-track">
              <div class="bpi-fill" style="width:{{ $item['taux'] }}%;background:{{ $color }}"></div>
            </div>
            <div class="bpi-sub">
              Perçu : <strong>{{ number_format($item['paye'], 0, ',', ' ') }} FCFA</strong>
              / Dû : {{ number_format($item['du'], 0, ',', ' ') }} FCFA
              · <span style="color:#0ab39c">{{ $item['ajour'] }} à jour</span>
              · <span style="color:#f7b84b">{{ $item['partiel'] }} partiel</span>
              · <span style="color:#f06548">{{ $item['retard'] }} retard</span>
            </div>
          </div>
          @endforeach
        </div>
        @else
        <div class="bl-empty"><i class="ri-file-search-line"></i><p>Aucune cotisation sur la période</p></div>
        @endif
      </div>
    </div>

    {{-- ── Colonne droite : donut statuts + dépenses types ── --}}
    <div style="display:flex;flex-direction:column;gap:16px">

      {{-- Statuts cotisations --}}
      <div class="bl-card">
        <div class="bl-card-header">
          <div>
            <p class="bch-title">Statuts des cotisations</p>
            <p class="bch-sub">Répartition sur la période</p>
          </div>
        </div>
        <div class="bl-card-body" style="display:flex;align-items:center;gap:16px">
          <div style="width:120px;flex-shrink:0"><canvas id="chartStatuts"></canvas></div>
          <div id="chartStatutsLegend" style="flex:1"></div>
        </div>
      </div>

      {{-- Dépenses par type --}}
      <div class="bl-card">
        <div class="bl-card-header">
          <div>
            <p class="bch-title">Dépenses par catégorie</p>
            <p class="bch-sub">Sur la période sélectionnée</p>
          </div>
        </div>
        <div class="bl-card-body" style="display:flex;align-items:center;gap:16px">
          <div style="width:120px;flex-shrink:0"><canvas id="chartDepenses"></canvas></div>
          <div id="chartDepensesLegend" style="flex:1"></div>
        </div>
      </div>

    </div>
  </div>

  {{-- ══ GRILLE SECONDAIRE : Transactions + Dépenses ════════ --}}
  <div class="bl-main-grid fu fu-6">

    {{-- Transactions récentes --}}
    <div class="bl-card">
      <div class="bl-card-header">
        <div>
          <p class="bch-title">Transactions récentes</p>
          <p class="bch-sub">10 dernières sur la période</p>
        </div>
        <span class="bch-badge" style="background:rgba(10,179,156,.10);color:#0ab39c">Journal</span>
      </div>
      @if($transactions->count())
      <div class="bl-tx-list">
        @foreach($transactions as $tx)
        <div class="bl-tx-item" wire:click="openDetailTx({{ $tx->id }})">
          <div class="bl-tx-icon {{ $tx->type === 'entree' ? 'ti-entree' : 'ti-sortie' }}">
            <i class="{{ $tx->type === 'entree' ? 'ri-arrow-down-line' : 'ri-arrow-up-line' }}"></i>
          </div>
          <div class="bl-tx-body">
            <div class="bl-tx-label">{{ $tx->libelle }}</div>
            <div class="bl-tx-meta">
              {{ $tx->date_transaction->format('d M Y H:i') }}
              · <span class="bl-pill {{ $tx->type === 'entree' ? 'bp-entree' : 'bp-sortie' }}" style="font-size:9px">{{ $tx->type === 'entree' ? 'Entrée' : 'Sortie' }}</span>
            </div>
          </div>
          <div class="bl-tx-amount {{ $tx->type === 'entree' ? 'ta-entree' : 'ta-sortie' }}">
            {{ $tx->type === 'entree' ? '+' : '-' }}{{ number_format($tx->montant, 0, ',', ' ') }} F
          </div>
        </div>
        @endforeach
      </div>
      @else
      <div class="bl-empty"><i class="ri-exchange-line"></i><p>Aucune transaction sur la période</p></div>
      @endif
    </div>

    {{-- Dépenses récentes --}}
    <div class="bl-card">
      <div class="bl-card-header">
        <div>
          <p class="bch-title">Dépenses récentes</p>
          <p class="bch-sub">8 dernières sur la période</p>
        </div>
        <span class="bch-badge" style="background:rgba(240,101,72,.10);color:#f06548">Sorties</span>
      </div>
      @if($depensesRecentes->count())
      <div class="bl-tx-list">
        @foreach($depensesRecentes as $dep)
        <div class="bl-tx-item" wire:click="openDetailDep({{ $dep->id }})">
          <div class="bl-tx-icon ti-sortie">
            <i class="ri-shopping-cart-line"></i>
          </div>
          <div class="bl-tx-body">
            <div class="bl-tx-label">{{ $dep->libelle ?? $dep->typeDepense?->libelle ?? '—' }}</div>
            <div class="bl-tx-meta">
              {{ $dep->date_depense->format('d M Y') }}
              @if($dep->typeDepense) · {{ $dep->typeDepense->libelle }} @endif
            </div>
          </div>
          <div class="bl-tx-amount ta-sortie">-{{ number_format($dep->montant, 0, ',', ' ') }} F</div>
        </div>
        @endforeach
      </div>
      @else
      <div class="bl-empty"><i class="ri-shopping-cart-line"></i><p>Aucune dépense sur la période</p></div>
      @endif
    </div>

  </div>

  {{-- ══ TABLEAU DÉPENSES PAR CATÉGORIE ══════════════════════ --}}
  @if($depensesParType->count())
  <div class="bl-card fu fu-6" style="margin-bottom:24px">
    <div class="bl-card-header">
      <div>
        <p class="bch-title">Récapitulatif des dépenses</p>
        <p class="bch-sub">Par catégorie — période sélectionnée</p>
      </div>
      <span class="bch-badge" style="background:rgba(240,101,72,.10);color:#f06548">
        Total : {{ number_format($totalDepenses, 0, ',', ' ') }} FCFA
      </span>
    </div>
    <div class="table-responsive">
      <table class="bl-table">
        <thead>
          <tr>
            <th>Catégorie</th>
            <th>Nb opérations</th>
            <th>Montant total</th>
            <th>Part du total</th>
          </tr>
        </thead>
        <tbody>
          @foreach($depensesParType as $dep)
          @php $pct = $totalDepenses > 0 ? round(($dep['total'] / $totalDepenses) * 100) : 0; @endphp
          <tr>
            <td><strong style="color:#212529">{{ $dep['libelle'] }}</strong></td>
            <td><span class="bl-pill" style="background:rgba(64,81,137,.08);color:#405189">{{ $dep['count'] }} opération(s)</span></td>
            <td><span style="font-family:var(--bl-mono);font-weight:800;color:#f06548">{{ number_format($dep['total'], 0, ',', ' ') }} FCFA</span></td>
            <td style="width:200px">
              <div style="display:flex;align-items:center;gap:8px">
                <div style="flex:1;height:6px;background:rgba(240,101,72,.12);border-radius:3px;overflow:hidden">
                  <div style="width:{{ $pct }}%;height:100%;background:#f06548;border-radius:3px"></div>
                </div>
                <span style="font-size:11px;font-weight:800;color:#f06548;min-width:32px">{{ $pct }}%</span>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif

</div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL DÉTAIL TRANSACTION / DÉPENSE
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalDetailBilan" tabindex="-1" aria-hidden="true" wire:ignore.self>
  <div class="modal-dialog" style="max-width:580px">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;">

      @if($detailTx)
      @php
        $tx = $detailTx;
        $isEntree = $tx->type === 'entree';
        $grad = $isEntree
            ? 'linear-gradient(130deg,#0a7a6a,#0ab39c)'
            : 'linear-gradient(130deg,#c43520,#f06548)';
        $icon = $isEntree ? 'ri-arrow-down-circle-line' : 'ri-arrow-up-circle-line';
      @endphp
      <div class="bl-modal-header" style="background:{{ $grad }}">
        <button class="bmh-close" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>
        <div class="bmh-inner">
          <div class="bmh-icon"><i class="{{ $icon }}"></i></div>
          <div>
            <div class="bmh-title">{{ $isEntree ? 'Transaction — Entrée' : 'Transaction — Sortie' }}</div>
            <div class="bmh-meta">
              <span><i class="ri-calendar-line"></i>{{ $tx->date_transaction->format('d M Y H:i') }}</span>
              <span><i class="ri-link-m"></i>Source : {{ $tx->source }}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="bl-modal-stats">
        <div class="bl-ms-box">
          <div class="bmsb-v" style="color:{{ $isEntree ? '#0ab39c' : '#f06548' }}">
            {{ $isEntree ? '+' : '-' }}{{ number_format($tx->montant, 0, ',', ' ') }} F
          </div>
          <div class="bmsb-l">Montant</div>
        </div>
        <div class="bl-ms-box">
          <div class="bmsb-v">
            <span class="bl-pill {{ $isEntree ? 'bp-entree' : 'bp-sortie' }}">
              <i class="{{ $isEntree ? 'ri-arrow-down-line' : 'ri-arrow-up-line' }}"></i>
              {{ $isEntree ? 'Entrée' : 'Sortie' }}
            </span>
          </div>
          <div class="bmsb-l">Type</div>
        </div>
        <div class="bl-ms-box">
          <div class="bmsb-v" style="font-size:11px;font-weight:700">{{ ucfirst($tx->source) }} #{{ $tx->source_id }}</div>
          <div class="bmsb-l">Source</div>
        </div>
      </div>
      <div style="overflow-y:auto;max-height:calc(90vh - 230px);">
        <div class="bl-modal-body">
          <div class="bl-section-title {{ $isEntree ? 'accent' : 'danger' }}">Détails</div>
          <div class="bl-detail-grid">
            <div class="bl-detail-item full">
              <div class="bdi-l"><i class="ri-file-text-line me-1"></i>Libellé</div>
              <div class="bdi-v" style="font-weight:600;color:var(--bl-text)">{{ $tx->libelle }}</div>
            </div>
            <div class="bl-detail-item">
              <div class="bdi-l"><i class="ri-calendar-line me-1"></i>Date</div>
              <div class="bdi-v">{{ $tx->date_transaction->format('d M Y à H:i') }}</div>
            </div>
            <div class="bl-detail-item">
              <div class="bdi-l"><i class="ri-money-cny-circle-line me-1"></i>Montant</div>
              <div class="bdi-v" style="color:{{ $isEntree ? '#0ab39c' : '#f06548' }};font-family:var(--bl-mono)">
                {{ $isEntree ? '+' : '-' }}{{ number_format($tx->montant, 0, ',', ' ') }} FCFA
              </div>
            </div>
            <div class="bl-detail-item">
              <div class="bdi-l"><i class="ri-link-m me-1"></i>Source</div>
              <div class="bdi-v">{{ ucfirst($tx->source) }} #{{ $tx->source_id }}</div>
            </div>
            <div class="bl-detail-item">
              <div class="bdi-l"><i class="ri-calendar-event-line me-1"></i>Enregistré le</div>
              <div class="bdi-v">{{ $tx->created_at->format('d M Y') }}</div>
            </div>
          </div>
        </div>
      </div>
      <div class="bl-modal-footer">
        <button class="btn-bl-secondary" data-bs-dismiss="modal"><i class="ri-close-line me-1"></i> Fermer</button>
      </div>

      @elseif($detailDep)
      @php $dep = $detailDep; @endphp
      <div class="bl-modal-header" style="background:linear-gradient(130deg,#c43520,#f06548)">
        <button class="bmh-close" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>
        <div class="bmh-inner">
          <div class="bmh-icon"><i class="ri-shopping-cart-line"></i></div>
          <div>
            <div class="bmh-title">Dépense</div>
            <div class="bmh-meta">
              <span><i class="ri-calendar-line"></i>{{ $dep->date_depense->format('d M Y') }}</span>
              @if($dep->typeDepense)
              <span><i class="ri-tag-line"></i>{{ $dep->typeDepense->libelle }}</span>
              @endif
            </div>
          </div>
        </div>
      </div>
      <div class="bl-modal-stats">
        <div class="bl-ms-box">
          <div class="bmsb-v" style="color:#f06548">-{{ number_format($dep->montant, 0, ',', ' ') }} F</div>
          <div class="bmsb-l">Montant</div>
        </div>
        <div class="bl-ms-box">
          <div class="bmsb-v" style="font-size:12px">{{ $dep->typeDepense?->libelle ?? '—' }}</div>
          <div class="bmsb-l">Catégorie</div>
        </div>
        <div class="bl-ms-box">
          <div class="bmsb-v" style="font-size:11px">{{ $dep->date_depense->format('d M Y') }}</div>
          <div class="bmsb-l">Date</div>
        </div>
      </div>
      <div style="overflow-y:auto;max-height:calc(90vh - 230px);">
        <div class="bl-modal-body">
          <div class="bl-section-title danger">Détails de la dépense</div>
          <div class="bl-detail-grid">
            <div class="bl-detail-item full">
              <div class="bdi-l"><i class="ri-file-text-line me-1"></i>Libellé</div>
              <div class="bdi-v" style="font-weight:600;color:var(--bl-text)">{{ $dep->libelle ?? $dep->typeDepense?->libelle ?? '—' }}</div>
            </div>
            <div class="bl-detail-item">
              <div class="bdi-l"><i class="ri-money-cny-circle-line me-1"></i>Montant</div>
              <div class="bdi-v" style="color:#f06548;font-family:var(--bl-mono)">{{ number_format($dep->montant, 0, ',', ' ') }} FCFA</div>
            </div>
            <div class="bl-detail-item">
              <div class="bdi-l"><i class="ri-tag-line me-1"></i>Catégorie</div>
              <div class="bdi-v">{{ $dep->typeDepense?->libelle ?? '—' }}</div>
            </div>
            <div class="bl-detail-item">
              <div class="bdi-l"><i class="ri-calendar-line me-1"></i>Date</div>
              <div class="bdi-v">{{ $dep->date_depense->format('d M Y') }}</div>
            </div>
            @if(isset($dep->note) && $dep->note)
            <div class="bl-detail-item full">
              <div class="bdi-l"><i class="ri-sticky-note-line me-1"></i>Note</div>
              <div class="bdi-v" style="font-weight:500;color:var(--bl-text)">{{ $dep->note }}</div>
            </div>
            @endif
          </div>
        </div>
      </div>
      <div class="bl-modal-footer">
        <button class="btn-bl-secondary" data-bs-dismiss="modal"><i class="ri-close-line me-1"></i> Fermer</button>
      </div>
      @endif

    </div>
  </div>
</div>

</div>


@push('styles')
<link href="{{ asset('assets/css/bilan.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const GRAPH = @json($graphData);

/* ── Évolution 12 mois ──────────────────────────────────── */
(function() {
  const ctx = document.getElementById('chartEvolution');
  if (!ctx) return;
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: GRAPH.evolution.labels,
      datasets: [
        {
          label: 'Entrées',
          data: GRAPH.evolution.entrees,
          backgroundColor: 'rgba(10,179,156,.18)',
          borderColor: '#0ab39c', borderWidth: 2,
          borderRadius: 5, borderSkipped: false,
        },
        {
          label: 'Sorties',
          data: GRAPH.evolution.sorties,
          backgroundColor: 'rgba(240,101,72,.15)',
          borderColor: '#f06548', borderWidth: 2,
          borderRadius: 5, borderSkipped: false,
        },
        {
          label: 'Solde net',
          data: GRAPH.evolution.nets,
          type: 'line',
          borderColor: '#405189', backgroundColor: 'rgba(64,81,137,.06)',
          borderWidth: 2.5, pointBackgroundColor: '#405189', pointRadius: 4,
          fill: true, tension: 0.4, yAxisID: 'y',
        },
      ]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        y: { ticks: { font:{size:10}, callback: v => v >= 1000 ? (v/1000)+'k' : v }, grid: { color:'rgba(0,0,0,.04)' } },
        x: { ticks: { font:{size:10} }, grid: { display:false } },
      }
    }
  });
})();

/* ── Statuts cotisations ────────────────────────────────── */
(function() {
  const ctx = document.getElementById('chartStatuts');
  if (!ctx) return;
  const colors = ['#0ab39c','#f7b84b','#f06548'];
  const labels = GRAPH.statuts_cotisation.labels;
  const vals   = GRAPH.statuts_cotisation.vals;
  new Chart(ctx, {
    type: 'doughnut',
    data: { labels, datasets: [{ data: vals, backgroundColor: colors, borderWidth: 2, borderColor:'#fff' }] },
    options: { responsive:true, cutout:'70%', plugins:{ legend:{ display:false } } }
  });
  const leg = document.getElementById('chartStatutsLegend');
  if (leg) {
    leg.innerHTML = labels.map((l, i) => `
      <div style="display:flex;align-items:center;gap:7px;margin-bottom:8px">
        <span style="width:10px;height:10px;border-radius:3px;background:${colors[i]};flex-shrink:0"></span>
        <div>
          <div style="font-size:12px;font-weight:700;color:#212529">${l}</div>
          <div style="font-size:11px;color:#878a99">${vals[i]} cotisation${vals[i]>1?'s':''}</div>
        </div>
      </div>`).join('');
  }
})();

/* ── Dépenses par type ──────────────────────────────────── */
(function() {
  const ctx = document.getElementById('chartDepenses');
  if (!ctx) return;
  const colors = ['#f06548','#f7b84b','#405189','#0ab39c','#d4a843','#299cdb','#6559cc'];
  const labels = GRAPH.depenses_types.labels;
  const vals   = GRAPH.depenses_types.vals;
  if (!vals.length || vals.every(v => v === 0)) {
    ctx.parentElement.innerHTML = '<div style="text-align:center;padding:20px;color:#878a99;font-size:12px"><i class="ri-shopping-cart-line" style="font-size:24px;display:block;opacity:.3;margin-bottom:6px"></i>Aucune dépense</div>';
    return;
  }
  new Chart(ctx, {
    type: 'doughnut',
    data: { labels, datasets: [{ data: vals, backgroundColor: colors, borderWidth: 2, borderColor:'#fff' }] },
    options: { responsive:true, cutout:'70%', plugins:{ legend:{ display:false } } }
  });
  const leg = document.getElementById('chartDepensesLegend');
  if (leg) {
    leg.innerHTML = labels.map((l, i) => `
      <div style="display:flex;align-items:center;gap:7px;margin-bottom:8px">
        <span style="width:10px;height:10px;border-radius:3px;background:${colors[i % colors.length]};flex-shrink:0"></span>
        <div>
          <div style="font-size:12px;font-weight:700;color:#212529">${l}</div>
          <div style="font-size:11px;color:#878a99">${new Intl.NumberFormat('fr-FR').format(vals[i])} FCFA</div>
        </div>
      </div>`).join('');
  }
})();

/* ── Livewire events ────────────────────────────────────── */
Livewire.on('OpenModalModilEdit', ({ name_modal }) => {
  const el = document.getElementById(name_modal);
  if (el) bootstrap.Modal.getOrCreateInstance(el).show();
});
Livewire.on('closeModalModilEdit', ({ name_modal }) => {
  const el = document.getElementById(name_modal);
  if (el) bootstrap.Modal.getOrCreateInstance(el).hide();
});
Livewire.on('modalShowmessageToast', (payload) => {
  const data = Array.isArray(payload) ? payload[0] : payload;
  Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true })
    .fire({ icon: data.type, title: data.title });
});
</script>
@endpush


@push('styles')
<style>
    .feedback-text{ width:100%; margin-top:.25rem; font-size:.875em; color:#f06548; }
</style>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
@endpush

