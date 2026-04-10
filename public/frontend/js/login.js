/* ============================================================
   LOGIN JS
   ============================================================ */
'use strict';

function sendOtp() {
  const phone = document.getElementById('phone').value.trim();
  const inp   = document.getElementById('phone');
  inp.classList.remove('err');

  if (!phone || phone.replace(/\s/g,'').length < 8) {
    inp.classList.add('err');
    inp.focus();
    return;
  }

  const dial = document.getElementById('dial').value;
  const full = dial.replace('+','00') + phone.replace(/\s/g,'');

  /* Stocker pour la page OTP */
  sessionStorage.setItem('pwa_phone_display', dial + ' ' + phone);

  const btn = document.getElementById('btn-send');
  btn.innerHTML = '<div class="spinner"></div>';
  btn.disabled  = true;

  /* En prod : appel API SMS */
  setTimeout(() => {
    window.location.href = 'otp.html';
  }, 1200);
}

document.getElementById('phone').addEventListener('keydown', e => {
  if (e.key === 'Enter') sendOtp();
});
document.getElementById('phone').addEventListener('input', function () {
  this.classList.remove('err');
});