<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
  <meta name="theme-color" content="#405189"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
   <!-- ── PWA ───────────────────────────────────────────── -->
    <link rel="manifest" href="/manifest.json">
    <meta name="author" content="CMRP">
    <title>CMRP | Espace Fidèle</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css"/>
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

    <link rel="shortcut icon" href="{{ asset('logo.png')}}">


    <link rel="stylesheet" href="{{ asset('frontend/css/pwa.css') }}"/>
    <link rel="stylesheet" href="{{ asset('frontend/css/shell.css') }}"/>
    <link rel="stylesheet" href="{{ asset('frontend/css/app.css') }}"/>
    <link rel="stylesheet" href="{{ asset('frontend/css/cotisation.css') }}"/>
    <link rel="stylesheet" href="{{ asset('frontend/css/add-cotisation.css') }}"/>
    <link rel="stylesheet" href="{{ asset('frontend/css/paiement.css') }}"/>
    <link rel="stylesheet" href="{{ asset('frontend/css/profile.css') }}"/>
    <link rel="stylesheet" href="{{ asset('frontend/css/reclammation.css') }}"/>


    {{-- iOS --}}
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="CMRP">
    <link rel="apple-touch-icon" href="{{ asset('/images/icons/android/android-launchericon-192-192.png') }}">

    {{-- Splash screens iOS (optionnel) --}}
    <link rel="apple-touch-startup-image" href="{{ asset('/images/icons/android/android-launchericon-512-512.png') }}">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="{{ asset('/images/icons/android/android-launchericon-96-96.png') }}">

    <style>
        .pwa-splash {
            position: fixed; inset: 0; z-index: 99999;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            transition: opacity .5s ease;
        }
        .pwa-splash.hidden { opacity: 0; pointer-events: none; }
        .splash-content { text-align: center; }
        .splash-logo { width: 96px; height: 96px; border-radius: 22px; margin-bottom: 16px; }
        .splash-text { font-size: 14px; color: #878a99; font-weight: 600; }

        .livewire-loader {
            position: fixed; inset: 0; z-index: 99998;
            background: rgba(255,255,255,.6);
            display: flex; align-items: center; justify-content: center;
        }
        .loader-spinner {
            width: 40px; height: 40px;
            border: 3px solid rgba(64,81,137,.2);
            border-top-color: #405189;
            border-radius: 50%;
            animation: _spin .7s linear infinite;
        }
        @keyframes _spin { to { transform: rotate(360deg); } }
    </style>

  @stack('styles')

  @livewireStyles
</head>
<body>
<div class="app-shell">

    {{-- ── Splash screen PWA ─────────────────────────────── --}}
    <div id="pwa-splash-loader" class="pwa-splash">
        <div class="splash-content">
            <img src="{{ asset('images/icons/android/android-launchericon-192-192.png') }}"
                 alt="CMRP"
                 class="splash-logo">
            <p class="splash-text">Chargement...</p>
        </div>
    </div>

    {{-- ── Livewire loading indicator ────────────────────── --}}
    <div wire:loading.flex class="livewire-loader">
        <div class="loader-spinner"></div>
    </div>

  <!-- ══ SIDEBAR DESKTOP ══════════════════════════════ -->
    @include('partials.frontend.sidebar')


  <!-- ══ MAIN ══════════════════════════════════════════ -->
   <div class="main-wrapper">

    @include('partials.frontend.appbar')

        {{$slot}}

    @include('partials.frontend.bottombar')

  </main>
</div>


     @livewireScripts

@stack('scripts')
@stack('modal')

<script src="{{ asset('frontend/js/app.js') }}"></script>
<script src="{{ asset('frontend/js/cotisation.js') }}"></script>
<script src="{{ asset('frontend/js/add-cotisation.js') }}"></script>
<script src="{{ asset('frontend/js/profile.js') }}"></script>
<script src="{{ asset('frontend/js/reclammation.js') }}"></script>


  <!-- Sweet Alerts js -->
  <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

  <!-- Sweet alert init js-->
  <script src="{{ asset('assets/js/pages/sweetalerts.init.js') }}"></script>
  <script src="{{ asset('assets/js/NotifSweet.js') }}"></script>


  {{-- ── Splash : masquer après chargement ─────────────── --}}
    <script>
        window.addEventListener('load', function () {
            setTimeout(function () {
                const splash = document.getElementById('pwa-splash-loader');
                if (splash) {
                    splash.classList.add('hidden');
                    setTimeout(() => splash.remove(), 500);
                }
            }, 800);
        });
    </script>

    {{-- ── Service Worker ─────────────────────────────────── --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/serviceworker.js')
                    .then(reg  => console.log('✅ SW enregistré:', reg.scope))
                    .catch(err => console.error('❌ SW erreur:', err));
            });

            /* Prompt d'installation PWA */
            let deferredPrompt;
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                /* Afficher un bouton "Installer" si tu veux */
                console.log('💡 PWA installable');
            });

            window.addEventListener('appinstalled', () => {
                console.log('✅ PWA installée');
                deferredPrompt = null;
            });
        }
    </script>
</body>
</html>
