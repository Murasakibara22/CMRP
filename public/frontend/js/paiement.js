/* ============================================================
   PAIEMENTS JS — réactivité uniquement, zéro donnée
   ============================================================ */
'use strict';

/* ── Filtre statut ───────────────────────────────────────── */
function filterPay(btn) {
  document.querySelectorAll('.pay-filter').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');

  const filter = btn.dataset.filter;
  document.querySelectorAll('.pay-item').forEach(item => {
    item.style.display =
      (filter === 'tous' || item.dataset.statut === filter) ? '' : 'none';
  });
}

/* ── Modale détail ───────────────────────────────────────── */
let _currentDetail = null;

function openDetail(data) {
  _currentDetail = data;

  /* Couleur header selon statut */
  const header = document.getElementById('pay-modal-header');
  const icon   = document.getElementById('pmh-icon');
  const statuts = {
    success: { bg: 'rgba(10,179,156,.10)', color: '#0ab39c', icon: 'ri-checkbox-circle-line', pill: '<span class="pill pill-ok">Succès</span>' },
    attente: { bg: 'rgba(247,184,75,.12)',  color: '#f7b84b', icon: 'ri-time-line',           pill: '<span class="pill pill-warn">En attente</span>' },
    echec:   { bg: 'rgba(240,101,72,.10)',  color: '#f06548', icon: 'ri-close-circle-line',   pill: '<span class="pill pill-danger">Échoué</span>' },
  };
  const s = statuts[data.statut] || statuts.success;
  icon.style.background = s.bg;
  icon.style.color      = s.color;
  icon.innerHTML        = `<i class="${s.icon}"></i>`;

  /* Remplir les champs */
  setText('pmh-title',        'Détail du paiement');
  setText('pmh-ref',          data.ref);
  setText('pmd-type',         data.type);
  setText('pmd-periode',      data.periode);
  setText('pmd-mode',         data.mode);
  setText('pmd-operateur',    data.operateur || '—');
  setText('pmd-ref-val',      data.ref);
  setText('pmd-date',         data.date);
  setText('pmd-validated-by', data.validated_by || '—');
  setText('pmd-validated-at', data.validated_at || '—');

  /* Montant coloré */
  const amountEl = document.getElementById('pmd-amount');
  amountEl.textContent  = data.montant;
  amountEl.style.color  = s.color;

  /* Pill statut */
  document.getElementById('pmd-statut').innerHTML = s.pill;

  /* Note (si présente) */
  const noteWrap = document.getElementById('pmd-note-wrap');
  if (data.note) {
    noteWrap.style.display = '';
    setText('pmd-note', data.note);
  } else {
    noteWrap.style.display = 'none';
  }

  /* Ouvrir overlay */
  document.getElementById('pay-modal-overlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeDetail() {
  document.getElementById('pay-modal-overlay').classList.remove('open');
  document.body.style.overflow = '';
}

/* Fermer avec Escape */
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeDetail();
});

/* ── Impression ──────────────────────────────────────────── */
function printDetail() {
  if (!_currentDetail) return;
  const d = _currentDetail;

  const statuts = { success:'Succès', attente:'En attente', echec:'Échoué' };

  setText('pr-ref',       d.ref);
  setText('pr-type',      d.type);
  setText('pr-periode',   d.periode);
  setText('pr-montant',   d.montant);
  setText('pr-mode',      d.mode);
  setText('pr-operateur', d.operateur || '—');
  setText('pr-date',      d.date);
  setText('pr-valby',     d.validated_by || '—');
  setText('pr-valat',     d.validated_at || '—');
  setText('pr-statut',    statuts[d.statut] || '—');
  setText('pr-now',       new Date().toLocaleString('fr-FR'));

  document.getElementById('print-zone').style.display = 'block';
  closeDetail();

  setTimeout(() => {
    window.print();
    document.getElementById('print-zone').style.display = 'none';
  }, 100);
}

/* ── Animation entrée des items ──────────────────────────── */
(function animateItems() {
  document.querySelectorAll('.pay-item').forEach((item, i) => {
    item.style.opacity   = '0';
    item.style.transform = 'translateX(-8px)';
    setTimeout(() => {
      item.style.transition = 'opacity .3s ease, transform .3s ease';
      item.style.opacity    = '1';
      item.style.transform  = 'translateX(0)';
    }, 60 + i * 55);
  });
})();

/* ── Helper ──────────────────────────────────────────────── */
function setText(id, val) {
  const el = document.getElementById(id);
  if (el) el.textContent = val || '—';
}