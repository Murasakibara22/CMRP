<div>
<div class="page-content">
  <div class="container-fluid">

    {{-- ══ PAGE HEADER ═══════════════════════════════════════ --}}
    <div class="dash-header fade-up">
      <div class="page-title-area">
        <h4>Tableau de bord</h4>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="#">Mosquée</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
        </nav>
      </div>
      <div class="d-flex align-items-center gap-3">
        <div class="date-badge">
          <i class="ri-time-line"></i>
          <span id="live-time">--:--</span>
        </div>
        <div class="date-badge">
          <i class="ri-calendar-event-line"></i>
          <span>{{ now()->translatedFormat('l d F Y') }}</span>
        </div>
        <button class="btn btn-soft-primary btn-sm waves-effect" onclick="location.reload()">
          <i class="ri-refresh-line me-1"></i> Actualiser
        </button>
      </div>
    </div>

    {{-- ══ BANNIÈRE FINANCIÈRE ════════════════════════════════ --}}
    <div class="finance-banner fade-up fade-up-1">
      <i class="ri-mosque-line deco-icon"></i>
      <div class="banner-inner row align-items-center g-0">
        <div class="col-lg-4 mb-3 mb-lg-0">
          <div class="banner-label">Solde disponible · {{ now()->translatedFormat('F Y') }}</div>
          <div class="solde-title" id="banner-solde">--</div>
          <div class="solde-subtitle">Entrées moins dépenses du mois</div>
        </div>
        <div class="col-lg-8">
          <div class="mini-stats">
            <div class="mini-stat-item">
              <div class="msi-label"><i class="ri-arrow-down-circle-line me-1"></i>Entrées du mois</div>
              <div class="msi-value" id="banner-entrees">--</div>
            </div>
            <div class="mini-stat-item">
              <div class="msi-label"><i class="ri-arrow-up-circle-line me-1"></i>Dépenses du mois</div>
              <div class="msi-value" id="banner-sorties">--</div>
            </div>
            <div class="mini-stat-item">
              <div class="msi-label"><i class="ri-calendar-check-line me-1"></i>Cotisations collectées</div>
              <div class="msi-value" id="banner-cot">--</div>
            </div>
            <div class="mini-stat-item">
              <div class="msi-label"><i class="ri-group-line me-1"></i>Fidèles actifs</div>
              <div class="msi-value" id="banner-fideles">--</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ══ ACTIONS RAPIDES ════════════════════════════════════ --}}
    <div class="section-title fade-up fade-up-2">
      <span class="st-label">Actions rapides</span>
    </div>
    <div class="quick-actions fade-up fade-up-2">
      <a href="{{ route('admin.cotisations.index') }}" class="qa-btn">
        <div class="qa-icon" style="background:rgba(64,81,137,0.10);color:#405189"><i class="ri-add-circle-line"></i></div>
        <span class="qa-label">Nouvelle cotisation</span>
      </a>
      <a href="{{ route('admin.paiements.index') }}" class="qa-btn">
        <div class="qa-icon" style="background:rgba(10,179,156,0.10);color:#0ab39c"><i class="ri-money-cny-circle-line"></i></div>
        <span class="qa-label">Valider paiement</span>
      </a>
      <a href="{{ route('admin.depenses.index') }}" class="qa-btn">
        <div class="qa-icon" style="background:rgba(240,101,72,0.10);color:#f06548"><i class="ri-bill-line"></i></div>
        <span class="qa-label">Saisir dépense</span>
      </a>
      <a href="{{ route('admin.membres.index') }}" class="qa-btn">
        <div class="qa-icon" style="background:rgba(41,156,219,0.10);color:#299cdb"><i class="ri-user-add-line"></i></div>
        <span class="qa-label">Ajouter fidèle</span>
      </a>
      <a href="{{ route('admin.bilan.index') }}" class="qa-btn">
        <div class="qa-icon" style="background:rgba(247,184,75,0.12);color:#f7b84b"><i class="ri-bar-chart-line"></i></div>
        <span class="qa-label">État financier</span>
      </a>
      <a href="{{ route('admin.type-cotisations.index') }}" class="qa-btn">
        <div class="qa-icon" style="background:rgba(212,168,67,0.12);color:#d4a843"><i class="ri-settings-3-line"></i></div>
        <span class="qa-label">Nouveau type</span>
      </a>
    </div>

    {{-- ══ KPI CARDS ══════════════════════════════════════════ --}}
    <div class="section-title fade-up fade-up-3">
      <span class="st-label">Indicateurs clés</span>
      <a href="{{ route('admin.bilan.index') }}" class="st-link">Voir détails <i class="ri-arrow-right-s-line"></i></a>
    </div>
    <div class="row g-3 mb-4">
      @foreach([
        ['id'=>'kpi-solde',   'col'=>'col-xl-2 col-lg-4 col-sm-6'],
        ['id'=>'kpi-entrees', 'col'=>'col-xl-2 col-lg-4 col-sm-6'],
        ['id'=>'kpi-sorties', 'col'=>'col-xl-2 col-lg-4 col-sm-6'],
        ['id'=>'kpi-fideles', 'col'=>'col-xl-2 col-lg-4 col-sm-6'],
        ['id'=>'kpi-ajour',   'col'=>'col-xl-2 col-lg-4 col-sm-6'],
        ['id'=>'kpi-retard',  'col'=>'col-xl-2 col-lg-4 col-sm-6'],
      ] as $i => $kpi)
      <div class="{{ $kpi['col'] }} fade-up" style="animation-delay:{{ ($i + 3) * 0.05 }}s">
        <div class="kpi-card h-100" id="{{ $kpi['id'] }}">
          <div class="kpi-icon"></div>
          <div class="kpi-label">–</div>
          <div class="kpi-value">–</div>
          <div><span class="kpi-trend">–</span><div class="kpi-sub">–</div></div>
        </div>
      </div>
      @endforeach
    </div>

    {{-- ══ ROW 2 ═══════════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
      @if(auth()->user()?->hasPermission('PAIEMENT_SHOW'))
      <div class="col-xl-8 fade-up fade-up-5">
        <div class="chart-card h-100">
          <div class="cc-header">
            <div>
              <div class="cc-title">Flux financiers mensuels</div>
              <div class="cc-subtitle">Entrées vs Dépenses — 12 derniers mois</div>
            </div>
            <div class="cc-filter">
              <button class="filter-btn active" data-range="6">6M</button>
              <button class="filter-btn" data-range="12">1A</button>
            </div>
          </div>
          <div id="chart-flux"></div>
        </div>
      </div>
      @endif

      @if(auth()->user()?->hasPermission('FIDELE_SHOW'))
      <div class="col-xl-4 fade-up fade-up-6">
        <div class="statut-card h-100">
          <div class="sc-title">Statut des fidèles</div>
          <div class="sc-subtitle">Engagement mensuel · {{ now()->translatedFormat('F Y') }}</div>
          <div id="chart-statut"></div>
          <div class="statut-legend" id="statut-legend"></div>
          <div class="text-center mt-3">
            <a href="{{ route('admin.cotisations.index') }}" class="btn btn-soft-primary btn-sm waves-effect">
              <i class="ri-list-check me-1"></i> Voir tous les statuts
            </a>
          </div>
        </div>
      </div>
      @endif
    </div>

    {{-- ══ ROW 3 ═══════════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
      @if(auth()->user()?->hasPermission('TYPE_COTISATION_SHOW') )
      <div class="col-xl-5 fade-up fade-up-5">
        <div class="type-cot-card h-100">
          <div class="section-title mb-3">
            <span class="st-label">Types de cotisation</span>
            <a href="{{ route('admin.type-cotisations.index') }}" class="st-link">Gérer <i class="ri-arrow-right-s-line"></i></a>
          </div>
          <div id="types-cot-list"></div>
        </div>
      </div>
      @endif
      <div class="col-xl-4 fade-up fade-up-6">
        <div class="chart-card h-100">
          <div class="section-title mb-3"><span class="st-label">Collectes en cours</span></div>
          <div id="collectes-list"></div>
        </div>
      </div>
      <div class="col-xl-3 fade-up fade-up-7">
        <div class="chart-card h-100 text-center">
          <div class="cc-title mb-1">Objectif mensuel</div>
          <div class="cc-subtitle mb-2">Cotisations · {{ now()->translatedFormat('F Y') }}</div>
          <div id="chart-collecte"></div>
          <div class="mt-2">
            <div style="font-size:13px;color:var(--msq-muted);">Collecté</div>
            <div style="font-size:18px;font-weight:800;color:#212529;" id="cot-collecte-lbl">--</div>
            <div style="font-size:11px;color:var(--msq-muted);">sur <span id="cot-objectif-lbl">--</span></div>
          </div>
          <div class="mt-3">
            <a href="{{ route('admin.cotisations.index') }}" class="btn btn-soft-primary btn-sm w-100 waves-effect">Voir cotisations</a>
          </div>
        </div>
      </div>
    </div>

    {{-- ══ ROW 4 ═══════════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">
      @if(auth()->user()?->hasPermission('PAIEMENT_SHOW') )
      <div class="col-xl-7 fade-up fade-up-6">
        <div class="transactions-card h-100">
          <div class="section-title mb-3">
            <span class="st-label">Transactions récentes</span>
            <a href="{{ route('admin.bilan.index') }}" class="st-link">Tout voir <i class="ri-arrow-right-s-line"></i></a>
          </div>
          <ul class="tx-list" id="tx-list"></ul>
        </div>
      </div>
      @endif

      @if(auth()->user()?->hasPermission('DEPENSE_SHOW') )
      <div class="col-xl-5 fade-up fade-up-7">
        <div class="chart-card h-100">
          <div class="section-title mb-3">
            <span class="st-label">Dépenses par catégorie</span>
            <a href="{{ route('admin.depenses.index') }}" class="st-link">Voir tout <i class="ri-arrow-right-s-line"></i></a>
          </div>
          <div id="depenses-type-list"></div>
          <div class="d-flex align-items-center justify-content-between pt-3 mt-2 border-top">
            <span style="font-size:13px;font-weight:700;color:var(--msq-text);">Total dépenses du mois</span>
            <span style="font-size:16px;font-weight:800;color:var(--msq-danger);" id="total-dep-mois">--</span>
          </div>
        </div>
      </div>
      @endif
    </div>

    {{-- ══ ROW 5 ═══════════════════════════════════════════════ --}}
    @if(auth()->user()?->hasPermission('FIDELE_SHOW'))
    <div class="fade-up fade-up-8 mb-4">
      <div class="retard-card">
        <div class="section-title mb-3">
          <span class="st-label">Fidèles en retard de cotisation</span>
          <a href="{{ route('admin.cotisations.index') }}" class="st-link">Voir tous <i class="ri-arrow-right-s-line"></i></a>
        </div>
        <div class="table-responsive">
          <table class="retard-table">
            <thead>
              <tr><th>Fidèle</th><th>Statut</th><th>Retard</th><th>Montant dû</th><th>Action</th></tr>
            </thead>
            <tbody id="retard-tbody"></tbody>
          </table>
        </div>
        <div class="d-flex align-items-center justify-content-between pt-3 mt-2 border-top">
          <div style="font-size:12px;color:var(--msq-muted);">
            <i class="ri-information-line me-1"></i>
            Top 5 · Total : <strong style="color:#212529;">{{ $fidelesRetard }}</strong> fidèle(s) en retard
          </div>
          <a href="{{ route('admin.cotisations.index') }}?statut=en_retard" class="btn btn-soft-danger btn-sm waves-effect">
            <i class="ri-error-warning-line me-1"></i> Voir tous les retards
          </a>
        </div>
      </div>
    </div>
    @endif

  </div>
</div>
</div>


@push('styles')
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
<script>

const DATA = {
  solde:          {{ (int) $solde }},
  entrees:        {{ (int) $entreesMois }},
  sorties:        {{ (int) $sortiesMois }},
  cotisationMois: {{ (int) $cotisationMois }},
  cotisationObj:  {{ (int) $cotisationObj }},
  fidelesActifs:  {{ (int) $fidelesActifs }},
  moisLabel:      '{{ now()->translatedFormat("F Y") }}',

  fidelesTotal:   {{ (int) $fidelesTotal }},
  fidelesAJour:   {{ (int) $fidelesAJour }},
  fidelesPartiel: {{ (int) $fidelesPartiel }},
  fidelesRetard:  {{ (int) $fidelesRetard }},

  trendEntrees:   {{ $trendEntrees }},
  trendSorties:   {{ $trendSorties }},

  chartLabels:    @json($chartLabels),
  chartEntrees:   @json($chartEntrees),
  chartSorties:   @json($chartSorties),

  typesCotisation:  @json($typesCotisationJs),
  collectesEnCours: @json($collectesEnCoursJs),
  transactions:     @json($transactionsJs),
  depensesParType:  @json($depensesParTypeJs),
  retards:          @json($fidelesEnRetardJs),

  totalDepMois: {{ (int) $totalDepMois }},
};

/* Labels dynamiques */
document.getElementById('cot-collecte-lbl').textContent = new Intl.NumberFormat('fr-FR').format(DATA.cotisationMois) + ' FCFA';
document.getElementById('cot-objectif-lbl').textContent = new Intl.NumberFormat('fr-FR').format(DATA.cotisationObj) + ' FCFA';
document.getElementById('total-dep-mois').textContent   = new Intl.NumberFormat('fr-FR').format(DATA.totalDepMois) + ' FCFA';
const banFideles = document.getElementById('banner-fideles');
if (banFideles) banFideles.textContent = DATA.fidelesActifs;
</script>
<script src="{{ asset('assets/js/dashboard.js') }}"></script>
@endpush