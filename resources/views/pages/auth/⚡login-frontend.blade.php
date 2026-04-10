<?php

use Livewire\Component;
use Livewire\Attributes\Layout;


new #[Layout('auth.layouts.app-frontend')] class extends Component
{
    //
};
?>

<div class="auth-right">
    
    @push('pre-content')
        <!-- ── Panneau gauche desktop ── -->
        <div class="auth-left">
            <div class="auth-left-inner">
            <div class="al-logo"><i class="ri-mosque-line"></i></div>
            <div class="al-title">Espace Fidèle</div>
            <div class="al-sub">Gérez vos cotisations, suivez votre historique et restez connecté à votre mosquée.</div>
            <div class="al-features">
                <div class="al-feat"><i class="ri-shield-check-line"></i><span>Connexion sécurisée par SMS</span></div>
                <div class="al-feat"><i class="ri-calendar-check-line"></i><span>Suivi de vos cotisations en temps réel</span></div>
                <div class="al-feat"><i class="ri-notification-3-line"></i><span>Alertes et rappels automatiques</span></div>
                <div class="al-feat"><i class="ri-file-list-3-line"></i><span>Gestion de vos documents</span></div>
            </div>
            </div>
        </div>
    @endpush

    <!-- App bar (mobile) -->
    <div class="auth-bar">
      <div class="auth-bar-title">Connexion</div>
      <div class="auth-bar-ph"></div>
    </div>

    <div class="auth-content">
      <div class="view-header fu fu-1">
        <div class="view-title">Bienvenue</div>
        <div class="view-sub">Entrez votre numéro pour recevoir un code OTP.</div>
      </div>

      <div class="auth-card fu fu-2">
        <div class="f-label">Numéro de téléphone</div>
        <div class="phone-row">
          <select class="dial-sel" id="dial">
            <option value="+225">🇨🇮 +225</option>
            <option value="+223">🇲🇱 +223</option>
            <option value="+226">🇧🇫 +226</option>
            <option value="+227">🇳🇪 +227</option>
            <option value="+228">🇹🇬 +228</option>
            <option value="+229">🇧🇯 +229</option>
            <option value="+221">🇸🇳 +221</option>
          </select>
          <input class="phone-input" type="tel" id="phone"
            placeholder="01 23 45 67 89"
            inputmode="numeric" maxlength="12"
            autocomplete="tel"/>
        </div>
        <div class="field-hint">Aucun mot de passe. Un code par SMS suffit.</div>

        <button class="auth-btn" id="btn-send" onclick="sendOtp()">
          Recevoir mon code
        </button>
      </div>

      <div class="auth-help fu fu-3" onclick="alert('Contactez votre administrateur de mosquée.')">
        Besoin d'aide ?
      </div>
    </div>






  </div>