<aside class="sidebar">
    <div class="sb-logo">
      <div class="sb-logo-icon"><i class="ri-mosque-line"></i></div>
      <div>
        <div class="sb-logo-name">Espace Fidèle</div>
        <div class="sb-logo-sub">CMRP Mosquée</div>
      </div>
    </div>
    <nav class="sb-nav">
      <button class="sb-item {{ request()->routeIs('customer.home') ? 'active' : ''}}" onclick="window.location.href='{{ route('customer.home') }}'">
        <i class="ri-home-5-line"></i> Accueil
      </button>
      <button class="sb-item {{ request()->routeIs('customer.cotisations') ? 'active' : ''}}" onclick="window.location.href='{{ route('customer.cotisations') }}'">
        <i class="ri-calendar-check-line"></i> Cotisations
      </button>
      <button class="sb-item {{ request()->routeIs('customer.ajout-cotisations') ? 'active' : ''}}" onclick="window.location.href='{{ route('customer.ajout-cotisations') }}'">
        <i class="ri-add-circle-line"></i> Nouvelle cotisation
      </button>
      <button class="sb-item {{ request()->routeIs('customer.paiements') ? 'active' : ''}}" onclick="window.location.href='{{ route('customer.paiements') }}'">
        <i class="ri-bank-card-line"></i> Paiements
      </button>
      <button class="sb-item {{ request()->routeIs('customer.notifications') ? 'active' : ''}}" onclick="window.location.href='{{ route('customer.notifications') }}'">
        <i class="ri-notification-3-line"></i> Notifications
        <span class="sb-notif">3</span>
      </button>
      <button class="sb-item {{ request()->routeIs('customer.profile') ? 'active' : ''}}" onclick="window.location.href='{{ route('customer.profile') }}'">
        <i class="ri-user-3-line"></i> Profil
      </button>
    </nav>
    <div class="sb-profile">
      <div class="sb-avatar">{{ substr(auth('customers')->user()->nom, 0, 2) }}</div>
      <div>
        <div class="sb-profile-name">{{ auth('customers')->user()->nom. ' ' . auth('customers')->user()->prenom }}</div>
        <div class="sb-profile-phone">{{ auth('customers')->user()->dial_code }} {{ auth('customers')->user()->phone }}</div>
      </div>
    </div>
  </aside>
