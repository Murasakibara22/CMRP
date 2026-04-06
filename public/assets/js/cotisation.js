/* ============================================================
   MOSQUÉE – Cotisations JS (version HTML statique)
   Logique métier : engagement, report mensuel, statuts
   Modal ouvert 100% via JS pur (pattern customers/paiements)
   ============================================================ */
'use strict';

/* ── Couleurs & constantes ──────────────────────────────── */
const AVATAR_COLORS = ['#405189','#0ab39c','#f06548','#f7b84b','#299cdb','#d4a843','#3577f1','#6559cc','#ea4c4c','#2dce89'];
const MOIS_FR = ['','Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
const avColor = id => AVATAR_COLORS[(id - 1) % AVATAR_COLORS.length];

/* ── Données statiques ─────────────────────────────────── */
const CUSTOMERS = [
  { id:1, prenom:'Moussa',    nom:'Koné',      phone:'+225 07 00 11 22', engagement:10000 },
  { id:2, prenom:'Fatoumata', nom:'Traoré',    phone:'+225 05 00 33 44', engagement:5000  },
  { id:3, prenom:'Ibrahim',   nom:'Diabaté',   phone:'+225 01 00 55 66', engagement:15000 },
  { id:4, prenom:'Aminata',   nom:'Coulibaly', phone:'+225 07 00 77 88', engagement:5000  },
  { id:5, prenom:'Ousmane',   nom:'Bamba',     phone:'+225 05 00 99 00', engagement:20000 },
  { id:6, prenom:'Daouda',    nom:'Ouattara',  phone:'+225 07 11 22 33', engagement:10000 },
  { id:7, prenom:'Kadiatou',  nom:'Sanogo',    phone:'+225 01 22 33 44', engagement:null  },
  { id:8, prenom:'Seydou',    nom:'Touré',     phone:'+225 05 33 44 55', engagement:5000  },
];

const TYPES_COTISATION = [
  { id:1, libelle:'Cotisation mensuelle', type:'mensuel',     is_required:true  },
  { id:2, libelle:'Quête du vendredi',    type:'jour_precis', is_required:false },
  { id:3, libelle:'Don ordinaire',        type:'ordinaire',   is_required:false },
  { id:4, libelle:'Ramadan 1446',         type:'ramadan',     is_required:false },
  { id:5, libelle:'Collecte Rénovation',  type:'ordinaire',   is_required:false },
];

const TYPE_META = {
  mensuel:     { icon:'ri-calendar-check-line', color:'#405189', bg:'rgba(64,81,137,.10)',  grad:'linear-gradient(130deg,#2d3a63,#405189)' },
  ordinaire:   { icon:'ri-gift-line',           color:'#0ab39c', bg:'rgba(10,179,156,.10)', grad:'linear-gradient(130deg,#0a7a6a,#0ab39c)' },
  jour_precis: { icon:'ri-hand-heart-line',     color:'#d4a843', bg:'rgba(212,168,67,.12)', grad:'linear-gradient(130deg,#a07c10,#d4a843)' },
  ramadan:     { icon:'ri-moon-line',           color:'#299cdb', bg:'rgba(41,156,219,.12)', grad:'linear-gradient(130deg,#1a6080,#299cdb)' },
};

let COTISATIONS = [
  { id:1,  customer_id:1, type_id:1, mois:4, annee:2025, montant_du:10000, montant_paye:0,     montant_restant:10000, statut:'en_retard', mode:null,          validated_by:null, validated_at:null,           created:'2025-04-01' },
  { id:2,  customer_id:1, type_id:1, mois:3, annee:2025, montant_du:10000, montant_paye:0,     montant_restant:10000, statut:'en_retard', mode:null,          validated_by:null, validated_at:null,           created:'2025-03-01' },
  { id:3,  customer_id:2, type_id:1, mois:4, annee:2025, montant_du:5000,  montant_paye:5000,  montant_restant:0,     statut:'a_jour',    mode:'mobile_money',validated_by:null, validated_at:'2025-04-02 08:30', created:'2025-04-01' },
  { id:4,  customer_id:3, type_id:1, mois:4, annee:2025, montant_du:15000, montant_paye:15000, montant_restant:0,     statut:'a_jour',    mode:'virement',    validated_by:null, validated_at:'2025-04-01 10:00', created:'2025-04-01' },
  { id:5,  customer_id:4, type_id:1, mois:4, annee:2025, montant_du:5000,  montant_paye:2000,  montant_restant:3000,  statut:'partiel',   mode:'espece',      validated_by:1,    validated_at:'2025-04-03 14:20', created:'2025-04-01' },
  { id:6,  customer_id:5, type_id:1, mois:4, annee:2025, montant_du:20000, montant_paye:20000, montant_restant:0,     statut:'a_jour',    mode:'mobile_money',validated_by:null, validated_at:'2025-04-01 09:15', created:'2025-04-01' },
  { id:7,  customer_id:6, type_id:1, mois:4, annee:2025, montant_du:10000, montant_paye:0,     montant_restant:10000, statut:'en_retard', mode:null,          validated_by:null, validated_at:null,           created:'2025-04-01' },
  { id:8,  customer_id:6, type_id:1, mois:3, annee:2025, montant_du:10000, montant_paye:0,     montant_restant:10000, statut:'en_retard', mode:null,          validated_by:null, validated_at:null,           created:'2025-03-01' },
  { id:9,  customer_id:7, type_id:2, mois:null, annee:null, montant_du:null, montant_paye:2000, montant_restant:0,   statut:'a_jour',    mode:'espece',      validated_by:1,    validated_at:'2025-04-04 13:30', created:'2025-04-04' },
  { id:10, customer_id:3, type_id:4, mois:null, annee:null, montant_du:null, montant_paye:5000, montant_restant:0,   statut:'a_jour',    mode:'mobile_money',validated_by:null, validated_at:'2025-03-28 20:00', created:'2025-03-28' },
  { id:11, customer_id:8, type_id:1, mois:4, annee:2025, montant_du:5000,  montant_paye:5000,  montant_restant:0,     statut:'a_jour',    mode:'espece',      validated_by:1,    validated_at:'2025-04-05 11:00', created:'2025-04-01' },
  { id:12, customer_id:2, type_id:1, mois:3, annee:2025, montant_du:5000,  montant_paye:5000,  montant_restant:0,     statut:'a_jour',    mode:'mobile_money',validated_by:null, validated_at:'2025-03-05 08:00', created:'2025-03-01' },
  { id:13, customer_id:5, type_id:5, mois:null, annee:null, montant_du:null, montant_paye:15000,montant_restant:0,   statut:'a_jour',    mode:'virement',    validated_by:null, validated_at:'2025-04-02 16:00', created:'2025-04-02' },
  { id:14, customer_id:1, type_id:1, mois:2, annee:2025, montant_du:10000, montant_paye:0,     montant_restant:10000, statut:'en_retard', mode:null,          validated_by:null, validated_at:null,           created:'2025-02-01' },
];

const HISTORIQUES = {
  5:  [
    { cls:'hi-creation',   icon:'ri-add-circle-line',          bg:'rgba(64,81,137,.10)',  color:'#405189', note:'Création automatique',          date:'01/04/2025', montant:5000 },
    { cls:'hi-paiement',   icon:'ri-money-dollar-circle-line', bg:'rgba(10,179,156,.12)', color:'#0ab39c', note:'Paiement partiel – espèces',     date:'03/04/2025', montant:2000 },
  ],
  3:  [
    { cls:'hi-creation',   icon:'ri-add-circle-line',          bg:'rgba(64,81,137,.10)',  color:'#405189', note:'Création automatique',           date:'01/04/2025', montant:5000 },
    { cls:'hi-paiement',   icon:'ri-money-dollar-circle-line', bg:'rgba(10,179,156,.12)', color:'#0ab39c', note:'Paiement complet – Orange Money', date:'02/04/2025', montant:5000 },
  ],
  11: [
    { cls:'hi-creation',   icon:'ri-add-circle-line',          bg:'rgba(64,81,137,.10)',  color:'#405189', note:'Création automatique',           date:'01/04/2025', montant:5000 },
    { cls:'hi-paiement',   icon:'ri-money-dollar-circle-line', bg:'rgba(10,179,156,.12)', color:'#0ab39c', note:'Paiement espèces – Admin',        date:'05/04/2025', montant:5000 },
  ],
};

/* ── État ──────────────────────────────────────────────── */
const state = {
  tab:     'tous',
  search:  '',
  type:    'tous',
  mois:    'tous',
  mode:    'tous',
  page:    1,
  perPage: 10,
  selectedCustomerId: null,
};

/* ── Helpers ───────────────────────────────────────────── */
const getCust = id => CUSTOMERS.find(c => c.id === id);
const getTC   = id => TYPES_COTISATION.find(t => t.id === id);
const fmt  = n => n != null ? new Intl.NumberFormat('fr-FR').format(n) + ' FCFA' : '—';
const fmtN = n => n != null ? new Intl.NumberFormat('fr-FR').format(n) : '—';
const pct  = c => {
  if (!c.montant_du) return c.montant_paye > 0 ? 100 : 0;
  return Math.min(Math.round(c.montant_paye / c.montant_du * 100), 100);
};
const period = c => c.mois && c.annee ? `${MOIS_FR[c.mois]} ${c.annee}` : null;

/* ── Filtrage ──────────────────────────────────────────── */
function getFiltered() {
  return COTISATIONS.filter(c => {
    const cust = getCust(c.customer_id);
    const tc   = getTC(c.type_id);
    const q    = state.search.toLowerCase();
    const matchTab    = state.tab  === 'tous' || c.statut      === state.tab;
    const matchType   = state.type === 'tous' || String(c.type_id) === state.type;
    const matchMois   = state.mois === 'tous' || String(c.mois)    === state.mois;
    const matchMode   = state.mode === 'tous' || (state.mode === 'nd' ? !c.mode : c.mode === state.mode);
    const matchSearch = !q ||
      (cust?.prenom||'').toLowerCase().includes(q) ||
      (cust?.nom||'').toLowerCase().includes(q)    ||
      (tc?.libelle||'').toLowerCase().includes(q)  ||
      (c.mois && MOIS_FR[c.mois].toLowerCase().includes(q));
    return matchTab && matchType && matchMois && matchMode && matchSearch;
  });
}

/* ── KPIs ──────────────────────────────────────────────── */
function renderKPIs() {
  const all     = COTISATIONS;
  const ajour   = all.filter(c => c.statut==='a_jour').length;
  const partiel = all.filter(c => c.statut==='partiel').length;
  const retard  = all.filter(c => c.statut==='en_retard').length;
  const montant = all.reduce((s,c) => s+c.montant_paye, 0);
  animVal('kpi-total',   all.length, '');
  animVal('kpi-ajour',   ajour,      '');
  animVal('kpi-partiel', partiel,    '');
  animVal('kpi-retard',  retard,     '');
  document.getElementById('kpi-montant').textContent = new Intl.NumberFormat('fr-FR').format(montant) + ' FCFA';
}

function animVal(id, target, suffix='', dur=900) {
  const el = document.getElementById(id); if (!el) return;
  const s = performance.now();
  const ease = t => 1 - Math.pow(1-t, 3);
  (function tick(now) {
    const p = Math.min((now-s)/dur, 1);
    el.textContent = Math.floor(ease(p)*target) + suffix;
    if (p < 1) requestAnimationFrame(tick);
  })(s);
}

/* ── Tabs counts ───────────────────────────────────────── */
function updateTabCounts() {
  const fil = getFiltered();
  document.getElementById('cnt-tous').textContent    = fil.length;
  document.getElementById('cnt-ajour').textContent   = fil.filter(c=>c.statut==='a_jour').length;
  document.getElementById('cnt-partiel').textContent = fil.filter(c=>c.statut==='partiel').length;
  document.getElementById('cnt-retard').textContent  = fil.filter(c=>c.statut==='en_retard').length;
}

/* ── Statut pill ───────────────────────────────────────── */
function statutPill(s) {
  const m = { a_jour:['cp-ajour','ri-checkbox-circle-line','À jour'], partiel:['cp-partiel','ri-error-warning-line','Partiel'], en_retard:['cp-retard','ri-time-line','En retard'] };
  const [cls,icon,lbl] = m[s] || ['cp-retard','ri-question-line','Inconnu'];
  return `<span class="co-pill ${cls}"><i class="${icon}"></i>${lbl}</span>`;
}

/* ── Mode badge ────────────────────────────────────────── */
function modeBadge(mode) {
  const m = { mobile_money:['mb-mm','ri-smartphone-line','Mobile Money'], espece:['mb-esp','ri-money-dollar-circle-line','Espèces'], virement:['mb-vir','ri-bank-line','Virement'] };
  if (!mode) return `<span class="mode-badge mb-nd"><i class="ri-question-line"></i>—</span>`;
  const [cls,icon,lbl] = m[mode] || ['mb-nd','ri-question-line','—'];
  return `<span class="mode-badge ${cls}"><i class="${icon}"></i>${lbl}</span>`;
}

/* ── Render table ──────────────────────────────────────── */
function renderTable() {
  const filtered = getFiltered();
  const start    = (state.page-1)*state.perPage;
  const paged    = filtered.slice(start, start+state.perPage);
  const tbody    = document.getElementById('co-tbody');
  if (!tbody) return;

  if (!paged.length) {
    tbody.innerHTML = `<tr><td colspan="9"><div class="co-empty"><i class="ri-file-search-line"></i><p>Aucune cotisation trouvée</p></div></td></tr>`;
    renderPagination(0, filtered.length);
    updateTabCounts();
    return;
  }

  const rowCls = { a_jour:'row-ajour', partiel:'row-partiel', en_retard:'row-retard' };

  tbody.innerHTML = paged.map(c => {
    const cust  = getCust(c.customer_id);
    const tc    = getTC(c.type_id);
    const meta  = TYPE_META[tc?.type] || TYPE_META.ordinaire;
    const init  = cust ? cust.prenom[0]+cust.nom[0] : '??';
    const col   = avColor(c.customer_id);
    const per   = period(c);
    const p     = pct(c);
    const barColor = p===100 ? 'var(--co-accent)' : p>0 ? 'var(--co-warning)' : 'var(--co-danger)';
    const cls   = rowCls[c.statut] || '';
    const valBadge = c.validated_at
      ? (c.validated_by
          ? `<span class="val-badge validated"><i class="ri-shield-check-line"></i>Admin validé</span>`
          : `<span class="val-badge auto"><i class="ri-robot-line"></i>Auto</span>`)
      : `<span class="val-badge pending"><i class="ri-time-line"></i>En attente</span>`;

    return `
      <tr class="${cls}" onclick="openDetail(${c.id})">
        <td>
          <div class="td-fidele">
            <div class="td-avatar" style="background:${col}">${init}</div>
            <div>
              <div class="td-fidele-name">${cust ? cust.prenom+' '+cust.nom : '—'}</div>
              <div class="td-fidele-phone">${cust?.phone||''}</div>
            </div>
          </div>
        </td>
        <td>
          <div style="display:flex;align-items:center;gap:6px">
            <span style="width:26px;height:26px;border-radius:7px;background:${meta.bg};color:${meta.color};display:inline-flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0"><i class="${meta.icon}"></i></span>
            <span style="font-size:12px;font-weight:600;color:var(--co-text)">${tc?.libelle||'—'}</span>
          </div>
        </td>
        <td>${per ? `<span class="td-period">${per}</span>` : `<span class="td-period tp-nonmensuel">Ponctuel</span>`}</td>
        <td>
          <div class="td-montants">
            <div class="tm-du">${c.montant_du!=null ? fmt(c.montant_du) : '—'}</div>
            ${c.montant_du!=null ? `<div class="tm-bar"><div class="tm-bar-fill" style="width:${p}%;background:${barColor}"></div></div>` : ''}
          </div>
        </td>
        <td><span class="tm-paye">${fmt(c.montant_paye)}</span></td>
        <td>${c.montant_restant>0 ? `<span class="tm-restant">${fmt(c.montant_restant)}</span>` : `<span style="color:var(--co-muted);font-size:11px">—</span>`}</td>
        <td>${statutPill(c.statut)}</td>
        <td>${modeBadge(c.mode)}</td>
        <td onclick="event.stopPropagation()">
          <div class="td-actions">
            <button class="btn btn-soft-primary waves-effect" onclick="openDetail(${c.id})" title="Détails" style="width:28px;height:28px;padding:0;display:flex;align-items:center;justify-content:center;font-size:14px;border-radius:7px"><i class="ri-eye-line"></i></button>
            ${!c.validated_at && c.mode==='espece' ? `<button class="btn btn-soft-success waves-effect" onclick="validerCotisation(${c.id})" title="Valider" style="width:28px;height:28px;padding:0;display:flex;align-items:center;justify-content:center;font-size:14px;border-radius:7px"><i class="ri-checkbox-circle-line"></i></button>` : ''}
            ${c.statut!=='a_jour' ? `<button class="btn btn-soft-warning waves-effect" onclick="openCreate(${c.customer_id})" title="Enregistrer paiement" style="width:28px;height:28px;padding:0;display:flex;align-items:center;justify-content:center;font-size:14px;border-radius:7px"><i class="ri-money-cny-circle-line"></i></button>` : ''}
            <div class="dropdown">
              <button class="btn btn-soft-secondary waves-effect" style="width:28px;height:28px;padding:0;display:flex;align-items:center;justify-content:center;font-size:14px;border-radius:7px" data-bs-toggle="dropdown"><i class="ri-more-2-fill"></i></button>
              <ul class="dropdown-menu dropdown-menu-end" style="font-size:12px;min-width:160px">
                <li><span class="dropdown-header" style="font-size:10px;font-weight:800;text-transform:uppercase">Changer statut</span></li>
                ${c.statut!=='a_jour'    ? `<li><a class="dropdown-item" href="#" onclick="changerStatut(${c.id},'a_jour');return false"><i class="ri-checkbox-circle-line me-2" style="color:#0ab39c"></i>Marquer À jour</a></li>` : ''}
                ${c.statut!=='partiel'   ? `<li><a class="dropdown-item" href="#" onclick="changerStatut(${c.id},'partiel');return false"><i class="ri-error-warning-line me-2" style="color:#f7b84b"></i>Marquer Partiel</a></li>` : ''}
                ${c.statut!=='en_retard' ? `<li><a class="dropdown-item" href="#" onclick="changerStatut(${c.id},'en_retard');return false"><i class="ri-time-line me-2" style="color:#f06548"></i>Marquer En retard</a></li>` : ''}
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="#" onclick="supprimerCotisation(${c.id});return false"><i class="ri-delete-bin-line me-2"></i>Supprimer</a></li>
              </ul>
            </div>
          </div>
        </td>
      </tr>`;
  }).join('');

  renderPagination(start, filtered.length);
  updateTabCounts();

  /* Animer barres */
  setTimeout(() => {
    document.querySelectorAll('.tm-bar-fill').forEach(el => {
      const w = el.style.width; el.style.width='0'; requestAnimationFrame(()=>{ el.style.width=w; });
    });
  }, 60);
}

/* ── Pagination ────────────────────────────────────────── */
function renderPagination(start, total) {
  const pages=Math.ceil(total/state.perPage), current=state.page;
  const from=total?start+1:0, to=Math.min(start+state.perPage,total);
  const infoEl=document.getElementById('co-pag-info'); const btnsEl=document.getElementById('co-pag-btns');
  if (infoEl) infoEl.textContent=`Affichage de ${from} à ${to} sur ${total} cotisation(s)`;
  if (!btnsEl) return;
  let html=`<button class="co-pag-btn" onclick="goPage(${current-1})" ${current===1?'disabled':''}><i class="ri-arrow-left-s-line"></i></button>`;
  for (let p=1;p<=pages;p++) {
    if (pages>7&&p>3&&p<pages-1&&Math.abs(p-current)>1){ if(p===4||p===pages-2) html+=`<button class="co-pag-btn" disabled>…</button>`; continue; }
    html+=`<button class="co-pag-btn ${p===current?'active':''}" onclick="goPage(${p})">${p}</button>`;
  }
  html+=`<button class="co-pag-btn" onclick="goPage(${current+1})" ${current===pages||!pages?'disabled':''}><i class="ri-arrow-right-s-line"></i></button>`;
  btnsEl.innerHTML=html;
}
function goPage(p) {
  const pages=Math.ceil(getFiltered().length/state.perPage);
  if (p<1||p>pages) return; state.page=p; renderTable();
}

/* ── Modal DÉTAIL (100% JS) ────────────────────────────── */
function openDetail(id) {
  const c = COTISATIONS.find(x => x.id===id); if (!c) return;
  const cust = getCust(c.customer_id);
  const tc   = getTC(c.type_id);
  const meta = TYPE_META[tc?.type] || TYPE_META.ordinaire;
  const per  = period(c);
  const p    = pct(c);
  const barColor = p===100 ? '#0ab39c' : p>0 ? '#f7b84b' : '#f06548';

  /* Header */
  document.getElementById('cmh-header').style.background = meta.grad;
  document.getElementById('cmh-icon').innerHTML = `<i class="${meta.icon}"></i>`;
  document.getElementById('cmh-name').textContent = cust ? `${cust.prenom} ${cust.nom}` : '—';
  document.getElementById('cmh-type').textContent = tc?.libelle || '—';
  document.getElementById('cmh-period').textContent = per || 'Ponctuel';
  document.getElementById('cmh-statut-badge').innerHTML = c.statut==='a_jour'
    ? `<span style="background:rgba(255,255,255,.2);color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px">✓ À jour</span>`
    : c.statut==='partiel'
    ? `<span style="background:rgba(255,255,255,.2);color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px">◑ Partiel</span>`
    : `<span style="background:rgba(255,255,255,.2);color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px">⚠ En retard</span>`;

  /* Stats */
  document.getElementById('cms1').textContent = c.montant_du!=null ? fmt(c.montant_du) : '—';
  document.getElementById('cms2').textContent = fmt(c.montant_paye);
  document.getElementById('cms3').textContent = c.montant_restant>0 ? fmt(c.montant_restant) : '—';
  document.getElementById('cms4').textContent = p+'%';

  /* Progress bar */
  document.getElementById('cpp-pct').textContent = p+'%';
  document.getElementById('cpp-pct').style.color = barColor;
  const fillEl = document.getElementById('cpp-fill');
  fillEl.style.width='0'; fillEl.style.background=barColor;
  setTimeout(()=>{ fillEl.style.width=p+'%'; }, 80);
  document.getElementById('cpp-paid').textContent = fmt(c.montant_paye)+' payés';
  document.getElementById('cpp-due').textContent  = c.montant_restant>0 ? fmt(c.montant_restant)+' restants' : 'Soldé ✓';

  /* Détails */
  document.getElementById('di-fidele').textContent     = cust ? `${cust.prenom} ${cust.nom}` : '—';
  document.getElementById('di-engagement').textContent = cust?.engagement ? fmt(cust.engagement)+'/mois' : 'Aucun';
  document.getElementById('di-type').textContent       = tc?.libelle || '—';
  document.getElementById('di-period').textContent     = per || 'Ponctuel';
  document.getElementById('di-mode').innerHTML         = modeBadge(c.mode);
  document.getElementById('di-created').textContent    = c.created || '—';
  document.getElementById('di-validated').innerHTML    = c.validated_at&&c.validated_by
    ? `<span class="val-badge validated"><i class="ri-shield-check-line"></i>Admin · ${c.validated_at}</span>`
    : c.validated_at
    ? `<span class="val-badge auto"><i class="ri-robot-line"></i>Auto · ${c.validated_at}</span>`
    : `<span class="val-badge pending"><i class="ri-time-line"></i>Non validé</span>`;

  /* Historique */
  const hists = HISTORIQUES[id] || [];
  document.getElementById('co-hist-list').innerHTML = hists.length
    ? hists.map(h=>`
        <div class="co-hist-item ${h.cls}">
          <div class="hi-icon" style="background:${h.bg};color:${h.color}"><i class="${h.icon}"></i></div>
          <div class="hi-body"><div class="hi-op">${h.note}</div><div class="hi-meta">${h.date}</div></div>
          <div class="hi-amt" style="color:${h.color}">+${fmtN(h.montant)} FCFA</div>
        </div>`).join('')
    : `<div style="text-align:center;padding:20px;color:var(--co-muted);font-size:13px"><i class="ri-inbox-line" style="font-size:28px;display:block;margin-bottom:8px;opacity:.4"></i>Aucun historique</div>`;

  /* Actions */
  const actions = document.getElementById('detail-actions');
  actions.innerHTML = '';
  if (!c.validated_at && c.mode==='espece') {
    actions.innerHTML += `<button class="btn btn-success waves-effect" onclick="validerCotisation(${c.id});bootstrap.Modal.getInstance(document.getElementById('modalDetailCotisation'))?.hide()"><i class="ri-shield-check-line me-1"></i>Valider ce paiement</button>`;
  }
  if (c.statut!=='a_jour') {
    actions.innerHTML += `<button class="btn btn-primary waves-effect" style="margin-left:8px" onclick="bootstrap.Modal.getInstance(document.getElementById('modalDetailCotisation'))?.hide();openCreate(${c.customer_id})"><i class="ri-money-cny-circle-line me-1"></i>Enregistrer paiement</button>`;
  }

  new bootstrap.Modal(document.getElementById('modalDetailCotisation')).show();
}

/* ── Valider cotisation espèces ────────────────────────── */
function validerCotisation(id) {
  const c = COTISATIONS.find(x=>x.id===id); if(!c) return;
  Swal.fire({
    title:'Valider ce paiement ?', html:`<p>Vous confirmez la réception de <strong>${fmt(c.montant_paye)}</strong> en espèces.</p>`,
    icon:'question', showCancelButton:true, confirmButtonText:'Oui, valider', cancelButtonText:'Annuler',
    confirmButtonColor:'#0ab39c', cancelButtonColor:'#878a99',
  }).then(r=>{
    if(r.isConfirmed){ c.validated_by=1; c.validated_at=new Date().toLocaleString('fr-FR'); renderTable(); toast('Paiement validé !','success'); }
  });
}

/* ── Changer statut ────────────────────────────────────── */
function changerStatut(id, nouveauStatut) {
  const c = COTISATIONS.find(x=>x.id===id); if(!c) return;
  c.statut = nouveauStatut;
  renderTable();
  toast('Statut mis à jour', 'success');
}

/* ── Supprimer ─────────────────────────────────────────── */
function supprimerCotisation(id) {
  Swal.fire({ title:'Supprimer ?', text:'Cette action est irréversible.', icon:'warning', showCancelButton:true, confirmButtonText:'Oui, supprimer', cancelButtonText:'Annuler', confirmButtonColor:'#f06548', cancelButtonColor:'#878a99' })
    .then(r=>{ if(r.isConfirmed){ COTISATIONS=COTISATIONS.filter(c=>c.id!==id); renderTable(); renderKPIs(); toast('Cotisation supprimée','success'); } });
}

/* ── Toast ─────────────────────────────────────────────── */
function toast(msg, icon='success') {
  Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true }).fire({ icon, title:msg });
}

/* ══════════════════════════════════════════════════════════
   MODAL CRÉER COTISATION — logique métier complète
══════════════════════════════════════════════════════════ */
function openCreate(preCustomerId=null) {
  resetForm();
  if (preCustomerId) {
    const c = CUSTOMERS.find(x=>x.id===preCustomerId);
    if (c) selectFidele(c);
  }
  new bootstrap.Modal(document.getElementById('modalCreateCotisation')).show();
}

/* Suggestions fidèle */
function buildSuggestions(query) {
  if (!query || query.length < 2) { hideSuggestions(); return; }
  const q = query.toLowerCase();
  const results = CUSTOMERS.filter(c =>
    c.prenom.toLowerCase().includes(q) || c.nom.toLowerCase().includes(q) || c.phone.includes(q)
  );
  const box = document.getElementById('fidele-suggestions');
  if (!results.length) { hideSuggestions(); return; }
  box.innerHTML = results.map(c => {
    const col  = avColor(c.id);
    const init = c.prenom[0]+c.nom[0];
    return `<div style="display:flex;align-items:center;gap:10px;padding:10px 14px;cursor:pointer;border-bottom:1px solid var(--co-border);transition:background .15s"
      onmouseover="this.style.background='rgba(64,81,137,.04)'" onmouseout="this.style.background=''"
      onclick="selectFidele({id:${c.id},prenom:'${c.prenom}',nom:'${c.nom}',phone:'${c.phone}',engagement:${c.engagement||'null'}})">
      <div style="width:34px;height:34px;border-radius:50%;background:${col};color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex-shrink:0">${init}</div>
      <div>
        <div style="font-size:13px;font-weight:700;color:#212529">${c.prenom} ${c.nom}</div>
        <div style="font-size:11px;color:var(--co-muted)">${c.phone}</div>
      </div>
      ${c.engagement ? `<div style="margin-left:auto;font-size:11px;font-weight:700;color:var(--co-primary)">${fmtN(c.engagement)} FCFA/mois</div>` : ''}
    </div>`;
  }).join('');
  box.style.display='';
}

function hideSuggestions() {
  const box=document.getElementById('fidele-suggestions'); if(box) box.style.display='none';
}

function selectFidele(cust) {
  hideSuggestions();
  state.selectedCustomerId = cust.id;
  document.getElementById('f-search-fidele').value = '';
  document.getElementById('search-fidele-wrap').style.display = 'none';

  const col  = avColor(cust.id);
  const init = cust.prenom[0]+cust.nom[0];
  document.getElementById('fsc-avatar').style.background = col;
  document.getElementById('fsc-avatar').textContent      = init;
  document.getElementById('fsc-name').textContent        = `${cust.prenom} ${cust.nom}`;
  document.getElementById('fsc-phone').textContent       = cust.phone;

  const engEl = document.getElementById('fsc-eng');
  if (cust.engagement) {
    engEl.style.display = '';
    document.getElementById('fsc-eng-val').textContent = fmtN(cust.engagement);
  } else {
    engEl.style.display = 'none';
  }
  document.getElementById('fidele-card').style.display = '';
  document.getElementById('alerte-engagement').style.display = 'none';

  computeReport();
}

function clearFidele() {
  state.selectedCustomerId = null;
  document.getElementById('fidele-card').style.display = 'none';
  document.getElementById('search-fidele-wrap').style.display = '';
  document.getElementById('alerte-engagement').style.display = 'none';
  document.getElementById('report-calc').style.display = 'none';
  document.getElementById('f-search-fidele').value = '';
}

/* Logique mensuel / période */
function onTypeChange() {
  const sel  = document.getElementById('f-type');
  const opt  = sel.selectedOptions[0];
  const type = opt?.dataset.type;
  const req  = opt?.dataset.required === '1';

  document.getElementById('periode-wrap').style.display = type==='mensuel' ? '' : 'none';

  /* Vérification engagement si mensuel+obligatoire */
  if (type==='mensuel' && req && state.selectedCustomerId) {
    const cust = CUSTOMERS.find(c=>c.id===state.selectedCustomerId);
    if (!cust?.engagement) {
      document.getElementById('alerte-engagement').style.display = '';
    } else {
      document.getElementById('alerte-engagement').style.display = 'none';
    }
  } else {
    document.getElementById('alerte-engagement').style.display = 'none';
  }
  computeReport();
}

/* Calcul report automatique */
function computeReport() {
  const montant = parseInt(document.getElementById('f-montant').value)||0;
  const sel     = document.getElementById('f-type');
  const opt     = sel.selectedOptions[0];
  const type    = opt?.dataset.type;
  const custId  = state.selectedCustomerId;
  const cust    = CUSTOMERS.find(c=>c.id===custId);

  const calcEl = document.getElementById('report-calc');
  if (!type || type!=='mensuel' || !cust?.engagement || montant<1) { calcEl.style.display='none'; return; }

  const eng  = cust.engagement;
  const mois = parseInt(document.getElementById('f-mois').value);
  const annee= parseInt(document.getElementById('f-annee').value);

  /* Trouver cotisations en retard du fidèle (mensuel) */
  const existing = COTISATIONS.filter(c=>c.customer_id===custId&&c.type_id===1)
    .sort((a,b)=>(b.annee*100+b.mois)-(a.annee*100+a.mois));

  let remaining = montant;
  const rows = [];
  let curMois=mois, curAnnee=annee;

  /* Solde les mois en retard d'abord */
  existing.filter(c=>c.statut!=='a_jour').forEach(c=>{
    if(remaining<=0) return;
    const toSolde = Math.min(remaining, c.montant_restant);
    remaining -= toSolde;
    const newPaye = c.montant_paye+toSolde;
    rows.push({ mois:`${MOIS_FR[c.mois]} ${c.annee}`, amount:toSolde, statut: newPaye>=c.montant_du?'a_jour':'partiel' });
    curMois=c.mois; curAnnee=c.annee;
  });

  /* Couvre les mois suivants */
  while(remaining>=eng) {
    curMois++; if(curMois>12){curMois=1;curAnnee++;}
    rows.push({ mois:`${MOIS_FR[curMois]} ${curAnnee}`, amount:eng, statut:'a_jour' });
    remaining-=eng;
  }
  if(remaining>0) {
    curMois++; if(curMois>12){curMois=1;curAnnee++;}
    rows.push({ mois:`${MOIS_FR[curMois]} ${curAnnee}`, amount:remaining, statut:'partiel' });
  }

  if(!rows.length){ calcEl.style.display='none'; return; }
  calcEl.style.display='';
  document.getElementById('rc-rows').innerHTML = rows.map(r=>`
    <div class="rc-row">
      <span class="rr-mois"><i class="ri-calendar-line me-1"></i>${r.mois}</span>
      <span style="display:flex;align-items:center;gap:6px">
        <span class="rr-amount">${fmtN(r.amount)} FCFA</span>
        <span class="rr-status ${r.statut==='a_jour'?'s-solde':'s-partiel'}">${r.statut==='a_jour'?'Soldé':'Partiel'}</span>
      </span>
    </div>`).join('');
}

/* Save form */
function saveForm() {
  let valid=true;
  const errors=[];

  /* Validation */
  if(!state.selectedCustomerId)          { errors.push('Veuillez sélectionner un fidèle.'); valid=false; }
  if(!document.getElementById('f-type').value) { errors.push('Veuillez choisir un type de cotisation.'); valid=false; }
  if(!document.getElementById('f-montant').value||parseInt(document.getElementById('f-montant').value)<1) { errors.push('Montant invalide.'); valid=false; }
  if(!document.getElementById('hidden-mode').value) { errors.push('Veuillez sélectionner un mode de paiement.'); valid=false; }

  /* Vérif engagement si mensuel+obligatoire */
  const sel=document.getElementById('f-type'); const opt=sel.selectedOptions[0];
  const type=opt?.dataset.type; const req=opt?.dataset.required==='1';
  if(type==='mensuel'&&req&&state.selectedCustomerId){
    const cust=CUSTOMERS.find(c=>c.id===state.selectedCustomerId);
    if(!cust?.engagement){ errors.push('Ce fidèle n\'a pas de montant d\'engagement. Modifiez d\'abord sa fiche.'); valid=false; document.getElementById('alerte-engagement').style.display=''; }
  }

  if(!valid){
    const errEl=document.getElementById('form-errors');
    errEl.style.display='';
    document.getElementById('form-errors-list').innerHTML=errors.map(e=>`<div>• ${e}</div>`).join('');
    return;
  }

  /* Créer la cotisation */
  const typeId   = parseInt(sel.value);
  const montant  = parseInt(document.getElementById('f-montant').value);
  const mode     = document.getElementById('hidden-mode').value;
  const cust     = CUSTOMERS.find(c=>c.id===state.selectedCustomerId);
  const validated= document.getElementById('f-valider').checked;
  const isMensuel= type==='mensuel';
  const moisVal  = isMensuel ? parseInt(document.getElementById('f-mois').value) : null;
  const anneeVal = isMensuel ? parseInt(document.getElementById('f-annee').value) : null;
  const montantDu= isMensuel&&cust?.engagement ? cust.engagement : (isMensuel?montant:null);
  const restant  = montantDu ? Math.max(0,montantDu-montant) : 0;
  const statut   = !montantDu ? 'a_jour' : montant>=montantDu ? 'a_jour' : montant>0 ? 'partiel' : 'en_retard';

  const newId=Math.max(...COTISATIONS.map(c=>c.id),0)+1;
  COTISATIONS.unshift({
    id:newId, customer_id:state.selectedCustomerId, type_id:typeId,
    mois:moisVal, annee:anneeVal,
    montant_du:montantDu, montant_paye:montant, montant_restant:restant,
    statut, mode,
    validated_by:validated?1:null,
    validated_at:validated?new Date().toLocaleString('fr-FR'):null,
    created:new Date().toLocaleDateString('fr-FR'),
  });

  bootstrap.Modal.getInstance(document.getElementById('modalCreateCotisation'))?.hide();
  state.page=1; renderTable(); renderKPIs();
  toast('Cotisation enregistrée avec succès !','success');
}

/* Reset formulaire */
function resetForm() {
  state.selectedCustomerId=null;
  document.getElementById('f-search-fidele').value='';
  document.getElementById('fidele-card').style.display='none';
  document.getElementById('search-fidele-wrap').style.display='';
  document.getElementById('alerte-engagement').style.display='none';
  document.getElementById('f-type').value='';
  document.getElementById('periode-wrap').style.display='none';
  document.getElementById('f-montant').value='';
  document.getElementById('report-calc').style.display='none';
  document.getElementById('hidden-mode').value='';
  document.querySelectorAll('.mode-btn').forEach(b=>b.classList.remove('selected'));
  document.getElementById('ref-wrap').style.display='none';
  document.getElementById('f-reference').value='';
  document.getElementById('f-valider').checked=true;
  document.getElementById('form-errors').style.display='none';
  hideSuggestions();
}

/* ── INIT ──────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
  renderKPIs();
  renderTable();

  /* Tabs */
  document.querySelectorAll('.co-tab').forEach(tab=>{
    tab.addEventListener('click', function(){
      document.querySelectorAll('.co-tab').forEach(t=>t.classList.remove('active'));
      this.classList.add('active');
      state.tab=this.dataset.tab; state.page=1; renderTable();
    });
  });

  /* Search */
  document.getElementById('co-search')?.addEventListener('input',function(){ state.search=this.value; state.page=1; renderTable(); });

  /* Filtres */
  document.getElementById('co-filter-type')?.addEventListener('change',function(){ state.type=this.value; state.page=1; renderTable(); });
  document.getElementById('co-filter-mois')?.addEventListener('change',function(){ state.mois=this.value; state.page=1; renderTable(); });
  document.getElementById('co-filter-mode')?.addEventListener('change',function(){ state.mode=this.value; state.page=1; renderTable(); });

  /* Recherche fidèle dans le modal */
  document.getElementById('f-search-fidele')?.addEventListener('input',function(){ buildSuggestions(this.value); });
  document.addEventListener('click',function(e){ if(!e.target.closest('#search-fidele-wrap')) hideSuggestions(); });

  /* Type change */
  document.getElementById('f-type')?.addEventListener('change', onTypeChange);

  /* Montant → recalcul report */
  document.getElementById('f-montant')?.addEventListener('input', computeReport);
  document.getElementById('f-mois')?.addEventListener('change', computeReport);
  document.getElementById('f-annee')?.addEventListener('change', computeReport);

  /* Mode paiement */
  document.querySelectorAll('.mode-btn').forEach(btn=>{
    btn.addEventListener('click',function(){
      document.querySelectorAll('.mode-btn').forEach(b=>b.classList.remove('selected'));
      this.classList.add('selected');
      document.getElementById('hidden-mode').value=this.dataset.mode;
      document.getElementById('ref-wrap').style.display = ['mobile_money','virement'].includes(this.dataset.mode) ? '' : 'none';
    });
  });

  /* Reset au close du modal créer */
  document.getElementById('modalCreateCotisation')?.addEventListener('hidden.bs.modal', resetForm);
});