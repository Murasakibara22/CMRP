/* ============================================================
   MOSQUÉE – Types de Cotisation JS
   ============================================================ */
'use strict';

/* ── Configuration des types ──────────────────────────── */
const TYPE_META = {
  mensuel:     { label:'Mensuel',     icon:'ri-calendar-check-line', color:'#405189', bg:'rgba(64,81,137,.12)',  desc:'Cotisation mensuelle récurrente avec engagement du fidèle.' },
  ordinaire:   { label:'Ordinaire',   icon:'ri-gift-line',            color:'#0ab39c', bg:'rgba(10,179,156,.12)', desc:'Contribution libre, sans périodicité ni obligation.' },
  jour_precis: { label:'Jour précis', icon:'ri-hand-heart-line',      color:'#d4a843', bg:'rgba(212,168,67,.12)', desc:'Collecte un jour précis, ex : quête du vendredi.' },
  ramadan:     { label:'Ramadan',     icon:'ri-moon-line',            color:'#299cdb', bg:'rgba(41,156,219,.12)', desc:'Cotisation sur la période du mois de Ramadan.' },
};

const JOURS = ['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'];

/* ── Données simulées ─────────────────────────────────── */
let TYPES = [
  {
    id:1, libelle:'Cotisation mensuelle', description:'Engagement mensuel obligatoire pour les membres de la mosquée.',
    type:'mensuel', is_required:true, jour_recurrence:null,
    montant_objectif:null, status:'actif', start_at:null, end_at:null,
    nb_contributions:214, total_collecte:2650000, nb_fideles:214,
  },
  {
    id:2, libelle:'Quête du vendredi', description:'Collecte hebdomadaire lors de la prière du vendredi.',
    type:'jour_precis', is_required:false, jour_recurrence:'vendredi',
    montant_objectif:null, status:'actif', start_at:null, end_at:null,
    nb_contributions:87, total_collecte:380000, nb_fideles:87,
  },
  {
    id:3, libelle:'Don ordinaire', description:'Don spontané, libre de montant et de fréquence.',
    type:'ordinaire', is_required:false, jour_recurrence:null,
    montant_objectif:null, status:'actif', start_at:null, end_at:null,
    nb_contributions:42, total_collecte:195000, nb_fideles:35,
  },
  {
    id:4, libelle:'Ramadan 1446', description:'Collecte spéciale pendant le mois sacré du Ramadan 1446.',
    type:'ramadan', is_required:false, jour_recurrence:null,
    montant_objectif:800000, status:'actif', start_at:'2025-03-01', end_at:'2025-03-30',
    nb_contributions:118, total_collecte:425000, nb_fideles:118,
  },
  {
    id:5, libelle:'Collecte Rénovation', description:'Financement des travaux de rénovation de la salle de prière.',
    type:'ordinaire', is_required:false, jour_recurrence:null,
    montant_objectif:3000000, status:'actif', start_at:'2025-01-01', end_at:'2025-12-31',
    nb_contributions:63, total_collecte:1150000, nb_fideles:63,
  },
  {
    id:6, libelle:'Quête de Aïd el-Fitr', description:'Collecte spéciale pour la fête de la rupture du jeûne.',
    type:'jour_precis', is_required:false, jour_recurrence:'dimanche',
    montant_objectif:500000, status:'inactif', start_at:null, end_at:'2025-03-30',
    nb_contributions:156, total_collecte:498000, nb_fideles:156,
  },
];

/* Quelques contributions récentes simulées par type */
const RECENT_CONTRIBS = {
  1:[
    {nom:'Koné M.',      color:'#405189', montant:10000, date:'Auj. 08:30'},
    {nom:'Traoré F.',    color:'#0ab39c', montant:5000,  date:'Auj. 07:15'},
    {nom:'Diabaté I.',   color:'#f7b84b', montant:15000, date:'Hier 18:00'},
    {nom:'Bamba O.',     color:'#f06548', montant:20000, date:'Hier 10:30'},
  ],
  2:[
    {nom:'Ouattara D.',  color:'#d4a843', montant:2000,  date:'Vend. 13:30'},
    {nom:'Sanogo K.',    color:'#405189', montant:3000,  date:'Vend. 13:00'},
    {nom:'Barry M.',     color:'#299cdb', montant:1000,  date:'Vend. 12:45'},
  ],
  4:[
    {nom:'Coulibaly A.', color:'#0ab39c', montant:5000,  date:'28/03 20:00'},
    {nom:'Cissé H.',     color:'#405189', montant:3000,  date:'27/03 19:30'},
    {nom:'Diallo B.',    color:'#f06548', montant:10000, date:'26/03 21:00'},
  ],
};

/* ── État ─────────────────────────────────────────────── */
const state = {
  view: 'grid',
  search: '',
  filterType: 'tous',
  filterStatus: 'tous',
  editId: null,
  selectedType: null,
};

/* ── Helpers ──────────────────────────────────────────── */
const fmt  = n => new Intl.NumberFormat('fr-FR').format(n);
const fmtK = n => n >= 1_000_000 ? (n/1_000_000).toFixed(1)+'M' : n >= 1_000 ? (n/1_000).toFixed(0)+'k' : n;

function statutBadge(status) {
  return status === 'actif'
    ? `<span class="tc-badge tb-actif"><i class="ri-checkbox-circle-line me-1"></i>Actif</span>`
    : `<span class="tc-badge tb-inactif"><i class="ri-close-circle-line me-1"></i>Inactif</span>`;
}

function typeBadge(type) {
  const meta = TYPE_META[type] || {};
  return `<span class="tc-badge tb-type"><i class="${meta.icon} me-1"></i>${meta.label || type}</span>`;
}

function reqBadge(req) {
  return req
    ? `<span class="tc-badge tb-requis"><i class="ri-lock-line me-1"></i>Obligatoire</span>`
    : '';
}

function periodeEnCours(tc) {
  if (!tc.start_at && !tc.end_at) return null;
  const now = new Date();
  const start = tc.start_at ? new Date(tc.start_at) : null;
  const end   = tc.end_at   ? new Date(tc.end_at)   : null;
  if (end && end < now) return 'expire';
  if (start && start > now) return 'future';
  return 'encours';
}

function formatDateFR(d) {
  if (!d) return '—';
  return new Date(d).toLocaleDateString('fr-FR', {day:'2-digit', month:'short', year:'numeric'});
}

/* ── KPIs ─────────────────────────────────────────────── */
function renderKPIs() {
  const total  = TYPES.length;
  const actifs = TYPES.filter(t => t.status === 'actif').length;
  const requis = TYPES.filter(t => t.is_required).length;
  const cours  = TYPES.filter(t => {
    const pe = periodeEnCours(t);
    return t.status === 'actif' && (!pe || pe === 'encours');
  }).length;

  animVal('kpi-tc-total',  total,  '');
  animVal('kpi-tc-actifs', actifs, '');
  animVal('kpi-tc-requis', requis, '');
  animVal('kpi-tc-cours',  cours,  '');
}

function animVal(id, target, suffix, dur=900) {
  const el = document.getElementById(id);
  if (!el) return;
  const s = performance.now();
  const ease = t => 1 - Math.pow(1-t, 3);
  (function tick(now) {
    const p = Math.min((now-s)/dur, 1);
    el.textContent = Math.floor(ease(p)*target)+suffix;
    if (p < 1) requestAnimationFrame(tick);
  })(s);
}

/* ── Filtrage ─────────────────────────────────────────── */
function getFiltered() {
  return TYPES.filter(tc => {
    const mt = state.filterType === 'tous' || tc.type === state.filterType;
    const ms = state.filterStatus === 'tous' || tc.status === state.filterStatus;
    const q  = state.search.toLowerCase();
    const mq = !q || tc.libelle.toLowerCase().includes(q) || (tc.description||'').toLowerCase().includes(q);
    return mt && ms && mq;
  });
}

/* ── RENDER GRID ──────────────────────────────────────── */
function renderGrid() {
  const container = document.getElementById('tc-grid-view');
  if (!container) return;
  const list = getFiltered();

  if (!list.length) {
    container.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:60px;color:var(--tc-muted)">
      <i class="ri-search-line" style="font-size:40px;display:block;margin-bottom:10px;opacity:.4"></i>
      <p style="font-size:14px;font-weight:600">Aucun type de cotisation trouvé</p>
    </div>`;
    return;
  }

  container.innerHTML = list.map((tc, idx) => {
    const meta = TYPE_META[tc.type] || {};
    const pe   = periodeEnCours(tc);
    const pct  = tc.montant_objectif
      ? Math.min(Math.round((tc.total_collecte / tc.montant_objectif) * 100), 100)
      : null;

    return `
      <div class="tc-card fu" style="animation-delay:${idx * 0.06}s" onclick="openDetail(${tc.id})">
        <div class="tc-card-header" style="border-top-color:${meta.color}">
          <div class="tc-type-icon" style="background:${meta.bg};color:${meta.color}">
            <i class="${meta.icon}"></i>
          </div>
          <div class="tc-name">${tc.libelle}</div>
          <div class="tc-desc">${tc.description || '—'}</div>

          <div class="tc-badges" onclick="event.stopPropagation()">
            ${typeBadge(tc.type)}
            ${statutBadge(tc.status)}
            ${tc.is_required ? reqBadge(true) : ''}
            ${pe === 'expire'  ? '<span class="tc-badge tb-expire">Expiré</span>' : ''}
            ${pe === 'encours' && tc.status==='actif' ? '<span class="tc-badge tb-encours">En cours</span>' : ''}
          </div>
        </div>

        <div class="tc-card-body">
          <div class="tc-info-grid">
            <div class="tc-info-item">
              <div class="tii-label"><i class="ri-bar-chart-line me-1"></i>Total collecté</div>
              <div class="tii-value" style="color:var(--tc-accent)">${fmt(tc.total_collecte)} FCFA</div>
            </div>
            <div class="tc-info-item">
              <div class="tii-label"><i class="ri-group-line me-1"></i>Contributions</div>
              <div class="tii-value">${tc.nb_contributions}</div>
            </div>

            ${tc.type === 'mensuel' ? `
            <div class="tc-info-item full">
              <div class="tii-label"><i class="ri-money-cny-circle-line me-1"></i>Engagement fidèle</div>
              <div class="tii-value" style="color:var(--tc-primary)">Selon palier choisi à l'adhésion</div>
            </div>` : ''}

            ${tc.type === 'jour_precis' ? `
            <div class="tc-info-item full">
              <div class="tii-label"><i class="ri-calendar-line me-1"></i>Jour de collecte</div>
              <div class="tii-value" style="color:var(--tc-gold);text-transform:capitalize">${tc.jour_recurrence || '—'}</div>
            </div>` : ''}

            ${(tc.type === 'ramadan' || tc.type === 'ordinaire') && (tc.start_at || tc.end_at) ? `
            <div class="tc-info-item full">
              <div class="tii-label"><i class="ri-calendar-event-line me-1"></i>Période</div>
              <div class="tii-value" style="font-size:12px">${formatDateFR(tc.start_at)} → ${formatDateFR(tc.end_at)}</div>
            </div>` : ''}
          </div>

          ${pct !== null ? `
          <div class="tc-obj-bar">
            <div class="ob-header">
              <span class="ob-label"><i class="ri-target-line me-1"></i>Objectif ${fmt(tc.montant_objectif)} FCFA</span>
              <span class="ob-pct" style="color:${meta.color}">${pct}%</span>
            </div>
            <div class="ob-track">
              <div class="ob-fill" style="width:0%;background:${meta.color}" data-w="${pct}%"></div>
            </div>
          </div>` : ''}
        </div>

        <div class="tc-card-footer" onclick="event.stopPropagation()">
          <div class="tc-stat">
            <div class="ts-val">${fmtK(tc.total_collecte)}</div>
            <div class="ts-lbl">Collecté</div>
          </div>
          <div class="divider"></div>
          <div class="tc-stat">
            <div class="ts-val">${tc.nb_fideles}</div>
            <div class="ts-lbl">Fidèles</div>
          </div>
          <div class="divider"></div>
          <div class="tc-actions">
            <button class="btn btn-soft-primary waves-effect" onclick="openDetail(${tc.id})" title="Voir détails">
              <i class="ri-eye-line"></i>
            </button>
            <button class="btn btn-soft-warning waves-effect" onclick="openEdit(${tc.id})" title="Modifier">
              <i class="ri-edit-line"></i>
            </button>
            <button class="btn btn-soft-${tc.status==='actif'?'secondary':'success'} waves-effect"
              onclick="toggleStatus(${tc.id})" title="${tc.status==='actif'?'Désactiver':'Activer'}">
              <i class="ri-${tc.status==='actif'?'pause':'play'}-circle-line"></i>
            </button>
            <button class="btn btn-soft-danger waves-effect" onclick="confirmDelete(${tc.id})" title="Supprimer">
              <i class="ri-delete-bin-line"></i>
            </button>
          </div>
        </div>
      </div>`;
  }).join('');

  /* Animate progress bars */
  setTimeout(() => {
    document.querySelectorAll('.ob-fill[data-w]').forEach(el => {
      el.style.width = el.dataset.w;
    });
  }, 300);
}

/* ── RENDER LIST ──────────────────────────────────────── */
function renderList() {
  const tbody = document.getElementById('tc-list-tbody');
  if (!tbody) return;
  const list = getFiltered();

  if (!list.length) {
    tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--tc-muted)">Aucun résultat</td></tr>`;
    return;
  }

  tbody.innerHTML = list.map(tc => {
    const meta = TYPE_META[tc.type] || {};
    const pe   = periodeEnCours(tc);
    return `
      <tr onclick="openDetail(${tc.id})">
        <td>
          <div style="display:flex;align-items:center;gap:10px">
            <div class="type-icon-sm" style="background:${meta.bg};color:${meta.color}">
              <i class="${meta.icon}"></i>
            </div>
            <div class="tc-name-cell">
              <div class="tcn-name">${tc.libelle}</div>
              <div class="tcn-desc">${(tc.description||'').slice(0,50)}${tc.description?.length>50?'…':''}</div>
            </div>
          </div>
        </td>
        <td>${typeBadge(tc.type)}</td>
        <td>
          ${tc.is_required
            ? '<span class="tc-badge tb-requis"><i class="ri-lock-line me-1"></i>Oui</span>'
            : '<span style="color:var(--tc-muted);font-size:12px">Non</span>'}
        </td>
        <td>
          ${tc.montant_objectif
            ? `<span style="font-weight:700;font-size:13px;color:var(--tc-accent)">${fmt(tc.montant_objectif)}</span><span style="font-size:11px;color:var(--tc-muted)"> FCFA</span>`
            : '<span style="color:var(--tc-muted);font-size:12px">—</span>'}
        </td>
        <td>
          <span style="font-weight:700;color:var(--tc-accent)">${fmt(tc.total_collecte)} FCFA</span><br>
          <span style="font-size:11px;color:var(--tc-muted)">${tc.nb_contributions} contributions</span>
        </td>
        <td>
          ${statutBadge(tc.status)}
          ${pe === 'expire' ? '<span class="tc-badge tb-expire ms-1">Expiré</span>' : ''}
          ${pe === 'encours' && tc.status==='actif' ? '<span class="tc-badge tb-encours ms-1">En cours</span>' : ''}
        </td>
        <td onclick="event.stopPropagation()">
          <div class="tbl-actions" style="display:flex;gap:5px">
            <button class="btn btn-soft-primary waves-effect" style="width:30px;height:30px;padding:0;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:15px"
              onclick="openDetail(${tc.id})"><i class="ri-eye-line"></i></button>
            <button class="btn btn-soft-warning waves-effect" style="width:30px;height:30px;padding:0;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:15px"
              onclick="openEdit(${tc.id})"><i class="ri-edit-line"></i></button>
            <button class="btn btn-soft-danger waves-effect" style="width:30px;height:30px;padding:0;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:15px"
              onclick="confirmDelete(${tc.id})"><i class="ri-delete-bin-line"></i></button>
          </div>
        </td>
      </tr>`;
  }).join('');
}

/* ── Render principal ─────────────────────────────────── */
function render() {
  const gv = document.getElementById('tc-grid-view-wrap');
  const lv = document.getElementById('tc-list-view-wrap');
  if (state.view === 'grid') {
    if(gv) gv.style.display='';
    if(lv) lv.style.display='none';
    renderGrid();
  } else {
    if(gv) gv.style.display='none';
    if(lv) lv.style.display='';
    renderList();
  }
  renderKPIs();
}

/* ── Toggle statut ────────────────────────────────────── */
function toggleStatus(id) {
  const tc = TYPES.find(t => t.id === id);
  if (!tc) return;
  const newStatus = tc.status === 'actif' ? 'inactif' : 'actif';
  const label     = newStatus === 'actif' ? 'activé' : 'désactivé';

  if (typeof Swal !== 'undefined') {
    Swal.fire({
      title: `${newStatus === 'actif' ? 'Activer' : 'Désactiver'} ce type ?`,
      text: `Le type "${tc.libelle}" sera ${label}.`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Confirmer',
      cancelButtonText: 'Annuler',
      confirmButtonColor: newStatus === 'actif' ? '#0ab39c' : '#878a99',
    }).then(r => {
      if (r.isConfirmed) {
        tc.status = newStatus;
        render();
        toast(`Type "${tc.libelle}" ${label}.`, newStatus === 'actif' ? 'success' : 'info');
      }
    });
  } else {
    tc.status = newStatus;
    render();
  }
}

/* ── Confirm delete ───────────────────────────────────── */
function confirmDelete(id) {
  const tc = TYPES.find(t => t.id === id);
  if (!tc) return;

  if (typeof Swal !== 'undefined') {
    Swal.fire({
      title: 'Supprimer ce type ?',
      html: `<p>Le type <strong>"${tc.libelle}"</strong> et toutes ses cotisations liées seront supprimés.</p>
             <p style="color:#f06548;font-size:13px;font-weight:600"><i class="ri-error-warning-line me-1"></i>Action irréversible.</p>`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Supprimer',
      cancelButtonText: 'Annuler',
      confirmButtonColor: '#f06548',
    }).then(r => {
      if (r.isConfirmed) {
        TYPES = TYPES.filter(t => t.id !== id);
        render();
        toast(`Type "${tc.libelle}" supprimé.`, 'error');
      }
    });
  } else {
    if (confirm(`Supprimer "${tc.libelle}" ?`)) {
      TYPES = TYPES.filter(t => t.id !== id);
      render();
    }
  }
}

/* ── Toast ────────────────────────────────────────────── */
function toast(msg, icon='success') {
  if (typeof Swal !== 'undefined') {
    Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true })
      .fire({ icon, title: msg });
  }
}

/* ═══════════════════════════════════════════════════════
   MODAL DÉTAIL
═══════════════════════════════════════════════════════ */
function openDetail(id) {
  const tc   = TYPES.find(t => t.id === id);
  if (!tc) return;
  const meta = TYPE_META[tc.type] || {};
  const pe   = periodeEnCours(tc);
  const pct  = tc.montant_objectif
    ? Math.min(Math.round((tc.total_collecte / tc.montant_objectif)*100), 100)
    : null;

  /* Header */
  const hdr = document.getElementById('mdt-header');
  hdr.style.background = `linear-gradient(130deg, ${meta.color}dd 0%, ${meta.color} 100%)`;

  document.getElementById('mdt-icon').innerHTML      = `<i class="${meta.icon}"></i>`;
  document.getElementById('mdt-name').textContent    = tc.libelle;
  document.getElementById('mdt-type-span').textContent  = meta.label || tc.type;
  document.getElementById('mdt-status-span').textContent = tc.status === 'actif' ? '● Actif' : '○ Inactif';
  document.getElementById('mdt-status-span').style.color = tc.status === 'actif' ? '#6ef5e5' : 'rgba(255,255,255,.5)';

  /* Stats overlap */
  document.getElementById('mdt-s1').textContent = fmt(tc.total_collecte) + ' FCFA';
  document.getElementById('mdt-s2').textContent = tc.nb_contributions;
  document.getElementById('mdt-s3').textContent = tc.nb_fideles;

  /* Config grid */
  document.getElementById('cfg-type').textContent      = meta.label || tc.type;
  document.getElementById('cfg-required').innerHTML    = tc.is_required
    ? '<span class="tc-badge tb-requis">Oui – obligatoire</span>'
    : '<span style="color:var(--tc-muted);font-size:13px">Non</span>';
  document.getElementById('cfg-objectif').textContent  = tc.montant_objectif ? fmt(tc.montant_objectif)+' FCFA' : 'Pas défini';
  document.getElementById('cfg-start').textContent     = formatDateFR(tc.start_at);
  document.getElementById('cfg-end').textContent       = formatDateFR(tc.end_at);
  document.getElementById('cfg-jour').textContent      = tc.jour_recurrence
    ? tc.jour_recurrence.charAt(0).toUpperCase() + tc.jour_recurrence.slice(1)
    : '—';
  document.getElementById('cfg-desc').textContent      = tc.description || '—';

  /* Objectif progress */
  const objWrap = document.getElementById('obj-progress-wrap');
  if (pct !== null) {
    objWrap.style.display = '';
    document.getElementById('opw-pct').textContent    = pct + '%';
    document.getElementById('opw-pct').style.color    = meta.color;
    document.getElementById('opw-collecte').textContent = fmt(tc.total_collecte) + ' FCFA collectés';
    document.getElementById('opw-objectif').textContent = '/ ' + fmt(tc.montant_objectif) + ' FCFA';
    const fill = document.getElementById('opw-fill');
    fill.style.width      = '0%';
    fill.style.background = meta.color;
    setTimeout(() => { fill.style.width = pct + '%'; }, 200);
  } else {
    objWrap.style.display = 'none';
  }

  /* Contributions récentes */
  const contribs = RECENT_CONTRIBS[id] || [];
  const clist    = document.getElementById('contrib-list');
  clist.innerHTML = contribs.length
    ? contribs.map(c => `
      <div class="contrib-item">
        <div class="ca-avatar" style="background:${c.color}">${c.nom.slice(0,2).toUpperCase()}</div>
        <div class="ca-name">${c.nom}</div>
        <div class="ca-amount">+${fmt(c.montant)} FCFA</div>
        <div class="ca-date">${c.date}</div>
      </div>`).join('')
    : `<div style="text-align:center;padding:20px;color:var(--tc-muted);font-size:13px">
        <i class="ri-inbox-line" style="font-size:28px;display:block;margin-bottom:8px;opacity:.4"></i>
        Aucune contribution récente
      </div>`;

  /* Bouton modifier dans le modal */
  document.getElementById('mdt-edit-btn').onclick = () => {
    bootstrap.Modal.getInstance(document.getElementById('modalDetailTC'))?.hide();
    openEdit(id);
  };

  new bootstrap.Modal(document.getElementById('modalDetailTC')).show();
}

/* ═══════════════════════════════════════════════════════
   MODAL AJOUT / ÉDITION
═══════════════════════════════════════════════════════ */
function openAdd() {
  state.editId = null;
  state.selectedType = null;

  document.getElementById('modal-tc-title').textContent = 'Nouveau type de cotisation';
  document.getElementById('modal-tc-sub').textContent   = 'Définissez le type, les règles et la configuration.';
  document.getElementById('tc-form').reset();

  /* Reset type selector */
  document.querySelectorAll('.type-sel-btn').forEach(b => b.classList.remove('selected'));
  document.getElementById('hidden-type').value = '';

  /* Reset champs conditionnels */
  updateConditionalFields(null);

  /* Reset toggle */
  document.getElementById('tc-is-required').checked = false;
  document.querySelector('.req-toggle-row')?.classList.remove('active');

  new bootstrap.Modal(document.getElementById('modalTypeCotisation')).show();
}

function openEdit(id) {
  const tc = TYPES.find(t => t.id === id);
  if (!tc) return;
  state.editId = id;

  document.getElementById('modal-tc-title').textContent = 'Modifier le type';
  document.getElementById('modal-tc-sub').textContent   = tc.libelle;

  /* Sélectionner le type */
  document.querySelectorAll('.type-sel-btn').forEach(b => {
    b.classList.toggle('selected', b.dataset.type === tc.type);
  });
  document.getElementById('hidden-type').value = tc.type;
  state.selectedType = tc.type;
  updateConditionalFields(tc.type);

  /* Remplir les champs */
  setTimeout(() => {
    document.getElementById('tc-libelle').value       = tc.libelle;
    document.getElementById('tc-description').value   = tc.description || '';
    document.getElementById('tc-objectif').value      = tc.montant_objectif || '';
    document.getElementById('tc-start').value         = tc.start_at || '';
    document.getElementById('tc-end').value           = tc.end_at || '';
    document.getElementById('tc-jour').value          = tc.jour_recurrence || '';
    document.getElementById('tc-is-required').checked = tc.is_required;
    if (tc.is_required) document.querySelector('.req-toggle-row')?.classList.add('active');
    else document.querySelector('.req-toggle-row')?.classList.remove('active');
  }, 100);

  new bootstrap.Modal(document.getElementById('modalTypeCotisation')).show();
}

/* ── Champs conditionnels selon le type ─────────────── */
function selectType(type) {
  state.selectedType = type;
  document.querySelectorAll('.type-sel-btn').forEach(b => {
    b.classList.toggle('selected', b.dataset.type === type);
  });
  document.getElementById('hidden-type').value = type;
  updateConditionalFields(type);
}

function updateConditionalFields(type) {
  const showJour    = ['jour_precis'].includes(type);
  const showPeriode = ['ramadan', 'ordinaire'].includes(type);
  const showObjectif = ['ramadan', 'ordinaire', 'jour_precis'].includes(type);

  toggle('cond-jour',     showJour);
  toggle('cond-periode',  showPeriode);
  toggle('cond-objectif', showObjectif);

  /* Mensuel : is_required par défaut */
  if (type === 'mensuel') {
    document.getElementById('tc-is-required').checked = true;
    document.querySelector('.req-toggle-row')?.classList.add('active');
  }
}

function toggle(id, show) {
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.toggle('show', show);
}

/* ── Sauvegarde ───────────────────────────────────────── */
function saveTcForm() {
  /* Validation */
  let valid = true;
  const required = ['tc-libelle'];
  required.forEach(id => {
    const el  = document.getElementById(id);
    const err = document.getElementById(id+'-err');
    if (!el.value.trim()) {
      el.classList.add('is-err');
      if (err) { err.textContent='Ce champ est requis.'; err.classList.add('show'); }
      valid = false;
    } else {
      el.classList.remove('is-err');
      if (err) err.classList.remove('show');
    }
  });

  const type = document.getElementById('hidden-type').value;
  if (!type) {
    toast('Veuillez sélectionner un type de cotisation.', 'warning');
    valid = false;
  }

  if (!valid) return;

  const data = {
    libelle:          document.getElementById('tc-libelle').value.trim(),
    description:      document.getElementById('tc-description').value.trim(),
    type,
    is_required:      document.getElementById('tc-is-required').checked,
    montant_objectif: parseInt(document.getElementById('tc-objectif').value) || null,
    start_at:         document.getElementById('tc-start').value || null,
    end_at:           document.getElementById('tc-end').value   || null,
    jour_recurrence:  document.getElementById('tc-jour').value  || null,
    status:           'actif',
    nb_contributions: 0,
    total_collecte:   0,
    nb_fideles:       0,
  };

  if (state.editId) {
    const idx = TYPES.findIndex(t => t.id === state.editId);
    if (idx !== -1) Object.assign(TYPES[idx], data);
    toast('Type de cotisation modifié avec succès !', 'success');
  } else {
    data.id = Math.max(...TYPES.map(t => t.id), 0) + 1;
    TYPES.push(data);
    toast('Type de cotisation créé avec succès !', 'success');
  }

  bootstrap.Modal.getInstance(document.getElementById('modalTypeCotisation'))?.hide();
  render();
}

/* ── INIT ─────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
  render();

  /* Search */
  document.getElementById('tc-search')?.addEventListener('input', function () {
    state.search = this.value; render();
  });

  /* Filter type */
  document.getElementById('tc-filter-type')?.addEventListener('change', function () {
    state.filterType = this.value; render();
  });

  /* Filter status */
  document.getElementById('tc-filter-status')?.addEventListener('change', function () {
    state.filterStatus = this.value; render();
  });

  /* View toggle */
  document.getElementById('btn-tc-grid')?.addEventListener('click', function () {
    state.view = 'grid';
    this.classList.add('active');
    document.getElementById('btn-tc-list')?.classList.remove('active');
    render();
  });
  document.getElementById('btn-tc-list')?.addEventListener('click', function () {
    state.view = 'list';
    this.classList.add('active');
    document.getElementById('btn-tc-grid')?.classList.remove('active');
    render();
  });

  /* Type selector dans le modal */
  document.querySelectorAll('.type-sel-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      selectType(this.dataset.type);
    });
  });

  /* Toggle is_required */
  document.getElementById('tc-is-required')?.addEventListener('change', function () {
    document.querySelector('.req-toggle-row')?.classList.toggle('active', this.checked);
  });

  /* Clear errors live */
  document.querySelectorAll('.input-tc').forEach(el => {
    el.addEventListener('input', function () {
      this.classList.remove('is-err');
      const err = document.getElementById(this.id + '-err');
      if (err) err.classList.remove('show');
    });
  });
});
