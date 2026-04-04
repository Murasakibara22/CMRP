<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\User;
use Carbon\Carbon;

new #[Layout('auth.layouts.app')] class extends Component
{
    // ─── État des étapes ──────────────────────────────────────
    public int $step = 1;

    // ─── Step 1 ───────────────────────────────────────────────
    public string $email    = '';
    public string $password = '';
    public bool   $remember = false;

    // ─── Step 2 ───────────────────────────────────────────────
    public string $otp         = '';
    public string $maskedEmail = '';

    // ─── Progress bar ─────────────────────────────────────────
    public function progressWidth(): int
    {
        return match ($this->step) {
            1 => 33,
            2 => 66,
            3 => 100,
        };
    }

    // ─── Step 1 : Vérification identifiants → envoi OTP ──────
    public function submitStep1(): void
    {
        $this->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        $key = 'login.' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('email', "Trop de tentatives. Réessayez dans {$seconds}s.");
            return;
        }

        if (! Auth::validate(['email' => $this->email, 'password' => $this->password])) {
            RateLimiter::hit($key, 60);
            $this->addError('email', 'Email ou mot de passe incorrect.');
            $this->password = '';
            return;
        }

        $user = User::where('email', $this->email)->first();

        if ($user->status !== 'actif') {
            $this->addError('email', 'Votre compte est désactivé. Contactez un administrateur.');
            return;
        }

        RateLimiter::clear($key);

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'otp'             => $otp,
            'otp_expired_at'  => Carbon::now()->addMinutes(5),
            'otp_verified_at' => null,
        ]);

        logger("🔐 OTP pour {$user->email} : {$otp}");

        [$local, $domain]  = explode('@', $this->email);
        $this->maskedEmail = substr($local, 0, 1) . str_repeat('*', max(strlen($local) - 1, 3)) . '@' . $domain;

        $this->step = 2;

        // Signaler au JS que le DOM OTP va être rendu
        $this->dispatch('otp-step-ready');
    }

    // ─── Step 2 : Vérification OTP ───────────────────────────
    public function submitStep2(): void
    {
        $this->validate([
            'otp' => 'required|digits:6',
        ]);

        $user = User::where('email', $this->email)->first();

        if (! $user) {
            $this->addError('otp', 'Session expirée. Recommencez.');
            $this->step = 1;
            return;
        }

        if (! $user->otp_expired_at || Carbon::now()->gt($user->otp_expired_at)) {
            $this->addError('otp', 'Le code OTP a expiré. Renvoyez-en un nouveau.');
            return;
        }

        if ($user->otp !== $this->otp) {
            $this->addError('otp', 'Code incorrect. Vérifiez votre email.');
            $this->otp = '';
            $this->dispatch('otp-clear');
            return;
        }

        $user->update([
            'otp'             => null,
            'otp_verified_at' => Carbon::now(),
        ]);

        Auth::login($user, $this->remember);

        $this->step = 3;
        $this->dispatch('login-success');
    }

    // ─── Renvoyer un OTP ─────────────────────────────────────
    public function resendOtp(): void
    {
        $user = User::where('email', $this->email)->first();

        if (! $user) {
            $this->step = 1;
            return;
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'otp'             => $otp,
            'otp_expired_at'  => Carbon::now()->addMinutes(5),
            'otp_verified_at' => null,
        ]);

        logger("🔐 OTP renvoyé pour {$user->email} : {$otp}");

        $this->otp = '';
        $this->dispatch('otp-resent');
    }

    // ─── Retour step 1 ───────────────────────────────────────
    public function goBack(): void
    {
        $this->resetErrorBag();
        $this->otp      = '';
        $this->password = '';
        $this->step     = 1;
    }
};
?>

<div>
<div class="auth-page">

    {{-- ===== LEFT PANEL ===== --}}
    <div class="auth-left">
        <div class="geometric-pattern"></div>
        <div class="mosque-logo">
            <div class="logo-icon"><i class="ri-building-3-line"></i></div>
            <h1>المسجد</h1>
            <span class="subtitle">Gestion de la Mosquée</span>
        </div>
        <div class="stat-cards">
            <div class="stat-card">
                <div class="stat-icon green"><i class="ri-group-line"></i></div>
                <div class="stat-info">
                    <div class="stat-label">Fidèles inscrits</div>
                    <div class="stat-value" id="counter1">0</div>
                </div>
                <span class="stat-badge">+12 ce mois</span>
            </div>
            <div class="stat-card">
                <div class="stat-icon gold"><i class="ri-money-cny-circle-line"></i></div>
                <div class="stat-info">
                    <div class="stat-label">Collecte du mois</div>
                    <div class="stat-value" id="counter2">0 FCFA</div>
                </div>
                <span class="stat-badge">↑ 8%</span>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="ri-checkbox-circle-line"></i></div>
                <div class="stat-info">
                    <div class="stat-label">À jour ce mois</div>
                    <div class="stat-value" id="counter3">0%</div>
                </div>
                <span class="stat-badge">Excellent</span>
            </div>
        </div>
        <div class="islamic-quote">
            <div class="divider"></div>
            <p class="arabic-text">وَأَقِيمُوا الصَّلَاةَ وَآتُوا الزَّكَاةَ</p>
            <div class="divider"></div>
            <p class="quote-translation">Établissez la prière et acquittez la zakât — Coran 2:43</p>
        </div>
    </div>

    {{-- ===== RIGHT PANEL ===== --}}
    <div class="auth-right">
        <div class="auth-form-wrapper">

            {{-- Header dynamique --}}
            <div class="form-header">
                <span class="welcome-badge">
                    <i class="ri-shield-check-line"></i>
                    Espace Administrateur
                </span>
                <h2>
                    @if($step === 1) Bonne connexion
                    @elseif($step === 2) Vérification OTP
                    @else Accès accordé
                    @endif
                </h2>
                <p>
                    @if($step === 1) Accédez à votre tableau de bord de gestion de la mosquée.
                    @elseif($step === 2) Saisissez le code envoyé à votre adresse email.
                    @else Redirection en cours…
                    @endif
                </p>
            </div>

            {{-- Progress bar --}}
            <div class="progress-bar-wrapper">
                <div class="progress-bar-fill" style="width: {{ $this->progressWidth() }}%; transition: width 0.4s ease;"></div>
            </div>

            {{-- Steps indicator --}}
            <div class="step-indicator">
                <div class="step-wrapper">
                    <div class="step-dot {{ $step >= 1 ? 'active' : 'pending' }}">
                        @if($step > 1)<i class="ri-check-line" style="font-size:14px;"></i>@else 1 @endif
                    </div>
                    <div class="step-label {{ $step >= 1 ? 'active' : 'pending' }}">Identité</div>
                </div>
                <div class="step-line {{ $step >= 2 ? 'active' : '' }}"></div>
                <div class="step-wrapper">
                    <div class="step-dot {{ $step >= 2 ? 'active' : 'pending' }}">
                        @if($step > 2)<i class="ri-check-line" style="font-size:14px;"></i>@else 2 @endif
                    </div>
                    <div class="step-label {{ $step >= 2 ? 'active' : 'pending' }}">Code OTP</div>
                </div>
                <div class="step-line {{ $step >= 3 ? 'active' : '' }}"></div>
                <div class="step-wrapper">
                    <div class="step-dot {{ $step >= 3 ? 'active' : 'pending' }}">
                        <i class="ri-check-line" style="font-size:14px;"></i>
                    </div>
                    <div class="step-label {{ $step >= 3 ? 'active' : 'pending' }}">Accès</div>
                </div>
            </div>

            {{-- ===== STEP 1 ===== --}}
            @if($step === 1)
            <div class="form-step active">

                @if($errors->has('email') || $errors->has('password'))
                <div class="alert-error">
                    <i class="ri-error-warning-line alert-icon"></i>
                    <div>
                        <p class="alert-title">Erreur de connexion</p>
                        <p class="alert-message">{{ $errors->first('email') ?: $errors->first('password') }}</p>
                    </div>
                </div>
                @endif

                <div class="form-floating-custom">
                    <label for="emailInput">Adresse email</label>
                    <div class="input-group-custom {{ $errors->has('email') ? 'is-invalid' : '' }}">
                        <i class="ri-mail-line input-icon"></i>
                        <input
                            type="email"
                            class="form-control"
                            id="emailInput"
                            wire:model="email"
                            placeholder="admin@mosquee.ci"
                            autocomplete="email"
                            wire:keydown.enter="submitStep1"
                        >
                    </div>
                </div>

                <div class="form-floating-custom">
                    <label for="passwordInput">Mot de passe</label>
                    <div class="input-group-custom {{ $errors->has('password') ? 'is-invalid' : '' }}">
                        <i class="ri-lock-line input-icon"></i>
                        <input
                            type="password"
                            class="form-control"
                            id="passwordInput"
                            wire:model="password"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            wire:keydown.enter="submitStep1"
                        >
                        <button class="toggle-password" type="button" id="togglePwd">
                            <i class="ri-eye-line" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="form-check-mosque">
                        <input type="checkbox" id="rememberMe" wire:model="remember">
                        <label for="rememberMe">Se souvenir de moi</label>
                    </label>
                    <a href="#" class="forgot-link">Mot de passe oublié ?</a>
                </div>

                <button class="btn-mosque-primary" wire:click="submitStep1" wire:loading.attr="disabled">
                    <span class="spinner" wire:loading wire:target="submitStep1"></span>
                    <span class="btn-text">
                        <span wire:loading.remove wire:target="submitStep1">Continuer <i class="ri-arrow-right-line"></i></span>
                        <span wire:loading wire:target="submitStep1">Vérification…</span>
                    </span>
                </button>

            </div>
            @endif

            {{-- ===== STEP 2 : OTP ===== --}}
            @if($step === 2)
            <div class="form-step active" id="otpStep">

                <p class="email-hint">
                    Un code à 6 chiffres a été envoyé à<br>
                    <strong>{{ $maskedEmail }}</strong>
                </p>

                @if($errors->has('otp'))
                <div class="alert-error">
                    <i class="ri-error-warning-line alert-icon"></i>
                    <div>
                        <p class="alert-title">Code invalide</p>
                        <p class="alert-message">{{ $errors->first('otp') }}</p>
                    </div>
                </div>
                @endif

                {{--
                    6 inputs visuels — gérés intégralement par JS vanilla.
                    Un input caché (wire:model.live) reçoit l'OTP assemblé.
                    wire:model.live = Livewire est notifié à chaque frappe.
                --}}
                <div class="otp-container" id="otpContainer">
                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" id="otp_1" autocomplete="one-time-code">
                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" id="otp_2" autocomplete="off">
                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" id="otp_3" autocomplete="off">
                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" id="otp_4" autocomplete="off">
                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" id="otp_5" autocomplete="off">
                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" id="otp_6" autocomplete="off">
                </div>

                {{-- Input caché portant wire:model vers Livewire --}}
                <input
                    type="text"
                    id="otpHiddenInput"
                    wire:model.live="otp"
                    tabindex="-1"
                    aria-hidden="true"
                    readonly
                    style="position:absolute;opacity:0;pointer-events:none;width:1px;height:1px;overflow:hidden;"
                >

                {{-- Timer --}}
                <div class="otp-timer">
                    <span class="timer-text">
                        Code valide pendant <span class="timer-count" id="timerDisplay">05:00</span>
                    </span>
                    <br>
                    <button class="resend-btn" id="resendBtn" wire:click="resendOtp" wire:loading.attr="disabled" disabled>
                        <span wire:loading wire:target="resendOtp">Envoi…</span>
                        <span wire:loading.remove wire:target="resendOtp">Renvoyer le code</span>
                    </button>
                </div>

                <button class="btn-mosque-primary" wire:click="submitStep2" wire:loading.attr="disabled">
                    <span class="spinner" wire:loading wire:target="submitStep2"></span>
                    <span class="btn-text">
                        <span wire:loading.remove wire:target="submitStep2">Vérifier le code <i class="ri-shield-check-line"></i></span>
                        <span wire:loading wire:target="submitStep2">Vérification…</span>
                    </span>
                </button>

                <button class="btn-back" wire:click="goBack" wire:loading.attr="disabled">
                    <i class="ri-arrow-left-line"></i> Retour
                </button>

            </div>
            @endif

            {{-- ===== STEP 3 : Succès ===== --}}
            @if($step === 3)
            <div class="form-step active" id="successStep">
                <div class="otp-success show">
                    <div class="success-circle">
                        <i class="ri-check-double-line"></i>
                    </div>
                    <h5 style="color:#212529;font-weight:700;margin-bottom:8px;">Connexion réussie !</h5>
                    <p style="color:var(--mosque-muted);font-size:14px;line-height:1.6;">
                        Bienvenue. Redirection dans <strong id="redirectCount">3</strong>s…
                    </p>
                    <div class="progress-bar-wrapper" style="margin-top:20px;">
                        <div class="progress-bar-fill" id="redirectBar" style="width:0%;transition:width 0.1s linear;"></div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Footer --}}
            <div class="form-footer">
                <p>
                    <i class="ri-lock-2-line"></i> Connexion sécurisée SSL 256-bit<br>
                    <span style="font-size:11px;">© 2025 Mosquée – Tous droits réservés</span>
                </p>
            </div>

        </div>
    </div>
</div>

@script
<script>
// ─────────────────────────────────────────────────────────────
// JS VANILLA — UX uniquement, zéro Alpine, zéro logique métier
// ─────────────────────────────────────────────────────────────

let otpTimerInterval = null;

// ── Toggle password ───────────────────────────────────────
document.addEventListener('livewire:initialized', () => {
    bindTogglePassword();
});

Livewire.hook('morph.updated', () => {
    bindTogglePassword();
});

function bindTogglePassword() {
    const btn = document.getElementById('togglePwd');
    if (!btn || btn._bound) return;
    btn._bound = true;
    btn.addEventListener('click', () => {
        const input = document.getElementById('passwordInput');
        const icon  = document.getElementById('eyeIcon');
        if (!input) return;
        const hidden = input.type === 'password';
        input.type = hidden ? 'text' : 'password';
        icon.classList.toggle('ri-eye-line', !hidden);
        icon.classList.toggle('ri-eye-off-line', hidden);
    });
}

// ── Initialisation OTP après rendu du step 2 ─────────────
$wire.on('otp-step-ready', () => {
    // Attendre que Livewire ait fini de patcher le DOM
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            initOtpInputs();
        });
    });
});

function initOtpInputs() {
    const inputs = [1,2,3,4,5,6].map(i => document.getElementById(`otp_${i}`));
    const hidden = document.getElementById('otpHiddenInput');

    if (!inputs[0] || !hidden) return;

    // Focus automatique sur le premier champ
    inputs[0].focus();

    function getFullOtp() {
        return inputs.map(el => el.value).join('');
    }

    // Synchroniser vers wire:model via l'input caché
    // On utilise le setter natif pour bypasser le DOM virtuel de Livewire
    function syncWire() {
        const val    = getFullOtp();
        const setter = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'value').set;
        setter.call(hidden, val);
        hidden.dispatchEvent(new Event('input', { bubbles: true }));
    }

    inputs.forEach((input, idx) => {

        // Supprimer les anciens listeners pour éviter les doublons
        const clone = input.cloneNode(true);
        input.parentNode.replaceChild(clone, input);
        inputs[idx] = clone;
    });

    // Re-fetch après cloneNode
    const freshInputs = [1,2,3,4,5,6].map(i => document.getElementById(`otp_${i}`));

    freshInputs.forEach((input, idx) => {

        input.addEventListener('input', function () {
            // Forcer chiffre uniquement
            const digit = this.value.replace(/[^0-9]/g, '');
            this.value  = digit ? digit.slice(-1) : '';

            syncWire();

            // Passer à la case suivante
            if (this.value && idx < 5) {
                freshInputs[idx + 1].focus();
            }

            // Auto-submit si 6 chiffres
            const full = getFullOtp.call({ inputs: freshInputs, map: Array.prototype.map });
            const otp  = freshInputs.map(el => el.value).join('');
            if (otp.length === 6) {
                setTimeout(() => $wire.submitStep2(), 300);
            }
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace') {
                if (this.value) {
                    this.value = '';
                    syncWire();
                } else if (idx > 0) {
                    freshInputs[idx - 1].focus();
                    freshInputs[idx - 1].value = '';
                    syncWire();
                }
                e.preventDefault();
            }
            if (e.key === 'ArrowLeft'  && idx > 0) freshInputs[idx - 1].focus();
            if (e.key === 'ArrowRight' && idx < 5) freshInputs[idx + 1].focus();
        });

        input.addEventListener('paste', function (e) {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData)
                .getData('text')
                .replace(/[^0-9]/g, '')
                .slice(0, 6);

            freshInputs.forEach((el, i) => {
                el.value = pasted[i] || '';
            });

            syncWire();

            const lastIdx = Math.min(pasted.length - 1, 5);
            if (lastIdx >= 0) freshInputs[lastIdx].focus();

            if (pasted.length === 6) {
                setTimeout(() => $wire.submitStep2(), 300);
            }
        });

        input.addEventListener('focus', function () {
            this.select();
        });
    });

    // ── Timer ─────────────────────────────────────────────
    startOtpTimer();

    // Réinitialiser sur renvoi OTP
    $wire.on('otp-resent', () => {
        freshInputs.forEach(el => { el.value = ''; });
        syncWire();
        freshInputs[0].focus();
        startOtpTimer();
    });

    // Vider les cases sur erreur OTP
    $wire.on('otp-clear', () => {
        freshInputs.forEach(el => { el.value = ''; });
        syncWire();
        freshInputs[0].focus();
    });

    function syncWire() {
        const val    = freshInputs.map(el => el.value).join('');
        const hidden = document.getElementById('otpHiddenInput');
        const setter = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'value').set;
        setter.call(hidden, val);
        hidden.dispatchEvent(new Event('input', { bubbles: true }));
    }
}

// ── Timer OTP ─────────────────────────────────────────────
function startOtpTimer() {
    clearInterval(otpTimerInterval);

    const display   = document.getElementById('timerDisplay');
    const resendBtn = document.getElementById('resendBtn');

    if (!display) return;

    let timeLeft = 300;
    if (resendBtn) resendBtn.disabled = true;

    otpTimerInterval = setInterval(() => {
        timeLeft--;
        const m = String(Math.floor(timeLeft / 60)).padStart(2, '0');
        const s = String(timeLeft % 60).padStart(2, '0');
        display.textContent = `${m}:${s}`;

        if (timeLeft <= 0) {
            clearInterval(otpTimerInterval);
            display.textContent = 'Expiré';
            if (resendBtn) resendBtn.disabled = false;
        }
    }, 1000);
}

// ── Redirection step 3 ────────────────────────────────────
$wire.on('login-success', () => {
    const countEl = document.getElementById('redirectCount');
    const barEl   = document.getElementById('redirectBar');
    if (!countEl || !barEl) return;

    let width = 0;
    const tick = 100 / 30;

    const interval = setInterval(() => {
        width += tick;
        barEl.style.width = Math.min(width, 100) + '%';

        const remaining = Math.ceil(3 * (1 - width / 100));
        countEl.textContent = Math.max(remaining, 0);

        if (width >= 100) {
            clearInterval(interval);
            window.location.href = '{{ route('admin.home') }}';
        }
    }, 100);
});
</script>
@endscript

</div>