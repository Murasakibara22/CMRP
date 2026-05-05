<div>
<div class="page-content">
<div class="container-fluid">

  {{-- ══ PAGE HEADER ══════════════════════════════════════ --}}
  <div class="ap-header fade-up">
    <div>
      <h4>Mon profil</h4>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Mon profil</li>
        </ol>
      </nav>
    </div>
  </div>

  {{-- ══ LAYOUT ══════════════════════════════════════════ --}}
  <div class="ap-layout">

    {{-- ── COLONNE GAUCHE : carte profil ─────────────────── --}}
    <div class="ap-left-col fade-up fu-1">

      {{-- Carte avatar --}}
      <div class="ap-avatar-card">
        <div class="ap-avatar-wrap">
          <div class="ap-avatar" id="ap-avatar">
            @if($photoUrl)
              <img src="{{ $photoUrl }}" id="ap-avatar-img"
                   style="width:100%;height:100%;object-fit:cover;border-radius:50%">
            @else
              <span id="ap-avatar-initiales">{{ $initiales }}</span>
            @endif
          </div>
          <div class="ap-avatar-badge">
            <i class="ri-camera-line"></i>
          </div>
        </div>

        <div class="ap-avatar-name">{{ $user->prenom }} {{ $user->nom }}</div>
        <div class="ap-avatar-role">{{ $user->role?->libelle ?? 'Administrateur' }}</div>
        <div class="ap-avatar-status {{ $user->status === 'actif' ? 'status-actif' : 'status-suspendu' }}">
          <i class="{{ $user->status === 'actif' ? 'ri-shield-check-line' : 'ri-pause-circle-line' }}"></i>
          {{ $user->status === 'actif' ? 'Compte actif' : 'Suspendu' }}
        </div>

        {{-- Upload photo --}}
        <input type="file" id="ap-photo-input" accept="image/*"
               wire:model="photoFile"
               style="display:none"
               onchange="previewAdminPhoto(this)">

        <div class="ap-avatar-btns" id="ap-photo-select-btns">
          <button class="ap-btn-photo" onclick="triggerAdminCamera()">
            <i class="ri-camera-fill"></i> Prendre une photo
          </button>
          <button class="ap-btn-photo" onclick="triggerAdminGallery()">
            <i class="ri-image-2-line"></i> Choisir depuis la galerie
          </button>
          @if($photoUrl)
          <button class="ap-btn-photo ap-btn-danger" wire:click="supprimerPhoto">
            <i class="ri-delete-bin-line"></i> Supprimer la photo
          </button>
          @endif
        </div>

        {{-- Boutons confirmation photo --}}
        <div class="ap-avatar-btns" id="ap-photo-confirm-btns" style="display:none">
          <button class="ap-btn-photo" onclick="annulerAdminPhoto()">
            <i class="ri-close-line"></i> Annuler
          </button>
          <button class="ap-btn-photo ap-btn-primary"
                  wire:click="sauvegarderPhoto"
                  wire:loading.attr="disabled">
            <span wire:loading wire:target="sauvegarderPhoto" class="ap-spinner"></span>
            <i class="ri-save-line" wire:loading.remove wire:target="sauvegarderPhoto"></i>
            <span wire:loading.remove wire:target="sauvegarderPhoto">Enregistrer</span>
            <span wire:loading wire:target="sauvegarderPhoto">Enregistrement…</span>
          </button>
        </div>
      </div>

      {{-- Infos rapides --}}
      <div class="ap-info-card">
        <div class="ap-info-row">
          <i class="ri-mail-line"></i>
          <span>{{ $user->email }}</span>
        </div>
        <div class="ap-info-row">
          <i class="ri-smartphone-line"></i>
          <span>{{ $user->dial_code }} {{ $user->phone ?? '—' }}</span>
        </div>
        <div class="ap-info-row">
          <i class="ri-calendar-line"></i>
          <span>Depuis {{ $user->created_at->translatedFormat('F Y') }}</span>
        </div>
      </div>

    </div>

    {{-- ── COLONNE DROITE : formulaires ───────────────────── --}}
    <div class="ap-right-col">

      {{-- Message succès global --}}
      @if($successMsg)
      <div class="ap-success-banner fade-up">
        <i class="ri-checkbox-circle-line"></i>{{ $successMsg }}
      </div>
      @endif

      {{-- Tabs --}}
      <div class="ap-tabs fade-up fu-2">
        <button class="ap-tab {{ $activeTab === 'infos' ? 'active' : '' }}"
                wire:click="$set('activeTab','infos')">
          <i class="ri-user-line"></i> Informations personnelles
        </button>
        <button class="ap-tab {{ $activeTab === 'password' ? 'active' : '' }}"
                wire:click="$set('activeTab','password')">
          <i class="ri-lock-password-line"></i> Mot de passe
        </button>
      </div>

      {{-- ════ TAB INFOS ════ --}}
      @if($activeTab === 'infos')
      <div class="ap-form-card fade-up fu-3">

        <div class="ap-form-title">Modifier mes informations</div>

        <div class="row g-3">
          <div class="col-sm-6">
            <label class="ap-label">Nom <span class="ap-req">*</span></label>
            <div class="ap-input-wrap">
              <i class="ri-user-line ap-input-icon"></i>
              <input type="text" class="ap-input {{ $errorNom ? 'ap-input-err' : '' }}"
                     wire:model.lazy="nom" placeholder="Votre nom"/>
            </div>
            @if($errorNom)<div class="ap-err">{{ $errorNom }}</div>@endif
          </div>
          <div class="col-sm-6">
            <label class="ap-label">Prénom(s) <span class="ap-req">*</span></label>
            <div class="ap-input-wrap">
              <i class="ri-user-line ap-input-icon"></i>
              <input type="text" class="ap-input {{ $errorPrenom ? 'ap-input-err' : '' }}"
                     wire:model.lazy="prenom" placeholder="Vos prénom(s)"/>
            </div>
            @if($errorPrenom)<div class="ap-err">{{ $errorPrenom }}</div>@endif
          </div>
          <div class="col-12">
            <label class="ap-label">Email <span class="ap-req">*</span></label>
            <div class="ap-input-wrap">
              <i class="ri-mail-line ap-input-icon"></i>
              <input type="email" class="ap-input {{ $errorEmail ? 'ap-input-err' : '' }}"
                     wire:model.lazy="email" placeholder="votre@email.com"/>
            </div>
            @if($errorEmail)<div class="ap-err">{{ $errorEmail }}</div>@endif
          </div>
          <div class="col-sm-4">
            <label class="ap-label">Indicatif</label>
            <select class="ap-input" wire:model="dialCode" style="cursor:pointer">
              <option value="+225">🇨🇮 +225</option>
              <option value="+223">🇲🇱 +223</option>
              <option value="+226">🇧🇫 +226</option>
              <option value="+227">🇳🇪 +227</option>
              <option value="+221">🇸🇳 +221</option>
            </select>
          </div>
          <div class="col-sm-8">
            <label class="ap-label">Téléphone</label>
            <div class="ap-input-wrap">
              <i class="ri-smartphone-line ap-input-icon"></i>
              <input type="tel" class="ap-input" wire:model.lazy="phone"
                     placeholder="Numéro de téléphone" inputmode="numeric"/>
            </div>
          </div>
        </div>

        <div class="ap-form-footer">
          <button class="ap-btn-primary" wire:click="sauvegarderInfos" wire:loading.attr="disabled">
            <span wire:loading wire:target="sauvegarderInfos" class="ap-spinner"></span>
            <i class="ri-save-line" wire:loading.remove wire:target="sauvegarderInfos"></i>
            <span wire:loading.remove wire:target="sauvegarderInfos">Enregistrer les modifications</span>
            <span wire:loading wire:target="sauvegarderInfos">Enregistrement…</span>
          </button>
        </div>

      </div>
      @endif

      {{-- ════ TAB MOT DE PASSE ════ --}}
      @if($activeTab === 'password')
      <div class="ap-form-card fade-up fu-3">

        {{-- ÉTAPE 1 : Formulaire --}}
        @if($mdpStep === 'form')

        <div class="ap-form-title">
          <i class="ri-lock-password-line me-2" style="color:#405189"></i>
          Changer mon mot de passe
        </div>
        <div class="ap-form-sub">Un code de vérification sera envoyé à <strong>{{ $user->email }}</strong>.</div>

        <div class="ap-mdp-steps">
          <div class="ap-mdp-step active"><div class="ap-mdp-step-num">1</div><span>Saisir</span></div>
          <div class="ap-mdp-step-line"></div>
          <div class="ap-mdp-step"><div class="ap-mdp-step-num">2</div><span>Vérifier</span></div>
          <div class="ap-mdp-step-line"></div>
          <div class="ap-mdp-step"><div class="ap-mdp-step-num">3</div><span>Confirmé</span></div>
        </div>

        <div class="ap-field-group">
          <label class="ap-label">Mot de passe actuel <span class="ap-req">*</span></label>
          <div class="ap-input-wrap ap-pw-wrap">
            <i class="ri-lock-line ap-input-icon"></i>
            <input type="password" class="ap-input {{ $errorAncienMdp ? 'ap-input-err' : '' }}"
                   wire:model="ancienMdp" placeholder="Votre mot de passe actuel"
                   id="ap-old-pw"/>
            <button type="button" class="ap-pw-toggle" onclick="togglePw('ap-old-pw',this)">
              <i class="ri-eye-line"></i>
            </button>
          </div>
          @if($errorAncienMdp)<div class="ap-err">{{ $errorAncienMdp }}</div>@endif
        </div>

        <div class="ap-field-group">
          <label class="ap-label">Nouveau mot de passe <span class="ap-req">*</span></label>
          <div class="ap-input-wrap ap-pw-wrap">
            <i class="ri-lock-2-line ap-input-icon"></i>
            <input type="password" class="ap-input {{ $errorNouveauMdp ? 'ap-input-err' : '' }}"
                   wire:model="nouveauMdp" placeholder="Minimum 8 caractères"
                   id="ap-new-pw"/>
            <button type="button" class="ap-pw-toggle" onclick="togglePw('ap-new-pw',this)">
              <i class="ri-eye-line"></i>
            </button>
          </div>
          @if($errorNouveauMdp)<div class="ap-err">{{ $errorNouveauMdp }}</div>@endif

          {{-- Indicateur de force --}}
          <div class="ap-pw-strength" id="ap-pw-strength">
            <div class="ap-pw-bar" id="ap-pw-bar"></div>
          </div>
          <div class="ap-pw-strength-label" id="ap-pw-strength-label"></div>
        </div>

        <div class="ap-field-group">
          <label class="ap-label">Confirmer le nouveau mot de passe <span class="ap-req">*</span></label>
          <div class="ap-input-wrap ap-pw-wrap">
            <i class="ri-lock-2-line ap-input-icon"></i>
            <input type="password" class="ap-input {{ $errorConfirmMdp ? 'ap-input-err' : '' }}"
                   wire:model="confirmMdp" placeholder="Répétez le nouveau mot de passe"
                   id="ap-confirm-pw"/>
            <button type="button" class="ap-pw-toggle" onclick="togglePw('ap-confirm-pw',this)">
              <i class="ri-eye-line"></i>
            </button>
          </div>
          @if($errorConfirmMdp)<div class="ap-err">{{ $errorConfirmMdp }}</div>@endif
        </div>

        <div class="ap-form-footer">
          <button class="ap-btn-primary" wire:click="envoyerOtpMdp" wire:loading.attr="disabled">
            <span wire:loading wire:target="envoyerOtpMdp" class="ap-spinner"></span>
            <i class="ri-send-plane-line" wire:loading.remove wire:target="envoyerOtpMdp"></i>
            <span wire:loading.remove wire:target="envoyerOtpMdp">Envoyer le code de vérification</span>
            <span wire:loading wire:target="envoyerOtpMdp">Envoi en cours…</span>
          </button>
        </div>

        @endif

        {{-- ÉTAPE 2 : Saisie OTP --}}
        @if($mdpStep === 'otp_sent')

        <div class="ap-form-title">
          <i class="ri-mail-check-line me-2" style="color:#0ab39c"></i>
          Vérification par email
        </div>

        <div class="ap-mdp-steps">
          <div class="ap-mdp-step done"><div class="ap-mdp-step-num"><i class="ri-check-line"></i></div><span>Saisir</span></div>
          <div class="ap-mdp-step-line done"></div>
          <div class="ap-mdp-step active"><div class="ap-mdp-step-num">2</div><span>Vérifier</span></div>
          <div class="ap-mdp-step-line"></div>
          <div class="ap-mdp-step"><div class="ap-mdp-step-num">3</div><span>Confirmé</span></div>
        </div>

        <div style="text-align:center;margin:20px 0">
          <div style="width:64px;height:64px;border-radius:50%;background:rgba(10,179,156,.1);color:#0ab39c;display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto 12px">
            <i class="ri-mail-check-line"></i>
          </div>
          <div style="font-size:14px;font-weight:700;color:#212529;margin-bottom:6px">Code envoyé !</div>
          <div style="font-size:13px;color:#878a99">
            Un code à 6 chiffres a été envoyé à<br>
            <strong style="color:#405189">{{ $user->email }}</strong>
          </div>
        </div>

        {{-- Grille OTP --}}
        <input type="hidden" id="ap-otp-hidden" wire:model="otpSaisi">
        <div class="ap-otp-grid" id="ap-otp-grid">
          @for($i = 0; $i < 6; $i++)
          <input class="ap-otp-box" type="tel" inputmode="numeric" maxlength="1" autocomplete="off"/>
          @endfor
        </div>

        @if($errorOtp)
        <div class="ap-err" style="text-align:center;margin-top:8px">
          <i class="ri-error-warning-line me-1"></i>{{ $errorOtp }}
        </div>
        @endif

        <div style="text-align:center;margin-top:14px;font-size:12px;color:#878a99">
          Pas reçu ?
          <button wire:click="renvoyerOtp" style="background:none;border:none;color:#405189;font-weight:700;cursor:pointer;font-size:12px">
            Renvoyer le code
          </button>
        </div>

        <div class="ap-form-footer" style="gap:10px">
          <button class="ap-btn-secondary" wire:click="annulerChangementMdp">
            <i class="ri-arrow-left-line me-1"></i>Retour
          </button>
          <button class="ap-btn-primary" wire:click="confirmerOtpMdp" wire:loading.attr="disabled" id="ap-otp-confirm-btn">
            <span wire:loading wire:target="confirmerOtpMdp" class="ap-spinner"></span>
            <i class="ri-shield-check-line" wire:loading.remove wire:target="confirmerOtpMdp"></i>
            <span wire:loading.remove wire:target="confirmerOtpMdp">Confirmer</span>
            <span wire:loading wire:target="confirmerOtpMdp">Vérification…</span>
          </button>
        </div>

        @endif

      </div>
      @endif

    </div>
  </div>

</div>
</div>
</div>

@push('styles')
<link href="{{ asset('assets/css/admin-profile.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script>
/* ── Photo ── */
function triggerAdminCamera()  { const f = document.getElementById('ap-photo-input'); f.setAttribute('capture','user'); f.click(); }
function triggerAdminGallery() { const f = document.getElementById('ap-photo-input'); f.removeAttribute('capture'); f.click(); }

function previewAdminPhoto(input) {
  if (!input.files?.[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    const av = document.getElementById('ap-avatar');
    if (av) av.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:50%">`;
    document.getElementById('ap-photo-select-btns').style.display = 'none';
    document.getElementById('ap-photo-confirm-btns').style.display = 'flex';
  };
  reader.readAsDataURL(input.files[0]);
}

function annulerAdminPhoto() {
  document.getElementById('ap-photo-select-btns').style.display = '';
  document.getElementById('ap-photo-confirm-btns').style.display = 'none';
  document.getElementById('ap-photo-input').value = '';
}

Livewire.on('photoAdminSauvegardee', ({ url }) => {
  const av = document.getElementById('ap-avatar');
  if (av) av.innerHTML = `<img src="${url}" style="width:100%;height:100%;object-fit:cover;border-radius:50%">`;
  document.getElementById('ap-photo-select-btns').style.display = '';
  document.getElementById('ap-photo-confirm-btns').style.display = 'none';
});
Livewire.on('photoAdminSupprimee', () => {
  document.getElementById('ap-photo-select-btns').style.display = '';
  document.getElementById('ap-photo-confirm-btns').style.display = 'none';
});

/* ── Afficher/masquer mot de passe ── */
function togglePw(inputId, btn) {
  const input = document.getElementById(inputId);
  const isPassword = input.type === 'password';
  input.type = isPassword ? 'text' : 'password';
  btn.querySelector('i').className = isPassword ? 'ri-eye-off-line' : 'ri-eye-line';
}

/* ── Force du mot de passe ── */
document.addEventListener('DOMContentLoaded', () => {
  const newPw = document.getElementById('ap-new-pw');
  if (!newPw) return;
  newPw.addEventListener('input', function() {
    const bar   = document.getElementById('ap-pw-bar');
    const label = document.getElementById('ap-pw-strength-label');
    if (!bar || !label) return;
    const v = this.value;
    let score = 0;
    if (v.length >= 8)              score++;
    if (/[A-Z]/.test(v))            score++;
    if (/[0-9]/.test(v))            score++;
    if (/[^A-Za-z0-9]/.test(v))     score++;
    const colors = ['','#f06548','#f7b84b','#0ab39c','#405189'];
    const labels = ['','Faible','Moyen','Bon','Excellent'];
    bar.style.width   = (score * 25) + '%';
    bar.style.background = colors[score] || '';
    label.textContent = labels[score] || '';
    label.style.color = colors[score] || '';
  });
});

/* ── OTP Grid ── */
(function initOtp() {
  const grid   = document.getElementById('ap-otp-grid');
  const hidden = document.getElementById('ap-otp-hidden');
  if (!grid || !hidden) return;
  const boxes = [...grid.querySelectorAll('.ap-otp-box')];

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
      if (boxes.every(b => b.value)) document.getElementById('ap-otp-confirm-btn')?.click();
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
      if (text.length === 6) document.getElementById('ap-otp-confirm-btn')?.click();
    });
  });
  boxes[0]?.focus();
})();

Livewire.hook('commit', ({ succeed }) => {
  succeed(() => requestAnimationFrame(() => {
    if (document.getElementById('ap-otp-grid')) {
      const boxes = [...document.querySelectorAll('.ap-otp-box')];
      boxes.forEach(b => { b.value = ''; b.classList.remove('filled'); });
      boxes[0]?.focus();
    }
  }));
});
</script>
@endpush
