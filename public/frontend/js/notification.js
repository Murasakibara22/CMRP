/* ============================================================
   NOTIFICATIONS JS — réactivité uniquement, zéro donnée
   ============================================================ */
'use strict';

/* ════════════════════════════════════════════════════════
   ÉTAT (lu/non-lu sur la session, sans donnée en JS)
════════════════════════════════════════════════════════ */
function getUnreadCount() {
  return document.querySelectorAll('.notif-item.unread').length;
}

function refreshUnreadUI() {
  const count = getUnreadCount();
  /* Compteur badge */
  const uc = document.getElementById('unread-count');
  if (uc) uc.textContent = count > 0 ? `${count} non lue${count > 1 ? 's' : ''}` : 'Tout lu';
  /* Badge filtre */
  const nfBadge = document.getElementById('nf-badge-unread');
  if (nfBadge) nfBadge.textContent = count;
  /* Badge sidebar */
  const sb = document.getElementById('sb-badge');
  if (sb) { sb.textContent = count; sb.style.display = count > 0 ? '' : 'none'; }
  /* Bouton "Tout lire" */
  const markAll = document.getElementById('btn-mark-all');
  if (markAll) markAll.style.opacity = count > 0 ? '1' : '.4';
  markAll && (markAll.style.pointerEvents = count > 0 ? '' : 'none');
}

/* ════════════════════════════════════════════════════════
   FILTRES
════════════════════════════════════════════════════════ */
function filterNotif(btn) {
  document.querySelectorAll('.nf-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  const filter = btn.dataset.filter;
  const items  = document.querySelectorAll('.notif-item');
  const empty  = document.getElementById('notif-empty');
  const list   = document.getElementById('notif-list');
  let visible  = 0;

  items.forEach(item => {
    let show = false;
    if (filter === 'tous')    show = true;
    else if (filter === 'non_lu') show = item.dataset.read === 'false';
    else show = item.dataset.cat === filter;

    item.style.display = show ? '' : 'none';
    if (show) visible++;
  });

  list.style.display  = visible > 0 ? '' : 'none';
  empty.style.display = visible === 0 ? 'flex' : 'none';
}

/* ════════════════════════════════════════════════════════
   MARQUER COMME LU
════════════════════════════════════════════════════════ */
function markAsRead(item) {
  if (item.dataset.read === 'true') return;
  item.dataset.read = 'true';
  item.classList.remove('unread');
  const dot = item.querySelector('.ni-unread-dot');
  if (dot) { dot.className = 'ni-read-dot'; }
  refreshUnreadUI();
  /* Réappliquer le filtre si on est sur "non_lu" */
  const activeFilter = document.querySelector('.nf-btn.active');
  if (activeFilter && activeFilter.dataset.filter === 'non_lu') {
    filterNotif(activeFilter);
  }
}

function markAllRead() {
  document.querySelectorAll('.notif-item.unread').forEach(item => markAsRead(item));
}

/* ════════════════════════════════════════════════════════
   EFFACER TOUTES
════════════════════════════════════════════════════════ */
function confirmClearAll() {
  if (!confirm('Effacer toutes les notifications ? Cette action est irréversible.')) return;
  const items = document.querySelectorAll('.notif-item');
  items.forEach((item, i) => {
    setTimeout(() => {
      item.style.transition = 'opacity .2s ease, transform .2s ease';
      item.style.opacity    = '0';
      item.style.transform  = 'translateX(30px)';
      setTimeout(() => item.remove(), 210);
    }, i * 40);
  });
  setTimeout(() => {
    document.getElementById('notif-list').style.display = 'none';
    const empty = document.getElementById('notif-empty');
    empty.style.display = 'flex';
    refreshUnreadUI();
  }, items.length * 40 + 250);
}

/* ════════════════════════════════════════════════════════
   MENU CONTEXTUEL (⋯)
════════════════════════════════════════════════════════ */
let _ctxItem = null;

function openItemMenu(btn) {
  _ctxItem = btn.closest('.notif-item');
  const menu    = document.getElementById('ctx-menu');
  const overlay = document.getElementById('ctx-overlay');
  const markBtn = document.getElementById('ctx-mark');

  /* Adapter le label selon l'état */
  markBtn.innerHTML = _ctxItem.dataset.read === 'false'
    ? '<i class="ri-eye-line"></i> Marquer comme lu'
    : '<i class="ri-eye-off-line"></i> Marquer comme non lu';

  /* Positionner le menu près du bouton */
  const rect = btn.getBoundingClientRect();
  menu.style.top  = (rect.bottom + 6) + 'px';
  menu.style.right = (window.innerWidth - rect.right + 4) + 'px';
  menu.style.left  = 'auto';

  /* Vérifier que le menu ne dépasse pas en bas */
  if (rect.bottom + 90 > window.innerHeight) {
    menu.style.top    = 'auto';
    menu.style.bottom = (window.innerHeight - rect.top + 6) + 'px';
  } else {
    menu.style.bottom = 'auto';
  }

  menu.classList.add('open');
  overlay.classList.add('open');
}

function closeCtxMenu() {
  document.getElementById('ctx-menu').classList.remove('open');
  document.getElementById('ctx-overlay').classList.remove('open');
  _ctxItem = null;
}

function ctxMarkRead() {
  if (!_ctxItem) return;
  if (_ctxItem.dataset.read === 'false') {
    markAsRead(_ctxItem);
  } else {
    /* Remettre en non lu */
    _ctxItem.dataset.read = 'false';
    _ctxItem.classList.add('unread');
    const dot = _ctxItem.querySelector('.ni-read-dot');
    if (dot) dot.className = 'ni-unread-dot';
    refreshUnreadUI();
  }
  closeCtxMenu();
}

function ctxDelete() {
  if (!_ctxItem) return;
  _ctxItem.style.transition = 'opacity .2s, transform .2s';
  _ctxItem.style.opacity    = '0';
  _ctxItem.style.transform  = 'translateX(20px)';
  setTimeout(() => {
    _ctxItem.remove();
    refreshUnreadUI();
    /* Vérifier si la liste est vide */
    if (document.querySelectorAll('.notif-item').length === 0) {
      document.getElementById('notif-list').style.display = 'none';
      document.getElementById('notif-empty').style.display = 'flex';
    }
  }, 220);
  closeCtxMenu();
}

/* ════════════════════════════════════════════════════════
   MODAL DÉTAIL
════════════════════════════════════════════════════════ */

/* Config catégorie → couleur + label */
const CAT_CFG = {
  cotisation:  { label: 'Cotisation',  bg: 'rgba(247,184,75,.12)',  color: '#d4870a' },
  paiement:    { label: 'Paiement',    bg: 'rgba(10,179,156,.12)',  color: '#0ab39c' },
  reclamation: { label: 'Réclamation', bg: 'rgba(41,156,219,.12)',  color: '#299cdb' },
  compte:      { label: 'Compte',      bg: 'rgba(64,81,137,.10)',   color: '#405189' },
};

let _actionHref = '';

function openDetail(item) {
  /* Marquer comme lu automatiquement */
  markAsRead(item);

  const d   = item.dataset;
  const cat = CAT_CFG[d.cat] || { label: d.cat, bg: 'rgba(64,81,137,.10)', color: '#405189' };

  /* Icône */
  const iconEl = document.getElementById('nm-icon');
  iconEl.style.background = d.iconBg  || cat.bg;
  iconEl.style.color      = d.iconColor || cat.color;
  iconEl.innerHTML        = `<i class="${d.icon || 'ri-notification-3-line'}"></i>`;

  /* Badge catégorie */
  const badge = document.getElementById('nm-cat-badge');
  badge.textContent   = cat.label;
  badge.style.background = cat.bg;
  badge.style.color      = cat.color;

  /* Infos */
  setText('nm-date',    d.date  || '—');
  setText('nm-title',   d.title || '—');
  setText('nm-message', d.body  || '—');

  /* Bouton action */
  _actionHref = d.actionHref || '';
  setText('nm-action-label', d.actionLabel || 'Voir');

  /* Ouvrir */
  document.getElementById('notif-modal-overlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeDetail() {
  document.getElementById('notif-modal-overlay').classList.remove('open');
  document.body.style.overflow = '';
}

function goToAction() {
  closeDetail();
  if (_actionHref) window.location.href = _actionHref;
}

/* ── Escape ── */
document.addEventListener('keydown', e => {
  if (e.key !== 'Escape') {
    if (document.getElementById('ctx-menu').classList.contains('open')) closeCtxMenu();
    else closeDetail();
  }
});

/* ════════════════════════════════════════════════════════
   ANIMATIONS D'ENTRÉE
════════════════════════════════════════════════════════ */
(function animateItems() {
  document.querySelectorAll('.notif-item').forEach((item, i) => {
    item.style.opacity   = '0';
    item.style.transform = 'translateY(8px)';
    setTimeout(() => {
      item.style.transition = 'opacity .28s ease, transform .28s ease';
      item.style.opacity    = '1';
      item.style.transform  = 'translateY(0)';
    }, 50 + i * 45);
  });
})();

/* ── Init ── */
refreshUnreadUI();

/* ── Helper ── */
function setText(id, val) {
  const el = document.getElementById(id);
  if (el) el.textContent = val || '—';
}
