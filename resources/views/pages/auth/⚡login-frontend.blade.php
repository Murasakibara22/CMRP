<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Customer;
use App\Models\OtpVerification;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;


new #[Layout('auth.layouts.app-frontend')] class extends Component
{
    /* ── Étape active : phone | otp | register ── */
    public string $step = 'phone';

    /* ── Étape 1 : téléphone ── */
    public string $dialCode = '+225';
    public string $phone    = '';

    /* ── Étape 2 : OTP ── */
    public string $otp = '';

    /* ── Étape 3 : inscription ── */
    public string $nom     = '';
    public string $prenom  = '';
    public string $adresse = '';

    /* ── Erreurs manuelles ── */
    public string $errorPhone = '';
    public string $errorOtp   = '';
    public string $errorReg   = '';

    /* ── Step 1 : Envoyer OTP ─────────────────────────────── */
    public function sendOtp(): void
    {
        $this->errorPhone = '';

        $clean = preg_replace('/\s+/', '', $this->phone);
        if (! $clean || strlen($clean) < 8) {
            $this->errorPhone = 'Veuillez entrer un numéro valide.';
            return;
        }

        $fullPhone = $this->dialCode . $clean;

        $otpRecord = OtpVerification::createForPhone($fullPhone, request()->ip());

        /* En production : envoyer le SMS ici
           SmsService::send($fullPhone, "Votre code ISL : {$otpRecord->code}"); */

        /* En DEV : stocker le code pour debug dans session */
        Session::put('otp_debug', $otpRecord->code);

        $this->step = 'otp';
    }

    /* ── Step 2 : Vérifier OTP ────────────────────────────── */
    public function verifyOtp(): void
    {
        $this->errorOtp = '';

        $code = preg_replace('/\D/', '', $this->otp);

        if (strlen($code) < 6) {
            $this->errorOtp = 'Veuillez entrer le code complet à 6 chiffres.';
            return;
        }

        $fullPhone = $this->dialCode . preg_replace('/\s+/', '', $this->phone);

        $ok = OtpVerification::verify($fullPhone, $code);

        if (! $ok) {
            $this->errorOtp = 'Code incorrect ou expiré. Vérifiez et réessayez.';
            return;
        }

        /* Chercher si le fidèle existe déjà */
        $customer = Customer::where('phone', preg_replace('/\s+/', '', $this->phone))
            ->where('dial_code', $this->dialCode)
            ->first();

        if ($customer) {
            Auth::guard('customer')->login($customer);
            $this->redirect(route('customer.home'));
            return;
        }

        /* Nouveau numéro → inscription */
        $this->step = 'register';
    }

    /* ── Step 2 : Renvoyer OTP ────────────────────────────── */
    public function resendOtp(): void
    {
        $this->errorOtp = '';
        $this->otp      = '';

        $fullPhone = $this->dialCode . preg_replace('/\s+/', '', $this->phone);
        $otpRecord = OtpVerification::createForPhone($fullPhone, request()->ip());

        Session::put('otp_debug', $otpRecord->code);

        /* Déclencher le reset du timer JS via dispatch */
        $this->dispatch('otp-resent');
    }

    /* ── Step 3 : Créer le compte ─────────────────────────── */
    public function createAccount(): void
    {
        $this->errorReg = '';

        $this->nom    = trim($this->nom);
        $this->prenom = trim($this->prenom);

        if (! $this->nom || ! $this->prenom) {
            $this->errorReg = 'Le nom et le prénom sont obligatoires.';
            return;
        }

        $customer = Customer::create([
            'nom'          => strtoupper($this->nom),
            'prenom'       => ucwords(strtolower($this->prenom)),
            'dial_code'    => $this->dialCode,
            'phone'        => preg_replace('/\s+/', '', $this->phone),
            'adresse'      => trim($this->adresse) ?: null,
            'status'       => 'actif',   
            'date_adhesion'=> now()->toDateString(),
        ]);

        Session::put('customer_id', $customer->id);


        Auth::guard('customer')->login($customer);


        $this->redirect(route('customer.home'));   // page "validation en attente"
    }

    /* ── Retour à l'étape précédente ─────────────────────── */
    public function goBack(): void
    {
        $this->errorOtp = '';
        $this->errorReg = '';
        $this->otp      = '';

        $this->step = match ($this->step) {
            'otp'      => 'phone',
            'register' => 'otp',
            default    => 'phone',
        };
    }
};
?>

<div class="auth-right">

  @push('pre-content')
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


  {{-- ══════════════════════════════════════════════════════
       ÉTAPE 1 — NUMÉRO DE TÉLÉPHONE
  ══════════════════════════════════════════════════════ --}}
  @if($step === 'phone')

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
        <select class="dial-sel" wire:model="dialCode">
          <option value="+225">🇨🇮 +225</option>
          <option value="+223">🇲🇱 +223</option>
          <option value="+226">🇧🇫 +226</option>
          <option value="+227">🇳🇪 +227</option>
          <option value="+228">🇹🇬 +228</option>
          <option value="+229">🇧🇯 +229</option>
          <option value="+221">🇸🇳 +221</option>
        </select>
        <input class="phone-input {{ $errorPhone ? 'err' : '' }}"
               type="tel"
               wire:model="phone"
               placeholder="01 23 45 67 89"
               inputmode="numeric"
               maxlength="12"
               autocomplete="tel"
               wire:keydown.enter="sendOtp"/>
      </div>

      @if($errorPhone)
      <div style="font-size:13px;color:#f06548;margin-top:8px;font-weight:600">
        <i class="ri-error-warning-line me-1"></i>{{ $errorPhone }}
      </div>
      @endif

      <div class="field-hint">Aucun mot de passe. Un code par SMS suffit.</div>

      <button class="auth-btn" wire:click="sendOtp" wire:loading.attr="disabled">
        <span wire:loading wire:target="sendOtp"><div class="spinner"></div></span>
        <span wire:loading.remove wire:target="sendOtp">Recevoir mon code</span>
      </button>
    </div>

    <div class="auth-help fu fu-3" onclick="alert('Contactez votre administrateur de mosquée.')">
      Besoin d'aide ?
    </div>
  </div>

  @endif


  {{-- ══════════════════════════════════════════════════════
       ÉTAPE 2 — CODE OTP
  ══════════════════════════════════════════════════════ --}}
  @if($step === 'otp')

  <div class="auth-bar">
    <button class="auth-bar-back" wire:click="goBack">
      <i class="ri-arrow-left-line"></i>
    </button>
    <div class="auth-bar-title">Vérification</div>
    <button class="auth-bar-action" wire:click="goBack">Changer</button>
  </div>

  <div class="auth-content">
    <div class="view-header fu fu-1">
      <div class="view-title">Code de vérification</div>
      <div class="view-sub">Code envoyé au {{ $dialCode }} {{ $phone }}</div>
    </div>

    <div class="auth-card fu fu-2">
      <div class="f-label">OTP (6 chiffres)</div>

      {{-- Champ OTP caché lié à Livewire, les cases JS écrivent dedans --}}
      <input type="hidden" id="otp-hidden" wire:model="otp">

      <div class="otp-grid" id="otp-grid">
        <input class="otp-box" type="tel" inputmode="numeric" maxlength="1" autocomplete="off"/>
        <input class="otp-box" type="tel" inputmode="numeric" maxlength="1" autocomplete="off"/>
        <input class="otp-box" type="tel" inputmode="numeric" maxlength="1" autocomplete="off"/>
        <input class="otp-box" type="tel" inputmode="numeric" maxlength="1" autocomplete="off"/>
        <input class="otp-box" type="tel" inputmode="numeric" maxlength="1" autocomplete="off"/>
        <input class="otp-box" type="tel" inputmode="numeric" maxlength="1" autocomplete="off"/>
      </div>

      @if($errorOtp)
      <div style="font-size:13px;color:#f06548;margin-top:10px;font-weight:600;text-align:center">
        <i class="ri-error-warning-line me-1"></i>{{ $errorOtp }}
      </div>
      @endif

      {{-- Debug DEV uniquement --}}
      @if(config('app.debug') && session('otp_debug'))
      <div style="font-size:12px;color:#878a99;text-align:center;margin-top:8px;font-family:monospace">
        DEV — Code : <strong style="color:#405189">{{ session('otp_debug') }}</strong>
      </div>
      @endif

      <div class="otp-meta">
        <div class="otp-timer" id="otp-timer-wrap">
          Renvoyer dans <strong id="timer-val">01:00</strong>
        </div>
        <button class="otp-resend" id="btn-resend" wire:click="resendOtp" wire:loading.attr="disabled">
          Renvoyer
        </button>
      </div>

      <button class="auth-btn" id="btn-verify" wire:click="verifyOtp" wire:loading.attr="disabled">
        <span wire:loading wire:target="verifyOtp"><div class="spinner"></div></span>
        <span wire:loading.remove wire:target="verifyOtp">Continuer</span>
      </button>
    </div>
  </div>

  @endif


  {{-- ══════════════════════════════════════════════════════
       ÉTAPE 3 — INSCRIPTION
  ══════════════════════════════════════════════════════ --}}
  @if($step === 'register')

  <div class="auth-bar">
    <button class="auth-bar-back" wire:click="goBack">
      <i class="ri-arrow-left-line"></i>
    </button>
    <div class="auth-bar-title">Inscription</div>
    <div class="auth-bar-ph"></div>
  </div>

  <div class="auth-content">
    <div class="view-header fu fu-1">
      <div class="view-title">Créer votre compte</div>
      <div class="view-sub">Bienvenue ! Complétez vos informations pour finaliser.</div>
    </div>

    <div class="auth-card fu fu-2">

      @if($errorReg)
      <div style="background:rgba(240,101,72,.06);border:1px solid rgba(240,101,72,.25);border-left:3px solid #f06548;border-radius:0 10px 10px 0;padding:10px 14px;margin-bottom:14px;font-size:13px;color:#c44a2e;font-weight:600">
        <i class="ri-error-warning-line me-1"></i>{{ $errorReg }}
      </div>
      @endif

      <div class="reg-fields">
        <input class="f-input"
               type="text"
               wire:model="nom"
               placeholder="Nom *"
               autocomplete="family-name"
               style="margin-bottom:12px"/>
        <input class="f-input"
               type="text"
               wire:model="prenom"
               placeholder="Prénom(s) *"
               autocomplete="given-name"
               style="margin-bottom:12px"/>
        <input class="f-input"
               type="text"
               wire:model="adresse"
               placeholder="Adresse (optionnel)"
               autocomplete="street-address"/>
      </div>

      <button class="auth-btn" wire:click="createAccount" wire:loading.attr="disabled" style="margin-top:20px">
        <span wire:loading wire:target="createAccount"><div class="spinner"></div></span>
        <span wire:loading.remove wire:target="createAccount">Créer mon compte</span>
      </button>
    </div>

    <div class="reg-legal fu fu-3" style="margin-top:6%">
      En créant votre compte, vous acceptez la vérification manuelle par un administrateur de la mosquée.
    </div>
  </div>

  @endif

</div>


@push('scripts')
<script>
/* ════════════════════════════════════════════════════════════
   OTP — Cases JS (affichage visible, sync vers Livewire)
   Les cases sont purement JS. Le champ #otp-hidden est
   le seul wire:model — on y écrit le code assemblé.
════════════════════════════════════════════════════════════ */
(function () {
  function initOtp() {
    const grid   = document.getElementById('otp-grid');
    const hidden = document.getElementById('otp-hidden');
    if (!grid || !hidden) return;

    const boxes = [...grid.querySelectorAll('.otp-box')];

    function syncHidden() {
      const code = boxes.map(b => b.value).join('');
      hidden.value = code;
      /* Déclencher l'event input pour que Livewire détecte le changement */
      hidden.dispatchEvent(new Event('input', { bubbles: true }));
    }

    boxes.forEach((box, i) => {
      box.addEventListener('input', function () {
        const v = this.value.replace(/\D/g, '');
        this.value = v;                                    // ← texte visible
        this.classList.toggle('filled', !!v);
        this.classList.remove('err');
        syncHidden();
        if (v && i < boxes.length - 1) boxes[i + 1].focus();
        /* Auto-submit quand le 6e chiffre est saisi */
        if (boxes.every(b => b.value)) {
          document.getElementById('btn-verify')?.click();
        }
      });

      box.addEventListener('keydown', function (e) {
        if (e.key === 'Backspace' && !this.value && i > 0) {
          boxes[i - 1].value = '';
          boxes[i - 1].classList.remove('filled');
          boxes[i - 1].focus();
          syncHidden();
        }
      });

      box.addEventListener('paste', function (e) {
        e.preventDefault();
        const text = (e.clipboardData || window.clipboardData)
          .getData('text').replace(/\D/g, '').slice(0, 6);
        boxes.forEach((b, j) => {
          b.value = text[j] || '';
          b.classList.toggle('filled', !!text[j]);
        });
        syncHidden();
        const next = Math.min(text.length, 5);
        boxes[next]?.focus();
        if (text.length === 6) {
          document.getElementById('btn-verify')?.click();
        }
      });
    });

    /* Focus première case */
    boxes[0]?.focus();

    /* ── Timer countdown ── */
    let timerInterval;
    function startTimer(sec = 60) {
      clearInterval(timerInterval);
      const display = document.getElementById('timer-val');
      const resend  = document.getElementById('btn-resend');
      const wrap    = document.getElementById('otp-timer-wrap');
      if (!display || !resend) return;

      resend.classList.remove('on');
      let remaining = sec;

      function tick() {
        const m = String(Math.floor(remaining / 60)).padStart(2, '0');
        const s = String(remaining % 60).padStart(2, '0');
        if (display) display.textContent = m + ':' + s;
        if (remaining <= 0) {
          clearInterval(timerInterval);
          resend.classList.add('on');
          if (wrap) wrap.innerHTML = '<span style="color:var(--muted)">Code expiré</span>';
        }
        remaining--;
      }
      tick();
      timerInterval = setInterval(tick, 1000);
    }

    startTimer();

    /* Reset timer quand Livewire renvoie un OTP */
    Livewire.on('otp-resent', () => {
      boxes.forEach(b => { b.value = ''; b.classList.remove('filled', 'err'); });
      const wrap = document.getElementById('otp-timer-wrap');
      if (wrap) wrap.innerHTML = 'Renvoyer dans <strong id="timer-val">01:00</strong>';
      syncHidden();
      startTimer();
      boxes[0]?.focus();
    });

    /* Shake les cases si erreur Livewire */
    Livewire.on('otp-error', () => {
      boxes.forEach(b => b.classList.add('err'));
      setTimeout(() => boxes.forEach(b => b.classList.remove('err')), 600);
    });
  }

  /* Lancer quand le DOM est prêt ET après chaque re-render Livewire */
  document.addEventListener('DOMContentLoaded', initOtp);
  document.addEventListener('livewire:navigated', initOtp);
  Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
    succeed(({ snapshot, effect }) => {
      requestAnimationFrame(() => {
        if (document.getElementById('otp-grid')) initOtp();
      });
    });
  });
})();
</script>
@endpush