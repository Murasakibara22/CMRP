<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Paiements – ISL Mosquée</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Remix Icons -->
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
  <!-- CSS spécifique paiement -->
  <link href="{{ asset('assets/css/paiements.css') }}" rel="stylesheet">
</head>
<body>

<div class="page-content">
<div class="container-fluid" style="padding:24px">

  <!-- ══ PAGE HEADER ══════════════════════════════════════ -->
  <div class="pay-page-header fu fu-1">
    <div>
      <h4>Paiements</h4>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="#" style="color:var(--pay-muted);font-size:12px">Dashboard</a></li>
          <li class="breadcrumb-item active" style="font-size:12px;color:var(--pay-primary)">Paiements</li>
        </ol>
      </nav>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-soft-success btn-sm waves-effect" style="font-size:12px;font-weight:700;border:1.5px solid rgba(10,179,156,.3);background:rgba(10,179,156,.08);color:#0ab39c;border-radius:8px;padding:7px 14px">
        <i class="ri-file-excel-2-line me-1"></i> Exporter
      </button>
    </div>
  </div>

  <!-- ══ KPI STRIP ════════════════════════════════════════ -->
  <div class="pay-kpi-strip">
    <div class="pay-kpi pk-total fu fu-1">
      <div class="pki-icon" style="background:rgba(64,81,137,.10);color:#405189"><i class="ri-bank-card-line"></i></div>
      <div>
        <div class="pki-label">Total</div>
        <div class="pki-val" id="kpi-total">—</div>
        <div class="pki-sub">Tous paiements</div>
      </div>
    </div>
    <div class="pay-kpi pk-success fu fu-2">
      <div class="pki-icon" style="background:rgba(10,179,156,.10);color:#0ab39c"><i class="ri-checkbox-circle-line"></i></div>
      <div>
        <div class="pki-label">Validés</div>
        <div class="pki-val" id="kpi-success">—</div>
        <div class="pki-sub">Confirmés</div>
      </div>
    </div>
    <div class="pay-kpi pk-pending fu fu-3">
      <div class="pki-icon" style="background:rgba(247,184,75,.12);color:#f7b84b"><i class="ri-time-line"></i></div>
      <div>
        <div class="pki-label">En attente</div>
        <div class="pki-val" id="kpi-pending">—</div>
        <div class="pki-sub">À valider</div>
      </div>
    </div>
    <div class="pay-kpi pk-failed fu fu-4">
      <div class="pki-icon" style="background:rgba(240,101,72,.10);color:#f06548"><i class="ri-close-circle-line"></i></div>
      <div>
        <div class="pki-label">Échoués</div>
        <div class="pki-val" id="kpi-failed">—</div>
        <div class="pki-sub">Rejetés / annulés</div>
      </div>
    </div>
    <div class="pay-kpi pk-montant fu fu-5">
      <div class="pki-icon" style="background:rgba(212,168,67,.12);color:#d4a843"><i class="ri-money-cny-circle-line"></i></div>
      <div>
        <div class="pki-label">Total collecté</div>
        <div class="pki-val" id="kpi-montant" style="font-size:14px">—</div>
        <div class="pki-sub">Paiements validés</div>
      </div>
    </div>
  </div>

  <!-- ══ TABS STATUT ═══════════════════════════════════════ -->
  <div class="pay-tabs fu fu-2">
    <span class="tab-label"><i class="ri-filter-3-line me-1"></i>Statut :</span>
    <button class="pay-tab tab-tous active" data-tab="tous">
      <i class="ri-list-check"></i>Tous <span class="tab-count" id="cnt-tous">0</span>
    </button>
    <button class="pay-tab tab-success" data-tab="success">
      <i class="ri-checkbox-circle-line"></i>Validés <span class="tab-count" id="cnt-success">0</span>
    </button>
    <button class="pay-tab tab-pending" data-tab="pending">
      <i class="ri-time-line"></i>En attente <span class="tab-count" id="cnt-pending">0</span>
    </button>
    <button class="pay-tab tab-failed" data-tab="failed">
      <i class="ri-close-circle-line"></i>Échoués <span class="tab-count" id="cnt-failed">0</span>
    </button>
  </div>

  <!-- ══ TOOLBAR ══════════════════════════════════════════ -->
  <div class="pay-toolbar fu fu-3">
    <div class="sw">
      <i class="ri-search-line"></i>
      <input type="text" id="pay-search" placeholder="Rechercher fidèle, référence, mode…">
    </div>
    <select class="pay-sel" id="pay-filter-mode">
      <option value="tous">Tous modes</option>
      <option value="mobile_money">Mobile Money</option>
      <option value="espece">Espèces</option>
      <option value="virement">Virement</option>
    </select>
    <select class="pay-sel" id="pay-filter-mois" style="min-width:120px">
      <option value="tous">Tous mois</option>
      <option value="1">Janvier</option>
      <option value="2">Février</option>
      <option value="3">Mars</option>
      <option value="4">Avril</option>
      <option value="5">Mai</option>
      <option value="6">Juin</option>
      <option value="7">Juillet</option>
      <option value="8">Août</option>
      <option value="9">Septembre</option>
      <option value="10">Octobre</option>
      <option value="11">Novembre</option>
      <option value="12">Décembre</option>
    </select>
    <select class="pay-sel" id="pay-filter-source" style="min-width:160px">
      <option value="tous">Toutes sources</option>
      <option value="mensuel">Cotisation mensuelle</option>
      <option value="quete">Quête du vendredi</option>
      <option value="ordinaire">Don ordinaire</option>
      <option value="ramadan">Ramadan</option>
    </select>
  </div>

  <!-- ══ TABLE ════════════════════════════════════════════ -->
  <div class="pay-table-card fu fu-4">
    <div class="table-responsive">
      <table class="pay-table">
        <thead>
          <tr>
            <th>Fidèle</th>
            <th>Référence</th>
            <th>Montant</th>
            <th>Mode</th>
            <th>Source</th>
            <th>Date</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="pay-tbody">
          <!-- Peuplé par paiement.js -->
        </tbody>
      </table>
    </div>
    <div class="pay-pagination">
      <span class="pay-pag-info" id="pay-pag-info">—</span>
      <div class="pay-pag-btns" id="pay-pag-btns"></div>
    </div>
  </div>

  <!-- ══ GRAPHS ════════════════════════════════════════════ -->
  <div class="pay-graphs-grid fu fu-5">
    <div class="pay-graph-card">
      <div class="pgc-header">
        <div>
          <p class="pgc-title">Évolution mensuelle des paiements</p>
          <p class="pgc-sub">Montants collectés (validés) — 2025</p>
        </div>
        <span class="pgc-badge">2025</span>
      </div>
      <canvas id="chartEvolution" height="160"></canvas>
    </div>

    <div class="pay-graph-card">
      <div class="pgc-header">
        <div>
          <p class="pgc-title">Répartition par mode</p>
          <p class="pgc-sub">Nombre de paiements par mode</p>
        </div>
        <span class="pgc-badge" style="background:rgba(64,81,137,.08);color:#405189">Donut</span>
      </div>
      <div style="display:flex;align-items:center;gap:20px;margin-top:8px">
        <div style="flex-shrink:0;width:160px">
          <canvas id="chartModes"></canvas>
        </div>
        <div id="chartModesLegend" style="flex:1"></div>
      </div>
    </div>
  </div>

</div>
</div>


<!-- ══════════════════════════════════════════════════════════
     MODAL DÉTAIL PAIEMENT
══════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalDetailPaiement" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="max-width:620px">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;">

      <!-- Header coloré dynamique -->
      <div class="pay-modal-header" id="pmh-header" style="background:linear-gradient(130deg,#2d3a63,#405189)">
        <button class="pmh-close" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>
        <div class="pmh-inner">
          <div class="pmh-icon" id="pmh-icon"><i class="ri-bank-card-line"></i></div>
          <div>
            <div class="pmh-name" id="pmh-name">—</div>
            <div class="pmh-meta">
              <span><i class="ri-hashtag"></i><span id="pmh-ref">—</span></span>
              <span><i class="ri-calendar-line"></i><span id="pmh-date">—</span></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats overlap -->
      <div class="pay-modal-stats">
        <div class="pay-ms-box">
          <div class="pmsb-v" id="pms-montant">—</div>
          <div class="pmsb-l">Montant</div>
        </div>
        <div class="pay-ms-box">
          <div class="pmsb-v" id="pms-statut">—</div>
          <div class="pmsb-l">Statut</div>
        </div>
        <div class="pay-ms-box">
          <div class="pmsb-v" id="pms-mode">—</div>
          <div class="pmsb-l">Mode</div>
        </div>
      </div>

      <!-- Corps scrollable -->
      <div style="overflow-y:auto;max-height:calc(90vh - 260px);">
        <div class="pay-modal-body">

          <div class="pay-section-title">Détails du paiement</div>
          <div class="pay-detail-grid">
            <div class="pay-detail-item">
              <div class="pdi-l"><i class="ri-user-line me-1"></i>Fidèle</div>
              <div class="pdi-v" id="di-fidele">—</div>
            </div>
            <div class="pay-detail-item">
              <div class="pdi-l"><i class="ri-phone-line me-1"></i>Téléphone</div>
              <div class="pdi-v" id="di-tel">—</div>
            </div>
            <div class="pay-detail-item">
              <div class="pdi-l"><i class="ri-hashtag me-1"></i>Référence</div>
              <div class="pdi-v" id="di-ref" style="font-family:var(--pay-mono);color:var(--pay-primary)">—</div>
            </div>
            <div class="pay-detail-item">
              <div class="pdi-l"><i class="ri-calendar-line me-1"></i>Date paiement</div>
              <div class="pdi-v" id="di-date">—</div>
            </div>
            <div class="pay-detail-item">
              <div class="pdi-l"><i class="ri-link-m me-1"></i>Source</div>
              <div class="pdi-v" id="di-source">—</div>
            </div>
            <div class="pay-detail-item">
              <div class="pdi-l"><i class="ri-calendar-check-line me-1"></i>Période</div>
              <div class="pdi-v" id="di-periode">—</div>
            </div>
            <div class="pay-detail-item">
              <div class="pdi-l"><i class="ri-shield-check-line me-1"></i>Validé par</div>
              <div class="pdi-v" id="di-valide-par">—</div>
            </div>
            <div class="pay-detail-item">
              <div class="pdi-l"><i class="ri-time-line me-1"></i>Validé le</div>
              <div class="pdi-v" id="di-valide-at">—</div>
            </div>
            <div class="pay-detail-item full" id="di-note-wrap" style="display:none">
              <div class="pdi-l"><i class="ri-sticky-note-line me-1"></i>Note</div>
              <div class="pdi-v" id="di-note" style="font-weight:500;color:var(--pay-text)">—</div>
            </div>
          </div>

          <div class="pay-section-title accent">Historique</div>
          <div class="pay-hist-list" id="pay-hist-list">
            <!-- peuplé par JS -->
          </div>

          <!-- Actions dynamiques -->
          <div class="d-flex gap-2 mt-4 flex-wrap" id="detail-actions"></div>

        </div>
      </div>

      <div class="pay-modal-footer">
        <button class="btn-pay-secondary" data-bs-dismiss="modal">
          <i class="ri-close-line me-1"></i> Fermer
        </button>
        <button class="btn-pay-primary" id="btn-valider-detail" style="display:none">
          <i class="ri-shield-check-line"></i> Valider ce paiement
        </button>
      </div>

    </div>
  </div>
</div>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<!-- Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<!-- JS paiement -->
<script src="{{ asset('assets/js/paiements.js') }}"></script>

</body>
</html>