/* ============================================================
   MOSQUÉE – DASHBOARD JS
   Fichier JS spécifique au dashboard principal
   ============================================================ */

'use strict';

/* ── Utilitaires ─────────────────────────────────────────── */
const fmt = (n) => new Intl.NumberFormat('fr-FR').format(n) + ' FCFA';
const fmtShort = (n) => {
  if (n >= 1_000_000) return (n / 1_000_000).toFixed(1) + 'M';
  if (n >= 1_000)     return (n / 1_000).toFixed(0) + 'k';
  return n.toString();
};

/* ── Couleurs template Velzon ────────────────────────────── */
function getCssVar(name) {
  return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
}

const COLORS = {
  primary:  '#405189',
  accent:   '#0ab39c',
  danger:   '#f06548',
  warning:  '#f7b84b',
  info:     '#299cdb',
  gold:     '#d4a843',
  muted:    '#878a99',
};

/* ── Données simulées (remplacer par API Laravel/Livewire) ── */
const DATA = {
  solde:       3_420_500,
  entrees:     4_850_000,
  sorties:     1_429_500,
  moisLabel:   'Avril 2025',

  fidelesTotal:   347,
  fidelesAJour:   214,
  fidelesPartiel:  68,
  fidelesRetard:   65,
  fidelesLibres:    0, // sans engagement mensuel

  cotisationMois:  2_650_000,
  cotisationObj:   3_500_000,

  transactions: [
    { type:'entree', nom:'Koné Mamadou',        source:'Cotisation mensuelle', montant: 10_000, date:'Auj. 08:30', mode:'mobile_money' },
    { type:'entree', nom:'Diabaté Fatoumata',    source:'Quête du vendredi',    montant:  5_000, date:'Auj. 07:45', mode:'espece' },
    { type:'sortie', nom:'Facture CIE',          source:'Dépense',              montant: 42_000, date:'Hier 16:00', mode:'-' },
    { type:'entree', nom:'Coulibaly Ibrahim',    source:'Cotisation mensuelle', montant: 15_000, date:'Hier 11:20', mode:'mobile_money' },
    { type:'sortie', nom:'SODECI',               source:'Dépense',              montant: 18_500, date:'Hier 09:00', mode:'-' },
    { type:'entree', nom:'Touré Aminata',        source:'Don ordinaire',        montant:  8_000, date:'28/03 14:30', mode:'espece' },
    { type:'entree', nom:'Bamba Ousmane',        source:'Cotisation mensuelle', montant: 10_000, date:'27/03 10:00', mode:'virement' },
  ],

  retards: [
    { initiales:'KM', couleur:'#405189', nom:'Koné Moussa',       phone:'+225 07 XX XX 01', statut:'retard',  moisDu:3, montantDu:30_000 },
    { initiales:'TB', couleur:'#f06548', nom:'Traoré Bakary',     phone:'+225 05 XX XX 02', statut:'partiel', moisDu:1, montantDu: 5_000 },
    { initiales:'DS', couleur:'#f7b84b', nom:'Diabaté Salimata',  phone:'+225 01 XX XX 03', statut:'retard',  moisDu:2, montantDu:20_000 },
    { initiales:'CC', couleur:'#299cdb', nom:'Coulibaly Cheick',  phone:'+225 07 XX XX 04', statut:'partiel', moisDu:1, montantDu: 3_000 },
    { initiales:'OD', couleur:'#0ab39c', nom:'Ouattara Daouda',   phone:'+225 05 XX XX 05', statut:'retard',  moisDu:4, montantDu:40_000 },
  ],

  typesCotisation: [
    { nom:'Cotisation mensuelle', icon:'ri-calendar-check-line', color:'#405189', bg:'rgba(64,81,137,0.10)', collecte:2_650_000, objectif:3_500_000, nb:214, badge:'Actif', badgeColor:'rgba(64,81,137,0.10)', badgeText:'#405189' },
    { nom:'Quête du vendredi',    icon:'ri-hand-heart-line',     color:'#0ab39c', bg:'rgba(10,179,156,0.10)', collecte:380_000,   objectif:null,       nb:87,  badge:'En cours', badgeColor:'rgba(10,179,156,0.10)', badgeText:'#0ab39c' },
    { nom:'Don ordinaire',        icon:'ri-gift-line',           color:'#f7b84b', bg:'rgba(247,184,75,0.12)', collecte:195_000,   objectif:null,       nb:42,  badge:'Ouvert', badgeColor:'rgba(247,184,75,0.12)', badgeText:'#f7b84b' },
    { nom:'Ramadan 1446',         icon:'ri-moon-line',           color:'#d4a843', bg:'rgba(212,168,67,0.12)', collecte:425_000,   objectif:800_000,    nb:118, badge:'En cours', badgeColor:'rgba(212,168,67,0.12)', badgeText:'#d4a843' },
  ],

  depensesParType: [
    { nom:'CIE',         icon:'ri-flashlight-line', color:'#f7b84b', bg:'rgba(247,184,75,0.12)', montant:127_000, pct:42 },
    { nom:'SODECI',      icon:'ri-drop-line',        color:'#299cdb', bg:'rgba(41,156,219,0.10)',  montant: 74_500, pct:25 },
    { nom:'Entretien',   icon:'ri-tools-line',       color:'#0ab39c', bg:'rgba(10,179,156,0.10)',  montant: 48_000, pct:16 },
    { nom:'Salaires',    icon:'ri-user-line',         color:'#405189', bg:'rgba(64,81,137,0.10)',   montant: 90_000, pct:30 },
    { nom:'Autres',      icon:'ri-more-line',         color:'#878a99', bg:'rgba(135,138,153,0.10)', montant: 32_000, pct:11 },
  ],

  collectesEnCours: [
    { nom:'Ramadan 1446', type:'Ramadan', collecte:425_000, objectif:800_000, color:'#d4a843', bg:'rgba(212,168,67,0.12)', typeColor:'#d4a843', typeBg:'rgba(212,168,67,0.12)' },
    { nom:'Rénovation salle de prière', type:'Ordinaire', collecte:1_150_000, objectif:3_000_000, color:'#405189', bg:'rgba(64,81,137,0.10)', typeColor:'#405189', typeBg:'rgba(64,81,137,0.10)' },
  ],

  // Données pour le graphique flux mensuels
  chartEntrees: [820, 940, 1100, 780, 1250, 900, 1050, 1200, 980, 1350, 1180, 1420].map(x => x * 1000),
  chartSorties: [320, 280, 410, 350, 520, 380, 290, 430, 360, 480, 290, 370].map(x => x * 1000),
  chartLabels: ['Avr','Mai','Juin','Juil','Août','Sep','Oct','Nov','Déc','Jan','Fév','Mar'],
};

/* ── Compteurs animés ────────────────────────────────────── */
function animateValue(el, target, suffix = '', duration = 1400) {
  if (!el) return;
  const start = 0;
  const startTime = performance.now();
  const easeOut = t => 1 - Math.pow(1 - t, 3);

  function update(currentTime) {
    const elapsed = currentTime - startTime;
    const progress = Math.min(elapsed / duration, 1);
    const value = Math.floor(easeOut(progress) * target);
    el.textContent = new Intl.NumberFormat('fr-FR').format(value) + suffix;
    if (progress < 1) requestAnimationFrame(update);
  }
  requestAnimationFrame(update);
}

/* ── Render KPI Cards ────────────────────────────────────── */
function renderKPIs() {
  const kpis = [
    { id:'kpi-solde',     value: DATA.solde,         suffix:' FCFA', label:'Solde disponible',    icon:'ri-bank-line',           iconClass:'primary', cardClass:'kc-primary',  trend:'up',   trendVal:'+12.4%', sub:`Cumulé ${DATA.moisLabel}` },
    { id:'kpi-entrees',   value: DATA.entrees,        suffix:' FCFA', label:'Total entrées',       icon:'ri-arrow-down-circle-line', iconClass:'accent',  cardClass:'kc-accent',  trend:'up',   trendVal:'+8.2%',  sub:'vs mois précédent' },
    { id:'kpi-sorties',   value: DATA.sorties,        suffix:' FCFA', label:'Total dépenses',      icon:'ri-arrow-up-circle-line',  iconClass:'danger',  cardClass:'kc-danger',  trend:'down', trendVal:'+3.1%',  sub:'vs mois précédent' },
    { id:'kpi-fideles',   value: DATA.fidelesTotal,   suffix:'',      label:'Fidèles inscrits',    icon:'ri-group-line',           iconClass:'info',    cardClass:'kc-info',    trend:'up',   trendVal:'+5',     sub:'dont 214 avec engagement' },
    { id:'kpi-ajour',     value: DATA.fidelesAJour,   suffix:'',      label:'Fidèles à jour',      icon:'ri-checkbox-circle-line', iconClass:'accent',  cardClass:'kc-accent',  trend:'flat', trendVal:`${Math.round(DATA.fidelesAJour/DATA.fidelesTotal*100)}%`, sub:'du total' },
    { id:'kpi-retard',    value: DATA.fidelesRetard,  suffix:'',      label:'Fidèles en retard',   icon:'ri-time-line',            iconClass:'danger',  cardClass:'kc-danger',  trend:'down', trendVal:'-3',     sub:'vs mois précédent' },
  ];

  kpis.forEach(kpi => {
    const el = document.getElementById(kpi.id);
    if (!el) return;
    const card = el.closest('.kpi-card');
    if (card) card.classList.add(kpi.cardClass);

    const iconEl   = el.querySelector('.kpi-icon');
    const valueEl  = el.querySelector('.kpi-value');
    const labelEl  = el.querySelector('.kpi-label');
    const trendEl  = el.querySelector('.kpi-trend');
    const subEl    = el.querySelector('.kpi-sub');

    if (iconEl)  { iconEl.classList.add(kpi.iconClass); iconEl.innerHTML = `<i class="${kpi.icon}"></i>`; }
    if (labelEl) labelEl.textContent = kpi.label;
    if (trendEl) { trendEl.classList.add(kpi.trend); trendEl.innerHTML = `<i class="ri-arrow-${kpi.trend === 'up' ? 'up' : kpi.trend === 'down' ? 'down' : 'right'}-s-line"></i> ${kpi.trendVal}`; }
    if (subEl)   subEl.textContent = kpi.sub;
    if (valueEl) animateValue(valueEl, kpi.value, kpi.suffix);
  });
}

/* ── Graphique : Flux Mensuels (Area + Bar) ──────────────── */
function initChartFlux() {
  const el = document.getElementById('chart-flux');
  if (!el || typeof ApexCharts === 'undefined') return;

  const options = {
    series: [
      { name: 'Entrées', type: 'area', data: DATA.chartEntrees.slice(-6) },
      { name: 'Dépenses', type: 'bar', data: DATA.chartSorties.slice(-6) },
    ],
    chart: {
      height: 280,
      type: 'line',
      toolbar: { show: false },
      fontFamily: "'Nunito', sans-serif",
      animations: { enabled: true, easing: 'easeinout', speed: 800 },
    },
    stroke: { curve: 'smooth', width: [2.5, 0], dashArray: [0, 0] },
    fill: {
      opacity: [0.12, 0.9],
      type: ['gradient', 'solid'],
      gradient: {
        shadeIntensity: 1, inverseColors: false,
        opacityFrom: 0.25, opacityTo: 0,
        stops: [0, 100],
      },
    },
    colors: [COLORS.accent, COLORS.danger],
    xaxis: {
      categories: DATA.chartLabels.slice(-6),
      axisBorder: { show: false },
      axisTicks: { show: false },
      labels: { style: { colors: COLORS.muted, fontSize: '11px' } },
    },
    yaxis: {
      labels: {
        formatter: v => fmtShort(v),
        style: { colors: COLORS.muted, fontSize: '11px' },
      },
    },
    plotOptions: { bar: { columnWidth: '40%', borderRadius: 4 } },
    dataLabels: { enabled: false },
    markers: { size: [0, 0] },
    grid: {
      borderColor: '#f0f2f5',
      strokeDashArray: 4,
      yaxis: { lines: { show: true } },
      xaxis: { lines: { show: false } },
      padding: { top: 0, right: 10, bottom: 0, left: 10 },
    },
    legend: {
      show: true,
      position: 'top',
      horizontalAlign: 'right',
      markers: { width: 8, height: 8, radius: 4 },
      itemMargin: { horizontal: 8 },
      fontSize: '12px',
      fontWeight: 600,
    },
    tooltip: {
      shared: true,
      y: { formatter: v => new Intl.NumberFormat('fr-FR').format(v) + ' FCFA' },
    },
  };

  new ApexCharts(el, options).render();
}

/* ── Graphique : Donut Statut Fidèles ────────────────────── */
function initChartStatut() {
  const el = document.getElementById('chart-statut');
  if (!el || typeof ApexCharts === 'undefined') return;

  const options = {
    series: [DATA.fidelesAJour, DATA.fidelesPartiel, DATA.fidelesRetard],
    labels: ['À jour', 'Partiel', 'En retard'],
    chart: {
      height: 220,
      type: 'donut',
      fontFamily: "'Nunito', sans-serif",
    },
    colors: [COLORS.accent, COLORS.warning, COLORS.danger],
    plotOptions: {
      pie: {
        donut: {
          size: '72%',
          labels: {
            show: true,
            name: { fontSize: '13px', fontWeight: 600, color: COLORS.muted },
            value: { fontSize: '22px', fontWeight: 800, color: '#212529',
              formatter: v => v },
            total: {
              show: true, label: 'Total', fontSize: '12px',
              fontWeight: 600, color: COLORS.muted,
              formatter: w => w.globals.seriesTotals.reduce((a, b) => a + b, 0),
            },
          },
        },
      },
    },
    dataLabels: { enabled: false },
    legend: { show: false },
    stroke: { width: 0 },
    tooltip: { y: { formatter: v => v + ' fidèles' } },
  };

  new ApexCharts(el, options).render();
}

/* ── Graphique : Collecte mensuelle (Radial) ─────────────── */
function initChartCollecte() {
  const el = document.getElementById('chart-collecte');
  if (!el || typeof ApexCharts === 'undefined') return;

  const pct = Math.round((DATA.cotisationMois / DATA.cotisationObj) * 100);

  const options = {
    series: [pct],
    chart: { height: 200, type: 'radialBar', fontFamily: "'Nunito', sans-serif", toolbar: { show: false } },
    colors: [COLORS.primary],
    plotOptions: {
      radialBar: {
        startAngle: -135, endAngle: 135,
        hollow: { size: '65%' },
        dataLabels: {
          name: { show: false },
          value: { fontSize: '28px', fontWeight: 800, color: '#212529', offsetY: 8,
            formatter: v => v + '%' },
        },
        track: { background: '#f0f2f5', strokeWidth: '100%' },
      },
    },
    stroke: { lineCap: 'round' },
  };

  new ApexCharts(el, options).render();
}

/* ── Render Transactions ─────────────────────────────────── */
function renderTransactions() {
  const list = document.getElementById('tx-list');
  if (!list) return;

  list.innerHTML = DATA.transactions.map((tx, i) => `
    <li class="tx-item fade-up" style="animation-delay:${i * 0.06}s">
      <div class="tx-icon ${tx.type}">
        <i class="${tx.type === 'entree' ? 'ri-arrow-down-circle-line' : 'ri-arrow-up-circle-line'}"></i>
      </div>
      <div class="tx-body">
        <div class="tx-name">${tx.nom}</div>
        <div class="tx-meta">${tx.source} · ${tx.mode !== '-' ? '<i class="ri-smartphone-line"></i> ' + tx.mode : ''} · ${tx.date}</div>
      </div>
      <div class="tx-amount ${tx.type}">
        ${tx.type === 'entree' ? '+' : '-'}${new Intl.NumberFormat('fr-FR').format(tx.montant)}
      </div>
    </li>
  `).join('');
}

/* ── Render Retards ──────────────────────────────────────── */
function renderRetards() {
  const tbody = document.getElementById('retard-tbody');
  if (!tbody) return;

  const statutMap = {
    retard:  { label:'En retard', cls:'retard' },
    partiel: { label:'Partiel',   cls:'partiel' },
  };

  tbody.innerHTML = DATA.retards.map(r => {
    const s = statutMap[r.statut] || { label: r.statut, cls: 'retard' };
    const nbMoisLabel = r.moisDu > 1 ? `${r.moisDu} mois de retard` : `${r.moisDu} mois`;
    return `
      <tr>
        <td>
          <div style="display:flex;align-items:center;gap:10px;">
            <div class="fidele-avatar" style="background:${r.couleur}">${r.initiales}</div>
            <div>
              <div class="fidele-name">${r.nom}</div>
              <div class="fidele-phone">${r.phone}</div>
            </div>
          </div>
        </td>
        <td><span class="statut-pill ${s.cls}">${s.label}</span></td>
        <td><span style="font-size:12px;color:var(--msq-muted);">${nbMoisLabel}</span></td>
        <td><span class="montant-du">${new Intl.NumberFormat('fr-FR').format(r.montantDu)} FCFA</span></td>
        <td>
          <button class="btn btn-sm btn-soft-primary waves-effect" title="Créer cotisation BO">
            <i class="ri-add-circle-line"></i>
          </button>
          <button class="btn btn-sm btn-soft-warning waves-effect ms-1" title="Voir profil">
            <i class="ri-eye-line"></i>
          </button>
        </td>
      </tr>
    `;
  }).join('');
}

/* ── Render Types Cotisation ────────────────────────────── */
function renderTypesCotisation() {
  const container = document.getElementById('types-cot-list');
  if (!container) return;

  container.innerHTML = DATA.typesCotisation.map(tc => {
    const pct = tc.objectif ? Math.round((tc.collecte / tc.objectif) * 100) : null;
    return `
      <div class="type-cot-item">
        <div class="tci-header">
          <div class="tci-name">
            <span style="width:30px;height:30px;border-radius:8px;background:${tc.bg};color:${tc.color};display:inline-flex;align-items:center;justify-content:center;font-size:15px;">
              <i class="${tc.icon}"></i>
            </span>
            ${tc.nom}
          </div>
          <div style="display:flex;align-items:center;gap:8px;">
            <span class="tci-badge" style="background:${tc.badgeColor};color:${tc.badgeText}">${tc.badge}</span>
            <div class="tci-amount">${new Intl.NumberFormat('fr-FR').format(tc.collecte)} FCFA</div>
          </div>
        </div>
        ${pct !== null ? `
        <div class="tci-bar">
          <div class="tci-bar-fill" style="width:0%;background:${tc.color}" data-width="${pct}%"></div>
        </div>
        <div class="tci-meta">
          <span>${tc.nb} contributions</span>
          <span>${pct}% de l'objectif (${new Intl.NumberFormat('fr-FR').format(tc.objectif)} FCFA)</span>
        </div>
        ` : `
        <div class="tci-meta" style="margin-top:4px;">
          <span>${tc.nb} contributions</span>
          <span style="color:var(--msq-muted)">Pas d'objectif défini</span>
        </div>
        `}
      </div>
    `;
  }).join('');

  // Animate bars après render
  setTimeout(() => {
    document.querySelectorAll('.tci-bar-fill[data-width]').forEach(el => {
      el.style.width = el.dataset.width;
    });
  }, 300);
}

/* ── Render Dépenses par type ────────────────────────────── */
function renderDepensesType() {
  const container = document.getElementById('depenses-type-list');
  if (!container) return;

  container.innerHTML = DATA.depensesParType.map(d => `
    <div class="depense-type-item">
      <div class="dt-icon" style="background:${d.bg};color:${d.color}">
        <i class="${d.icon}"></i>
      </div>
      <div class="dt-name">${d.nom}</div>
      <div class="dt-pct">${d.pct}%</div>
      <div class="dt-amount">${new Intl.NumberFormat('fr-FR').format(d.montant)}</div>
    </div>
  `).join('');
}

/* ── Render Collectes en cours ───────────────────────────── */
function renderCollectes() {
  const container = document.getElementById('collectes-list');
  if (!container) return;

  container.innerHTML = DATA.collectesEnCours.map(c => {
    const pct = Math.round((c.collecte / c.objectif) * 100);
    return `
      <div class="collecte-card mb-3 fade-up">
        <span class="cc-type-badge" style="background:${c.typeBg};color:${c.typeColor}">${c.type}</span>
        <div class="cc-name">${c.nom}</div>
        <div class="cc-progress-bar">
          <div class="cc-progress-fill" style="width:0%;background:${c.color}" data-width="${pct}%"></div>
        </div>
        <div class="cc-amounts">
          <span class="cc-collected">${new Intl.NumberFormat('fr-FR').format(c.collecte)} FCFA <strong>(${pct}%)</strong></span>
          <span class="cc-target">/ ${new Intl.NumberFormat('fr-FR').format(c.objectif)}</span>
        </div>
      </div>
    `;
  }).join('');

  setTimeout(() => {
    document.querySelectorAll('.cc-progress-fill[data-width]').forEach(el => {
      el.style.width = el.dataset.width;
    });
  }, 400);
}

/* ── Render Banner Solde ─────────────────────────────────── */
function renderBanner() {
  const soldeEl   = document.getElementById('banner-solde');
  const entreesEl = document.getElementById('banner-entrees');
  const sortiesEl = document.getElementById('banner-sorties');
  const cotEl     = document.getElementById('banner-cot');

  if (soldeEl)   animateValue(soldeEl,   DATA.solde,            ' FCFA', 1600);
  if (entreesEl) animateValue(entreesEl, DATA.entrees,          ' FCFA', 1800);
  if (sortiesEl) animateValue(sortiesEl, DATA.sorties,          ' FCFA', 1800);
  if (cotEl)     animateValue(cotEl,     DATA.cotisationMois,   ' FCFA', 1400);
}

/* ── Statut legend ───────────────────────────────────────── */
function renderStatutLegend() {
  const el = document.getElementById('statut-legend');
  if (!el) return;

  const items = [
    { label:'À jour',    count: DATA.fidelesAJour,   color: COLORS.accent,   pct: Math.round(DATA.fidelesAJour/DATA.fidelesTotal*100) },
    { label:'Partiel',   count: DATA.fidelesPartiel, color: COLORS.warning,  pct: Math.round(DATA.fidelesPartiel/DATA.fidelesTotal*100) },
    { label:'En retard', count: DATA.fidelesRetard,  color: COLORS.danger,   pct: Math.round(DATA.fidelesRetard/DATA.fidelesTotal*100) },
  ];

  el.innerHTML = items.map(it => `
    <div class="legend-item">
      <div class="legend-left">
        <div class="legend-dot" style="background:${it.color}"></div>
        <span class="legend-name">${it.label}</span>
      </div>
      <div>
        <span class="legend-count">${it.count}</span>
        <span class="legend-pct">(${it.pct}%)</span>
      </div>
    </div>
  `).join('');
}

/* ── Filter buttons chart ────────────────────────────────── */
function initFilterBtns() {
  document.querySelectorAll('.cc-filter .filter-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      this.closest('.cc-filter').querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      // Ici: recharger les données du graphique selon la période
    });
  });
}

/* ── Heure en temps réel ─────────────────────────────────── */
function startClock() {
  const el = document.getElementById('live-time');
  if (!el) return;

  function update() {
    const now = new Date();
    el.textContent = now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
  }
  update();
  setInterval(update, 60_000);
}

/* ── INIT ────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
  renderBanner();
  renderKPIs();
  renderTransactions();
  renderRetards();
  renderTypesCotisation();
  renderDepensesType();
  renderCollectes();
  renderStatutLegend();
  initFilterBtns();
  startClock();

  // Charts après un petit délai pour laisser le DOM se stabiliser
  setTimeout(() => {
    initChartFlux();
    initChartStatut();
    initChartCollecte();
  }, 200);
});
