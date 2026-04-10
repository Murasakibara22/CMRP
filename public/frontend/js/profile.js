/* ============================================================
   PROFIL JS — réactivité uniquement, zéro donnée
   ============================================================ */
'use strict';

/* ════════════════════════════════════════════════════════
   MODAL MODIFIER INFOS
════════════════════════════════════════════════════════ */
function openEditModal() {
  document.getElementById('edit-overlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeEditModal() {
  document.getElementById('edit-overlay').classList.remove('open');
  document.body.style.overflow = '';
}

function saveEdit() {
  const nom    = document.getElementById('edit-nom').value.trim();
  const prenom = document.getElementById('edit-prenom').value.trim();
  if (!nom || !prenom) {
    if (!nom)    document.getElementById('edit-nom').classList.add('err');
    if (!prenom) document.getElementById('edit-prenom').classList.add('err');
    return;
  }

  const btn = document.querySelector('#edit-overlay .btn-main');
  btn.innerHTML = '<div class="spinner"></div> Enregistrement…';
  btn.disabled  = true;

  /* En prod : appel API / Livewire */
  setTimeout(() => {
    closeEditModal();
    btn.innerHTML = '<i class="ri-save-line"></i> Enregistrer';
    btn.disabled  = false;
    /* Mettre à jour l'affichage du nom */
    document.querySelector('.prof-name').textContent = prenom + ' ' + nom;
    document.querySelector('.sb-profile-name').textContent = prenom + ' ' + nom;
  }, 1200);
}

/* Nettoyer erreurs */
document.querySelectorAll('#edit-overlay .f-input').forEach(el => {
  el.addEventListener('focus', () => el.classList.remove('err'));
});

/* ════════════════════════════════════════════════════════
   MODAL PHOTO
════════════════════════════════════════════════════════ */
function openPhotoModal() {
  document.getElementById('photo-overlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closePhotoModal() {
  document.getElementById('photo-overlay').classList.remove('open');
  document.body.style.overflow = '';
}

function triggerCamera() {
  const input = document.getElementById('file-input');
  input.setAttribute('capture', 'environment');
  input.click();
}
function triggerGallery() {
  const input = document.getElementById('file-input');
  input.removeAttribute('capture');
  input.click();
}

function onFileSelect(input) {
  const file = input.files[0];
  if (!file) return;
  const url = URL.createObjectURL(file);

  /* Aperçu dans la modale */
  const prev = document.getElementById('photo-preview');
  prev.innerHTML = `<img src="${url}" alt="Photo"/>`;

  /* Aperçu dans le hero */
  const hero = document.getElementById('prof-avatar');
  hero.innerHTML = `<img src="${url}" alt="Photo"/>`;

  setTimeout(() => closePhotoModal(), 600);
}

function removePhoto() {
  document.getElementById('photo-preview').innerHTML = 'MK';
  document.getElementById('prof-avatar').innerHTML   = 'MK';
  document.getElementById('file-input').value        = '';
  closePhotoModal();
}

/* ── Fermer modals avec Escape ── */
document.addEventListener('keydown', e => {
  if (e.key !== 'Escape') return;
  closeEditModal();
  closePhotoModal();
});

/* ── Déconnexion ── */
function confirmDeconnexion() {
  if (confirm('Voulez-vous vous déconnecter ?')) {
    window.location.href = 'login.html';
  }
}