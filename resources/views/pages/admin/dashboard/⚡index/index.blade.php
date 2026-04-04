
<div>


<div class="page-content">
  <div class="container-fluid">

    {{-- ══════════════════════════════════════════════════════
         PAGE HEADER
    ══════════════════════════════════════════════════════ --}}
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
        {{-- Heure en temps réel --}}
        <div class="date-badge">
          <i class="ri-time-line"></i>
          <span id="live-time">--:--</span>
        </div>
        {{-- Date --}}
        <div class="date-badge">
          <i class="ri-calendar-event-line"></i>
          <span>{{ now()->translatedFormat('l d F Y') }}</span>
        </div>
        {{-- Bouton rafraîchir --}}
        <button class="btn btn-soft-primary btn-sm waves-effect" onclick="location.reload()">
          <i class="ri-refresh-line me-1"></i> Actualiser
        </button>
      </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         BANNIÈRE FINANCIÈRE
    ══════════════════════════════════════════════════════ --}}
    <div class="finance-banner fade-up fade-up-1">
      <i class="ri-mosque-line deco-icon"></i>
      <div class="banner-inner row align-items-center g-0">
        {{-- Solde --}}
        <div class="col-lg-4 mb-3 mb-lg-0">
          <div class="banner-label">Solde disponible · Avril 2025</div>
          <div class="solde-title" id="banner-solde">--</div>
          <div class="solde-subtitle">Entrées moins dépenses du mois</div>
        </div>

        {{-- Mini stats --}}
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
              <div class="msi-value">347</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         ACTIONS RAPIDES
    ══════════════════════════════════════════════════════ --}}
    <div class="section-title fade-up fade-up-2">
      <span class="st-label">Actions rapides</span>
    </div>

    <div class="quick-actions fade-up fade-up-2">
      <a href="#" class="qa-btn">
        <div class="qa-icon" style="background:rgba(64,81,137,0.10);color:#405189">
          <i class="ri-add-circle-line"></i>
        </div>
        <span class="qa-label">Nouvelle cotisation</span>
      </a>
      <a href="#" class="qa-btn">
        <div class="qa-icon" style="background:rgba(10,179,156,0.10);color:#0ab39c">
          <i class="ri-money-cny-circle-line"></i>
        </div>
        <span class="qa-label">Valider paiement</span>
      </a>
      <a href="#" class="qa-btn">
        <div class="qa-icon" style="background:rgba(240,101,72,0.10);color:#f06548">
          <i class="ri-bill-line"></i>
        </div>
        <span class="qa-label">Saisir dépense</span>
      </a>
      <a href="#" class="qa-btn">
        <div class="qa-icon" style="background:rgba(41,156,219,0.10);color:#299cdb">
          <i class="ri-user-add-line"></i>
        </div>
        <span class="qa-label">Ajouter fidèle</span>
      </a>
      <a href="#" class="qa-btn">
        <div class="qa-icon" style="background:rgba(247,184,75,0.12);color:#f7b84b">
          <i class="ri-bar-chart-line"></i>
        </div>
        <span class="qa-label">État financier</span>
      </a>
      <a href="#" class="qa-btn">
        <div class="qa-icon" style="background:rgba(212,168,67,0.12);color:#d4a843">
          <i class="ri-settings-3-line"></i>
        </div>
        <span class="qa-label">Nouveau type</span>
      </a>
    </div>

    {{-- ══════════════════════════════════════════════════════
         KPI CARDS — 6 indicateurs
    ══════════════════════════════════════════════════════ --}}
    <div class="section-title fade-up fade-up-3">
      <span class="st-label">Indicateurs clés</span>
      <a href="#" class="st-link">
        Voir détails <i class="ri-arrow-right-s-line"></i>
      </a>
    </div>

    <div class="row g-3 mb-4">
      {{-- Les 6 cards KPI sont peuplées dynamiquement par dashboard.js --}}
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
          <div>
            <span class="kpi-trend">–</span>
            <div class="kpi-sub">–</div>
          </div>
        </div>
      </div>
      @endforeach
    </div>

    {{-- ══════════════════════════════════════════════════════
         ROW 2 : Graphique flux + Statut fidèles
    ══════════════════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

      {{-- Graphique Flux Mensuels --}}
      <div class="col-xl-8 fade-up fade-up-5">
        <div class="chart-card h-100">
          <div class="cc-header">
            <div>
              <div class="cc-title">Flux financiers mensuels</div>
              <div class="cc-subtitle">Entrées vs Dépenses — 6 derniers mois</div>
            </div>
            <div class="cc-filter">
              <button class="filter-btn active">6M</button>
              <button class="filter-btn">1A</button>
              <button class="filter-btn">2A</button>
            </div>
          </div>
          <div id="chart-flux"></div>
        </div>
      </div>

      {{-- Statut fidèles donut --}}
      <div class="col-xl-4 fade-up fade-up-6">
        <div class="statut-card h-100">
          <div class="sc-title">Statut des fidèles</div>
          <div class="sc-subtitle">Engagement mensuel · Avril 2025</div>

          {{-- Donut chart --}}
          <div id="chart-statut"></div>

          {{-- Legend --}}
          <div class="statut-legend" id="statut-legend"></div>

          {{-- Lien --}}
          <div class="text-center mt-3">
            <a href="#" class="btn btn-soft-primary btn-sm waves-effect">
              <i class="ri-list-check me-1"></i> Voir tous les statuts
            </a>
          </div>
        </div>
      </div>

    </div>

    {{-- ══════════════════════════════════════════════════════
         ROW 3 : Types cotisation + Collectes en cours + Collecte radial
    ══════════════════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

      {{-- Types de cotisation --}}
      <div class="col-xl-5 fade-up fade-up-5">
        <div class="type-cot-card h-100">
          <div class="section-title mb-3">
            <span class="st-label">Types de cotisation</span>
            <a href="#" class="st-link">
              Gérer <i class="ri-arrow-right-s-line"></i>
            </a>
          </div>
          <div id="types-cot-list"></div>
        </div>
      </div>

      {{-- Collectes en cours --}}
      <div class="col-xl-4 fade-up fade-up-6">
        <div class="chart-card h-100">
          <div class="section-title mb-3">
            <span class="st-label">Collectes en cours</span>
          </div>
          <div id="collectes-list"></div>
        </div>
      </div>

      {{-- Radial collecte mensuelle --}}
      <div class="col-xl-3 fade-up fade-up-7">
        <div class="chart-card h-100 text-center">
          <div class="cc-title mb-1">Objectif mensuel</div>
          <div class="cc-subtitle mb-2">Cotisations · Avril 2025</div>
          <div id="chart-collecte"></div>
          <div class="mt-2">
            <div style="font-size:13px;color:var(--msq-muted);">Collecté</div>
            <div style="font-size:18px;font-weight:800;color:#212529;" id="cot-collecte-lbl">2 650 000 FCFA</div>
            <div style="font-size:11px;color:var(--msq-muted);">sur 3 500 000 FCFA</div>
          </div>
          <div class="mt-3">
            <a href="#" class="btn btn-soft-primary btn-sm w-100 waves-effect">
              Voir cotisations
            </a>
          </div>
        </div>
      </div>

    </div>

    {{-- ══════════════════════════════════════════════════════
         ROW 4 : Transactions récentes + Dépenses par type
    ══════════════════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

      {{-- Transactions récentes --}}
      <div class="col-xl-7 fade-up fade-up-6">
        <div class="transactions-card h-100">
          <div class="section-title mb-3">
            <span class="st-label">Transactions récentes</span>
            <a href="#" class="st-link">
              Tout voir <i class="ri-arrow-right-s-line"></i>
            </a>
          </div>
          <ul class="tx-list" id="tx-list">
            {{-- Peuplé par dashboard.js --}}
          </ul>
        </div>
      </div>

      {{-- Dépenses par type --}}
      <div class="col-xl-5 fade-up fade-up-7">
        <div class="chart-card h-100">
          <div class="section-title mb-3">
            <span class="st-label">Dépenses par catégorie</span>
            <a href="#" class="st-link">
              Voir tout <i class="ri-arrow-right-s-line"></i>
            </a>
          </div>
          <div id="depenses-type-list"></div>

          {{-- Total --}}
          <div class="d-flex align-items-center justify-content-between pt-3 mt-2 border-top">
            <span style="font-size:13px;font-weight:700;color:var(--msq-text);">Total dépenses du mois</span>
            <span style="font-size:16px;font-weight:800;color:var(--msq-danger);">371 500 FCFA</span>
          </div>
        </div>
      </div>

    </div>

    {{-- ══════════════════════════════════════════════════════
         ROW 5 : Fidèles en retard
    ══════════════════════════════════════════════════════ --}}
    <div class="fade-up fade-up-8 mb-4">
      <div class="retard-card">
        <div class="section-title mb-3">
          <span class="st-label">Fidèles en retard de cotisation</span>
          <a href="#" class="st-link">
            Voir tous <i class="ri-arrow-right-s-line"></i>
          </a>
        </div>

        <div class="table-responsive">
          <table class="retard-table">
            <thead>
              <tr>
                <th>Fidèle</th>
                <th>Statut</th>
                <th>Retard</th>
                <th>Montant dû</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="retard-tbody">
              {{-- Peuplé par dashboard.js --}}
            </tbody>
          </table>
        </div>

        {{-- Résumé bas --}}
        <div class="d-flex align-items-center justify-content-between pt-3 mt-2 border-top">
          <div style="font-size:12px;color:var(--msq-muted);">
            <i class="ri-information-line me-1"></i>
            Affichage des 5 premiers retards. Total : <strong style="color:#212529;">65 fidèles</strong> concernés.
          </div>
          <a href="#" class="btn btn-soft-danger btn-sm waves-effect">
            <i class="ri-error-warning-line me-1"></i> Voir tous les retards
          </a>
        </div>
      </div>
    </div>

  </div>{{-- /container-fluid --}}
</div>{{-- /page-content --}}


</div>


@push('scripts')
<script src="{{ asset('assets/js/dashboard.js') }}"></script>
@endpush


@push('styles')
<link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" type="text/css" />
@endpush