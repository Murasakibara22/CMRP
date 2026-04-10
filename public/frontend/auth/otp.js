/* ============================================================
   OTP JS
   ============================================================ */
'use strict';

/* Afficher le numéro depuis sessionStorage */
const phoneLabel = sessionStorage.getItem('pwa_phone_display');
if (phoneLabel) {
  document.getElementById('otp-phone-label').textContent = 'Code envoyé au ' + phoneLabel;
}

/* ── Navigation entre cases ── */
const boxes = [...document.querySelectorAll('.otp-box')];
boxes.forEach((box, i) => {
  box.addEventListener('input', function () {
    const v = this.value.replace(/\D/g, '');
    this.value = v;
    this.classList.toggle('filled', !!v);
    this.classList.remove('err');
    if (v && i < boxes.length - 1) boxes[i + 1].focus();
  });

  box.addEventListener('keydown', function (e) {
    if (e.key === 'Backspace' && !this.value && i > 0) {
      boxes[i - 1].focus();
      boxes[i - 1].classList.remove('filled');
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
    const next = Math.min(text.length, 5);
    boxes[next].focus();
  });
});

/* ── Timer countdown ── */
let timerInterval;
function startTimer(sec = 60) {
  clearInterval(timerInterval);
  const display = document.getElementById('timer-val');
  const resend  = document.getElementById('btn-resend');
  resend.classList.remove('on');

  let remaining = sec;
  function tick() {
    const m = String(Math.floor(remaining / 60)).padStart(2, '0');
    const s = String(remaining % 60).padStart(2, '0');
    display.textContent = m + ':' + s;
    if (remaining <= 0) {
      clearInterval(timerInterval);
      resend.classList.add('on');
      document.querySelector('.otp-timer').innerHTML = '<span style="color:var(--muted)">Code expiré</span>';
    }
    remaining--;
  }
  tick();
  timerInterval = setInterval(tick, 1000);
}

function resend() {
  const btn = document.getElementById('btn-resend');
  if (!btn.classList.contains('on')) return;
  boxes.forEach(b => { b.value = ''; b.classList.remove('filled', 'err'); });
  document.querySelector('.otp-timer').innerHTML = 'Renvoyer dans <strong id="timer-val">01:00</strong>';
  startTimer();
  boxes[0].focus();
}

/* ── Vérification ── */
function verify() {
  const code = boxes.map(b => b.value).join('');
  if (code.length < 6) {
    boxes.forEach(b => b.classList.add('err'));
    setTimeout(() => boxes.forEach(b => b.classList.remove('err')), 600);
    return;
  }

  const btn = document.getElementById('btn-verify');
  btn.innerHTML = '<div class="spinner"></div>';
  btn.disabled  = true;

  /* En prod : vérifier le code via API
     Si nouveau numéro → inscription.html
     Sinon             → app.html         */
  setTimeout(() => {
    const isNew = !sessionStorage.getItem('pwa_registered');
    window.location.href = isNew ? 'inscription.html' : 'app.html';
  }, 1400);
}

/* Init */
startTimer();
boxes[0].focus();