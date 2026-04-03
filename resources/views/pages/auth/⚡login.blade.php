<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;

new #[Layout('auth.layouts.app')] class extends Component
{
    // ─── État des étapes ──────────────────────────────────────
    public int $step = 1;

    // ─── Step 1 ───────────────────────────────────────────────
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|min:6')]
    public string $password = '';

    public bool $remember = false;

    // ─── Step 2 ───────────────────────────────────────────────
    public string $otp = '';
    public string $maskedEmail = '';

    // ─── Computed helpers ─────────────────────────────────────
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

        // Rate limiting : max 5 tentatives / minute par IP
        $key = 'login.' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('email', "Trop de tentatives. Réessayez dans {$seconds}s.");
            return;
        }

        // Vérification des credentials sans connecter l'utilisateur
        if (! Auth::validate(['email' => $this->email, 'password' => $this->password])) {
            RateLimiter::hit($key, 60);
            $this->addError('email', 'Email ou mot de passe incorrect.');
            $this->password = '';
            return;
        }

        $user = User::where('email', $this->email)->first();

        // Vérifier que le compte est actif
        if ($user->status !== 'actif') {
            $this->addError('email', 'Votre compte est désactivé. Contactez un administrateur.');
            return;
        }

        RateLimiter::clear($key);

        // Générer et stocker l'OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'otp'            => $otp,
            'otp_expired_at' => Carbon::now()->addMinutes(5),
            'otp_verified_at'=> null,
        ]);

        // TODO: Envoyer l'OTP par email → Mail::to($user->email)->send(new OtpMail($otp));
        // En développement : on le logue
        logger("🔐 OTP pour {$user->email} : {$otp}");

        // Masquer l'email pour l'affichage
        [$local, $domain]    = explode('@', $this->email);
        $this->maskedEmail   = substr($local, 0, 1) . str_repeat('*', max(strlen($local) - 1, 3)) . '@' . $domain;

        $this->step = 2;
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

        // Vérifier expiration
        if (! $user->otp_expired_at || Carbon::now()->gt($user->otp_expired_at)) {
            $this->addError('otp', 'Le code OTP a expiré. Renvoyez-en un nouveau.');
            return;
        }

        // Vérifier le code
        if ($user->otp !== $this->otp) {
            $this->addError('otp', 'Code incorrect. Vérifiez votre email.');
            $this->otp = '';
            return;
        }

        // Marquer OTP comme vérifié
        $user->update([
            'otp'             => null,
            'otp_verified_at' => Carbon::now(),
        ]);

        // Connecter l'utilisateur
        Auth::login($user, $this->remember);

        $this->step = 3;

        // Déclencher l'event JS pour le countdown de redirection
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
        $this->dispatch('otp-resent'); // redémarre le timer JS
    }

    // ─── Retour à l'étape précédente ─────────────────────────
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
            <div class="logo-icon">
                <i class="ri-building-3-line"></i>
            </div>
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

            {{-- Header dynamique selon l'étape --}}
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
                        @if($step > 1) <i class="ri-check-line" style="font-size:14px;"></i> @else 1 @endif
                    </div>
                    <div class="step-label {{ $step >= 1 ? 'active' : 'pending' }}">Identité</div>
                </div>
                <div class="step-line {{ $step >= 2 ? 'active' : '' }}"></div>
                <div class="step-wrapper">
                    <div class="step-dot {{ $step >= 2 ? 'active' : 'pending' }}">
                        @if($step > 2) <i class="ri-check-line" style="font-size:14px;"></i> @else 2 @endif
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

            {{-- ===== STEP 1 : Email + Password ===== --}}
            @if($step === 1)
            <div class="form-step active">

                @if($errors->any())
                <div class="alert-error">
                    <i class="ri-error-warning-line alert-icon"></i>
                    <div>
                        <p class="alert-title">Identifiants incorrects</p>
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
                            x-ref="passwordInput"
                        >
                        <button class="toggle-password" type="button"
                            onclick="
                                const i = this.querySelector('i');
                                const p = this.closest('.input-group-custom').querySelector('input');
                                p.type = p.type === 'password' ? 'text' : 'password';
                                i.classList.toggle('ri-eye-line');
                                i.classList.toggle('ri-eye-off-line');
                            ">
                            <i class="ri-eye-line"></i>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="form-check-mosque">
                        <input type="checkbox" wire:model="remember" id="rememberMe">
                        <label for="rememberMe">Se souvenir de moi</label>
                    </label>
                    <a href="#" class="forgot-link">Mot de passe oublié ?</a>
                </div>

                <button class="btn-mosque-primary" wire:click="submitStep1" wire:loading.attr="disabled">
                    <span class="spinner" wire:loading wire:target="submitStep1"></span>
                    <span wire:loading.remove wire:target="submitStep1">
                        Continuer <i class="ri-arrow-right-line"></i>
                    </span>
                    <span wire:loading wire:target="submitStep1">Vérification…</span>
                </button>

            </div>
            @endif

            {{-- ===== STEP 2 : OTP ===== --}}
            @if($step === 2)
            <div class="form-step active" x-data="otpManager()" x-init="startTimer()">

                <p class="email-hint">
                    Un code à 6 chiffres a été envoyé à<br>
                    <strong>{{ $maskedEmail }}</strong>
                </p>

                @if($errors->has('otp'))
                <div class="alert-error">
                    <i class="ri-error-warning-line"></i>
                    {{ $errors->first('otp') }}
                </div>
                @endif

                {{-- 6 inputs OTP avec gestion auto-focus en JS (inévitable) --}}
                <div class="otp-container" x-ref="otpContainer">
                    @for($i = 1; $i <= 6; $i++)
                    <input
                        type="text"
                        class="otp-input"
                        maxlength="1"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        x-on:input="handleInput($event, {{ $i }})"
                        x-on:keydown="handleKeydown($event, {{ $i }})"
                        x-on:paste.prevent="handlePaste($event)"
                        id="otp{{ $i }}"
                    >
                    @endfor
                </div>

                {{-- Input hidden Livewire qui reçoit l'OTP complet --}}
                <input type="hidden" wire:model="otp" x-ref="otpHidden">

                {{-- Timer --}}
                <div class="otp-timer">
                    <span class="timer-text">
                        Code valide pendant
                        <span class="timer-count" x-text="timerDisplay"></span>
                    </span>
                    <br>
                    <button
                        class="resend-btn"
                        x-bind:disabled="!canResend"
                        x-bind:class="{ 'opacity-50': !canResend }"
                        wire:click="resendOtp"
                        x-on:otp-resent.window="resetTimer()"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading wire:target="resendOtp">Envoi…</span>
                        <span wire:loading.remove wire:target="resendOtp">Renvoyer le code</span>
                    </button>
                </div>

                <button
                    class="btn-mosque-primary"
                    wire:click="submitStep2"
                    wire:loading.attr="disabled"
                    x-bind:disabled="otp.length < 6"
                >
                    <span class="spinner" wire:loading wire:target="submitStep2"></span>
                    <span wire:loading.remove wire:target="submitStep2">
                        Vérifier le code <i class="ri-shield-check-line"></i>
                    </span>
                    <span wire:loading wire:target="submitStep2">Vérification…</span>
                </button>

                <button class="btn-back" wire:click="goBack" wire:loading.attr="disabled">
                    <i class="ri-arrow-left-line"></i> Retour
                </button>

            </div>
            @endif

            {{-- ===== STEP 3 : Succès ===== --}}
            @if($step === 3)
            <div class="form-step active"
                x-data="{ count: 3, width: 0 }"
                x-init="
                    let interval = setInterval(() => {
                        width += (100/30);
                        if(count > 0 && width % (100/3) < (100/30)) count--;
                        if(width >= 100) {
                            clearInterval(interval);
                            window.location.href = '{{ route('admin.home') }}';
                        }
                    }, 100);
                "
                x-on:login-success.window="$el.dispatchEvent(new Event('x-init'))"
            >
                <div class="otp-success show">
                    <div class="success-circle">
                        <i class="ri-check-double-line"></i>
                    </div>
                    <h5 style="color:#212529; font-weight:700; margin-bottom:8px;">Connexion réussie !</h5>
                    <p style="color:var(--mosque-muted); font-size:14px; line-height:1.6;">
                        Bienvenue. Redirection vers votre tableau de bord dans
                        <strong x-text="count"></strong>s…
                    </p>
                    <div class="progress-bar-wrapper" style="margin-top: 20px;">
                        <div class="progress-bar-fill" x-bind:style="`width: ${width}%; transition: width 0.1s linear;`"></div>
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

    {{-- ===== JS minimal (UX only) ===== --}}
    @script
    <script>
    /**
     * Alpine component pour la gestion OTP
     * Responsabilités : auto-focus, paste, timer, alimentation du wire:model
     */
    function otpManager() {
        return {
            otp: '',
            timeLeft: 300, // 5 minutes en secondes
            canResend: false,
            timerDisplay: '05:00',
            timerInterval: null,

            startTimer() {
                this.canResend = false;
                this.timeLeft  = 300;
                this.timerInterval = setInterval(() => {
                    this.timeLeft--;
                    const m = String(Math.floor(this.timeLeft / 60)).padStart(2, '0');
                    const s = String(this.timeLeft % 60).padStart(2, '0');
                    this.timerDisplay = `${m}:${s}`;
                    if (this.timeLeft <= 0) {
                        clearInterval(this.timerInterval);
                        this.canResend    = true;
                        this.timerDisplay = 'Expiré';
                    }
                }, 1000);
            },

            resetTimer() {
                clearInterval(this.timerInterval);
                // Vider les inputs
                for (let i = 1; i <= 6; i++) {
                    document.getElementById(`otp${i}`).value = '';
                }
                this.otp = '';
                this.$refs.otpHidden.value = '';
                this.$refs.otpHidden.dispatchEvent(new Event('input'));
                this.startTimer();
                document.getElementById('otp1').focus();
            },

            getOtpValue() {
                let val = '';
                for (let i = 1; i <= 6; i++) {
                    val += document.getElementById(`otp${i}`).value;
                }
                return val;
            },

            syncToWire() {
                this.otp = this.getOtpValue();
                // Mettre à jour le wire:model via l'input hidden
                const hidden = this.$refs.otpHidden;
                hidden.value = this.otp;
                hidden.dispatchEvent(new Event('input'));
            },

            handleInput(event, index) {
                const val = event.target.value.replace(/[^0-9]/g, '');
                event.target.value = val;
                this.syncToWire();
                if (val && index < 6) {
                    document.getElementById(`otp${index + 1}`).focus();
                }
                // Auto-submit si les 6 chiffres sont saisis
                if (this.getOtpValue().length === 6) {
                    setTimeout(() => @this.submitStep2(), 200);
                }
            },

            handleKeydown(event, index) {
                if (event.key === 'Backspace' && !event.target.value && index > 1) {
                    document.getElementById(`otp${index - 1}`).focus();
                }
            },

            handlePaste(event) {
                const pasted = (event.clipboardData || window.clipboardData)
                    .getData('text')
                    .replace(/[^0-9]/g, '')
                    .slice(0, 6);
                for (let i = 0; i < pasted.length; i++) {
                    const input = document.getElementById(`otp${i + 1}`);
                    if (input) input.value = pasted[i];
                }
                this.syncToWire();
                const last = Math.min(pasted.length, 6);
                if (last > 0) document.getElementById(`otp${last}`).focus();
                if (pasted.length === 6) {
                    setTimeout(() => @this.submitStep2(), 200);
                }
            },
        }
    }
    </script>
    @endscript

</div>