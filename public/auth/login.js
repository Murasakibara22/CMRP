
  /* ===== COUNTERS ANIMATION ===== */
  function animateCounter(el, target, suffix, duration) {
    let start = 0;
    const step = target / (duration / 16);
    const timer = setInterval(() => {
      start += step;
      if (start >= target) { start = target; clearInterval(timer); }
      el.textContent = Math.floor(start).toLocaleString('fr-FR') + suffix;
    }, 16);
  }

  window.addEventListener('load', () => {
    setTimeout(() => {
      animateCounter(document.getElementById('counter1'), 347, '', 1800);
      animateCounter(document.getElementById('counter2'), 2850000, ' FCFA', 2000);
      animateCounter(document.getElementById('counter3'), 78, '%', 1500);
    }, 500);
  });

  /* ===== TOGGLE PASSWORD ===== */
  function togglePassword() {
    const input = document.getElementById('passwordInput');
    const icon = document.getElementById('eyeIcon');
    if (input.type === 'password') {
      input.type = 'text';
      icon.className = 'ri-eye-off-line';
    } else {
      input.type = 'password';
      icon.className = 'ri-eye-line';
    }
  }

  /* ===== SHOW ALERT ===== */
  function showAlert(msg, type = 'error') {
    const container = document.getElementById('alertContainer');
    const icon = type === 'error' ? 'ri-error-warning-line' : 'ri-checkbox-circle-line';
    container.innerHTML = `
      <div class="alert-custom ${type}">
        <i class="${icon}"></i>
        <span>${msg}</span>
      </div>
    `;
    setTimeout(() => { container.innerHTML = ''; }, 4000);
  }

  /* ===== UPDATE STEPS ===== */
  function updateSteps(step) {
    const dots = ['dot1','dot2','dot3'];
    const lbls = ['lbl1','lbl2','lbl3'];
    const lines = ['line1','line2'];
    const progressPct = { 1: '33%', 2: '66%', 3: '100%' };

    dots.forEach((id, i) => {
      const dot = document.getElementById(id);
      const lbl = document.getElementById(lbls[i]);
      const stepNum = i + 1;
      dot.className = 'step-dot';
      lbl.className = 'step-label';
      if (stepNum < step) { dot.classList.add('completed'); lbl.classList.add('completed'); }
      else if (stepNum === step) { dot.classList.add('active'); lbl.classList.add('active'); }
      else { dot.classList.add('pending'); lbl.classList.add('pending'); }
    });

    lines.forEach((id, i) => {
      const line = document.getElementById(id);
      line.className = 'step-line';
      if (i + 1 < step) line.classList.add('completed');
      else if (i + 1 < step) line.classList.add('active');
    });

    document.getElementById('progressBar').style.width = progressPct[step];
  }

  /* ===== STEP 1 → 2 ===== */
  function goToStep2() {
    const email = document.getElementById('emailInput').value.trim();
    const password = document.getElementById('passwordInput').value;

    if (!email || !email.includes('@')) {
      showAlert('Veuillez entrer une adresse email valide.');
      document.getElementById('emailInput').classList.add('is-invalid');
      setTimeout(() => document.getElementById('emailInput').classList.remove('is-invalid'), 2000);
      return;
    }

    if (password.length < 6) {
      showAlert('Le mot de passe doit contenir au moins 6 caractères.');
      document.getElementById('passwordInput').classList.add('is-invalid');
      setTimeout(() => document.getElementById('passwordInput').classList.remove('is-invalid'), 2000);
      return;
    }

    // Simulate loading
    const btn = document.getElementById('loginBtn');
    btn.classList.add('loading');

    setTimeout(() => {
      btn.classList.remove('loading');

      // Mask email
      const [user, domain] = email.split('@');
      const masked = user[0] + '***@' + domain;
      document.getElementById('maskedEmail').textContent = masked;

      // Update UI
      document.getElementById('formTitle').textContent = 'Vérification OTP';
      document.getElementById('formSubtitle').textContent = 'Saisissez le code de sécurité reçu pour confirmer votre identité.';

      document.getElementById('step1').classList.remove('active');
      document.getElementById('step2').classList.add('active');
      updateSteps(2);
      startOtpTimer();
      setTimeout(() => document.getElementById('otp1').focus(), 100);
    }, 1400);
  }

  /* ===== OTP TIMER ===== */
  let timerInterval;
  let timerSeconds = 300;

  function startOtpTimer() {
    timerSeconds = 300;
    clearInterval(timerInterval);
    document.getElementById('resendBtn').classList.remove('visible');

    timerInterval = setInterval(() => {
      timerSeconds--;
      const m = Math.floor(timerSeconds / 60).toString().padStart(2, '0');
      const s = (timerSeconds % 60).toString().padStart(2, '0');
      document.getElementById('timerDisplay').textContent = `${m}:${s}`;

      if (timerSeconds <= 0) {
        clearInterval(timerInterval);
        document.getElementById('timerDisplay').textContent = 'Expiré';
        document.getElementById('resendBtn').classList.add('visible');
      }
    }, 1000);
  }

  function resendOtp() {
    showAlert('Nouveau code envoyé à votre email.', 'success');
    startOtpTimer();
    document.getElementById('otp1').focus();
    ['otp1','otp2','otp3','otp4','otp5','otp6'].forEach(id => {
      document.getElementById(id).value = '';
      document.getElementById(id).classList.remove('filled');
    });
  }

  /* ===== OTP INPUTS NAVIGATION ===== */
  document.addEventListener('DOMContentLoaded', () => {
    const inputs = ['otp1','otp2','otp3','otp4','otp5','otp6'];

    inputs.forEach((id, idx) => {
      const input = document.getElementById(id);

      input.addEventListener('input', (e) => {
        const val = e.target.value.replace(/[^0-9]/g,'');
        e.target.value = val;

        if (val) {
          input.classList.add('filled');
          if (idx < 5) document.getElementById(inputs[idx + 1]).focus();
          else verifyOtp(); // auto-submit on last digit
        } else {
          input.classList.remove('filled');
        }
      });

      input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !input.value && idx > 0) {
          document.getElementById(inputs[idx - 1]).focus();
        }
      });

      input.addEventListener('paste', (e) => {
        e.preventDefault();
        const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g,'');
        paste.split('').forEach((char, i) => {
          if (i < 6) {
            const inp = document.getElementById(inputs[i]);
            inp.value = char;
            inp.classList.add('filled');
          }
        });
        setTimeout(verifyOtp, 200);
      });
    });
  });

  /* ===== OTP VERIFY ===== */
  function verifyOtp() {
    const inputs = ['otp1','otp2','otp3','otp4','otp5','otp6'];
    const code = inputs.map(id => document.getElementById(id).value).join('');

    if (code.length < 6) {
      showAlert('Veuillez saisir les 6 chiffres du code OTP.');
      inputs.forEach(id => document.getElementById(id).classList.add('shake'));
      setTimeout(() => inputs.forEach(id => document.getElementById(id).classList.remove('shake')), 500);
      return;
    }

    const btn = document.getElementById('verifyBtn');
    btn.classList.add('loading');

    setTimeout(() => {
      btn.classList.remove('loading');

      // Demo: accept any 6-digit code
      if (code.length === 6) {
        clearInterval(timerInterval);
        goToStep3();
      } else {
        showAlert('Code OTP incorrect. Veuillez réessayer.');
        inputs.forEach(id => {
          document.getElementById(id).value = '';
          document.getElementById(id).classList.remove('filled');
          document.getElementById(id).classList.add('shake');
        });
        setTimeout(() => inputs.forEach(id => document.getElementById(id).classList.remove('shake')), 500);
        document.getElementById('otp1').focus();
      }
    }, 1200);
  }

  /* ===== STEP 3: SUCCESS ===== */
  function goToStep3() {
    document.getElementById('formTitle').textContent = 'Accès accordé';
    document.getElementById('formSubtitle').textContent = 'Authentification réussie. Bienvenue sur la plateforme.';

    document.getElementById('step2').classList.remove('active');
    document.getElementById('step3').classList.add('active');
    updateSteps(3);

    launchConfetti();

    // Redirect countdown
    let count = 3;
    const bar = document.getElementById('redirectBar');
    const counter = document.getElementById('redirectCount');

    const countDown = setInterval(() => {
      count--;
      counter.textContent = count;
      bar.style.width = ((3 - count) / 3 * 100) + '%';
      if (count <= 0) {
        clearInterval(countDown);
        // window.location.href = 'dashboard.html';
        console.log('Redirection vers dashboard.html');
      }
    }, 1000);
  }

  /* ===== BACK ===== */
  function goBack(step) {
    clearInterval(timerInterval);
    document.getElementById('step2').classList.remove('active');
    document.getElementById('step1').classList.add('active');
    document.getElementById('formTitle').textContent = 'Bonne connexion';
    document.getElementById('formSubtitle').textContent = 'Accédez à votre tableau de bord de gestion de la mosquée.';
    updateSteps(1);
    document.getElementById('alertContainer').innerHTML = '';
  }

  /* ===== CONFETTI ===== */
  function launchConfetti() {
    const wrapper = document.getElementById('confettiWrapper');
    const colors = ['#405189','#0ab39c','#f7b84b','#3577f1','#d4a843'];

    for (let i = 0; i < 25; i++) {
      const dot = document.createElement('div');
      dot.className = 'confetti-dot';
      dot.style.left = Math.random() * 100 + '%';
      dot.style.background = colors[Math.floor(Math.random() * colors.length)];
      dot.style.width = (6 + Math.random() * 6) + 'px';
      dot.style.height = (6 + Math.random() * 6) + 'px';
      dot.style.animationDuration = (0.8 + Math.random() * 0.8) + 's';
      dot.style.animationDelay = (Math.random() * 0.5) + 's';
      wrapper.appendChild(dot);
      setTimeout(() => dot.remove(), 2000);
    }
  }

  /* ===== Enter key support ===== */
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      if (document.getElementById('step1').classList.contains('active')) goToStep2();
      else if (document.getElementById('step2').classList.contains('active')) verifyOtp();
    }
  });
