<!-- ══ BOTTOM BAR ════════════════════════════════════ -->
  <nav class="bottom-bar">
    <button class="bb-item {{ request()->routeIs('customer.home') ? 'active' : ''}}" onclick="window.location.href='{{ route('customer.home') }}'">
      <i class="ri-home-5-fill"></i>
      <span>Accueil</span>
    </button>
    <button class="bb-item {{ request()->routeIs('customer.cotisations') ? 'active' : ''}}" onclick="window.location.href='{{ route('customer.cotisations') }}'">
      <i class="ri-calendar-check-line"></i>
      <span>Cotisations</span>
    </button>
    <div class="bb-fab-wrap">
      <button class="bb-fab" onclick="window.location.href='{{ route('customer.ajout-cotisations') }}'" title="Nouvelle cotisation">
        <i class="ri-add-line"></i>
      </button>
    </div>
    <button class="bb-item {{ request()->routeIs('customer.paiements') ? 'active' : ''}}" onclick="window.location.href='{{ route('customer.paiements') }}'">
      <i class="ri-bank-card-line"></i>
      <span>Paiements</span>
    </button>
    <button class="bb-item {{ request()->routeIs('customer.profile') ? 'active' : ''}}" onclick="window.location.href='{{ route('customer.profile') }}'">
      <i class="ri-user-3-line"></i>
      <span>Profil</span>
    </button>
  </nav>