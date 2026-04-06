/* ============================================================
   MOSQUÉE – Paiements JS
   Données statiques dans le JS, rendu DOM, modal via JS pur
   ============================================================ */
'use strict';

/* ── Couleurs avatars ──────────────────────────────────── */
const AVATAR_COLORS = ['#405189','#0ab39c','#f06548','#f7b84b','#299cdb','#d4a843','#3577f1','#6559cc','#ea4c4c','#2dce89'];
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

const PAIEMENTS = [
  { id:1,  customer_id:3, montant:15000, mode:'virement',     ref:'VIR-2025-0041', statut:'success', source:'Cotisation mensuelle', source_type:'mensuel',  periode:'Avril 2025',   mois:4, annee:2025, date:'2025-04-01 10:12', valide_par:'Admin Konan', valide_at:'2025-04-01 10:15', note:null },
  { id:2,  customer_id:2, montant:5000,  mode:'mobile_money', ref:'OM-202504-0231', statut:'success', source:'Cotisation mensuelle', source_type:'mensuel',  periode:'Avril 2025',   mois:4, annee:2025, date:'2025-04-02 08:30', valide_par:null,           valide_at:'2025-04-02 08:31', note:null },
  { id:3,  customer_id:5, montant:20000, mode:'mobile_money', ref:'OM-202504-0287', statut:'success', source:'Cotisation mensuelle', source_type:'mensuel',  periode:'Avril 2025',   mois:4, annee:2025, date:'2025-04-01 09:15', valide_par:null,           valide_at:'2025-04-01 09:16', note:null },
  { id:4,  customer_id:4, montant:2000,  mode:'espece',       ref:'ESP-2025-0014',  statut:'success', source:'Cotisation mensuelle', source_type:'mensuel',  periode:'Avril 2025',   mois:4, annee:2025, date:'2025-04-03 14:20', valide_par:'Admin Konan', valide_at:'2025-04-03 14:25', note:'Paiement partiel accepté' },
  { id:5,  customer_id:8, montant:5000,  mode:'espece',       ref:'ESP-2025-0015',  statut:'success', source:'Cotisation mensuelle', source_type:'mensuel',  periode:'Avril 2025',   mois:4, annee:2025, date:'2025-04-05 11:00', valide_par:'Admin Konan', valide_at:'2025-04-05 11:05', note:null },
  { id:6,  customer_id:7, montant:2000,  mode:'espece',       ref:'ESP-2025-0016',  statut:'success', source:'Quête du vendredi',    source_type:'quete',    periode:'Ponctuel',     mois:null, annee:null, date:'2025-04-04 13:30', valide_par:'Admin Konan', valide_at:'2025-04-04 13:35', note:null },
  { id:7,  customer_id:3, montant:5000,  mode:'mobile_money', ref:'OM-202503-9981', statut:'success', source:'Ramadan 1446',         source_type:'ramadan',  periode:'Mars 2025',    mois:3, annee:2025, date:'2025-03-28 20:00', valide_par:null,           valide_at:'2025-03-28 20:01', note:null },
  { id:8,  customer_id:5, montant:15000, mode:'virement',     ref:'VIR-2025-0038',  statut:'success', source:'Collecte Rénovation',  source_type:'ordinaire',periode:'Ponctuel',     mois:null, annee:null, date:'2025-04-02 16:00', valide_par:null,           valide_at:'2025-04-02 16:02', note:null },
  { id:9,  customer_id:1, montant:10000, mode:'espece',       ref:'ESP-2025-0017',  statut:'pending', source:'Cotisation mensuelle', source_type:'mensuel',  periode:'Avril 2025',   mois:4, annee:2025, date:'2025-04-05 09:00', valide_par:null,           valide_at:null,               note:null },
  { id:10, customer_id:6, montant:10000, mode:'espece',       ref:'ESP-2025-0018',  statut:'pending', source:'Cotisation mensuelle', source_type:'mensuel',  periode:'Avril 2025',   mois:4, annee:2025, date:'2025-04-05 10:30', valide_par:null,           valide_at:null,               note:'En attente de confirmation' },
  { id:11, customer_id:2, montant:5000,  mode:'mobile_money', ref:'OM-202504-1102', statut:'failed',  source:'Cotisation mensuelle', source_type:'mensuel',  periode:'Mars 2025',    mois:3, annee:2025, date:'2025-03-05 08:00', valide_par:null,           valide_at:null,               note:'Solde insuffisant' },
  { id:12, customer_id:1, montant:10000, mode:'mobile_money', ref:'OM-202503-8842', statut:'failed',  source:'Cotisation mensuelle', source_type:'mensuel',  periode:'Mars 2025',    mois:3, annee:2025, date:'2025-03-01 07:45', valide_par:null,           valide_at:null,               note:'Transaction expirée' },
  { id:13, customer_id:4, montant:5000,  mode:'mobile_money', ref:'OM-202502-7731', statut:'success', source:'Cotisation mensuelle', source_type:'mensuel',  periode:'Février 2025', mois:2, annee:2025, date:'2025-02-10 11:20', valide_par:null,           valide_at:'2025-02-10 11:21', note:null },
  { id:14, customer_id:3, montant:15000, mode:'virement',     ref:'VIR-2025-0022',  statut:'success', source:'Cotisation mensuelle', source_type:'mensuel',  periode:'Mars 2025',    mois:3, annee:2025, date:'2025-03-02 10:00', valide_par:'Admin Konan', valide_at:'2025-03-02 10:05', note:null },
];

const HISTORIQUES = {
  4:  [
    { op:'creation', icon:'ri-add-circle-line', bg:'rgba(64,81,137,.10)', color:'#405189', note:'Création automatique', date:'01/04/2025', montant:5000 },
    { op:'paiement', icon:'ri-money-dollar-circle-line', bg:'rgba(10,179,156,.12)', color:'#0ab39c', note:'Paiement partiel en espèces', date:'03/04/2025', montant:2000 },
  ],
  9:  [
    { op:'creation', icon:'ri-add-circle-line', bg:'rgba(64,81,137,.10)', color:'#405189', note:'Paiement espèces reçu — en attente validation', date:'05/04/2025', montant:10000 },
  ],
  11: [
    { op:'creation', icon:'ri-add-circle-line', bg:'rgba(64,81,137,.10)', color:'#405189', note:'Tentative via Orange Money', date:'05/03/2025', montant:5000 },
    { op:'echec',    icon:'ri-close-circle-line', bg:'rgba(240,101,72,.10)', color:'#f06548', note:'Échec — Solde insuffisant', date:'05/03/2025', montant:0 },
  ],
};

/* ── État ──────────────────────────────────────────────── */
const state = {
  tab:     'tous',
  search:  '',
  mode:    'tous',
  mois:    'tous',
  source:  'tous',
  page:    1,
  perPage: 10,
};

/* ── Helpers ───────────────────────────────────────────── */
const getCustomer = id => CUSTOMERS.find(c => c.id === id);
const fmt  = n => n != null ? new Intl.NumberFormat('fr-FR').format(n) + ' FCFA' : '—';
const fmtN = n => n != null ? new Intl.NumberFormat('fr-FR').format(n) : '—';
const fmtDate = d => d ? new Date(d).toLocaleDateString('fr-FR',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}) : '—';

function statutPill(s) {
  const map = {
    success: ['pp-success','ri-checkbox-circle-line','Validé'],
    pending: ['pp-pending','ri-time-line','En attente'],
    failed:  ['pp-failed', 'ri-close-circle-line',  'Échoué'],
    refund:  ['pp-refund', 'ri-refund-2-line',       'Remboursé'],
  };
  const [cls,icon,lbl] = map[s] || ['pp-pending','ri-question-line',s];
  return `<span class="pay-pill ${cls}"><i class="${icon}"></i>${lbl}</span>`;
}

function modeBadge(m) {
  const map = {
    mobile_money: ['pm-mm','ri-smartphone-line','Mobile Money'],
    espece:       ['pm-esp','ri-money-dollar-circle-line','Espèces'],
    virement:     ['pm-vir','ri-bank-line','Virement'],
  };
  const [cls,icon,lbl] = map[m] || ['','ri-question-line','—'];
  return `<span class="pay-mode ${cls}"><i class="${icon}"></i>${lbl}</span>`;
}


function getFiltered() {
  return PAIEMENTS.filter(p => {
    const c = getCustomer(p.customer_id);
    const q = state.search.toLowerCase();

    const matchTab    = state.tab    === 'tous' || p.statut           === state.tab;
    const matchMode   = state.mode   === 'tous' || p.mode             === state.mode;
    const matchMois   = state.mois   === 'tous' || String(p.mois)     === state.mois;
    const matchSource = state.source === 'tous' || p.source_type      === state.source;
    const matchSearch = !q ||
      (c?.prenom||'').toLowerCase().includes(q) ||
      (c?.nom||'').toLowerCase().includes(q)    ||
      (p.ref||'').toLowerCase().includes(q)     ||
      (p.source||'').toLowerCase().includes(q);

    return matchTab && matchMode && matchMois && matchSource && matchSearch;
  });
}

/* ── KPIs ──────────────────────────────────────────────── */
function renderKPIs() {
  const total   = PAIEMENTS.length;
  const success = PAIEMENTS.filter(p => p.statut === 'success').length;
  const pending = PAIEMENTS.filter(p => p.statut === 'pending').length;
  const failed  = PAIEMENTS.filter(p => p.statut === 'failed').length;
  const montant = PAIEMENTS.filter(p => p.statut === 'success').reduce((s,p) => s+p.montant, 0);

  animVal('kpi-total',   total,   '');
  animVal('kpi-success', success, '');
  animVal('kpi-pending', pending, '');
  animVal('kpi-failed',  failed,  '');
  document.getElementById('kpi-montant').textContent = new Intl.NumberFormat('fr-FR').format(montant) + ' FCFA';
}

function animVal(id, target, suffix, dur=900) {
  const el = document.getElementById(id);
  if (!el) return;
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
  const all     = PAIEMENTS;
  document.getElementById('cnt-tous').textContent    = all.length;
  document.getElementById('cnt-success').textContent = all.filter(p=>p.statut==='success').length;
  document.getElementById('cnt-pending').textContent = all.filter(p=>p.statut==='pending').length;
  document.getElementById('cnt-failed').textContent  = all.filter(p=>p.statut==='failed').length;
}

/* ── Render table ──────────────────────────────────────── */
function renderTable() {
  const filtered = getFiltered();
  const start    = (state.page - 1) * state.perPage;
  const paged    = filtered.slice(start, start + state.perPage);
  const tbody    = document.getElementById('pay-tbody');
  if (!tbody) return;

  if (!paged.length) {
    tbody.innerHTML = `<tr><td colspan="8"><div class="pay-empty"><i class="ri-file-search-line"></i><p>Aucun paiement trouvé</p></div></td></tr>`;
    renderPagination(0, filtered.length);
    return;
  }

  const rowCls = { success:'row-success', pending:'row-pending', failed:'row-failed', refund:'row-refund' };

  tbody.innerHTML = paged.map(p => {
    const c    = getCustomer(p.customer_id);
    const init = c ? c.prenom[0]+c.nom[0] : '??';
    const col  = avColor(p.customer_id);
    const cls  = rowCls[p.statut] || '';
    const dateShort = p.date ? new Date(p.date).toLocaleDateString('fr-FR',{day:'2-digit',month:'short',year:'numeric'}) : '—';
    const dateTime  = p.date ? new Date(p.date).toLocaleTimeString('fr-FR',{hour:'2-digit',minute:'2-digit'}) : '';

    return `
      <tr class="${cls}" onclick="openDetail(${p.id})">
        <td>
          <div style="display:flex;align-items:center;gap:9px">
            <div class="pay-avatar" style="background:${col}">${init}</div>
            <div>
              <div class="pay-fidele-name">${c ? c.prenom+' '+c.nom : '—'}</div>
              <div class="pay-fidele-phone">${c?.phone || ''}</div>
            </div>
          </div>
        </td>
        <td><span class="pay-ref">${p.ref}</span></td>
        <td><span class="pay-montant">${fmt(p.montant)}</span></td>
        <td>${modeBadge(p.mode)}</td>
        <td>
          <span style="font-size:11px;font-weight:600;color:var(--pay-text);display:flex;align-items:center;gap:5px">
            <i class="ri-link-m" style="color:var(--pay-muted)"></i>${p.source}
          </span>
          ${p.periode !== 'Ponctuel' ? `<div style="font-size:10px;color:var(--pay-muted);margin-top:2px">${p.periode}</div>` : ''}
        </td>
        <td>
          <div style="font-size:12px;color:var(--pay-text)">${dateShort}</div>
          <div style="font-size:10px;color:var(--pay-muted)">${dateTime}</div>
        </td>
        <td>${statutPill(p.statut)}</td>
        <td onclick="event.stopPropagation()">
          <div class="pay-actions">
            <button class="btn btn-soft-primary waves-effect" onclick="openDetail(${p.id})" title="Détails" style="width:28px;height:28px;padding:0;display:flex;align-items:center;justify-content:center;font-size:14px;border-radius:7px">
              <i class="ri-eye-line"></i>
            </button>
            ${p.statut === 'pending' ? `
            <button class="btn btn-soft-success waves-effect" onclick="validerPaiement(${p.id})" title="Valider" style="width:28px;height:28px;padding:0;display:flex;align-items:center;justify-content:center;font-size:14px;border-radius:7px">
              <i class="ri-checkbox-circle-line"></i>
            </button>` : ''}
          </div>
        </td>
      </tr>`;
  }).join('');

  renderPagination(start, filtered.length);
}

/* ── Pagination ────────────────────────────────────────── */
function renderPagination(start, total) {
  const pages   = Math.ceil(total / state.perPage);
  const current = state.page;
  const from    = total ? start + 1 : 0;
  const to      = Math.min(start + state.perPage, total);

  const infoEl = document.getElementById('pay-pag-info');
  const btnsEl = document.getElementById('pay-pag-btns');
  if (infoEl) infoEl.textContent = `Affichage de ${from} à ${to} sur ${total} paiement(s)`;
  if (!btnsEl) return;

  let html = `<button class="pay-pag-btn" onclick="goPage(${current-1})" ${current===1?'disabled':''}><i class="ri-arrow-left-s-line"></i></button>`;
  for (let p = 1; p <= pages; p++) {
    if (pages > 7 && p > 3 && p < pages - 1 && Math.abs(p-current) > 1) {
      if (p === 4 || p === pages - 2) html += `<button class="pay-pag-btn" disabled>…</button>`;
      continue;
    }
    html += `<button class="pay-pag-btn ${p===current?'active':''}" onclick="goPage(${p})">${p}</button>`;
  }
  html += `<button class="pay-pag-btn" onclick="goPage(${current+1})" ${current===pages||!pages?'disabled':''}><i class="ri-arrow-right-s-line"></i></button>`;
  btnsEl.innerHTML = html;
}

function goPage(p) {
  const pages = Math.ceil(getFiltered().length / state.perPage);
  if (p < 1 || p > pages) return;
  state.page = p;
  renderTable();
}

/* ── Modal DÉTAIL ──────────────────────────────────────── */
function openDetail(id) {
  const p = PAIEMENTS.find(x => x.id === id);
  if (!p) return;
  const c = getCustomer(p.customer_id);

  /* Gradient selon statut */
  const grads = {
    success: 'linear-gradient(130deg,#0a7a6a,#0ab39c)',
    pending: 'linear-gradient(130deg,#a07c10,#d4a843)',
    failed:  'linear-gradient(130deg,#c43520,#f06548)',
    refund:  'linear-gradient(130deg,#1a6080,#299cdb)',
  };
  const icons = {
    success: 'ri-checkbox-circle-line',
    pending: 'ri-time-line',
    failed:  'ri-close-circle-line',
    refund:  'ri-refund-2-line',
  };

  /* Header */
  document.getElementById('pmh-header').style.background = grads[p.statut] || 'linear-gradient(130deg,#2d3a63,#405189)';
  document.getElementById('pmh-icon').innerHTML   = `<i class="${icons[p.statut] || 'ri-bank-card-line'}"></i>`;
  document.getElementById('pmh-name').textContent = c ? `${c.prenom} ${c.nom}` : '—';
  document.getElementById('pmh-ref').textContent  = p.ref;
  document.getElementById('pmh-date').textContent = fmtDate(p.date);

  /* Stats */
  document.getElementById('pms-montant').textContent = fmt(p.montant);
  document.getElementById('pms-statut').innerHTML    = statutPill(p.statut);
  document.getElementById('pms-mode').innerHTML      = modeBadge(p.mode);

  /* Détails */
  document.getElementById('di-fidele').textContent   = c ? `${c.prenom} ${c.nom}` : '—';
  document.getElementById('di-tel').textContent      = c?.phone || '—';
  document.getElementById('di-ref').textContent      = p.ref;
  document.getElementById('di-date').textContent     = fmtDate(p.date);
  document.getElementById('di-source').textContent   = p.source;
  document.getElementById('di-periode').textContent  = p.periode;
  document.getElementById('di-valide-par').textContent = p.valide_par || '—';
  document.getElementById('di-valide-at').textContent  = p.valide_at ? fmtDate(p.valide_at) : '—';

  const noteWrap = document.getElementById('di-note-wrap');
  if (p.note) {
    noteWrap.style.display = '';
    document.getElementById('di-note').textContent = p.note;
  } else {
    noteWrap.style.display = 'none';
  }

  /* Historique */
  const hists = HISTORIQUES[id] || [];
  const histEl = document.getElementById('pay-hist-list');
  histEl.innerHTML = hists.length
    ? hists.map(h => `
        <div class="pay-hist-item ${h.op === 'echec' ? 'hi-failed' : h.op === 'paiement' ? 'hi-success' : ''}">
          <div class="phi-icon" style="background:${h.bg};color:${h.color}"><i class="${h.icon}"></i></div>
          <div class="phi-body">
            <div class="phi-op">${h.note}</div>
            <div class="phi-meta">${h.date}</div>
          </div>
          ${h.montant > 0 ? `<div class="phi-amt" style="color:${h.color}">+${fmtN(h.montant)} FCFA</div>` : ''}
        </div>`).join('')
    : `<div style="text-align:center;padding:16px;color:var(--pay-muted);font-size:13px"><i class="ri-inbox-line" style="font-size:24px;display:block;margin-bottom:6px;opacity:.4"></i>Aucun historique</div>`;

  /* Bouton valider */
  const btnVal = document.getElementById('btn-valider-detail');
  if (p.statut === 'pending') {
    btnVal.style.display = 'flex';
    btnVal.onclick = () => {
      bootstrap.Modal.getInstance(document.getElementById('modalDetailPaiement'))?.hide();
      validerPaiement(id);
    };
  } else {
    btnVal.style.display = 'none';
  }

  /* Ouvrir le modal */
  new bootstrap.Modal(document.getElementById('modalDetailPaiement')).show();
}

/* ── Valider paiement ──────────────────────────────────── */
function validerPaiement(id) {
  const p = PAIEMENTS.find(x => x.id === id);
  if (!p) return;
  Swal.fire({
    title: 'Valider ce paiement ?',
    html: `<p>Vous confirmez la réception de <strong>${fmt(p.montant)}</strong> en ${p.mode === 'espece' ? 'espèces' : p.mode}.</p>`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Oui, valider',
    cancelButtonText: 'Annuler',
    confirmButtonColor: '#0ab39c',
    cancelButtonColor: '#878a99',
  }).then(r => {
    if (r.isConfirmed) {
      p.statut = 'success';
      p.valide_par = 'Admin';
      p.valide_at  = new Date().toLocaleString('fr-FR');
      renderTable();
      updateTabCounts();
      toast('Paiement validé avec succès !', 'success');
    }
  });
}

/* ── Toast ─────────────────────────────────────────────── */
function toast(msg, icon='success') {
  Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true })
    .fire({ icon, title: msg });
}

/* ── Graphs Chart.js ───────────────────────────────────── */
function initGraphs() {
  /* ── Graph 1 : Évolution mensuelle ────────────────────── */
  const moisLabels  = ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Août','Sep','Oct','Nov','Déc'];
  const moisMontants = [0,0,0,0,0,0,0,0,0,0,0,0];
  const moisCounts   = [0,0,0,0,0,0,0,0,0,0,0,0];

  PAIEMENTS.filter(p => p.statut === 'success' && p.mois).forEach(p => {
    moisMontants[p.mois - 1] += p.montant;
    moisCounts[p.mois - 1]   += 1;
  });

  const ctx1 = document.getElementById('chartEvolution');
  if (ctx1) {
    new Chart(ctx1, {
      type: 'bar',
      data: {
        labels: moisLabels,
        datasets: [{
          label: 'Montant (FCFA)',
          data: moisMontants,
          backgroundColor: 'rgba(64,81,137,.15)',
          borderColor: '#405189',
          borderWidth: 2,
          borderRadius: 6,
          borderSkipped: false,
          yAxisID: 'y',
        }, {
          label: 'Nb paiements',
          data: moisCounts,
          type: 'line',
          borderColor: '#0ab39c',
          backgroundColor: 'rgba(10,179,156,.08)',
          borderWidth: 2,
          pointBackgroundColor: '#0ab39c',
          pointRadius: 4,
          fill: true,
          tension: 0.4,
          yAxisID: 'y2',
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          y:  { ticks: { font:{size:10}, callback: v => v >= 1000 ? (v/1000)+'k' : v }, grid: { color:'rgba(0,0,0,.04)' } },
          y2: { position:'right', ticks: { font:{size:10} }, grid: { display:false } },
          x:  { ticks: { font:{size:10} }, grid: { display:false } },
        }
      }
    });
  }

  /* ── Graph 2 : Modes ───────────────────────────────────── */
  const mm  = PAIEMENTS.filter(p=>p.mode==='mobile_money').length;
  const esp = PAIEMENTS.filter(p=>p.mode==='espece').length;
  const vir = PAIEMENTS.filter(p=>p.mode==='virement').length;

  const ctx2 = document.getElementById('chartModes');
  if (ctx2) {
    new Chart(ctx2, {
      type: 'doughnut',
      data: {
        labels: ['Mobile Money','Espèces','Virement'],
        datasets: [{ data: [mm, esp, vir], backgroundColor: ['#0ab39c','#f7b84b','#405189'], borderWidth:2, borderColor:'#fff' }]
      },
      options: { responsive:true, cutout:'72%', plugins:{ legend:{display:false} } }
    });

    const legend = document.getElementById('chartModesLegend');
    if (legend) {
      const items = [
        { label:'Mobile Money', val:mm,  color:'#0ab39c' },
        { label:'Espèces',      val:esp, color:'#f7b84b' },
        { label:'Virement',     val:vir, color:'#405189' },
      ];
      legend.innerHTML = items.map(i => `
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
          <span style="width:12px;height:12px;border-radius:3px;background:${i.color};flex-shrink:0"></span>
          <div>
            <div style="font-size:12px;font-weight:700;color:#212529">${i.label}</div>
            <div style="font-size:11px;color:#878a99">${i.val} paiement${i.val>1?'s':''}</div>
          </div>
        </div>`).join('');
    }
  }
}

/* ── INIT ──────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
  renderKPIs();
  updateTabCounts();
  renderTable();
  initGraphs();

  /* Tabs */
  document.querySelectorAll('.pay-tab').forEach(tab => {
    tab.addEventListener('click', function () {
      document.querySelectorAll('.pay-tab').forEach(t => t.classList.remove('active'));
      this.classList.add('active');
      state.tab  = this.dataset.tab;
      state.page = 1;
      renderTable();
    });
  });

  /* Search */
  document.getElementById('pay-search')?.addEventListener('input', function () {
    state.search = this.value;
    state.page   = 1;
    renderTable();
  });

  /* Filtres */
  document.getElementById('pay-filter-mode')?.addEventListener('change', function () {
    state.mode = this.value; state.page = 1; renderTable();
  });
  document.getElementById('pay-filter-mois')?.addEventListener('change', function () {
    state.mois = this.value; state.page = 1; renderTable();
  });
  document.getElementById('pay-filter-source')?.addEventListener('change', function () {
    state.source = this.value; state.page = 1; renderTable();
  });
});