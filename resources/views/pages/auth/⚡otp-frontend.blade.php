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
   
    <div class="auth-bar">
      <button class="auth-bar-back" onclick="history.back()">
        <i class="ri-arrow-left-line"></i>
      </button>
      <div class="auth-bar-title">Vérification</div>
      <button class="auth-bar-action" onclick="history.back()">Changer</button>
    </div>
 
    <div class="auth-content">
      <div class="view-header fu fu-1">
        <div class="view-title">Code de vérification</div>
        <div class="view-sub" id="otp-phone-label">Code envoyé au +225 01 23 45 67 89</div>
      </div>
 
      <div class="auth-card fu fu-2">
        <div class="f-label">OTP (6 chiffres)</div>
 
        <div class="otp-grid" id="otp-grid">
          <input class="otp-box" type="tel" inputmode="numeric" maxlength="1" autocomplete="off"/>
          <input class="otp-box" type="tel" inputmode="numeric" maxlength="1" autocomplete="off"/>
          <input class="otp-box" type="tel" inputmode="numeric" maxlength="1" autocomplete="off"/>
          <input class="otp-box" type="tel" inputmode="numeric" maxlength="1" autocomplete="off"/>
          <input class="otp-box" type="tel" inputmode="numeric" maxlength="1" autocomplete="off"/>
          <input class="otp-box" type="tel" inputmode="numeric" maxlength="1" autocomplete="off"/>
        </div>
 
        <div class="otp-meta">
          <div class="otp-timer">
            Renvoyer dans <strong id="timer-val">01:00</strong>
          </div>
          <button class="otp-resend" id="btn-resend" onclick="resend()">Renvoyer</button>
        </div>
 
        <button class="auth-btn" id="btn-verify" onclick="verify()">
          Continuer
        </button>
      </div>
    </div>
</div>