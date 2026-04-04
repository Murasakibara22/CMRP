/* ============================================================
   MOSQUÉE – Cotisations JS
   Champs modèle : customer_id, type_cotisation_id, mois, annee,
   montant_du, montant_paye, montant_restant, statut,
   mode_paiement, validated_by, validated_at
   ============================================================ */
'use strict';

/* ── Référentiels ─────────────────────────────────────── */
const MOIS_FR = ['','Janvier','Février','Mars','Avril','Mai','Juin',
                 'Juillet','Août','Septembre','Octobre','Novembre','Décembre'];

const MODES = {
  mobile_money: { label:'Mobile Money', icon:'ri-smartphone-line', cls:'mb-mm' },
  espece:       { label:'Espèces',       icon:'ri-money-dollar-circle-line', cls:'mb-esp' },
  virement:     { label:'Virement',      icon:'ri-bank-line', cls:'mb-vir' },
};

const TYPE_META = {
  mensuel:     { label:'Mensuel',     icon:'ri-calendar-check-line', color:'#405189', bg:'rgba(64,81,137,.10)' },
  ordinaire:   { label:'Ordinaire',   icon:'ri-gift-line',            color:'#0ab39c', bg:'rgba(10,179,156,.10)' },
  jour_precis: { label:'Jour précis', icon:'ri-hand-heart-line',      color:'#d4a843', bg:'rgba(212,168,67,.12)' },
  ramadan:     { label:'Ramadan',     icon:'ri-moon-line',            color:'#299cdb', bg:'rgba(41,156,219,.12)' },
};

const AVATAR_COLORS = ['#405189','#0ab39c','#f06548','#f7b84b','#299cdb','#d4a843','#3577f1','#6559cc'];
const avColor = id => AVATAR_COLORS[(id-1) % AVATAR_COLORS.length];

/* ── Données simulées ─────────────────────────────────── */
const CUSTOMERS = [
  { id:1,  prenom:'Moussa',    nom:'Koné',      phone:'+225 07 00 11',  engagement:10000, adhesion:'2023-01-15' },
  { id:2,  prenom:'Fatoumata', nom:'Traoré',    phone:'+225 05 00 33',  engagement:5000,  adhesion:'2023-03-01' },
  { id:3,  prenom:'Ibrahim',   nom:'Diabaté',   phone:'+225 01 00 55',  engagement:15000, adhesion:'2022-09-10' },
  { id:4,  prenom:'Aminata',   nom:'Coulibaly', phone:'+225 07 00 77',  engagement:5000,  adhesion:'2024-01-20' },
  { id:5,  prenom:'Ousmane',   nom:'Bamba',     phone:'+225 05 00 99',  engagement:20000, adhesion:'2021-06-05' },
  { id:6,  prenom:'Daouda',    nom:'Ouattara',  phone:'+225 07 11 22',  engagement:10000, adhesion:'2023-07-11' },
  { id:7,  prenom:'Kadiatou',  nom:'Sanogo',    phone:'+225 01 22 33',  engagement:null,  adhesion:'2024-03-01' },
  { id:8,  prenom:'Seydou',    nom:'Touré',     phone:'+225 05 33 44',  engagement:5000,  adhesion:'2023-11-01' },
];

const TYPES_COTISATION = [
  { id:1, libelle:'Cotisation mensuelle', type:'mensuel' },
  { id:2, libelle:'Quête du vendredi',    type:'jour_precis' },
  { id:3, libelle:'Don ordinaire',        type:'ordinaire' },
  { id:4, libelle:'Ramadan 1446',         type:'ramadan' },
  { id:5, libelle:'Collecte Rénovation',  type:'ordinaire' },
];

let COTISATIONS = [
  { id:1,  customer_id:1, type_cotisation_id:1, mois:4, annee:2025, montant_du:10000, montant_paye:0,     montant_restant:10000, statut:'en_retard', mode_paiement:null,          validated_by:null, validated_at:null,               created_at:'2025-04-01' },
  { id:2,  customer_id:1, type_cotisation_id:1, mois:3, annee:2025, montant_du:10000, montant_paye:0,     montant_restant:10000, statut:'en_retard', mode_paiement:null,          validated_by:null, validated_at:null,               created_at:'2025-03-01' },
  { id:3,  customer_id:2, type_cotisation_id:1, mois:4, annee:2025, montant_du:5000,  montant_paye:5000,  montant_restant:0,     statut:'a_jour',    mode_paiement:'mobile_money',validated_by:null, validated_at:'2025-04-02 08:30', created_at:'2025-04-01' },
  { id:4,  customer_id:3, type_cotisation_id:1, mois:4, annee:2025, montant_du:15000, montant_paye:15000, montant_restant:0,     statut:'a_jour',    mode_paiement:'virement',    validated_by:null, validated_at:'2025-04-01 10:00', created_at:'2025-04-01' },
  { id:5,  customer_id:4, type_cotisation_id:1, mois:4, annee:2025, montant_du:5000,  montant_paye:2000,  montant_restant:3000,  statut:'partiel',   mode_paiement:'espece',      validated_by:1,    validated_at:'2025-04-03 14:20', created_at:'2025-04-01' },
  { id:6,  customer_id:5, type_cotisation_id:1, mois:4, annee:2025, montant_du:20000, montant_paye:20000, montant_restant:0,     statut:'a_jour',    mode_paiement:'mobile_money',validated_by:null, validated_at:'2025-04-01 09:15', created_at:'2025-04-01' },
  { id:7,  customer_id:6, type_cotisation_id:1, mois:4, annee:2025, montant_du:10000, montant_paye:0,     montant_restant:10000, statut:'en_retard', mode_paiement:null,          validated_by:null, validated_at:null,               created_at:'2025-04-01' },
  { id:8,  customer_id:6, type_cotisation_id:1, mois:3, annee:2025, montant_du:10000, montant_paye:0,     montant_restant:10000, statut:'en_retard', mode_paiement:null,          validated_by:null, validated_at:null,               created_at:'2025-03-01' },
  { id:9,  customer_id:7, type_cotisation_id:2, mois:null, annee:null, montant_du:null,montant_paye:2000,  montant_restant:0,     statut:'a_jour',    mode_paiement:'espece',      validated_by:1,    validated_at:'2025-04-04 13:30', created_at:'2025-04-04' },
  { id:10, customer_id:3, type_cotisation_id:4, mois:null, annee:null, montant_du:null,montant_paye:5000,  montant_restant:0,     statut:'a_jour',    mode_paiement:'mobile_money',validated_by:null, validated_at:'2025-03-28 20:00', created_at:'2025-03-28' },
  { id:11, customer_id:8, type_cotisation_id:1, mois:4, annee:2025, montant_du:5000,  montant_paye:5000,  montant_restant:0,     statut:'a_jour',    mode_paiement:'espece',      validated_by:1,    validated_at:'2025-04-05 11:00', created_at:'2025-04-01' },
  { id:12, customer_id:2, type_cotisation_id:1, mois:3, annee:2025, montant_du:5000,  montant_paye:5000,  montant_restant:0,     statut:'a_jour',    mode_paiement:'mobile_money',validated_by:null, validated_at:'2025-03-05 08:00', created_at:'2025-03-01' },
  { id:13, customer_id:5, type_cotisation_id:5, mois:null, annee:null, montant_du:null,montant_paye:15000, montant_restant:0,     statut:'a_jour',    mode_paiement:'virement',    validated_by:null, validated_at:'2025-04-02 16:00', created_at:'2025-04-02' },
  { id:14, customer_id:1, type_cotisation_id:1, mois:2, annee:2025, montant_du:10000, montant_paye:0,     montant_restant:10000, statut:'en_retard', mode_paiement:null,          validated_by:null, validated_at:null,               created_at:'2025-02-01' },
];

/* Historiques par cotisation */
const HISTORIQUES = {
  5:  [
    { type_operation:'creation', montant:5000,  note:'Création automatique', date:'01/04/2025', snapshot:{ statut:'en_retard', montant_paye:0 } },
    { type_operation:'paiement', montant:2000,  note:'Paiement partiel espèces',  date:'03/04/2025', snapshot:{ statut:'partiel', montant_paye:2000 } },
  ],
  3:  [
    { type_operation:'creation', montant:5000,  note:'Création automatique', date:'01/04/2025', snapshot:{ statut:'en_retard', montant_paye:0 } },
    { type_operation:'paiement', montant:5000,  note:'Paiement complet via Orange Money', date:'02/04/2025', snapshot:{ statut:'a_jour', montant_paye:5000 } },
  ],
};

/* ── État ─────────────────────────────────────────────── */
const state = {
  tabStatut:  'tous',
  search:     '',
  filterType: 'tous',
  filterMois: 'tous',
  filterMode: 'tous',
  sortCol:    'id',
  sortDir:    'desc',
  page:       1,
  perPage:    10,
  detailId:   null,
  createMode: 'create', // 'create' | 'validate'
};

/* ── Helpers ──────────────────────────────────────────── */
const fmt   = n => n != null ? new Intl.NumberFormat('fr-FR').format(n) + ' FCFA' : '—';
const fmtN  = n => n != null ? new Intl.NumberFormat('fr-FR').format(n) : '—';
const fmtDt = d => d ? new Date(d).toLocaleDateString('fr-FR',{day:'2-digit',month:'short',year:'numeric'}) : '—';
const fmtDtFull = d => d ? new Date(d).toLocaleString('fr-FR',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}) : null;

function getCustomer(id)       { return CUSTOMERS.find(c => c.id === id); }
function getTypeCot(id)        { return TYPES_COTISATION.find(t => t.id === id); }
function getTypeMeta(typeCotId){ const t = getTypeCot(typeCotId); return TYPE_META[t?.type] || {}; }
function period(c) {
  if (c.mois && c.annee) return `${MOIS_FR[c.mois]} ${c.annee}`;
  return null;
}
function pct(c) {
  if (!c.montant_du || c.montant_du === 0) return c.montant_paye > 0 ? 100 : 0;
  return Math.min(Math.round((c.montant_paye / c.montant_du) * 100), 100);
}

/* ── Filtrage ─────────────────────────────────────────── */
function getFiltered() {
  return COTISATIONS.filter(c => {
    const cust = getCustomer(c.customer_id);
    const tc   = getTypeCot(c.type_cotisation_id);

    const matchTab   = state.tabStatut === 'tous' ||
      (state.tabStatut === 'a_jour'   && c.statut === 'a_jour')   ||
      (state.tabStatut === 'partiel'  && c.statut === 'partiel')  ||
      (state.tabStatut === 'en_retard'&& c.statut === 'en_retard');

    const matchType  = state.filterType === 'tous' || c.type_cotisation_id == state.filterType;

    const matchMois  = state.filterMois === 'tous' ||
      (state.filterMois !== 'tous' && c.mois == state.filterMois);

    const matchMode  = state.filterMode === 'tous' ||
      (state.filterMode === 'nd'  && !c.mode_paiement) ||
      c.mode_paiement === state.filterMode;

    const q = state.search.toLowerCase();
    const matchSearch = !q ||
      (cust?.prenom || '').toLowerCase().includes(q) ||
      (cust?.nom    || '').toLowerCase().includes(q) ||
      (tc?.libelle  || '').toLowerCase().includes(q) ||
      (c.mois && MOIS_FR[c.mois].toLowerCase().includes(q));

    return matchTab && matchType && matchMois && matchMode && matchSearch;
  });
}

/* ── Tri ──────────────────────────────────────────────── */
function sorted(list) {
  return [...list].sort((a, b) => {
    let va, vb;
    switch (state.sortCol) {
      case 'fidele':   va = getCustomer(a.customer_id)?.nom || ''; vb = getCustomer(b.customer_id)?.nom || ''; break;
      case 'periode':  va = (a.annee||0)*100+(a.mois||0);          vb = (b.annee||0)*100+(b.mois||0);          break;
      case 'montant':  va = a.montant_du||0;                       vb = b.montant_du||0;                       break;
      case 'paye':     va = a.montant_paye;                        vb = b.montant_paye;                        break;
      case 'statut':   va = a.statut;                              vb = b.statut;                              break;
      default:         va = a.id;                                  vb = b.id;
    }
    const cmp = typeof va === 'string' ? va.localeCompare(vb) : va - vb;
    return state.sortDir === 'asc' ? cmp : -cmp;
  });
}

/* ── KPIs ─────────────────────────────────────────────── */
function renderKPIs() {
  const all     = COTISATIONS;
  const ajour   = all.filter(c => c.statut === 'a_jour').length;
  const partiel = all.filter(c => c.statut === 'partiel').length;
  const retard  = all.filter(c => c.statut === 'en_retard').length;
  const totalPayé = all.reduce((s,c) => s + c.montant_paye, 0);

  animV('kpi-co-total',   all.length,  '',     800);
  animV('kpi-co-ajour',   ajour,       '',     900);
  animV('kpi-co-partiel', partiel,     '',     900);
  animV('kpi-co-retard',  retard,      '',     900);
  animV('kpi-co-montant', totalPayé,   ' FCFA',1200);

  /* Counts dans les onglets */
  const fil = getFiltered();
  document.getElementById('cnt-tous')?.textContent    && (document.getElementById('cnt-tous').textContent    = fil.length);
  document.getElementById('cnt-ajour')?.textContent   && (document.getElementById('cnt-ajour').textContent   = fil.filter(c=>c.statut==='a_jour').length);
  document.getElementById('cnt-partiel')?.textContent && (document.getElementById('cnt-partiel').textContent = fil.filter(c=>c.statut==='partiel').length);
  document.getElementById('cnt-retard')?.textContent  && (document.getElementById('cnt-retard').textContent  = fil.filter(c=>c.statut==='en_retard').length);
}

function animV(id, target, suffix, dur=900) {
  const el = document.getElementById(id);
  if (!el) return;
  const s = performance.now();
  const ease = t => 1 - Math.pow(1-t, 3);
  (function tick(now) {
    const p = Math.min((now-s)/dur, 1);
    const v = Math.floor(ease(p)*target);
    el.textContent = new Intl.NumberFormat('fr-FR').format(v) + suffix;
    if (p < 1) requestAnimationFrame(tick);
  })(s);
}

/* ── Statut pill ──────────────────────────────────────── */
function statutPill(s) {
  const map = {
    a_jour:    ['cp-ajour',   'ri-checkbox-circle-line', 'À jour'],
    partiel:   ['cp-partiel', 'ri-error-warning-line',   'Partiel'],
    en_retard: ['cp-retard',  'ri-time-line',             'En retard'],
  };
  const [cls, icon, lbl] = map[s] || ['cp-retard','ri-question-line','Inconnu'];
  return `<span class="co-pill ${cls}"><i class="${icon}"></i>${lbl}</span>`;
}

/* ── Render table ─────────────────────────────────────── */
function renderTable() {
  const filtered = getFiltered();
  const list     = sorted(filtered);
  const start    = (state.page - 1) * state.perPage;
  const paged    = list.slice(start, start + state.perPage);
  const tbody    = document.getElementById('co-tbody');
  if (!tbody) return;

  if (!paged.length) {
    tbody.innerHTML = `<tr><td colspan="9"><div class="co-empty"><i class="ri-file-search-line"></i><p>Aucune cotisation trouvée</p></div></td></tr>`;
    renderPagination(0, filtered.length);
    renderKPIs();
    return;
  }

  tbody.innerHTML = paged.map(c => {
    const cust  = getCustomer(c.customer_id);
    const tc    = getTypeCot(c.type_cotisation_id);
    const meta  = getTypeMeta(c.type_cotisation_id);
    const init  = cust ? cust.prenom[0]+cust.nom[0] : '??';
    const color = avColor(c.customer_id);
    const per   = period(c);
    const prog  = pct(c);
    const mode  = c.mode_paiement ? MODES[c.mode_paiement] : null;
    const rowCls= `row-${c.statut === 'a_jour' ? 'ajour' : c.statut === 'partiel' ? 'partiel' : 'retard'}`;
    const valBadge = c.validated_at
      ? (c.validated_by
          ? `<span class="val-badge validated"><i class="ri-shield-check-line"></i>Admin · ${fmtDtFull(c.validated_at)?.split(' ')[0]}</span>`
          : `<span class="val-badge auto"><i class="ri-robot-line"></i>Auto</span>`)
      : `<span class="val-badge pending"><i class="ri-time-line"></i>Non validé</span>`;

    return `
      <tr class="${rowCls}" onclick="openDetail(${c.id})">
        <td>
          <div class="td-fidele">
            <div class="td-avatar" style="background:${color}">${init}</div>
            <div>
              <div class="td-fidele-name">${cust ? cust.prenom+' '+cust.nom : '—'}</div>
              <div class="td-fidele-phone">${cust?.phone || ''}</div>
            </div>
          </div>
        </td>
        <td>
          <div style="display:flex;align-items:center;gap:6px">
            <span style="width:26px;height:26px;border-radius:7px;background:${meta.bg};color:${meta.color};display:inline-flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0">
              <i class="${meta.icon}"></i>
            </span>
            <span style="font-size:12px;font-weight:600;color:var(--co-text)">${tc?.libelle || '—'}</span>
          </div>
        </td>
        <td>
          ${per
            ? `<span class="td-period">${per}</span>`
            : `<span class="td-period tp-nonmensuel">Ponctuel</span>`}
        </td>
        <td>
          <div class="td-montants">
            <div class="tm-du">${c.montant_du != null ? fmt(c.montant_du) : '—'}</div>
            ${c.montant_du != null ? `
            <div class="tm-bar">
              <div class="tm-bar-fill" style="width:${prog}%;background:${prog===100?'var(--co-accent)':prog>0?'var(--co-warning)':'var(--co-danger)'}"></div>
            </div>` : ''}
          </div>
        </td>
        <td><span class="tm-paye" style="font-size:12px;font-weight:700;color:var(--co-accent);font-family:var(--co-mono)">${fmt(c.montant_paye)}</span></td>
        <td>
          ${c.montant_restant > 0
            ? `<span class="tm-restant" style="font-size:12px;font-weight:700;color:var(--co-danger);font-family:var(--co-mono)">${fmt(c.montant_restant)}</span>`
            : `<span style="color:var(--co-muted);font-size:11px">—</span>`}
        </td>
        <td>${statutPill(c.statut)}</td>
        <td>
          ${mode
            ? `<span class="mode-badge ${mode.cls}"><i class="${mode.icon}"></i>${mode.label}</span>`
            : `<span class="mode-badge mb-nd"><i class="ri-question-line"></i>—</span>`}
        </td>
        <td onclick="event.stopPropagation()">
          <div class="td-actions">
            <button class="btn btn-soft-primary waves-effect" onclick="openDetail(${c.id})" title="Détails"><i class="ri-eye-line"></i></button>
            ${!c.validated_at && c.mode_paiement === 'espece'
              ? `<button class="btn btn-soft-success waves-effect" onclick="validateCotisation(${c.id})" title="Valider"><i class="ri-checkbox-circle-line"></i></button>`
              : ''}
            ${c.statut !== 'a_jour'
              ? `<button class="btn btn-soft-warning waves-effect" onclick="openCreate(${c.customer_id})" title="Enregistrer paiement"><i class="ri-money-cny-circle-line"></i></button>`
              : ''}
          </div>
        </td>
      </tr>`;
  }).join('');

  renderPagination(start, filtered.length);
  renderKPIs();

  /* Animer les barres de progression */
  setTimeout(() => {
    document.querySelectorAll('.tm-bar-fill').forEach(el => {
      const w = el.style.width;
      el.style.width = '0';
      requestAnimationFrame(() => { el.style.width = w; });
    });
  }, 50);
}

/* ── Pagination ───────────────────────────────────────── */
function renderPagination(start, total) {
  const pages   = Math.ceil(total / state.perPage);
  const current = state.page;
  const from    = total ? start + 1 : 0;
  const to      = Math.min(start + state.perPage, total);

  const infoEl = document.getElementById('co-pag-info');
  const btnsEl = document.getElementById('co-pag-btns');
  if (infoEl) infoEl.textContent = `Affichage de ${from} à ${to} sur ${total} cotisation(s)`;
  if (!btnsEl) return;

  let html = `<button class="co-pag-btn" onclick="goPage(${current-1})" ${current===1?'disabled':''}><i class="ri-arrow-left-s-line"></i></button>`;
  for (let p = 1; p <= pages; p++) {
    if (pages > 7 && p > 3 && p < pages - 1 && Math.abs(p-current) > 1) {
      if (p === 4 || p === pages - 2) html += `<button class="co-pag-btn" disabled>…</button>`;
      continue;
    }
    html += `<button class="co-pag-btn ${p===current?'active':''}" onclick="goPage(${p})">${p}</button>`;
  }
  html += `<button class="co-pag-btn" onclick="goPage(${current+1})" ${current===pages||!pages?'disabled':''}><i class="ri-arrow-right-s-line"></i></button>`;
  btnsEl.innerHTML = html;
}

function goPage(p) {
  const total = getFiltered().length;
  const pages = Math.ceil(total / state.perPage);
  if (p < 1 || p > pages) return;
  state.page = p;
  renderTable();
}

/* ── Tri colonnes ─────────────────────────────────────── */
function sortBy(col) {
  if (state.sortCol === col) state.sortDir = state.sortDir === 'asc' ? 'desc' : 'asc';
  else { state.sortCol = col; state.sortDir = 'desc'; }
  document.querySelectorAll('.co-table thead th').forEach(th => {
    th.classList.remove('sorted');
    const si = th.querySelector('.sort-icon');
    if (si) si.className = 'sort-icon ri-arrow-up-down-line';
  });
  const activeTh = document.getElementById(`th-${col}`);
  if (activeTh) {
    activeTh.classList.add('sorted');
    const si = activeTh.querySelector('.sort-icon');
    if (si) si.className = `sort-icon ri-arrow-${state.sortDir === 'asc' ? 'up' : 'down'}-line`;
  }
  renderTable();
}

/* ── Modal DÉTAIL ─────────────────────────────────────── */
function openDetail(id) {
  const c    = COTISATIONS.find(x => x.id === id);
  if (!c) return;
  state.detailId = id;

  const cust = getCustomer(c.customer_id);
  const tc   = getTypeCot(c.type_cotisation_id);
  const meta = getTypeMeta(c.type_cotisation_id);
  const prog = pct(c);

  /* Header couleur selon statut */
  const statColors = { a_jour:'#0ab39c,#0ab39c', partiel:'#f7b84b,#d4870a', en_retard:'#f06548,#d63c1f' };
  const [c1,c2] = (statColors[c.statut] || '').split(',');
  const hdr = document.getElementById('co-modal-hdr');
  hdr.style.background = `linear-gradient(130deg,${c1}dd 0%,${c2} 100%)`;

  document.getElementById('cmh-icon').innerHTML   = `<i class="${meta.icon || 'ri-file-list-line'}"></i>`;
  document.getElementById('cmh-name').textContent = cust ? `${cust.prenom} ${cust.nom}` : '—';
  const per = period(c);
  document.getElementById('cmh-type').textContent   = tc?.libelle || '—';
  document.getElementById('cmh-period').textContent = per || 'Ponctuel';
  document.getElementById('cmh-statut').innerHTML   = statutPill(c.statut);

  /* Stats */
  document.getElementById('cms1').textContent = c.montant_du != null ? fmtN(c.montant_du) + ' FCFA' : '—';
  document.getElementById('cms2').textContent = fmtN(c.montant_paye) + ' FCFA';
  document.getElementById('cms3').textContent = c.montant_restant > 0 ? fmtN(c.montant_restant) + ' FCFA' : '—';
  document.getElementById('cms4').textContent = prog + '%';
  document.getElementById('cms4').style.color = prog === 100 ? '#0ab39c' : prog > 0 ? '#f7b84b' : '#f06548';

  /* Progress bar */
  const fill = document.getElementById('cpp-fill');
  const fillColor = prog === 100 ? '#0ab39c' : prog > 0 ? '#f7b84b' : '#f06548';
  fill.style.background = fillColor;
  fill.style.width = '0%';
  document.getElementById('cpp-pct').textContent    = prog + '%';
  document.getElementById('cpp-pct').style.color    = fillColor;
  document.getElementById('cpp-paid').textContent   = fmt(c.montant_paye) + ' payés';
  document.getElementById('cpp-due').textContent    = c.montant_restant > 0 ? fmt(c.montant_restant) + ' restant' : 'Soldé ✓';
  setTimeout(() => { fill.style.width = prog + '%'; }, 200);

  /* Infos détail */
  document.getElementById('di-type').innerHTML     = `<span style="display:inline-flex;align-items:center;gap:6px"><span style="width:22px;height:22px;border-radius:6px;background:${meta.bg};color:${meta.color};display:inline-flex;align-items:center;justify-content:center;font-size:12px"><i class="${meta.icon}"></i></span>${tc?.libelle||'—'}</span>`;
  document.getElementById('di-period').textContent = per || 'Ponctuel (pas de mois)';
  document.getElementById('di-mode').innerHTML     = c.mode_paiement
    ? `<span class="mode-badge ${MODES[c.mode_paiement]?.cls}"><i class="${MODES[c.mode_paiement]?.icon}"></i>${MODES[c.mode_paiement]?.label}</span>`
    : '<span style="color:var(--co-muted);font-size:12px">Non renseigné</span>';
  document.getElementById('di-created').textContent = fmtDt(c.created_at);
  document.getElementById('di-validated').innerHTML = c.validated_at
    ? `<span style="color:#0ab39c;font-weight:700"><i class="ri-shield-check-line me-1"></i>${fmtDtFull(c.validated_at)}</span>${c.validated_by ? ' · par Admin' : ' · automatique'}`
    : '<span style="color:var(--co-warning);font-weight:600"><i class="ri-time-line me-1"></i>Non validé</span>';
  document.getElementById('di-fidele').textContent  = cust ? `${cust.prenom} ${cust.nom}` : '—';
  document.getElementById('di-engagement').textContent = cust?.engagement ? fmt(cust.engagement) + '/mois' : 'Sans engagement';

  /* Historique */
  const hists   = HISTORIQUES[id] || [];
  const histEl  = document.getElementById('co-hist-list');
  const opMeta  = {
    creation:    { cls:'hi-creation',   icon:'ri-add-circle-line',     color:'#405189', bg:'rgba(64,81,137,.12)'  },
    paiement:    { cls:'hi-paiement',   icon:'ri-money-cny-circle-line', color:'#0ab39c', bg:'rgba(10,179,156,.12)' },
    echec:       { cls:'hi-echec',      icon:'ri-error-warning-line',   color:'#f06548', bg:'rgba(240,101,72,.12)' },
    ajustement:  { cls:'hi-ajustement', icon:'ri-edit-circle-line',     color:'#f7b84b', bg:'rgba(247,184,75,.15)' },
  };
  histEl.innerHTML = hists.length
    ? hists.map(h => {
        const om = opMeta[h.type_operation] || opMeta.creation;
        return `
          <div class="co-hist-item ${om.cls}">
            <div class="hi-icon" style="background:${om.bg};color:${om.color}"><i class="${om.icon}"></i></div>
            <div class="hi-body">
              <div class="hi-op">${h.note || h.type_operation}</div>
              <div class="hi-meta">${h.date} · snapshot : statut=${h.snapshot.statut}, payé=${fmtN(h.snapshot.montant_paye)} FCFA</div>
            </div>
            <div class="hi-amt" style="color:${om.color}">+${fmtN(h.montant)} FCFA</div>
          </div>`;
      }).join('')
    : `<div style="text-align:center;padding:20px;color:var(--co-muted);font-size:13px">
        <i class="ri-inbox-line" style="font-size:28px;display:block;margin-bottom:8px;opacity:.4"></i>Aucun historique
       </div>`;

  /* Boutons d'action dans le modal */
  const actionBar = document.getElementById('detail-actions');
  actionBar.innerHTML = '';
  if (!c.validated_at && c.mode_paiement === 'espece') {
    actionBar.innerHTML += `<button class="btn btn-success waves-effect" onclick="validateCotisation(${c.id});bootstrap.Modal.getInstance(document.getElementById('modalDetailCotisation'))?.hide()">
      <i class="ri-shield-check-line me-1"></i>Valider ce paiement
    </button>`;
  }
  if (c.statut !== 'a_jour') {
    actionBar.innerHTML += `<button class="btn btn-primary waves-effect ms-2" onclick="bootstrap.Modal.getInstance(document.getElementById('modalDetailCotisation'))?.hide();openCreate(${c.customer_id})">
      <i class="ri-money-cny-circle-line me-1"></i>Enregistrer paiement
    </button>`;
  }

  new bootstrap.Modal(document.getElementById('modalDetailCotisation')).show();
}

/* ── Valider cotisation espèces ───────────────────────── */
function validateCotisation(id) {
  const c = COTISATIONS.find(x => x.id === id);
  if (!c) return;
  if (typeof Swal !== 'undefined') {
    Swal.fire({
      title: 'Valider ce paiement ?',
      html: `<p>Vous confirmez la réception de <strong>${fmt(c.montant_paye)}</strong> en espèces.</p>`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Oui, valider',
      cancelButtonText: 'Annuler',
      confirmButtonColor: '#0ab39c',
    }).then(r => {
      if (r.isConfirmed) {
        c.validated_by = 1;
        c.validated_at = new Date().toISOString();
        renderTable();
        toast('Paiement validé avec succès !', 'success');
      }
    });
  } else {
    c.validated_by = 1;
    c.validated_at = new Date().toISOString();
    renderTable();
  }
}

/* ── Modal CRÉER COTISATION (BO) ──────────────────────── */
function openCreate(preCustomerId = null) {
  state.createMode = 'create';
  document.getElementById('co-create-title').textContent = 'Enregistrer un paiement';
  document.getElementById('co-create-sub').textContent   = 'Saisie manuelle BO – Espèces ou autre mode';
  document.getElementById('co-form').reset();

  /* Reset mode paiement */
  document.querySelectorAll('.mode-btn').forEach(b => b.classList.remove('selected'));
  document.getElementById('hidden-mode').value = '';

  /* Pré-sélectionner fidèle */
  if (preCustomerId) {
    const sel = document.getElementById('f-customer');
    if (sel) sel.value = preCustomerId;
    updateFideleCard(preCustomerId);
  } else {
    hideFideleCard();
  }

  /* Reset report */
  document.getElementById('report-calc').style.display = 'none';
  document.getElementById('validate-toggle').checked   = true;

  new bootstrap.Modal(document.getElementById('modalCreateCotisation')).show();
}

function updateFideleCard(customerId) {
  const c = CUSTOMERS.find(x => x.id == customerId);
  if (!c) { hideFideleCard(); return; }

  const color = avColor(c.id);
  const init  = c.prenom[0] + c.nom[0];
  document.getElementById('fsc-avatar').style.background = color;
  document.getElementById('fsc-avatar').textContent      = init;
  document.getElementById('fsc-name').textContent        = `${c.prenom} ${c.nom}`;
  document.getElementById('fsc-phone').textContent       = c.phone;
  document.getElementById('fsc-eng').style.display       = c.engagement ? '' : 'none';
  document.getElementById('fsc-eng-val').textContent     = c.engagement ? fmtN(c.engagement) : '';
  document.getElementById('fidele-card').style.display   = '';

  /* Si mensuel sélectionné et montant saisi → calculer report */
  computeReport(customerId);
}

function hideFideleCard() {
  document.getElementById('fidele-card').style.display   = 'none';
  document.getElementById('report-calc').style.display   = 'none';
}

function computeReport(customerId) {
  const montant = parseInt(document.getElementById('f-montant').value) || 0;
  const typeId  = parseInt(document.getElementById('f-type').value) || 0;
  const tc      = getTypeCot(typeId);
  const cust    = CUSTOMERS.find(c => c.id == customerId);

  if (!tc || tc.type !== 'mensuel' || !cust?.engagement || montant < 1) {
    document.getElementById('report-calc').style.display = 'none';
    return;
  }

  /* Trouver la dernière cotisation mensuelle */
  const existing = COTISATIONS.filter(c =>
    c.customer_id == customerId && c.type_cotisation_id === typeId
  ).sort((a,b) => b.annee*100+b.mois - (a.annee*100+a.mois));

  const eng = cust.engagement;
  let remaining = montant;
  const rows   = [];
  let mois     = 4, annee = 2025; // mois courant simulé

  /* Solde les mois en retard d'abord */
  if (existing.length > 0) {
    const last = existing[0];
    if (last.statut !== 'a_jour') {
      const toSolde = Math.min(remaining, last.montant_restant);
      remaining -= toSolde;
      const newPaye = last.montant_paye + toSolde;
      const newStatut = newPaye >= last.montant_du ? 'a_jour' : 'partiel';
      rows.push({ mois: MOIS_FR[last.mois]+' '+last.annee, amount: toSolde, statut: newStatut });
      mois = last.mois; annee = last.annee;
    }
    mois = existing[0].mois; annee = existing[0].annee;
  }

  /* Couvre les mois suivants */
  while (remaining >= eng) {
    mois++; if (mois > 12) { mois = 1; annee++; }
    rows.push({ mois: MOIS_FR[mois]+' '+annee, amount: eng, statut: 'a_jour' });
    remaining -= eng;
  }
  /* Mois partiel */
  if (remaining > 0) {
    mois++; if (mois > 12) { mois = 1; annee++; }
    rows.push({ mois: MOIS_FR[mois]+' '+annee, amount: remaining, statut: 'partiel' });
  }

  if (!rows.length) { document.getElementById('report-calc').style.display = 'none'; return; }

  document.getElementById('report-calc').style.display = '';
  document.getElementById('rc-rows').innerHTML = rows.map(r => `
    <div class="rc-row">
      <span class="rr-mois"><i class="ri-calendar-line me-1"></i>${r.mois}</span>
      <span style="display:flex;align-items:center;gap:6px">
        <span class="rr-amount">${fmtN(r.amount)} FCFA</span>
        <span class="rr-status ${r.statut==='a_jour'?'s-solde':'s-partiel'}">${r.statut==='a_jour'?'Soldé':'Partiel'}</span>
      </span>
    </div>`).join('');
}

/* ── Sauvegarde BO ────────────────────────────────────── */
function saveCreateForm() {
  let valid = true;
  ['f-customer','f-type','f-montant'].forEach(id => {
    const el  = document.getElementById(id);
    const err = document.getElementById(id+'-err');
    if (!el.value || !el.value.trim()) {
      el.classList.add('is-err');
      if(err){ err.textContent='Requis.'; err.classList.add('show'); }
      valid = false;
    } else {
      el.classList.remove('is-err');
      if(err) err.classList.remove('show');
    }
  });

  const mode = document.getElementById('hidden-mode').value;
  if (!mode) {
    toast('Sélectionnez un mode de paiement.', 'warning');
    valid = false;
  }

  if (!valid) return;

  const customerId = parseInt(document.getElementById('f-customer').value);
  const typeId     = parseInt(document.getElementById('f-type').value);
  const montant    = parseInt(document.getElementById('f-montant').value);
  const tc         = getTypeCot(typeId);
  const cust       = CUSTOMERS.find(c => c.id === customerId);
  const validated  = document.getElementById('validate-toggle').checked;
  const moisInput  = parseInt(document.getElementById('f-mois').value) || null;
  const anneeInput = parseInt(document.getElementById('f-annee').value) || null;

  /* Créer la cotisation */
  const newId = Math.max(...COTISATIONS.map(c => c.id), 0) + 1;
  const isMensuel = tc?.type === 'mensuel';

  const newCot = {
    id: newId,
    customer_id: customerId,
    type_cotisation_id: typeId,
    mois:  isMensuel ? moisInput : null,
    annee: isMensuel ? anneeInput : null,
    montant_du:      isMensuel && cust?.engagement ? cust.engagement : (isMensuel ? montant : null),
    montant_paye:    montant,
    montant_restant: isMensuel && cust?.engagement ? Math.max(0, cust.engagement - montant) : 0,
    statut: isMensuel && cust?.engagement
      ? (montant >= cust.engagement ? 'a_jour' : 'partiel')
      : 'a_jour',
    mode_paiement: mode,
    validated_by:  validated ? 1 : null,
    validated_at:  validated ? new Date().toISOString() : null,
    created_at:    new Date().toISOString().split('T')[0],
  };

  COTISATIONS.unshift(newCot);

  /* Log historique */
  HISTORIQUES[newId] = [{
    type_operation: 'creation',
    montant: montant,
    note: `Saisie BO – ${MODES[mode]?.label || mode}`,
    date: new Date().toLocaleDateString('fr-FR',{day:'2-digit',month:'short',year:'numeric'}),
    snapshot: { statut: newCot.statut, montant_paye: montant },
  }];

  bootstrap.Modal.getInstance(document.getElementById('modalCreateCotisation'))?.hide();
  state.page = 1;
  renderTable();
  toast('Cotisation enregistrée avec succès !', 'success');
}

/* ── Toast ────────────────────────────────────────────── */
function toast(msg, icon='success') {
  if (typeof Swal !== 'undefined') {
    Swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000,timerProgressBar:true})
      .fire({icon,title:msg});
  }
}

/* ── INIT ─────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
  renderTable();

  /* Tabs statut */
  document.querySelectorAll('.co-tab').forEach(tab => {
    tab.addEventListener('click', function () {
      document.querySelectorAll('.co-tab').forEach(t => t.classList.remove('active'));
      this.classList.add('active');
      state.tabStatut = this.dataset.statut;
      state.page = 1;
      renderTable();
    });
  });

  /* Recherche */
  document.getElementById('co-search')?.addEventListener('input', function () {
    state.search = this.value; state.page = 1; renderTable();
  });

  /* Filtres */
  document.getElementById('co-filter-type')?.addEventListener('change', function () {
    state.filterType = this.value; state.page = 1; renderTable();
  });
  document.getElementById('co-filter-mois')?.addEventListener('change', function () {
    state.filterMois = this.value; state.page = 1; renderTable();
  });
  document.getElementById('co-filter-mode')?.addEventListener('change', function () {
    state.filterMode = this.value; state.page = 1; renderTable();
  });

  /* Mode paiement pills */
  document.querySelectorAll('.mode-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.mode-btn').forEach(b => b.classList.remove('selected'));
      this.classList.add('selected');
      document.getElementById('hidden-mode').value = this.dataset.mode;

      /* Afficher champ référence si mobile_money */
      const refWrap = document.getElementById('ref-wrap');
      if (refWrap) refWrap.style.display = this.dataset.mode === 'mobile_money' ? '' : 'none';
    });
  });

  /* Fidèle sélectionné dans le modal create */
  document.getElementById('f-customer')?.addEventListener('change', function () {
    if (this.value) updateFideleCard(parseInt(this.value));
    else hideFideleCard();
  });

  /* Recalculer report quand montant ou type change */
  ['f-montant','f-type'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', function () {
      const custId = parseInt(document.getElementById('f-customer').value);
      if (custId) computeReport(custId);
    });
  });

  /* Clear errors */
  document.querySelectorAll('.input-co').forEach(el => {
    el.addEventListener('input', function () {
      this.classList.remove('is-err');
      const err = document.getElementById(this.id+'-err');
      if (err) err.classList.remove('show');
    });
  });

  /* Type mensuel → afficher champs mois/année */
  document.getElementById('f-type')?.addEventListener('change', function () {
    const tc = TYPES_COTISATION.find(t => t.id == this.value);
    const periodeWrap = document.getElementById('periode-wrap');
    if (periodeWrap) periodeWrap.style.display = tc?.type === 'mensuel' ? '' : 'none';
    const custId = parseInt(document.getElementById('f-customer').value);
    if (custId) computeReport(custId);
  });
});
