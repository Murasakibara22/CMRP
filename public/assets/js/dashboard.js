/* ============================================================
   MOSQUÉE – DASHBOARD JS
   DATA est injecté par le blade Livewire (variables PHP réelles)
   ============================================================ */
'use strict';

/* ── Utilitaires ─────────────────────────────────────────── */
const fmt = (n) => new Intl.NumberFormat('fr-FR').format(n) + ' FCFA';
const fmtShort = (n) => {
  if (n >= 1_000_000) return (n / 1_000_000).toFixed(1) + 'M';
  if (n >= 1_000)     return (n / 1_000).toFixed(0) + 'k';
  return n.toString();
};

const COLORS = {
  primary: '#405189',
  accent:  '#0ab39c',
  danger:  '#f06548',
  warning: '#f7b84b',
  info:    '#299cdb',
  gold:    '#d4a843',
  muted:   '#878a99',
};

/* ── Compteurs animés ────────────────────────────────────── */
function animateValue(el, target, suffix = '', duration = 1400) {
  if (!el) return;
  const startTime = performance.now();
  const easeOut   = t => 1 - Math.pow(1 - t, 3);
  (function update(now) {
    const p = Math.min((now - startTime) / duration, 1);
    el.textContent = new Intl.NumberFormat('fr-FR').format(Math.floor(easeOut(p) * target)) + suffix;
    if (p < 1) requestAnimationFrame(update);
  })(startTime);
}

/* ── Bannière financière ─────────────────────────────────── */
function renderBanner() {
  animateValue(document.getElementById('banner-solde'),   DATA.solde,          ' FCFA', 1600);
  animateValue(document.getElementById('banner-entrees'), DATA.entrees,        ' FCFA', 1800);
  animateValue(document.getElementById('banner-sorties'), DATA.sorties,        ' FCFA', 1800);
  animateValue(document.getElementById('banner-cot'),     DATA.cotisationMois, ' FCFA', 1400);
  animateValue(document.getElementById('banner-fideles'), DATA.fidelesActifs,  '',       1200);
}

/* ── KPI Cards ───────────────────────────────────────────── */
function renderKPIs() {
  const pctAjour = DATA.fidelesTotal > 0
    ? Math.round(DATA.fidelesAJour / DATA.fidelesTotal * 100)
    : 0;

  const trendE = DATA.trendEntrees >= 0
    ? { cls:'up',   icon:'ri-arrow-up-s-line',   val:`+${DATA.trendEntrees}%` }
    : { cls:'down', icon:'ri-arrow-down-s-line',  val:`${DATA.trendEntrees}%` };
  const trendS = DATA.trendSorties >= 0
    ? { cls:'up',   icon:'ri-arrow-up-s-line',   val:`+${DATA.trendSorties}%` }
    : { cls:'down', icon:'ri-arrow-down-s-line',  val:`${DATA.trendSorties}%` };

  const kpis = [
    {
      id:'kpi-solde', value:DATA.solde, suffix:' FCFA',
      label:'Solde disponible', icon:'ri-bank-line', iconClass:'primary', cardClass:'kc-primary',
      trend:{ cls:'up', icon:'ri-arrow-up-s-line', val:'+' }, sub:`Cumulé ${DATA.moisLabel}`,
    },
    {
      id:'kpi-entrees', value:DATA.entrees, suffix:' FCFA',
      label:'Total entrées', icon:'ri-arrow-down-circle-line', iconClass:'accent', cardClass:'kc-accent',
      trend:trendE, sub:'vs mois précédent',
    },
    {
      id:'kpi-sorties', value:DATA.sorties, suffix:' FCFA',
      label:'Total dépenses', icon:'ri-arrow-up-circle-line', iconClass:'danger', cardClass:'kc-danger',
      trend:trendS, sub:'vs mois précédent',
    },
    {
      id:'kpi-fideles', value:DATA.fidelesTotal, suffix:'',
      label:'Fidèles inscrits', icon:'ri-group-line', iconClass:'info', cardClass:'kc-info',
      trend:{ cls:'flat', icon:'ri-arrow-right-s-line', val:`${DATA.fidelesActifs} actifs` }, sub:'Total inscrits',
    },
    {
      id:'kpi-ajour', value:DATA.fidelesAJour, suffix:'',
      label:'Fidèles à jour', icon:'ri-checkbox-circle-line', iconClass:'accent', cardClass:'kc-accent',
      trend:{ cls:'flat', icon:'ri-arrow-right-s-line', val:`${pctAjour}%` }, sub:'du total',
    },
    {
      id:'kpi-retard', value:DATA.fidelesRetard, suffix:'',
      label:'Fidèles en retard', icon:'ri-time-line', iconClass:'danger', cardClass:'kc-danger',
      trend:{ cls:'down', icon:'ri-arrow-down-s-line', val:DATA.fidelesRetard + ' cas' }, sub:'Ce mois',
    },
  ];

  kpis.forEach(kpi => {
    const el = document.getElementById(kpi.id);
    if (!el) return;
    el.closest('.kpi-card')?.classList.add(kpi.cardClass);
    const iconEl  = el.querySelector('.kpi-icon');
    const valueEl = el.querySelector('.kpi-value');
    const labelEl = el.querySelector('.kpi-label');
    const trendEl = el.querySelector('.kpi-trend');
    const subEl   = el.querySelector('.kpi-sub');
    if (iconEl)  { iconEl.classList.add(kpi.iconClass); iconEl.innerHTML = `<i class="${kpi.icon}"></i>`; }
    if (labelEl) labelEl.textContent = kpi.label;
    if (trendEl) { trendEl.className = `kpi-trend ${kpi.trend.cls}`; trendEl.innerHTML = `<i class="${kpi.trend.icon}"></i> ${kpi.trend.val}`; }
    if (subEl)   subEl.textContent = kpi.sub;
    if (valueEl) animateValue(valueEl, kpi.value, kpi.suffix);
  });
}

/* ── Graphe flux (ApexCharts) ── range 6 ou 12 mois ─────── */
let fluxChart = null;

function initChartFlux(range = 6) {
  const el = document.getElementById('chart-flux');
  if (!el || typeof ApexCharts === 'undefined') return;

  const labels  = DATA.chartLabels.slice(-range);
  const entrees = DATA.chartEntrees.slice(-range);
  const sorties = DATA.chartSorties.slice(-range);

  const options = {
    series: [
      { name:'Entrées',  type:'area', data: entrees },
      { name:'Dépenses', type:'bar',  data: sorties },
    ],
    chart: {
      height: 280, type:'line',
      toolbar:{ show:false },
      fontFamily:"'Nunito', sans-serif",
      animations:{ enabled:true, easing:'easeinout', speed:800 },
    },
    stroke:{ curve:'smooth', width:[2.5, 0] },
    fill:{
      opacity:[0.12, 0.9], type:['gradient','solid'],
      gradient:{ shadeIntensity:1, inverseColors:false, opacityFrom:0.25, opacityTo:0, stops:[0,100] },
    },
    colors:[COLORS.accent, COLORS.danger],
    xaxis:{
      categories: labels,
      axisBorder:{ show:false }, axisTicks:{ show:false },
      labels:{ style:{ colors:COLORS.muted, fontSize:'11px' } },
    },
    yaxis:{
      labels:{ formatter: v => fmtShort(v), style:{ colors:COLORS.muted, fontSize:'11px' } },
    },
    plotOptions:{ bar:{ columnWidth:'40%', borderRadius:4 } },
    dataLabels:{ enabled:false },
    grid:{ borderColor:'#f0f2f5', strokeDashArray:4, yaxis:{ lines:{ show:true } }, xaxis:{ lines:{ show:false } }, padding:{ top:0, right:10, bottom:0, left:10 } },
    legend:{ show:true, position:'top', horizontalAlign:'right', markers:{ width:8, height:8, radius:4 }, itemMargin:{ horizontal:8 }, fontSize:'12px', fontWeight:600 },
    tooltip:{ shared:true, y:{ formatter: v => new Intl.NumberFormat('fr-FR').format(v) + ' FCFA' } },
  };

  if (fluxChart) { fluxChart.destroy(); }
  fluxChart = new ApexCharts(el, options);
  fluxChart.render();
}

/* ── Donut statut fidèles ────────────────────────────────── */
function initChartStatut() {
  const el = document.getElementById('chart-statut');
  if (!el || typeof ApexCharts === 'undefined') return;

  new ApexCharts(el, {
    series: [DATA.fidelesAJour, DATA.fidelesPartiel, DATA.fidelesRetard],
    labels: ['À jour', 'Partiel', 'En retard'],
    chart: { height:220, type:'donut', fontFamily:"'Nunito', sans-serif" },
    colors: [COLORS.accent, COLORS.warning, COLORS.danger],
    plotOptions:{
      pie:{ donut:{ size:'72%', labels:{
        show:true,
        name:{ fontSize:'13px', fontWeight:600, color:COLORS.muted },
        value:{ fontSize:'22px', fontWeight:800, color:'#212529', formatter: v => v },
        total:{ show:true, label:'Total', fontSize:'12px', fontWeight:600, color:COLORS.muted,
          formatter: w => w.globals.seriesTotals.reduce((a,b) => a+b, 0) },
      } } },
    },
    dataLabels:{ enabled:false },
    legend:{ show:false },
    stroke:{ width:0 },
    tooltip:{ y:{ formatter: v => v + ' fidèles' } },
  }).render();
}

/* ── Radial objectif mensuel ─────────────────────────────── */
function initChartCollecte() {
  const el = document.getElementById('chart-collecte');
  if (!el || typeof ApexCharts === 'undefined') return;

  const pct = DATA.cotisationObj > 0
    ? Math.min(Math.round((DATA.cotisationMois / DATA.cotisationObj) * 100), 100)
    : 0;

  new ApexCharts(el, {
    series: [pct],
    chart: { height:200, type:'radialBar', fontFamily:"'Nunito', sans-serif", toolbar:{ show:false } },
    colors: [COLORS.primary],
    plotOptions:{
      radialBar:{
        startAngle:-135, endAngle:135,
        hollow:{ size:'65%' },
        dataLabels:{
          name:{ show:false },
          value:{ fontSize:'28px', fontWeight:800, color:'#212529', offsetY:8, formatter: v => v + '%' },
        },
        track:{ background:'#f0f2f5', strokeWidth:'100%' },
      },
    },
    stroke:{ lineCap:'round' },
  }).render();
}

/* ── Transactions récentes ───────────────────────────────── */
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
        <div class="tx-meta">${tx.source}${tx.mode !== '-' ? ' · <i class="ri-smartphone-line"></i> ' + tx.mode : ''} · ${tx.date}</div>
      </div>
      <div class="tx-amount ${tx.type}">
        ${tx.type === 'entree' ? '+' : '-'}${new Intl.NumberFormat('fr-FR').format(tx.montant)}
      </div>
    </li>
  `).join('');
}

/* ── Fidèles en retard ───────────────────────────────────── */
function renderRetards() {
  const tbody = document.getElementById('retard-tbody');
  if (!tbody) return;
  const statutMap = {
    retard:  { label:'En retard', cls:'retard' },
    partiel: { label:'Partiel',   cls:'partiel' },
  };
  tbody.innerHTML = DATA.retards.map(r => {
    const s = statutMap[r.statut] || { label:r.statut, cls:'retard' };
    return `
      <tr>
        <td>
          <div style="display:flex;align-items:center;gap:10px">
            <div class="fidele-avatar" style="background:${r.couleur}">${r.initiales}</div>
            <div>
              <div class="fidele-name">${r.nom}</div>
              <div class="fidele-phone">${r.phone}</div>
            </div>
          </div>
        </td>
        <td><span class="statut-pill ${s.cls}">${s.label}</span></td>
        <td><span style="font-size:12px;color:var(--msq-muted)">${r.moisDu > 1 ? r.moisDu + ' mois de retard' : r.moisDu + ' mois'}</span></td>
        <td><span class="montant-du">${new Intl.NumberFormat('fr-FR').format(r.montantDu)} FCFA</span></td>
        <td>
          <button class="btn btn-sm btn-soft-primary waves-effect" title="Créer cotisation"><i class="ri-add-circle-line"></i></button>
          <button class="btn btn-sm btn-soft-warning waves-effect ms-1" title="Voir profil"><i class="ri-eye-line"></i></button>
        </td>
      </tr>`;
  }).join('');
}

/* ── Types cotisation ────────────────────────────────────── */
function renderTypesCotisation() {
  const container = document.getElementById('types-cot-list');
  if (!container) return;
  container.innerHTML = DATA.typesCotisation.map(tc => {
    const pct = tc.objectif ? Math.round((tc.collecte / tc.objectif) * 100) : null;
    return `
      <div class="type-cot-item">
        <div class="tci-header">
          <div class="tci-name">
            <span style="width:30px;height:30px;border-radius:8px;background:${tc.bg};color:${tc.color};display:inline-flex;align-items:center;justify-content:center;font-size:15px">
              <i class="${tc.icon}"></i>
            </span>
            ${tc.nom}
          </div>
          <div style="display:flex;align-items:center;gap:8px">
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
          </div>` : `
          <div class="tci-meta" style="margin-top:4px">
            <span>${tc.nb} contributions</span>
            <span style="color:var(--msq-muted)">Pas d'objectif défini</span>
          </div>`}
      </div>`;
  }).join('');
  setTimeout(() => {
    document.querySelectorAll('.tci-bar-fill[data-width]').forEach(el => { el.style.width = el.dataset.width; });
  }, 300);
}

/* ── Dépenses par type ───────────────────────────────────── */
function renderDepensesType() {
  const container = document.getElementById('depenses-type-list');
  if (!container) return;
  if (!DATA.depensesParType.length) {
    container.innerHTML = '<div style="text-align:center;padding:20px;color:var(--msq-muted);font-size:13px">Aucune dépense ce mois</div>';
    return;
  }
  container.innerHTML = DATA.depensesParType.map(d => `
    <div class="depense-type-item">
      <div class="dt-icon" style="background:${d.bg};color:${d.color}"><i class="${d.icon}"></i></div>
      <div class="dt-name">${d.nom}</div>
      <div class="dt-pct">${d.pct}%</div>
      <div class="dt-amount">${new Intl.NumberFormat('fr-FR').format(d.montant)}</div>
    </div>
  `).join('');
}

/* ── Collectes en cours ──────────────────────────────────── */
function renderCollectes() {
  const container = document.getElementById('collectes-list');
  if (!container) return;
  if (!DATA.collectesEnCours.length) {
    container.innerHTML = '<div style="text-align:center;padding:20px;color:var(--msq-muted);font-size:13px">Aucune collecte avec objectif</div>';
    return;
  }
  container.innerHTML = DATA.collectesEnCours.map(c => {
    const pct = c.objectif > 0 ? Math.min(Math.round((c.collecte / c.objectif) * 100), 100) : 0;
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
      </div>`;
  }).join('');
  setTimeout(() => {
    document.querySelectorAll('.cc-progress-fill[data-width]').forEach(el => { el.style.width = el.dataset.width; });
  }, 400);
}

/* ── Légende statut ──────────────────────────────────────── */
function renderStatutLegend() {
  const el = document.getElementById('statut-legend');
  if (!el) return;
  const items = [
    { label:'À jour',    count:DATA.fidelesAJour,   color:COLORS.accent,   pct:Math.round(DATA.fidelesAJour   / DATA.fidelesTotal * 100) },
    { label:'Partiel',   count:DATA.fidelesPartiel, color:COLORS.warning,  pct:Math.round(DATA.fidelesPartiel / DATA.fidelesTotal * 100) },
    { label:'En retard', count:DATA.fidelesRetard,  color:COLORS.danger,   pct:Math.round(DATA.fidelesRetard  / DATA.fidelesTotal * 100) },
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
    </div>`).join('');
}

/* ── Filter boutons graphe ───────────────────────────────── */
function initFilterBtns() {
  document.querySelectorAll('.cc-filter .filter-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      this.closest('.cc-filter').querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      const range = parseInt(this.dataset.range) || 6;
      initChartFlux(range);
    });
  });
}

/* ── Horloge temps réel ──────────────────────────────────── */
function startClock() {
  const el = document.getElementById('live-time');
  if (!el) return;
  const update = () => { el.textContent = new Date().toLocaleTimeString('fr-FR', { hour:'2-digit', minute:'2-digit' }); };
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

  setTimeout(() => {
    initChartFlux(6);
    initChartStatut();
    initChartCollecte();
  }, 200);
});
