/* ============================================================
   MOSQUÉE – Fidèles (Customers) JS
   ============================================================ */
'use strict';

/* ── Données simulées ──────────────────────────────────── */
const CUSTOMERS = [
  { id:1,  nom:'Koné',       prenom:'Moussa',      dial:'+225', tel:'07 00 11 22',  adresse:'Adjamé, Abidjan',    engagement:10000, adhesion:'2023-01-15', statut:'retard',  statutGlobal:'retard',  moisRetard:3, montantDu:30000,  nbPaiements:14, totalPaye:140000, documents:2 },
  { id:2,  nom:'Traoré',     prenom:'Fatoumata',   dial:'+225', tel:'05 00 33 44',  adresse:'Yopougon, Abidjan',  engagement:5000,  adhesion:'2023-03-01', statut:'ajour',   statutGlobal:'ajour',   moisRetard:0, montantDu:0,      nbPaiements:25, totalPaye:125000, documents:1 },
  { id:3,  nom:'Diabaté',    prenom:'Ibrahim',     dial:'+225', tel:'01 00 55 66',  adresse:'Cocody, Abidjan',    engagement:15000, adhesion:'2022-09-10', statut:'ajour',   statutGlobal:'ajour',   moisRetard:0, montantDu:0,      nbPaiements:30, totalPaye:450000, documents:3 },
  { id:4,  nom:'Coulibaly',  prenom:'Aminata',     dial:'+225', tel:'07 00 77 88',  adresse:'Marcory, Abidjan',   engagement:5000,  adhesion:'2024-01-20', statut:'partiel', statutGlobal:'partiel', moisRetard:1, montantDu:3000,   nbPaiements:15, totalPaye:75000,  documents:1 },
  { id:5,  nom:'Bamba',      prenom:'Ousmane',     dial:'+225', tel:'05 00 99 00',  adresse:'Plateau, Abidjan',   engagement:20000, adhesion:'2021-06-05', statut:'ajour',   statutGlobal:'ajour',   moisRetard:0, montantDu:0,      nbPaiements:38, totalPaye:760000, documents:2 },
  { id:6,  nom:'Ouattara',   prenom:'Daouda',      dial:'+225', tel:'07 11 22 33',  adresse:'Treichville',        engagement:10000, adhesion:'2023-07-11', statut:'retard',  statutGlobal:'retard',  moisRetard:4, montantDu:40000,  nbPaiements:9,  totalPaye:90000,  documents:0 },
  { id:7,  nom:'Sanogo',     prenom:'Kadiatou',    dial:'+225', tel:'01 22 33 44',  adresse:'Williamsville',      engagement:null,  adhesion:'2024-03-01', statut:'libre',   statutGlobal:'libre',   moisRetard:0, montantDu:0,      nbPaiements:5,  totalPaye:25000,  documents:1 },
  { id:8,  nom:'Touré',      prenom:'Seydou',      dial:'+225', tel:'05 33 44 55',  adresse:'Abobo, Abidjan',     engagement:5000,  adhesion:'2023-11-01', statut:'ajour',   statutGlobal:'ajour',   moisRetard:0, montantDu:0,      nbPaiements:6,  totalPaye:30000,  documents:1 },
  { id:9,  nom:'Konaté',     prenom:'Mariam',      dial:'+225', tel:'07 44 55 66',  adresse:'Port-Bouët',         engagement:10000, adhesion:'2022-04-18', statut:'retard',  statutGlobal:'retard',  moisRetard:2, montantDu:20000,  nbPaiements:22, totalPaye:220000, documents:2 },
  { id:10, nom:'Diallo',     prenom:'Bakary',      dial:'+225', tel:'01 55 66 77',  adresse:'Koumassi',           engagement:15000, adhesion:'2021-12-01', statut:'ajour',   statutGlobal:'ajour',   moisRetard:0, montantDu:0,      nbPaiements:40, totalPaye:600000, documents:3 },
  { id:11, nom:'Cissé',      prenom:'Hawa',        dial:'+225', tel:'05 66 77 88',  adresse:'Attécoubé',          engagement:5000,  adhesion:'2024-02-14', statut:'partiel', statutGlobal:'partiel', moisRetard:1, montantDu:2500,   nbPaiements:2,  totalPaye:10000,  documents:0 },
  { id:12, nom:'Barry',      prenom:'Mamadou',     dial:'+225', tel:'07 77 88 99',  adresse:'Bingerville',        engagement:null,  adhesion:'2023-08-20', statut:'libre',   statutGlobal:'libre',   moisRetard:0, montantDu:0,      nbPaiements:8,  totalPaye:40000,  documents:1 },
];

/* Historique cotisations simulé par fidèle */
const HIST_COTISATIONS = {
  1: [
    { periode:'Mars 2025',   type:'Mensuel', montantDu:10000, montantPaye:0,     restant:10000, statut:'retard',  mode:'-'           },
    { periode:'Février 2025',type:'Mensuel', montantDu:10000, montantPaye:0,     restant:10000, statut:'retard',  mode:'-'           },
    { periode:'Janvier 2025',type:'Mensuel', montantDu:10000, montantPaye:0,     restant:10000, statut:'retard',  mode:'-'           },
    { periode:'Déc 2024',    type:'Mensuel', montantDu:10000, montantPaye:10000, restant:0,     statut:'ajour',   mode:'mobile_money'},
    { periode:'Nov 2024',    type:'Mensuel', montantDu:10000, montantPaye:10000, restant:0,     statut:'ajour',   mode:'espece'      },
  ],
  2: [
    { periode:'Mars 2025',   type:'Mensuel', montantDu:5000,  montantPaye:5000,  restant:0,     statut:'ajour',   mode:'mobile_money'},
    { periode:'Février 2025',type:'Mensuel', montantDu:5000,  montantPaye:5000,  restant:0,     statut:'ajour',   mode:'espece'      },
    { periode:'Quête Mar',   type:'Quête',   montantDu:null,  montantPaye:2000,  restant:0,     statut:'ajour',   mode:'espece'      },
  ],
};

/* ── Couleurs avatars ──────────────────────────────────── */
const AVATAR_COLORS = ['#405189','#0ab39c','#f06548','#f7b84b','#299cdb','#d4a843','#3577f1','#6559cc','#ea4c4c','#2dce89'];
function getAvatarColor(id) { return AVATAR_COLORS[(id - 1) % AVATAR_COLORS.length]; }

/* ── État de l'application ────────────────────────────── */
let state = {
  view:       'table',    // 'table' | 'grid'
  filter:     'tous',     // 'tous' | 'ajour' | 'retard' | 'partiel' | 'libre'
  search:     '',
  page:       1,
  perPage:    8,
  currentId:  null,
  addStep:    1,
  editMode:   false,
};

/* ── Filtrage + recherche ─────────────────────────────── */
function getFiltered() {
  return CUSTOMERS.filter(c => {
    const matchFilter =
      state.filter === 'tous'    ||
      (state.filter === 'ajour'   && c.statutGlobal === 'ajour')  ||
      (state.filter === 'retard'  && c.statutGlobal === 'retard') ||
      (state.filter === 'partiel' && c.statutGlobal === 'partiel')||
      (state.filter === 'libre'   && c.statutGlobal === 'libre');

    const q = state.search.toLowerCase();
    const matchSearch = !q ||
      c.nom.toLowerCase().includes(q)    ||
      c.prenom.toLowerCase().includes(q) ||
      c.tel.includes(q)                  ||
      (c.adresse || '').toLowerCase().includes(q);

    return matchFilter && matchSearch;
  });
}

/* ── KPI update ───────────────────────────────────────── */
function renderKPIs() {
  const total   = CUSTOMERS.length;
  const ajour   = CUSTOMERS.filter(c => c.statutGlobal === 'ajour').length;
  const retard  = CUSTOMERS.filter(c => c.statutGlobal === 'retard').length;
  const libre   = CUSTOMERS.filter(c => c.statutGlobal === 'libre').length;

  animVal('kpi-total',  total,  '');
  animVal('kpi-ajour',  ajour,  '');
  animVal('kpi-retard', retard, '');
  animVal('kpi-libre',  libre,  '');
}

function animVal(id, target, suffix, dur = 900) {
  const el = document.getElementById(id);
  if (!el) return;
  const start = performance.now();
  const ease = t => 1 - Math.pow(1 - t, 3);
  (function update(now) {
    const p = Math.min((now - start) / dur, 1);
    el.textContent = Math.floor(ease(p) * target) + suffix;
    if (p < 1) requestAnimationFrame(update);
  })(start);
}

/* ── Statut pill HTML ─────────────────────────────────── */
function statutPill(s) {
  const map = {
    ajour:   ['sp-ajour',   '<i class="ri-checkbox-circle-line"></i> À jour'],
    retard:  ['sp-retard',  '<i class="ri-time-line"></i> En retard'],
    partiel: ['sp-partiel', '<i class="ri-error-warning-line"></i> Partiel'],
    libre:   ['sp-libre',   '<i class="ri-user-line"></i> Sans engagement'],
    inactif: ['sp-inactif', '<i class="ri-close-circle-line"></i> Inactif'],
  };
  const [cls, txt] = map[s] || ['sp-libre', s];
  return `<span class="statut-pill ${cls}">${txt}</span>`;
}

/* ── Render TABLE ─────────────────────────────────────── */
function renderTable() {
  const filtered = getFiltered();
  const start    = (state.page - 1) * state.perPage;
  const paged    = filtered.slice(start, start + state.perPage);
  const tbody    = document.getElementById('cust-tbody');
  if (!tbody) return;

  if (!paged.length) {
    tbody.innerHTML = `<tr><td colspan="7"><div class="empty-state"><i class="ri-user-search-line"></i><p>Aucun fidèle trouvé</p></div></td></tr>`;
    renderPagination(0, filtered.length);
    return;
  }

  tbody.innerHTML = paged.map(c => {
    const color    = getAvatarColor(c.id);
    const initials = c.prenom[0] + c.nom[0];
    const eng      = c.engagement ? new Intl.NumberFormat('fr-FR').format(c.engagement) + ' FCFA/mois' : '<span style="color:var(--msq-muted);font-style:italic;">Aucun</span>';
    return `
      <tr onclick="openDetail(${c.id})" title="Voir le profil de ${c.prenom} ${c.nom}">
        <td>
          <div style="display:flex;align-items:center;gap:10px;">
            <div class="cust-avatar" style="background:${color}">${initials}</div>
            <div>
              <div class="cust-name">${c.prenom} ${c.nom}</div>
              <div class="cust-phone"><i class="ri-phone-line me-1"></i>${c.dial} ${c.tel}</div>
            </div>
          </div>
        </td>
        <td><span style="font-size:12px;color:var(--msq-muted);">${c.adresse || '—'}</span></td>
        <td><span class="cust-engagement">${eng}</span></td>
        <td><span class="cust-date"><i class="ri-calendar-line me-1"></i>${formatDate(c.adhesion)}</span></td>
        <td>${statutPill(c.statutGlobal)}</td>
        <td>
          ${c.statutGlobal === 'retard' || c.statutGlobal === 'partiel'
            ? `<span style="font-size:12px;font-weight:700;color:var(--msq-danger);">
                ${new Intl.NumberFormat('fr-FR').format(c.montantDu)} FCFA
               </span>`
            : `<span style="color:var(--msq-muted);font-size:12px;">—</span>`}
        </td>
        <td onclick="event.stopPropagation()">
          <div class="tbl-actions">
            <button class="btn btn-soft-primary waves-effect" onclick="openDetail(${c.id})" title="Voir détails">
              <i class="ri-eye-line"></i>
            </button>
            <button class="btn btn-soft-warning waves-effect" onclick="openEdit(${c.id})" title="Modifier">
              <i class="ri-edit-line"></i>
            </button>
            <button class="btn btn-soft-danger waves-effect" onclick="confirmDelete(${c.id})" title="Supprimer">
              <i class="ri-delete-bin-line"></i>
            </button>
          </div>
        </td>
      </tr>`;
  }).join('');

  renderPagination(start, filtered.length);
}

/* ── Render GRID ──────────────────────────────────────── */
function renderGrid() {
  const filtered = getFiltered();
  const start    = (state.page - 1) * state.perPage;
  const paged    = filtered.slice(start, start + state.perPage);
  const grid     = document.getElementById('cust-grid');
  if (!grid) return;

  if (!paged.length) {
    grid.innerHTML = `<div class="empty-state" style="grid-column:1/-1"><i class="ri-user-search-line"></i><p>Aucun fidèle trouvé</p></div>`;
    return;
  }

  grid.innerHTML = paged.map(c => {
    const color    = getAvatarColor(c.id);
    const initials = c.prenom[0] + c.nom[0];
    const statusColors = { ajour:'#0ab39c', retard:'#f06548', partiel:'#f7b84b', libre:'#878a99' };
    const sc = statusColors[c.statutGlobal] || '#878a99';
    return `
      <div class="cust-card" style="border-top-color:${sc}" onclick="openDetail(${c.id})">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
          <div class="card-avatar" style="background:${color}">${initials}</div>
          ${statutPill(c.statutGlobal)}
        </div>
        <div class="card-name">${c.prenom} ${c.nom}</div>
        <div class="card-phone"><i class="ri-phone-line me-1"></i>${c.dial} ${c.tel}</div>

        <div class="card-info-row">
          <span class="ci-label"><i class="ri-map-pin-line me-1"></i>Adresse</span>
          <span class="ci-value">${(c.adresse || '—').slice(0,18)}${c.adresse?.length > 18 ? '…' : ''}</span>
        </div>
        <div class="card-info-row">
          <span class="ci-label"><i class="ri-money-cny-circle-line me-1"></i>Engagement</span>
          <span class="ci-value" style="color:var(--msq-primary)">
            ${c.engagement ? new Intl.NumberFormat('fr-FR').format(c.engagement) + ' FCFA' : 'Aucun'}
          </span>
        </div>
        <div class="card-info-row">
          <span class="ci-label"><i class="ri-calendar-line me-1"></i>Adhésion</span>
          <span class="ci-value">${formatDate(c.adhesion)}</span>
        </div>

        <div class="card-actions" onclick="event.stopPropagation()">
          <button class="btn btn-soft-primary waves-effect" onclick="openDetail(${c.id})">
            <i class="ri-eye-line me-1"></i>Détails
          </button>
          <button class="btn btn-soft-warning waves-effect" onclick="openEdit(${c.id})">
            <i class="ri-edit-line me-1"></i>Modifier
          </button>
        </div>
      </div>`;
  }).join('');
}

/* ── Render Pagination ────────────────────────────────── */
function renderPagination(start, total) {
  const pages    = Math.ceil(total / state.perPage);
  const current  = state.page;
  const infoEl   = document.getElementById('pag-info');
  const btnsEl   = document.getElementById('pag-btns');
  if (!infoEl || !btnsEl) return;

  const from = total ? start + 1 : 0;
  const to   = Math.min(start + state.perPage, total);
  infoEl.textContent = `Affichage de ${from} à ${to} sur ${total} fidèle(s)`;

  let btns = `<button class="pag-btn" onclick="goPage(${current-1})" ${current===1?'disabled':''}>
    <i class="ri-arrow-left-s-line"></i></button>`;
  for (let p = 1; p <= pages; p++) {
    if (pages > 7 && p > 3 && p < pages - 1 && Math.abs(p - current) > 1) {
      if (p === 4 || p === pages - 2) btns += `<button class="pag-btn" disabled>…</button>`;
      continue;
    }
    btns += `<button class="pag-btn ${p===current?'active':''}" onclick="goPage(${p})">${p}</button>`;
  }
  btns += `<button class="pag-btn" onclick="goPage(${current+1})" ${current===pages||!pages?'disabled':''}>
    <i class="ri-arrow-right-s-line"></i></button>`;

  btnsEl.innerHTML = btns;
}

function goPage(p) {
  const total = getFiltered().length;
  const pages = Math.ceil(total / state.perPage);
  if (p < 1 || p > pages) return;
  state.page = p;
  render();
}

/* ── Render principal ─────────────────────────────────── */
function render() {
  const tableWrap = document.getElementById('table-view');
  const gridWrap  = document.getElementById('grid-view');

  if (state.view === 'table') {
    if (tableWrap) tableWrap.style.display = '';
    if (gridWrap)  gridWrap.style.display  = 'none';
    renderTable();
  } else {
    if (tableWrap) tableWrap.style.display = 'none';
    if (gridWrap)  gridWrap.style.display  = '';
    renderGrid();
    renderPagination((state.page-1)*state.perPage, getFiltered().length);
  }
}

/* ── Modal DÉTAIL ─────────────────────────────────────── */
function openDetail(id) {
  const c = CUSTOMERS.find(x => x.id === id);
  if (!c) return;
  state.currentId = id;

  const color    = getAvatarColor(id);
  const initials = c.prenom[0] + c.nom[0];

  /* Avatar + nom */
  document.getElementById('mfh-avatar').style.background = color;
  document.getElementById('mfh-avatar').textContent      = initials;
  document.getElementById('mfh-name').textContent        = `${c.prenom} ${c.nom}`;
  document.getElementById('mfh-phone').textContent       = `${c.dial} ${c.tel}`;
  document.getElementById('mfh-adresse').textContent     = c.adresse || '—';
  document.getElementById('mfh-date').textContent        = `Adhérent depuis ${formatDate(c.adhesion)}`;

  /* Badge statut header */
  const sbadge = document.getElementById('mfh-statut-badge');
  const statusMap = {
    ajour:   ['background:rgba(10,179,156,.2);color:#0ab39c',   '✓ À jour'],
    retard:  ['background:rgba(240,101,72,.2);color:#f06548',   '⚠ En retard'],
    partiel: ['background:rgba(247,184,75,.2);color:#f7b84b',   '◑ Partiel'],
    libre:   ['background:rgba(135,138,153,.2);color:#878a99',  '○ Sans engagement'],
  };
  const [ss, st] = statusMap[c.statutGlobal] || statusMap.libre;
  sbadge.setAttribute('style', ss + ';font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;');
  sbadge.textContent = st;

  /* Stats rapides */
  const totalPayeFmt = new Intl.NumberFormat('fr-FR').format(c.totalPaye);
  document.getElementById('ms-paiements').textContent = c.nbPaiements;
  document.getElementById('ms-total').textContent     = totalPayeFmt + ' FCFA';
  document.getElementById('ms-docs').textContent      = c.documents;

  /* Onglet Infos */
  document.getElementById('di-nom').textContent      = `${c.prenom} ${c.nom}`;
  document.getElementById('di-tel').textContent      = `${c.dial} ${c.tel}`;
  document.getElementById('di-adresse').textContent  = c.adresse || '—';
  document.getElementById('di-adhesion').textContent = formatDate(c.adhesion);
  document.getElementById('di-eng').textContent      = c.engagement
    ? new Intl.NumberFormat('fr-FR').format(c.engagement) + ' FCFA / mois'
    : 'Aucun engagement mensuel';
  document.getElementById('di-statut').innerHTML     = statutPill(c.statutGlobal);
  document.getElementById('di-retard').textContent   = c.moisRetard > 0
    ? `${c.moisRetard} mois (${new Intl.NumberFormat('fr-FR').format(c.montantDu)} FCFA)`
    : '—';

  /* Historique cotisations */
  const hists = HIST_COTISATIONS[id] || [];
  const histTbody = document.getElementById('hist-tbody');
  histTbody.innerHTML = hists.length ? hists.map(h => `
    <tr>
      <td>${h.periode}</td>
      <td><span style="font-size:11px;padding:2px 7px;border-radius:10px;background:rgba(64,81,137,.08);color:#405189;font-weight:700">${h.type}</span></td>
      <td>${h.montantDu !== null ? new Intl.NumberFormat('fr-FR').format(h.montantDu) : '—'}</td>
      <td style="color:#0ab39c;font-weight:700">${new Intl.NumberFormat('fr-FR').format(h.montantPaye)}</td>
      <td>${statutPill(h.statut)}</td>
      <td style="font-size:11px;color:var(--msq-muted)">${h.mode}</td>
    </tr>`).join('')
    : `<tr><td colspan="6" style="text-align:center;padding:20px;color:var(--msq-muted);font-size:13px;">Aucun historique disponible</td></tr>`;

  /* Documents */
  const docsEl = document.getElementById('docs-list');
  if (c.documents > 0) {
    const fakeDoc = [
      { nom:`CNI_${c.nom}.pdf`, type:'Pièce d\'identité', icon:'ri-file-text-line', color:'#f06548', bg:'rgba(240,101,72,.10)' },
      { nom:`Photo_${c.nom}.jpg`, type:'Photo', icon:'ri-image-line', color:'#0ab39c', bg:'rgba(10,179,156,.10)' },
      { nom:`Fiche_adhesion.pdf`, type:'Fiche', icon:'ri-file-pdf-line', color:'#405189', bg:'rgba(64,81,137,.10)' },
    ].slice(0, c.documents);
    docsEl.innerHTML = fakeDoc.map(d => `
      <div class="doc-item">
        <div class="doc-icon" style="background:${d.bg};color:${d.color}"><i class="${d.icon}"></i></div>
        <div><div class="doc-name">${d.nom}</div><div class="doc-type">${d.type}</div></div>
        <button class="btn btn-sm btn-soft-primary waves-effect ms-auto"><i class="ri-download-line"></i></button>
      </div>`).join('');
  } else {
    docsEl.innerHTML = `<div style="text-align:center;padding:20px;color:var(--msq-muted);font-size:13px;">
      <i class="ri-folder-open-line" style="font-size:32px;display:block;margin-bottom:8px;opacity:.4"></i>
      Aucun document enregistré
    </div>`;
  }

  /* Activer le premier onglet */
  switchTab('tab-infos');

  /* Ouvrir modal */
  const modal = new bootstrap.Modal(document.getElementById('modalDetailFidele'));
  modal.show();
}

/* ── Switch Tabs modal détail ─────────────────────────── */
function switchTab(tabId) {
  document.querySelectorAll('.fidele-tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.fidele-tab-panel').forEach(p => p.classList.remove('active'));
  document.querySelector(`[data-tab="${tabId}"]`)?.classList.add('active');
  document.getElementById(tabId)?.classList.add('active');
}

/* ── Modal AJOUT / ÉDITION ────────────────────────────── */
function openAdd() {
  state.editMode = false;
  state.addStep  = 1;
  document.getElementById('modal-add-title').textContent = 'Nouveau fidèle';
  document.getElementById('modal-add-subtitle').textContent = 'Renseigner les informations du fidèle';
  document.getElementById('add-fidele-form').reset();
  document.querySelectorAll('.eng-pill').forEach(p => p.classList.remove('selected'));
  document.getElementById('selected-engagement').value = '';
  updateAddSteps(1);
  const modal = new bootstrap.Modal(document.getElementById('modalAddFidele'));
  modal.show();
}

function openEdit(id) {
  const c = CUSTOMERS.find(x => x.id === id);
  if (!c) return;
  state.editMode = true;
  state.currentId = id;

  // Fermer le modal détail s'il est ouvert
  const detailModal = bootstrap.Modal.getInstance(document.getElementById('modalDetailFidele'));
  if (detailModal) detailModal.hide();

  document.getElementById('modal-add-title').textContent = 'Modifier le fidèle';
  document.getElementById('modal-add-subtitle').textContent = `${c.prenom} ${c.nom}`;

  // Pré-remplir
  setTimeout(() => {
    document.getElementById('f-prenom').value   = c.prenom;
    document.getElementById('f-nom').value      = c.nom;
    document.getElementById('f-tel').value      = c.tel;
    document.getElementById('f-adresse').value  = c.adresse || '';
    document.getElementById('f-adhesion').value = c.adhesion;

    if (c.engagement) {
      document.getElementById('selected-engagement').value = c.engagement;
      document.querySelectorAll('.eng-pill').forEach(p => {
        if (parseInt(p.dataset.val) === c.engagement) p.classList.add('selected');
        else p.classList.remove('selected');
      });
    }
  }, 100);

  state.addStep = 1;
  updateAddSteps(1);
  const modal = new bootstrap.Modal(document.getElementById('modalAddFidele'));
  modal.show();
}

/* ── Steps navigation add modal ──────────────────────── */
function updateAddSteps(step) {
  state.addStep = step;
  document.querySelectorAll('.add-step-btn').forEach((btn, i) => {
    btn.classList.remove('active', 'done');
    if (i + 1 < step)    btn.classList.add('done');
    if (i + 1 === step)  btn.classList.add('active');
  });
  document.querySelectorAll('.add-panel').forEach((p, i) => {
    p.classList.toggle('active', i + 1 === step);
  });
  // Footer buttons
  const prevBtn = document.getElementById('btn-prev');
  const nextBtn = document.getElementById('btn-next');
  const saveBtn = document.getElementById('btn-save');
  if (prevBtn) prevBtn.style.display = step > 1 ? 'flex' : 'none';
  if (nextBtn) nextBtn.style.display = step < 2 ? 'flex' : 'none';
  if (saveBtn) saveBtn.style.display = step === 2 ? 'flex' : 'none';
}

function nextStep() {
  if (!validateStep(state.addStep)) return;
  if (state.addStep < 2) updateAddSteps(state.addStep + 1);
}
function prevStep() {
  if (state.addStep > 1) updateAddSteps(state.addStep - 1);
}

function validateStep(step) {
  let valid = true;
  if (step === 1) {
    ['f-prenom','f-nom','f-tel','f-adhesion'].forEach(id => {
      const el = document.getElementById(id);
      const err = document.getElementById(id + '-err');
      if (!el.value.trim()) {
        el.classList.add('is-error');
        if (err) { err.textContent = 'Ce champ est requis.'; err.classList.add('show'); }
        valid = false;
      } else {
        el.classList.remove('is-error');
        if (err) err.classList.remove('show');
      }
    });
  }
  return valid;
}

function saveForm() {
  if (!validateStep(state.addStep)) return;
  const prenom   = document.getElementById('f-prenom').value.trim();
  const nom      = document.getElementById('f-nom').value.trim();
  const tel      = document.getElementById('f-tel').value.trim();
  const adresse  = document.getElementById('f-adresse').value.trim();
  const adhesion = document.getElementById('f-adhesion').value;
  const engagement = parseInt(document.getElementById('selected-engagement').value) || null;

  if (state.editMode) {
    const idx = CUSTOMERS.findIndex(c => c.id === state.currentId);
    if (idx !== -1) {
      Object.assign(CUSTOMERS[idx], { prenom, nom, tel, adresse, adhesion, engagement });
    }
    showToast('Fidèle modifié avec succès !', 'success');
  } else {
    const newId = Math.max(...CUSTOMERS.map(c => c.id)) + 1;
    CUSTOMERS.push({
      id: newId, nom, prenom, dial: '+225', tel, adresse, engagement, adhesion,
      statut: engagement ? 'retard' : 'libre',
      statutGlobal: engagement ? 'retard' : 'libre',
      moisRetard: 0, montantDu: 0, nbPaiements: 0, totalPaye: 0, documents: 0,
    });
    showToast('Fidèle ajouté avec succès !', 'success');
  }

  bootstrap.Modal.getInstance(document.getElementById('modalAddFidele'))?.hide();
  renderKPIs();
  render();
}

/* ── Confirm delete ───────────────────────────────────── */
function confirmDelete(id) {
  if (typeof Swal !== 'undefined') {
    Swal.fire({
      title: 'Supprimer ce fidèle ?',
      text: 'Cette action est irréversible.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Oui, supprimer',
      cancelButtonText: 'Annuler',
      confirmButtonColor: '#f06548',
      cancelButtonColor: '#6c757d',
    }).then(r => {
      if (r.isConfirmed) {
        const idx = CUSTOMERS.findIndex(c => c.id === id);
        if (idx !== -1) CUSTOMERS.splice(idx, 1);
        renderKPIs();
        render();
        showToast('Fidèle supprimé.', 'error');
      }
    });
  } else {
    if (confirm('Supprimer ce fidèle ?')) {
      const idx = CUSTOMERS.findIndex(c => c.id === id);
      if (idx !== -1) CUSTOMERS.splice(idx, 1);
      renderKPIs();
      render();
    }
  }
}

/* ── Toast notification ───────────────────────────────── */
function showToast(msg, type = 'success') {
  if (typeof Swal !== 'undefined') {
    Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true })
      .fire({ icon: type, title: msg });
  }
}

/* ── Helpers ──────────────────────────────────────────── */
function formatDate(d) {
  if (!d) return '—';
  const dt = new Date(d);
  return dt.toLocaleDateString('fr-FR', { day:'2-digit', month:'short', year:'numeric' });
}

/* ── INIT ─────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
  /* KPIs */
  renderKPIs();

  /* Render initial */
  render();

  /* Search */
  const searchEl = document.getElementById('cust-search');
  if (searchEl) {
    searchEl.addEventListener('input', function () {
      state.search = this.value;
      state.page   = 1;
      render();
    });
  }

  /* Filter statut */
  const filterEl = document.getElementById('cust-filter');
  if (filterEl) {
    filterEl.addEventListener('change', function () {
      state.filter = this.value;
      state.page   = 1;
      render();
    });
  }

  /* View toggle */
  document.getElementById('btn-table-view')?.addEventListener('click', function () {
    state.view = 'table';
    this.classList.add('active');
    document.getElementById('btn-grid-view')?.classList.remove('active');
    render();
  });
  document.getElementById('btn-grid-view')?.addEventListener('click', function () {
    state.view = 'grid';
    this.classList.add('active');
    document.getElementById('btn-table-view')?.classList.remove('active');
    render();
  });

  /* Engagement pills */
  document.querySelectorAll('.eng-pill').forEach(pill => {
    pill.addEventListener('click', function () {
      document.querySelectorAll('.eng-pill').forEach(p => p.classList.remove('selected'));
      this.classList.add('selected');
      document.getElementById('selected-engagement').value = this.dataset.val;
    });
  });

  /* Effacer les erreurs en live */
  document.querySelectorAll('.input-msq').forEach(input => {
    input.addEventListener('input', function () {
      this.classList.remove('is-error');
      const err = document.getElementById(this.id + '-err');
      if (err) err.classList.remove('show');
    });
  });

  /* Tab switcher dans le modal détail */
  document.querySelectorAll('.fidele-tab').forEach(tab => {
    tab.addEventListener('click', function () {
      switchTab(this.dataset.tab);
    });
  });
});
