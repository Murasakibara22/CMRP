/* ============================================================
   RÉCLAMATIONS JS — réactivité uniquement, zéro donnée
   ============================================================ */
'use strict';

/* ── Filtre statut ───────────────────────────────────────── */
function filterRecla(btn) {
  document.querySelectorAll('.recla-filter').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');

  const filter = btn.dataset.filter;
  const items  = document.querySelectorAll('.recla-item');
  const empty  = document.getElementById('recla-empty');
  let count    = 0;

  items.forEach(item => {
    const show = filter === 'tous' || item.dataset.statut === filter;
    item.style.display = show ? '' : 'none';
    if (show) count++;
  });

  if (empty) empty.style.display = count === 0 ? 'flex' : 'none';
}

/* ════════════════════════════════════════════════════════
   MODAL NOUVELLE RÉCLAMATION
════════════════════════════════════════════════════════ */
function openAddModal() {
  /* Reset formulaire */
  document.getElementById('add-cotisation').value = '';
  document.getElementById('add-titre').value      = '';
  document.getElementById('add-message').value    = '';
  document.querySelectorAll('#add-overlay .f-input').forEach(el => el.classList.remove('err'));
  const btn = document.getElementById('btn-submit-recla');
  btn.innerHTML = '<i class="ri-send-plane-line"></i> Envoyer';
  btn.disabled  = false;

  document.getElementById('add-overlay').classList.add('open');
  document.body.style.overflow = 'hidden';
  setTimeout(() => document.getElementById('add-titre').focus(), 350);
}

function closeAddModal() {
  document.getElementById('add-overlay').classList.remove('open');
  document.body.style.overflow = '';
}

function submitRecla() {
  const titre   = document.getElementById('add-titre').value.trim();
  const message = document.getElementById('add-message').value.trim();
  let ok = true;

  document.getElementById('add-titre').classList.remove('err');
  document.getElementById('add-message').classList.remove('err');

  if (!titre)   { document.getElementById('add-titre').classList.add('err');   ok = false; }
  if (!message) { document.getElementById('add-message').classList.add('err'); ok = false; }
  if (!ok) return;

  const btn = document.getElementById('btn-submit-recla');
  btn.innerHTML = '<div class="spinner"></div> Envoi…';
  btn.disabled  = true;

  /* En prod : appel API / Livewire */
  setTimeout(() => {
    closeAddModal();
    /* Petit feedback visuel */
    showToast('Réclamation envoyée avec succès !');
  }, 1300);
}

/* ════════════════════════════════════════════════════════
   MODAL DÉTAIL RÉCLAMATION
════════════════════════════════════════════════════════ */
function openDetailModal(data) {
  /* Statut pill */
  const pills = {
    en_cours: '<span class="pill pill-info">En cours</span>',
    resolu:   '<span class="pill pill-ok">Résolu</span>',
    rejete:   '<span class="pill pill-danger">Rejeté</span>',
  };

  setText('det-id',        data.id);
  setHTML('det-statut-pill', pills[data.statut] || '');
  setText('det-titre',     data.titre);
  setText('det-date',      'Soumise le ' + data.date);
  setText('det-message',   data.message);

  /* Cotisation liée */
  const cotWrap = document.getElementById('det-cot-wrap');
  if (data.cotisation) {
    cotWrap.style.display = '';
    setText('det-cotisation', data.cotisation);
  } else {
    cotWrap.style.display = 'none';
  }

  /* Réponse admin */
  const repWrap = document.getElementById('det-reponse-wrap');
  const repEl   = document.getElementById('det-reponse');
  if (data.reponse) {
    repWrap.style.display = '';
    repEl.textContent = data.reponse;
  } else {
    repWrap.style.display = 'none';
  }

  document.getElementById('detail-overlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeDetailModal() {
  document.getElementById('detail-overlay').classList.remove('open');
  document.body.style.overflow = '';
}

/* ── Fermer avec Escape ── */
document.addEventListener('keydown', e => {
  if (e.key !== 'Escape') return;
  closeAddModal();
  closeDetailModal();
});

/* Nettoyer erreurs */
document.querySelectorAll('#add-overlay .f-input').forEach(el => {
  el.addEventListener('focus', () => el.classList.remove('err'));
});

/* ── Animation items ─────────────────────────────────────── */
(function animateItems() {
  document.querySelectorAll('.recla-item').forEach((item, i) => {
    item.style.opacity   = '0';
    item.style.transform = 'translateY(10px)';
    setTimeout(() => {
      item.style.transition = 'opacity .3s ease, transform .3s ease';
      item.style.opacity    = '1';
      item.style.transform  = 'translateY(0)';
    }, 60 + i * 70);
  });
})();

/* ── Toast simple ────────────────────────────────────────── */
function showToast(msg) {
  let toast = document.getElementById('pwa-toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'pwa-toast';
    toast.style.cssText = `
      position:fixed; bottom:90px; left:50%; transform:translateX(-50%);
      background:#1a1d2e; color:#fff; padding:12px 20px; border-radius:12px;
      font-size:14px; font-weight:700; font-family:'Nunito',sans-serif;
      z-index:400; box-shadow:0 4px 20px rgba(0,0,0,.25);
      opacity:0; transition:opacity .3s ease; white-space:nowrap;
    `;
    document.body.appendChild(toast);
  }
  toast.textContent = msg;
  toast.style.opacity = '1';
  setTimeout(() => { toast.style.opacity = '0'; }, 3000);
}

/* ── Helpers ── */
function setText(id, val) { const el = document.getElementById(id); if (el) el.textContent = val || '—'; }
function setHTML(id, val) { const el = document.getElementById(id); if (el) el.innerHTML   = val || ''; }