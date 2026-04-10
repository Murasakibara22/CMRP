/* ============================================================
   AJOUT COTISATION JS — réactivité uniquement, zéro donnée
   ============================================================ */
'use strict';

/* ── Sélection type → afficher/masquer champs ─────────────
   Les champs sont déjà dans le HTML en statique.
   Le JS gère uniquement leur visibilité selon le type choisi.
─────────────────────────────────────────────────────────── */
function onTypeChange(sel) {
  const val = sel.value;

  /* Période mois/année : uniquement pour mensuel */
  document.getElementById('field-periode').style.display =
    val === 'mensuel' ? 'block' : 'none';

  /* Bloc engagement : mensuel sans engagement défini
     En prod : conditionnel API. Ici affiché si mensuel. */
  document.getElementById('field-engagement').style.display =
    val === 'mensuel' ? 'block' : 'none';

  /* Info partiel : uniquement mensuel */
  document.getElementById('partial-info').style.display =
    val === 'mensuel' ? 'flex' : 'none';

  updateRecap();
}

/* ── Paliers d'engagement ────────────────────────────────── */
function selectPalier(btn) {
  document.querySelectorAll('.ac-palier').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');

  const isAutre = btn.dataset.val === 'autre';
  document.getElementById('field-montant-libre').style.display =
    isAutre ? 'block' : 'none';

  /* Pré-remplir le montant si palier fixe */
  if (!isAutre) {
    document.getElementById('input-montant').value = btn.dataset.val;
    updateRecap();
  } else {
    document.getElementById('input-montant').value = '';
    document.getElementById('input-montant-libre').focus();
  }
}

/* ── Mode paiement ───────────────────────────────────────── */
function selectMode(btn) {
  document.querySelectorAll('.ac-mode').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');

  /* Référence : Mobile Money & Virement uniquement */
  const mode = btn.dataset.mode;
  document.getElementById('field-reference').style.display =
    (mode === 'mobile_money' || mode === 'virement') ? 'block' : 'none';

  updateRecap();
}

/* ── Toggle validation ───────────────────────────────────── */
function onToggleValidation(chk) {
  /* Pas de logique métier ici — juste état visuel */
}

/* ── Récapitulatif dynamique ─────────────────────────────── */
function updateRecap() {
  const typeEl   = document.getElementById('select-type');
  const montant  = document.getElementById('input-montant').value;
  const moisEl   = document.getElementById('select-mois');
  const anneeEl  = document.getElementById('select-annee');
  const modeBtn  = document.querySelector('.ac-mode.active');

  /* Type */
  const typeLabels = {
    mensuel:  'Cotisation mensuelle',
    quete:    'Quête du vendredi',
    ordinaire:'Don ordinaire',
    ramadan:  'Ramadan 1446',
  };
  document.getElementById('recap-type').textContent =
    typeLabels[typeEl.value] || '—';

  /* Période */
  const moisNames = ['','Janvier','Février','Mars','Avril','Mai','Juin',
    'Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
  const periodeEl = document.getElementById('recap-periode');
  if (typeEl.value === 'mensuel' && moisEl.value) {
    periodeEl.textContent = (moisNames[moisEl.value] || '') + ' ' + (anneeEl.value || '');
  } else {
    periodeEl.textContent = '—';
  }

  /* Montant */
  document.getElementById('recap-montant').textContent =
    montant ? parseInt(montant).toLocaleString('fr-FR') + ' FCFA' : '—';

  /* Mode */
  if (modeBtn) {
    document.getElementById('recap-mode').textContent =
      modeBtn.querySelector('span').textContent;
  }
}

/* ── Écoutes pour mise à jour du récap ──────────────────── */
['select-type','select-mois','select-annee','input-montant'].forEach(id => {
  document.getElementById(id)?.addEventListener('change', updateRecap);
  document.getElementById(id)?.addEventListener('input',  updateRecap);
});

/* ── Montant libre → sync montant principal ──────────────── */
document.getElementById('input-montant-libre')?.addEventListener('input', function () {
  document.getElementById('input-montant').value = this.value;
  updateRecap();
});

/* ── Soumission ──────────────────────────────────────────── */
function submitForm() {
  const type    = document.getElementById('select-type').value;
  const montant = document.getElementById('input-montant').value;

  if (!type) {
    document.getElementById('select-type').classList.add('err');
    document.getElementById('select-type').focus();
    return;
  }
  if (!montant || parseInt(montant) < 1) {
    document.getElementById('input-montant').classList.add('err');
    document.getElementById('input-montant').focus();
    return;
  }

  const btn = document.getElementById('btn-submit');
  btn.innerHTML = '<div class="spinner"></div> Envoi en cours…';
  btn.disabled  = true;

  /* En prod : appel API Livewire */
  setTimeout(() => {
    window.location.href = 'cotisations.html';
  }, 1400);
}

/* Nettoyer erreur au focus */
document.querySelectorAll('.f-input').forEach(el => {
  el.addEventListener('focus', () => el.classList.remove('err'));
});

/* Init : masquer champs conditionnels par défaut */
(function init() {
  document.getElementById('field-periode').style.display       = 'none';
  document.getElementById('field-engagement').style.display    = 'none';
  document.getElementById('partial-info').style.display        = 'none';
  document.getElementById('field-montant-libre').style.display = 'none';
})();