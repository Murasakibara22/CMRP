<?php

use Livewire\Component;
use Livewire\Attributes\Layout;


new #[Layout('auth.layouts.app-frontend')] class extends Component
{
    //
};
?>

<div class="auth-root">
 
    @push('pre-content')
  <!-- ── Panneau gauche desktop ── -->
  <div class="auth-left">
    <div class="auth-left-inner">
      <div class="al-logo"><i class="ri-mosque-line"></i></div>
      <div class="al-title">Créer votre compte</div>
      <div class="al-sub">Rejoignez la communauté et gérez vos cotisations facilement depuis votre téléphone.</div>
      <div class="al-features">
        <div class="al-feat"><i class="ri-user-add-line"></i><span>Inscription simple et rapide</span></div>
        <div class="al-feat"><i class="ri-shield-check-line"></i><span>Validé par un administrateur</span></div>
        <div class="al-feat"><i class="ri-group-line"></i><span>Parrainage disponible</span></div>
      </div>
    </div>
  </div>
        @endpush
 
  <!-- ── Panneau droit ── -->
  <div class="auth-right">
 
    <!-- App bar -->
    <div class="auth-bar">
      <button class="auth-bar-back" onclick="history.back()">
        <i class="ri-arrow-left-line"></i>
      </button>
      <div class="auth-bar-title">Inscription</div>
      <div class="auth-bar-ph"></div>
    </div>
 
    <div class="auth-content">
      <div class="view-header fu fu-1">
        <div class="view-title">Créer votre compte</div>
        <div class="view-sub">Aucun mot de passe. Votre téléphone suffit.</div>
      </div>
 
      <div class="auth-card fu fu-2">
        <div class="reg-fields">
          <input class="f-input" type="text" id="nom"    placeholder="Nom"    autocomplete="family-name"/>
          <input class="f-input" type="text" id="prenom" placeholder="Prénoms" autocomplete="given-name"/>
          <input class="f-input" type="text" id="parrain" placeholder="Code parrainage (optionnel)"/>
        </div>
        <button class="auth-btn" onclick="createAccount()">
          Créer mon compte
        </button>
      </div>
 
      <div class="reg-legal fu fu-3">
        En créant votre compte, vous acceptez la vérification manuelle par un administrateur.
      </div>
    </div>
  </div>
</div>


@push('scripts')

<script>
function createAccount() {
  const nom    = document.getElementById('nom').value.trim();
  const prenom = document.getElementById('prenom').value.trim();
  let ok = true;
 
  [document.getElementById('nom'), document.getElementById('prenom')].forEach(el => el.classList.remove('err'));
 
  if (!nom)    { document.getElementById('nom').classList.add('err');    ok = false; }
  if (!prenom) { document.getElementById('prenom').classList.add('err'); ok = false; }
  if (!ok) return;
 
  const btn = document.querySelector('.auth-btn');
  btn.innerHTML = '<div class="spinner"></div>';
  btn.disabled  = true;
 
  /* En prod → appel API */
  setTimeout(() => { window.location.href = 'app.html'; }, 1200);
}
 
document.querySelectorAll('.f-input').forEach(el => {
  el.addEventListener('focus', () => el.classList.remove('err'));
});
</script>

@endpush