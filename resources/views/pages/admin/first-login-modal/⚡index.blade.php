<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

new class extends Component
{
    /* ── Visibilité ── */
    public bool   $showModal    = false;

    /* ── Étapes : form | otp_sent ── */
    public string $step         = 'form';
    public string $nouveauMdp   = '';
    public string $confirmMdp   = '';
    public string $otpSaisi     = '';

    public string $errorNouveauMdp = '';
    public string $errorConfirmMdp = '';
    public string $errorOtp        = '';

    public function mount(): void
    {
        $this->_evaluerAffichage();
    }

    /* ── Évaluer si le modal doit s'afficher ── */
    private function _evaluerAffichage(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->is_first_connexion) {
            $this->showModal = false;
            return;
        }

        $dismissed = $user->first_connexion_modal_dismissed_at;

        /* Afficher si jamais dismissed OU si dismissed il y a plus de 10 min */
        $this->showModal = is_null($dismissed)
            || Carbon::parse($dismissed)->addMinutes(10)->isPast();
    }

    /* ── L'admin ferme sans changer ── */
    public function dismissModal(): void
    {
        auth()->user()->update([
            'first_connexion_modal_dismissed_at' => now(),
        ]);
        $this->showModal = false;
        $this->_resetForm();
    }

    /* ── ÉTAPE 1 : Valider nouveau mdp + envoyer OTP ── */
    public function envoyerOtp(): void
    {
        $this->errorNouveauMdp = '';
        $this->errorConfirmMdp = '';

        if (strlen($this->nouveauMdp) < 8) {
            $this->errorNouveauMdp = 'Minimum 8 caractères.';
            return;
        }

        if ($this->nouveauMdp !== $this->confirmMdp) {
            $this->errorConfirmMdp = 'Les mots de passe ne correspondent pas.';
            return;
        }

        $user = auth()->user();

        /* Vérifier que le nouveau ≠ au mot de passe par défaut
           (optionnel : supprimer si pas de mdp par défaut défini) */
        if (Hash::check($this->nouveauMdp, $user->password)) {
            $this->errorNouveauMdp = 'Votre nouveau mot de passe doit être différent de l\'actuel.';
            return;
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp'             => $otp,
            'otp_verified_at' => null,
            'otp_expired_at'  => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(
            new OtpMail($otp, $user->prenom . ' ' . $user->nom)
        );

        $this->step     = 'otp_sent';
        $this->otpSaisi = '';
        $this->errorOtp = '';
    }

    /* ── Renvoyer l'OTP ── */
    public function renvoyerOtp(): void
    {
        $user = auth()->user();
        $otp  = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp'             => $otp,
            'otp_verified_at' => null,
            'otp_expired_at'  => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(
            new OtpMail($otp, $user->prenom . ' ' . $user->nom)
        );

        $this->otpSaisi = '';
        $this->errorOtp = 'Un nouveau code a été envoyé à ' . $user->email;
    }

    /* ── ÉTAPE 2 : Confirmer OTP + changer mdp ── */
    public function confirmerOtp(): void
    {
        $this->errorOtp = '';
        $user = auth()->user();

        $code = preg_replace('/\D/', '', $this->otpSaisi);

        if (strlen($code) < 6) {
            $this->errorOtp = 'Veuillez entrer le code complet à 6 chiffres.';
            return;
        }

        if ($user->otp !== $code) {
            $this->errorOtp = 'Code incorrect. Vérifiez votre email.';
            return;
        }

        if (! $user->isOtpValid()) {
            $this->errorOtp = 'Ce code a expiré. Cliquez sur "Renvoyer".';
            return;
        }

        $user->update([
            'password'                            => Hash::make($this->nouveauMdp),
            'is_first_connexion'                  => false,
            'first_connexion_modal_dismissed_at'  => null,
            'otp'                                 => null,
            'otp_verified_at'                     => now(),
            'otp_expired_at'                      => null,
        ]);

        $this->showModal = false;
        $this->_resetForm();

        /* Notifier les autres composants sur la page */
        $this->dispatch('passwordChanged');
    }

    public function retourForm(): void
    {
        $this->step     = 'form';
        $this->otpSaisi = '';
        $this->errorOtp = '';
        auth()->user()->clearOtp();
    }

    /* ── Écouter si le Profile Admin a déjà changé le mdp ── */
    #[On('passwordChanged')]
    public function onPasswordChanged(): void
    {
        $this->showModal = false;
        $this->_resetForm();
    }

    private function _resetForm(): void
    {
        $this->step            = 'form';
        $this->nouveauMdp      = '';
        $this->confirmMdp      = '';
        $this->otpSaisi        = '';
        $this->errorNouveauMdp = '';
        $this->errorConfirmMdp = '';
        $this->errorOtp        = '';
    }

    public function with()
    {
        /* Re-évaluer à chaque render (pour le cas du timer 10min) */
        if (! $this->showModal) {
            $this->_evaluerAffichage();
        }

        $user = auth()->user();
        return [
            'user' => $user,
        ];
    }
};

?>

<div>
    @if($showModal)
{{-- Overlay backdrop --}}
<div style="position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9998;backdrop-filter:blur(3px)"></div>

{{-- Modal --}}
<div style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px">
  <div style="background:#fff;border-radius:20px;width:100%;max-width:460px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.2);animation:flmSlideIn .35s ease">

    {{-- Header --}}
    <div style="background:linear-gradient(135deg,#2d3a63,#405189);padding:24px 28px;position:relative">
      <div style="display:flex;align-items:center;gap:14px">
        <div style="width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;flex-shrink:0">
          <i class="ri-shield-keyhole-line"></i>
        </div>
        <div>
          <div style="font-size:16px;font-weight:800;color:#fff">Première connexion</div>
          <div style="font-size:12px;color:rgba(255,255,255,.75);margin-top:2px">
            Définissez votre mot de passe personnel
          </div>
        </div>
      </div>

      {{-- Bouton fermer (disponible mais modal réapparaît après 10min) --}}
      <button wire:click="dismissModal"
              style="position:absolute;top:14px;right:14px;background:rgba(255,255,255,.15);border:none;color:#fff;width:30px;height:30px;border-radius:50%;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;transition:background .2s"
              title="Fermer — le modal réapparaîtra dans 10 minutes">
        <i class="ri-close-line"></i>
      </button>
    </div>

    {{-- Stepper --}}
    <div style="background:#f8f9fa;padding:12px 28px;display:flex;align-items:center;border-bottom:1px solid #e9ebec">
      @php
        $s1 = $step === 'form'     ? 'active' : 'done';
        $s2 = $step === 'otp_sent' ? 'active' : ($step === 'form' ? '' : 'done');
      @endphp
      <div style="display:flex;align-items:center;gap:6px">
        <div style="width:24px;height:24px;border-radius:50%;background:{{ $s1 === 'done' ? '#0ab39c' : ($s1 === 'active' ? '#405189' : '#e9ebec') }};color:#fff;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800">
          @if($s1 === 'done')<i class="ri-check-line"></i>@else 1 @endif
        </div>
        <span style="font-size:12px;font-weight:700;color:{{ $s1 === 'active' ? '#405189' : '#878a99' }}">Mot de passe</span>
      </div>
      <div style="flex:1;height:2px;background:{{ $step === 'otp_sent' ? '#0ab39c' : '#e9ebec' }};margin:0 10px;transition:background .3s"></div>
      <div style="display:flex;align-items:center;gap:6px">
        <div style="width:24px;height:24px;border-radius:50%;background:{{ $s2 === 'active' ? '#405189' : '#e9ebec' }};color:{{ $s2 === 'active' ? '#fff' : '#878a99' }};display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800">
          2
        </div>
        <span style="font-size:12px;font-weight:700;color:{{ $s2 === 'active' ? '#405189' : '#878a99' }}">Vérification</span>
      </div>
    </div>

    {{-- Body --}}
    <div style="padding:24px 28px">

      {{-- ── ÉTAPE 1 : Formulaire ── --}}
      @if($step === 'form')

      <div style="font-size:13px;color:#878a99;margin-bottom:20px;line-height:1.6">
        Pour sécuriser votre compte, vous devez définir un mot de passe personnel.
        Un code de vérification sera envoyé à <strong style="color:#405189">{{ $user->email }}</strong>.
      </div>

      {{-- Nouveau mdp --}}
      <div style="margin-bottom:14px">
        <label style="display:block;font-size:11px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px">
          Nouveau mot de passe <span style="color:#f06548">*</span>
        </label>
        <div style="position:relative">
          <i class="ri-lock-2-line" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#878a99;font-size:15px;pointer-events:none"></i>
          <input type="password" wire:model="nouveauMdp"
                 id="flm-new-pw"
                 placeholder="Minimum 8 caractères"
                 style="width:100%;height:44px;border:1.5px solid {{ $errorNouveauMdp ? '#f06548' : '#e9ebec' }};border-radius:10px;padding:0 44px 0 36px;font-size:13px;color:#212529;outline:none;transition:border-color .2s;font-family:inherit"/>
          <button type="button" onclick="flmToggle('flm-new-pw',this)"
                  style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#878a99;font-size:16px;display:flex;align-items:center">
            <i class="ri-eye-line"></i>
          </button>
        </div>
        {{-- Force --}}
        <div style="height:4px;background:#e9ebec;border-radius:4px;overflow:hidden;margin-top:6px">
          <div id="flm-pw-bar" style="height:100%;width:0;border-radius:4px;transition:width .3s,background .3s"></div>
        </div>
        <div id="flm-pw-label" style="font-size:11px;font-weight:700;margin-top:3px;min-height:14px"></div>
        @if($errorNouveauMdp)
        <div style="font-size:12px;color:#f06548;margin-top:4px;font-weight:600"><i class="ri-error-warning-line me-1"></i>{{ $errorNouveauMdp }}</div>
        @endif
      </div>

      {{-- Confirm --}}
      <div style="margin-bottom:6px">
        <label style="display:block;font-size:11px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px">
          Confirmer le mot de passe <span style="color:#f06548">*</span>
        </label>
        <div style="position:relative">
          <i class="ri-lock-line" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#878a99;font-size:15px;pointer-events:none"></i>
          <input type="password" wire:model="confirmMdp"
                 id="flm-confirm-pw"
                 placeholder="Répétez le mot de passe"
                 style="width:100%;height:44px;border:1.5px solid {{ $errorConfirmMdp ? '#f06548' : '#e9ebec' }};border-radius:10px;padding:0 44px 0 36px;font-size:13px;color:#212529;outline:none;font-family:inherit"/>
          <button type="button" onclick="flmToggle('flm-confirm-pw',this)"
                  style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#878a99;font-size:16px;display:flex;align-items:center">
            <i class="ri-eye-line"></i>
          </button>
        </div>
        @if($errorConfirmMdp)
        <div style="font-size:12px;color:#f06548;margin-top:4px;font-weight:600"><i class="ri-error-warning-line me-1"></i>{{ $errorConfirmMdp }}</div>
        @endif
      </div>

      @endif

      {{-- ── ÉTAPE 2 : OTP ── --}}
      @if($step === 'otp_sent')

      <div style="text-align:center;margin-bottom:20px">
        <div style="width:60px;height:60px;border-radius:50%;background:rgba(10,179,156,.1);color:#0ab39c;display:flex;align-items:center;justify-content:center;font-size:26px;margin:0 auto 12px">
          <i class="ri-mail-check-line"></i>
        </div>
        <div style="font-size:14px;font-weight:700;color:#212529;margin-bottom:6px">Code envoyé !</div>
        <div style="font-size:12px;color:#878a99;line-height:1.6">
          Consultez votre email<br>
          <strong style="color:#405189">{{ $user->email }}</strong>
        </div>
      </div>

      {{-- Grille OTP --}}
      <input type="hidden" id="flm-otp-hidden" wire:model="otpSaisi">
      <div id="flm-otp-grid" style="display:flex;justify-content:center;gap:8px;margin-bottom:10px">
        @for($i = 0; $i < 6; $i++)
        <input class="flm-otp-box" type="tel" inputmode="numeric" maxlength="1" autocomplete="off"
               style="width:46px;height:54px;border:2px solid #e9ebec;border-radius:10px;font-size:22px;font-weight:900;text-align:center;color:#212529;background:#f8f9fa;outline:none;font-family:monospace;transition:all .2s"/>
        @endfor
      </div>

      @if($errorOtp)
      <div style="font-size:12px;color:#f06548;text-align:center;font-weight:600;margin-bottom:8px">
        <i class="ri-error-warning-line me-1"></i>{{ $errorOtp }}
      </div>
      @endif

      <div style="text-align:center;font-size:12px;color:#878a99;margin-bottom:4px">
        Pas reçu ?
        <button wire:click="renvoyerOtp"
                style="background:none;border:none;color:#405189;font-weight:700;cursor:pointer;font-size:12px;font-family:inherit">
          Renvoyer le code
        </button>
      </div>

      @endif

    </div>

    {{-- Footer --}}
    <div style="padding:14px 28px;border-top:1px solid #e9ebec;display:flex;justify-content:space-between;gap:10px">
      <div style="font-size:11px;color:#878a99;display:flex;align-items:center;gap:4px">
        <i class="ri-shield-check-line" style="color:#0ab39c"></i>
        Action sécurisée
      </div>
      <div style="display:flex;gap:8px">
        @if($step === 'otp_sent')
        <button wire:click="retourForm"
                style="padding:10px 18px;border:1.5px solid #e9ebec;border-radius:9px;background:#fff;color:#878a99;font-size:12px;font-weight:700;cursor:pointer;font-family:inherit">
          <i class="ri-arrow-left-line me-1"></i>Retour
        </button>
        <button wire:click="confirmerOtp"
                wire:loading.attr="disabled"
                id="flm-otp-confirm-btn"
                style="padding:10px 20px;border:none;border-radius:9px;background:linear-gradient(135deg,#2d3a63,#405189);color:#fff;font-size:12px;font-weight:700;cursor:pointer;font-family:inherit;display:inline-flex;align-items:center;gap:6px">
          <span wire:loading wire:target="confirmerOtp" style="width:14px;height:14px;border:2px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:flmSpin .7s linear infinite;display:inline-block"></span>
          <i class="ri-shield-check-line" wire:loading.remove wire:target="confirmerOtp"></i>
          <span wire:loading.remove wire:target="confirmerOtp">Confirmer</span>
          <span wire:loading wire:target="confirmerOtp">Vérification…</span>
        </button>
        @else
        <button wire:click="envoyerOtp"
                wire:loading.attr="disabled"
                style="padding:10px 20px;border:none;border-radius:9px;background:linear-gradient(135deg,#2d3a63,#405189);color:#fff;font-size:12px;font-weight:700;cursor:pointer;font-family:inherit;display:inline-flex;align-items:center;gap:6px">
          <span wire:loading wire:target="envoyerOtp" style="width:14px;height:14px;border:2px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:flmSpin .7s linear infinite;display:inline-block"></span>
          <i class="ri-send-plane-line" wire:loading.remove wire:target="envoyerOtp"></i>
          <span wire:loading.remove wire:target="envoyerOtp">Envoyer le code</span>
          <span wire:loading wire:target="envoyerOtp">Envoi…</span>
        </button>
        @endif
      </div>
    </div>

  </div>
</div>

@endif
</div>


@push('styles')

    <style>
        @keyframes flmSlideIn { from { opacity:0; transform:scale(.96) translateY(12px); } to { opacity:1; transform:scale(1) translateY(0); } }
        @keyframes flmSpin    { to { transform:rotate(360deg); } }
        .flm-otp-box:focus  { border-color:#405189 !important; background:#fff !important; box-shadow:0 0 0 3px rgba(64,81,137,.12); }
        .flm-otp-box.filled { border-color:#0ab39c !important; background:rgba(10,179,156,.04) !important; color:#0ab39c !important; }
    </style>
@endpush


@push('scripts')

<script>
/* ── Toggle mot de passe ── */
function flmToggle(id, btn) {
  const input = document.getElementById(id);
  const isPassword = input.type === 'password';
  input.type = isPassword ? 'text' : 'password';
  btn.querySelector('i').className = isPassword ? 'ri-eye-off-line' : 'ri-eye-line';
}

/* ── Force mot de passe ── */
document.addEventListener('input', function(e) {
  if (e.target.id !== 'flm-new-pw') return;
  const v = e.target.value;
  let score = 0;
  if (v.length >= 8) score++;
  if (/[A-Z]/.test(v)) score++;
  if (/[0-9]/.test(v)) score++;
  if (/[^A-Za-z0-9]/.test(v)) score++;
  const colors = ['','#f06548','#f7b84b','#0ab39c','#405189'];
  const labels = ['','Faible','Moyen','Bon','Excellent'];
  const bar   = document.getElementById('flm-pw-bar');
  const label = document.getElementById('flm-pw-label');
  if (bar)   { bar.style.width = (score * 25) + '%'; bar.style.background = colors[score] || ''; }
  if (label) { label.textContent = labels[score] || ''; label.style.color = colors[score] || ''; }
});

/* ── OTP Grid ── */
(function initFlmOtp() {
  const grid   = document.getElementById('flm-otp-grid');
  const hidden = document.getElementById('flm-otp-hidden');
  if (!grid || !hidden) return;
  const boxes = [...grid.querySelectorAll('.flm-otp-box')];

  function sync() {
    const code = boxes.map(b => b.value).join('');
    hidden.value = code;
    hidden.dispatchEvent(new Event('input', { bubbles: true }));
  }

  boxes.forEach((box, i) => {
    box.addEventListener('input', function() {
      this.value = this.value.replace(/\D/g, '');
      this.classList.toggle('filled', !!this.value);
      sync();
      if (this.value && i < boxes.length - 1) boxes[i+1].focus();
      if (boxes.every(b => b.value)) document.getElementById('flm-otp-confirm-btn')?.click();
    });
    box.addEventListener('keydown', function(e) {
      if (e.key === 'Backspace' && !this.value && i > 0) {
        boxes[i-1].value = '';
        boxes[i-1].classList.remove('filled');
        boxes[i-1].focus();
        sync();
      }
    });
    box.addEventListener('paste', function(e) {
      e.preventDefault();
      const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g,'').slice(0,6);
      boxes.forEach((b, j) => { b.value = text[j] || ''; b.classList.toggle('filled', !!text[j]); });
      sync();
      boxes[Math.min(text.length, 5)]?.focus();
      if (text.length === 6) document.getElementById('flm-otp-confirm-btn')?.click();
    });
  });
  boxes[0]?.focus();
})();

Livewire.hook('commit', ({ succeed }) => {
  succeed(() => requestAnimationFrame(() => {
    const grid = document.getElementById('flm-otp-grid');
    if (grid) {
      [...grid.querySelectorAll('.flm-otp-box')].forEach(b => { b.value=''; b.classList.remove('filled'); });
      grid.querySelector('.flm-otp-box')?.focus();
      (function initFlmOtp() {
        const boxes = [...grid.querySelectorAll('.flm-otp-box')];
        const hidden = document.getElementById('flm-otp-hidden');
        if (!hidden) return;
        function sync() {
          hidden.value = boxes.map(b => b.value).join('');
          hidden.dispatchEvent(new Event('input', { bubbles: true }));
        }
        boxes.forEach((box, i) => {
          box.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g,'');
            this.classList.toggle('filled', !!this.value);
            sync();
            if (this.value && i < boxes.length-1) boxes[i+1].focus();
            if (boxes.every(b => b.value)) document.getElementById('flm-otp-confirm-btn')?.click();
          });
        });
      })();
    }
  }));
});
</script>

@endpush
