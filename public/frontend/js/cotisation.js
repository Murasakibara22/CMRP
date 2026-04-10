/* ============================================================
   COTISATIONS JS — réactivité uniquement, zéro donnée
   ============================================================ */
'use strict';

/* ════════════════════════════════════════════════════════
   FILTRE PAR STATUT
════════════════════════════════════════════════════════ */
function filterCot(btn) {
  document.querySelectorAll('.stab').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  const statut = btn.dataset.statut;
  const items  = document.querySelectorAll('.cot-item');
  const labels = document.querySelectorAll('.cot-month-label');
  const empty  = document.getElementById('cot-empty');
  let visible  = 0;
  items.forEach(item => {
    const show = statut === 'tous' || item.dataset.statut === statut;
    item.style.display = show ? '' : 'none';
    if (show) visible++;
  });
  labels.forEach(label => {
    let sib = label.nextElementSibling;
    let has = false;
    while (sib && !sib.classList.contains('cot-month-label')) {
      if (sib.classList.contains('cot-item') && sib.style.display !== 'none') { has = true; break; }
      sib = sib.nextElementSibling;
    }
    label.style.display = has ? '' : 'none';
  });
  if (empty) empty.style.display = visible === 0 ? 'flex' : 'none';
}

/* ════════════════════════════════════════════════════════
   MODAL DÉTAIL COTISATION
════════════════════════════════════════════════════════ */
const STATUT_CFG = {
  retard:  { bg:'linear-gradient(135deg,#c0341a,#f06548)', pill:'<span class="pill" style="background:rgba(255,255,255,.2);color:#fff;font-size:11px"><i class="ri-time-line"></i> En retard</span>', progColor:'#f06548' },
  partiel: { bg:'linear-gradient(135deg,#c07a10,#f7b84b)', pill:'<span class="pill" style="background:rgba(255,255,255,.2);color:#fff;font-size:11px"><i class="ri-error-warning-line"></i> Partiel</span>', progColor:'#f7b84b' },
  ajour:   { bg:'linear-gradient(135deg,#089383,#0ab39c)', pill:'<span class="pill" style="background:rgba(255,255,255,.2);color:#fff;font-size:11px"><i class="ri-checkbox-circle-line"></i> À jour</span>',  progColor:'#0ab39c' },
};

let _currentPeriode = '';
let _currentType    = '';

function openDetail(item) {
  const d      = item.dataset;
  const statut = d.statut || 'ajour';
  const cfg    = STATUT_CFG[statut] || STATUT_CFG.ajour;

  _currentPeriode = d.periode || '';
  _currentType    = d.type    || '';

  /* Header */
  document.getElementById('cot-modal-header').style.background = cfg.bg;
  const icon = document.getElementById('cmh-icon');
  icon.style.background = 'rgba(255,255,255,.18)';
  icon.innerHTML = `<i class="${d.typeIcon || 'ri-calendar-check-line'}" style="color:#fff"></i>`;
  setText('cmh-type',   d.type   || '—');
  setText('cmh-period', d.periode || '—');
  setText('cmh-amount', (d.montantDu && d.montantDu !== '—') ? d.montantDu : (d.montantPaye || '—'));
  setHTML('cmh-pill', cfg.pill);

  /* Grille infos */
  const det = (id, val, color) => {
    const el = document.getElementById(id);
    if (!el) return;
    el.textContent = val || '—';
    el.style.color = color || 'var(--text)';
  };
  det('det-montant-du',   d.montantDu,   statut === 'retard' ? '#f06548' : 'var(--text)');
  det('det-montant-paye', d.montantPaye, (d.montantPaye && d.montantPaye !== '0 FCFA') ? '#0ab39c' : 'var(--muted)');
  det('det-restant',      d.restant,     (d.restant && d.restant !== '—') ? '#f06548' : 'var(--muted)');
  det('det-mode',         d.mode);
  det('det-engagement',   d.engagement,  'var(--p)');
  det('det-created',      d.created);

  /* Barre progression */
  const pct  = parseInt(d.pct) || 0;
  const fill = document.getElementById('det-prog-fill');
  fill.style.background = cfg.progColor;
  fill.style.width = '0%';
  document.getElementById('det-pct-label').style.color = cfg.progColor;
  document.getElementById('det-pct-label').textContent = pct + '%';
  setTimeout(() => { fill.style.width = pct + '%'; }, 80);
  setText('det-prog-paye-lbl', (d.montantPaye || '—') + ' payé');
  setText('det-prog-du-lbl',   (d.montantDu && d.montantDu !== '—' ? d.montantDu : '—') + ' dû');

  /* Paiements */
  filterPayRows(d.periode, d.type);

  /* Bouton Payer : masqué si à jour */
  document.querySelector('.cot-footer-pay').style.display = statut !== 'ajour' ? 'flex' : 'none';

  document.getElementById('cot-modal-overlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeDetail() {
  document.getElementById('cot-modal-overlay').classList.remove('open');
  document.body.style.overflow = '';
}

function filterPayRows(periode, type) {
  const typeKeys = { 'Quête du vendredi':'Quête du vendredi', 'Ramadan 1446':'Ramadan 1446' };
  const key = typeKeys[type] || periode;
  const rows  = document.querySelectorAll('.cot-pay-row');
  const empty = document.getElementById('det-pay-empty');
  let visible = 0;
  rows.forEach(row => {
    const show = row.dataset.periode === key;
    row.style.display = show ? '' : 'none';
    if (show) visible++;
  });
  if (empty) empty.style.display = visible === 0 ? 'flex' : 'none';
}

function goToPaiement(ref) {
  window.location.href = 'paiements.html';
}

function goToPay() {
  closeDetail();
  window.location.href = 'ajout-cotisation.html';
}

/* ════════════════════════════════════════════════════════
   MODAL RÉCLAMATION
════════════════════════════════════════════════════════ */
function openReclaModal() {
  document.getElementById('recla-cotisation').value =
    (_currentType && _currentPeriode) ? `${_currentType} — ${_currentPeriode}` : '';
  document.getElementById('recla-titre').value   = '';
  document.getElementById('recla-message').value = '';
  ['recla-titre','recla-message'].forEach(id => document.getElementById(id)?.classList.remove('err'));
  const btn = document.getElementById('btn-recla-submit');
  btn.innerHTML = '<i class="ri-send-plane-line"></i> Envoyer';
  btn.disabled  = false;
  document.getElementById('recla-overlay').classList.add('open');
}

function closeReclaModal() {
  document.getElementById('recla-overlay').classList.remove('open');
}

function submitRecla() {
  const titre   = document.getElementById('recla-titre').value.trim();
  const message = document.getElementById('recla-message').value.trim();
  let ok = true;
  document.getElementById('recla-titre').classList.remove('err');
  document.getElementById('recla-message').classList.remove('err');
  if (!titre)   { document.getElementById('recla-titre').classList.add('err');   ok = false; }
  if (!message) { document.getElementById('recla-message').classList.add('err'); ok = false; }
  if (!ok) return;
  const btn = document.getElementById('btn-recla-submit');
  btn.innerHTML = '<div class="spinner"></div>';
  btn.disabled  = true;
  setTimeout(() => {
    closeReclaModal();
    closeDetail();
    showToast('Réclamation envoyée avec succès !');
  }, 1300);
}

/* ── Escape ── */
document.addEventListener('keydown', e => {
  if (e.key !== 'Escape') return;
  if (document.getElementById('recla-overlay').classList.contains('open')) { closeReclaModal(); return; }
  closeDetail();
});

/* ── Nettoyer erreurs ── */
['recla-titre','recla-message'].forEach(id => {
  document.getElementById(id)?.addEventListener('focus', function(){ this.classList.remove('err'); });
});

/* ════════════════════════════════════════════════════════
   ANIMATIONS D'ENTRÉE
════════════════════════════════════════════════════════ */
(function animateItems() {
  document.querySelectorAll('.cot-item').forEach((item, i) => {
    item.style.opacity   = '0';
    item.style.transform = 'translateY(10px)';
    setTimeout(() => {
      item.style.transition = 'opacity .3s ease, transform .3s ease';
      item.style.opacity    = '1';
      item.style.transform  = 'translateY(0)';
    }, 60 + i * 50);
  });
})();

/* ════════════════════════════════════════════════════════
   SPINNER + TOAST
════════════════════════════════════════════════════════ */
(function injectSpinner() {
  if (document.getElementById('_spin_style')) return;
  const s = document.createElement('style');
  s.id = '_spin_style';
  s.textContent = '@keyframes _spin{to{transform:rotate(360deg)}}.spinner{width:18px;height:18px;border:2.5px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:_spin .7s linear infinite;flex-shrink:0}';
  document.head.appendChild(s);
})();

function showToast(msg) {
  let t = document.getElementById('pwa-toast');
  if (!t) {
    t = document.createElement('div');
    t.id = 'pwa-toast';
    t.style.cssText='position:fixed;bottom:88px;left:50%;transform:translateX(-50%);background:#1a1d2e;color:#fff;padding:12px 20px;border-radius:12px;font-size:14px;font-weight:700;font-family:Nunito,sans-serif;z-index:500;box-shadow:0 4px 20px rgba(0,0,0,.25);opacity:0;transition:opacity .3s ease;white-space:nowrap';
    document.body.appendChild(t);
  }
  t.textContent = msg;
  t.style.opacity = '1';
  setTimeout(() => { t.style.opacity = '0'; }, 3000);
}

/* ── Helpers ── */
function setText(id, val) { const el=document.getElementById(id); if(el) el.textContent=val||'—'; }
function setHTML(id, val) { const el=document.getElementById(id); if(el) el.innerHTML=val||''; }