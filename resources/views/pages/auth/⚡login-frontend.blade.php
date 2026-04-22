<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Customer;
use App\Models\Cotisation;
use App\Models\TypeCotisation;
use App\Models\CoutEngagement;
use App\Models\OtpVerification;
use App\Models\HistoriqueCotisation;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Services\SmsService;
use Carbon\Carbon;

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

    /*
     * Cotisation mensuelle optionnelle à l'inscription.
     * Le fidèle peut choisir un type mensuel is_required
     * et son montant d'engagement. Si sélectionné, une
     * première cotisation est créée pour le mois en cours.
     */
    public ?int $typeCotisationMensuelId = null;
    public ?int $montantEngagement       = null;
    public string $errorEngagement       = '';

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
        // Session::put('otp_debug', $otpRecord->code);

        try {
            $phonesender = $this->dialCode . trim($this->phone);
            SmsService::send($phonesender, 'votre code OTP est : '.$otpRecord->code.' , utilisez-le pour vous connecter , il expirera dans 5 minutes !!');
        } catch (\Exception $e) {
            // Log::error("Erreur envoi SMS OTP à $fullPhone : " . $e->getMessage());
            // $this->errorPhone = 'Impossible d\'envoyer le code. Veuillez réessayer plus tard.';
            // return;
        }


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
        $ok        = OtpVerification::verify($fullPhone, $code);

        if (! $ok) {
            $this->errorOtp = 'Code incorrect ou expiré. Vérifiez et réessayez.';
            return;
        }

        $customer = Customer::where('phone', preg_replace('/\s+/', '', $this->phone))
            ->where('dial_code', $this->dialCode)
            ->first();

        if ($customer) {
            Auth::guard('customer')->login($customer);
            $this->redirect(route('customer.home'));
            return;
        }

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

          try {
            $phonesender = $this->dialCode . trim($this->phone);
            SmsService::send($phonesender, 'votre code OTP est : '.$otpRecord->code.' , utilisez-le pour vous connecter , il expirera dans 5 minutes !!');
        } catch (\Exception $e) {
            // Log::error("Erreur envoi SMS OTP à $fullPhone : " . $e->getMessage());
            // $this->errorPhone = 'Impossible d\'envoyer le code. Veuillez réessayer plus tard.';
            // return;
        }

        $this->dispatch('otp-resent');
    }

    /* ── Sélection type cotisation mensuel ─────────────────── */
    public function selectTypeMensuel(?int $id): void
    {
        $this->typeCotisationMensuelId = ($this->typeCotisationMensuelId === $id) ? null : $id;
        $this->montantEngagement       = null;
        $this->errorEngagement         = '';
    }

    /* ── Sélection montant engagement ──────────────────────── */
    public function selectEngagement(?int $montant): void
    {
        $this->montantEngagement = ($this->montantEngagement === $montant) ? null : $montant;
        $this->errorEngagement   = '';
    }

    /* ── Step 3 : Créer le compte ─────────────────────────── */
    public function createAccount(): void
    {
        $this->errorReg        = '';
        $this->errorEngagement = '';

        $this->nom    = trim($this->nom);
        $this->prenom = trim($this->prenom);

        if (! $this->nom || ! $this->prenom) {
            $this->errorReg = 'Le nom et le prénom sont obligatoires.';
            return;
        }

        /* Valider le montant d'engagement si un type a été sélectionné */
        $tc = $this->typeCotisationMensuelId
            ? TypeCotisation::find($this->typeCotisationMensuelId)
            : null;

        if ($tc) {
            if (! $this->montantEngagement || $this->montantEngagement < 1) {
                $this->errorEngagement = 'Veuillez renseigner votre montant d\'engagement mensuel.';
                return;
            }
            if ($tc->montant_minimum && $this->montantEngagement < $tc->montant_minimum) {
                $this->errorEngagement =
                    "Le montant minimum pour « {$tc->libelle} » est " .
                    number_format($tc->montant_minimum, 0, ',', ' ') . " FCFA/mois.";
                return;
            }
        }

        /* Créer le customer */
        $customer = Customer::create([
            'nom'                        => strtoupper($this->nom),
            'prenom'                     => ucwords(strtolower($this->prenom)),
            'dial_code'                  => $this->dialCode,
            'phone'                      => preg_replace('/\s+/', '', $this->phone),
            'adresse'                    => trim($this->adresse) ?: null,
            'status'                     => 'actif',
            'date_adhesion'              => now()->toDateString(),
            'montant_engagement'         => $tc ? $this->montantEngagement : null,
            'type_cotisation_mensuel_id' => $tc?->id,
        ]);

        /*
         * Si un type mensuel a été sélectionné → créer la première
         * cotisation pour le mois en cours (statut en_retard,
         * pas de paiement ni de transaction — l'argent n'a pas encore
         * été remis).
         */
        if ($tc && $this->montantEngagement) {
            $cot = Cotisation::create([
                'customer_id'        => $customer->id,
                'type_cotisation_id' => $tc->id,
                'mois'               => now()->month,
                'annee'              => now()->year,
                'montant_du'         => $this->montantEngagement,
                'montant_paye'       => 0,
                'montant_restant'    => $this->montantEngagement,
                'statut'             => 'en_retard',
                'mode_paiement'      => null,
                'reference'          => null,
                'validated_by'       => null,
                'validated_at'       => null,
            ]);

            HistoriqueCotisation::log($cot, 'creation', $this->montantEngagement,
                'Première cotisation à l\'inscription');
        }

        Session::put('customer_id', $customer->id);
        Auth::guard('customer')->login($customer);

        $this->redirect(route('customer.home'));
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

    /* ── Données vue ────────────────────────────────────── */
    public function with(): array
    {
        $typesMensuels   = TypeCotisation::where('type', 'mensuel')
            ->where('is_required', true)
            ->where('status', 'actif')
            ->orderBy('libelle')
            ->get();

        $coutEngagements = CoutEngagement::actif()->orderBy('montant')->get();

        return compact('typesMensuels', 'coutEngagements');
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


  {{-- ══ ÉTAPE 1 — NUMÉRO DE TÉLÉPHONE ══════════════════════ --}}
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


  {{-- ══ ÉTAPE 2 — OTP ════════════════════════════════════════ --}}
  @if($step === 'otp')

  <div class="auth-bar">
    <button class="auth-bar-back" wire:click="goBack">
      <i class="ri-arrow-left-line"></i>
    </button>
    <div class="auth-bar-title">Vérification</div>
    <div class="auth-bar-ph"></div>
  </div>

  <div class="auth-content">
    <div class="view-header fu fu-1">
      <div class="view-title">Code reçu ?</div>
      <div class="view-sub">Entrez le code à 6 chiffres envoyé au {{ $dialCode }} {{ $phone }}.</div>
    </div>

    <div class="auth-card fu fu-2">
      <div class="f-label">OTP (6 chiffres)</div>

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


  {{-- ══ ÉTAPE 3 — INSCRIPTION ════════════════════════════════ --}}
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

      {{-- Identité --}}
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

      {{-- ── Cotisation mensuelle optionnelle ──────────────── --}}
      <div style="margin-top:24px;padding-top:20px;border-top:1px dashed rgba(64,81,137,.2)">
        <div style="font-size:13px;font-weight:800;color:#405189;margin-bottom:4px;display:flex;align-items:center;gap:6px">
          <i class="ri-calendar-check-line"></i> Cotisation mensuelle
          <span style="font-size:10px;font-weight:500;color:#878a99;margin-left:2px">(optionnel)</span>
        </div>
        <div style="font-size:12px;color:#878a99;margin-bottom:14px;line-height:1.5">
          Choisissez votre type de cotisation mensuel. Vous pourrez le modifier ultérieurement depuis votre profil.
        </div>

        {{-- Sélection type --}}
        <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:16px">
          @foreach($typesMensuels as $tm)
          @php $selected = $typeCotisationMensuelId === $tm->id; @endphp
          <div wire:click="selectTypeMensuel({{ $tm->id }})"
               style="
                 display:flex;align-items:center;justify-content:space-between;
                 border:1.5px solid {{ $selected ? '#405189' : 'rgba(64,81,137,.15)' }};
                 background:{{ $selected ? 'rgba(64,81,137,.06)' : '#fff' }};
                 border-radius:12px;padding:12px 14px;cursor:pointer;transition:all .2s;
               ">
            <div style="display:flex;align-items:center;gap:10px">
              <div style="width:34px;height:34px;border-radius:8px;background:{{ $selected ? 'rgba(64,81,137,.15)' : 'rgba(135,138,153,.08)' }};color:{{ $selected ? '#405189' : '#878a99' }};display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0">
                <i class="ri-calendar-check-line"></i>
              </div>
              <div>
                <div style="font-size:13px;font-weight:700;color:{{ $selected ? '#405189' : '#212529' }}">
                  {{ $tm->libelle }}
                </div>
                @if($tm->montant_minimum)
                <div style="font-size:11px;color:#878a99;margin-top:2px">
                  Minimum {{ number_format($tm->montant_minimum, 0, ',', ' ') }} FCFA/mois
                </div>
                @endif
              </div>
            </div>
            <div style="width:20px;height:20px;border-radius:50%;border:2px solid {{ $selected ? '#405189' : '#e9ebec' }};background:{{ $selected ? '#405189' : 'transparent' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
              @if($selected)<i class="ri-check-line" style="color:#fff;font-size:11px"></i>@endif
            </div>
          </div>
          @endforeach
        </div>

        {{-- Montant d'engagement (affiché si un type est sélectionné) --}}
        @if($typeCotisationMensuelId)
        <div style="animation:fadeIn .2s ease">
          <div style="font-size:12px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px">
            Montant d'engagement mensuel <span style="color:#f06548">*</span>
          </div>

          {{-- Montants prédéfinis --}}
          @if($coutEngagements->count())
          <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px">
            @foreach($coutEngagements as $cout)
            <div wire:click="selectEngagement({{ $cout->montant }})"
                 style="
                   padding:8px 14px;border-radius:20px;cursor:pointer;
                   border:1.5px solid {{ $montantEngagement === $cout->montant ? '#405189' : '#e9ebec' }};
                   background:{{ $montantEngagement === $cout->montant ? 'rgba(64,81,137,.08)' : '#fff' }};
                   color:{{ $montantEngagement === $cout->montant ? '#405189' : '#495057' }};
                   font-size:12px;font-weight:700;transition:all .15s;
                 ">
              {{ number_format($cout->montant, 0, ',', ' ') }} FCFA
            </div>
            @endforeach
          </div>
          @endif

          {{-- Montant personnalisé --}}
          <div style="position:relative">
            <i class="ri-money-cny-circle-line" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#878a99;font-size:15px;pointer-events:none"></i>
            <input type="number"
                   wire:model.live="montantEngagement"
                   min="1"
                   placeholder="Ou saisir un montant personnalisé…"
                   inputmode="numeric"
                   style="
                     border:1.5px solid {{ $errorEngagement ? '#f06548' : '#e9ebec' }};
                     border-radius:10px;height:44px;padding:0 14px 0 38px;
                     font-size:13px;width:100%;background:#fff;color:#212529;
                   "/>
          </div>
          @if($errorEngagement)
          <div style="font-size:12px;color:#f06548;margin-top:5px;font-weight:600">
            <i class="ri-error-warning-line me-1"></i>{{ $errorEngagement }}
          </div>
          @endif
          <div style="font-size:11px;color:#878a99;margin-top:6px;line-height:1.5">
            Une première cotisation sera créée pour le mois en cours avec le statut <em>En retard</em> jusqu'à votre premier paiement.
          </div>
        </div>
        @endif

      </div>

      <button class="auth-btn" wire:click="createAccount" wire:loading.attr="disabled" style="margin-top:24px">
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
(function () {
  function initOtp() {
    const grid   = document.getElementById('otp-grid');
    const hidden = document.getElementById('otp-hidden');
    if (!grid || !hidden) return;

    const boxes = [...grid.querySelectorAll('.otp-box')];

    function syncHidden() {
      const code = boxes.map(b => b.value).join('');
      hidden.value = code;
      hidden.dispatchEvent(new Event('input', { bubbles: true }));
    }

    boxes.forEach((box, i) => {
      box.addEventListener('input', function () {
        const v = this.value.replace(/\D/g, '');
        this.value = v;
        this.classList.toggle('filled', !!v);
        this.classList.remove('err');
        syncHidden();
        if (v && i < boxes.length - 1) boxes[i + 1].focus();
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

    boxes[0]?.focus();

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

    Livewire.on('otp-resent', () => {
      boxes.forEach(b => { b.value = ''; b.classList.remove('filled', 'err'); });
      const wrap = document.getElementById('otp-timer-wrap');
      if (wrap) wrap.innerHTML = 'Renvoyer dans <strong id="timer-val">01:00</strong>';
      syncHidden();
      startTimer();
      boxes[0]?.focus();
    });

    Livewire.on('otp-error', () => {
      boxes.forEach(b => b.classList.add('err'));
      setTimeout(() => boxes.forEach(b => b.classList.remove('err')), 600);
    });
  }

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
