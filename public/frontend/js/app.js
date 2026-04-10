/* ============================================================
   ACCUEIL JS — réactivité uniquement, zéro donnée
   ============================================================ */
'use strict';

/* ── Filtre historique ───────────────────────────────────── */
function filterHist(btn) {
  document.querySelectorAll('.hf-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');

  const filter = btn.dataset.filter;
  document.querySelectorAll('.hist-item').forEach(item => {
    item.style.display =
      (filter === 'tous' || item.dataset.type === filter) ? '' : 'none';
  });
}

/* ── Animation KPI au chargement ────────────────────────── */
(function animateKPI() {
  const cards = document.querySelectorAll('.kpi-card');
  cards.forEach((card, i) => {
    card.style.opacity  = '0';
    card.style.transform = 'translateY(12px)';
    setTimeout(() => {
      card.style.transition = 'opacity .35s ease, transform .35s ease';
      card.style.opacity    = '1';
      card.style.transform  = 'translateY(0)';
    }, 100 + i * 60);
  });
})();

/* ── Animation alert card ────────────────────────────────── */
(function animateAlert() {
  const card = document.querySelector('.alert-card');
  if (!card) return;
  card.style.opacity  = '0';
  card.style.transform = 'translateY(10px)';
  setTimeout(() => {
    card.style.transition = 'opacity .4s ease, transform .4s ease';
    card.style.opacity    = '1';
    card.style.transform  = 'translateY(0)';
  }, 80);
})();