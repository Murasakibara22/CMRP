 <!-- ══ APP BAR ══════════════════════════════════════ -->
  <header class="app-bar">
    <div class="ab-brand">

        <img src="{{ asset('logo.png') }}" alt="" height="52" >

      <div class="ab-name">CMRP Mosquée</div>
    </div>
    <div class="ab-actions">
      <button class="ab-btn" onclick="window.location.href=''" title="Notifications">
        <i class="ri-notification-3-line"></i>
        <span class="ab-notif-dot"></span>
      </button>
      <div class="ab-avatar" onclick="window.location.href='{{ route('customer.profile') }}'">{{ Auth::guard('customer')->user()->nom[0].Auth::guard('customer')->user()->prenom[1] }}</div>
    </div>
  </header>
