<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
  <meta name="theme-color" content="#405189"/>
  <title>Espace Fidèle</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css"/>
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

  <link rel="stylesheet" href="{{ asset('frontend/css/pwa.css') }}"/>
  <link rel="stylesheet" href="{{ asset('frontend/css/shell.css') }}"/>
  <link rel="stylesheet" href="{{ asset('frontend/css/app.css') }}"/>
  <link rel="stylesheet" href="{{ asset('frontend/css/cotisation.css') }}"/>
  <link rel="stylesheet" href="{{ asset('frontend/css/add-cotisation.css') }}"/>
  <link rel="stylesheet" href="{{ asset('frontend/css/paiement.css') }}"/>
  <link rel="stylesheet" href="{{ asset('frontend/css/profile.css') }}"/>
  <link rel="stylesheet" href="{{ asset('frontend/css/reclammation.css') }}"/>

  @stack('styles')

  @livewireStyles
</head>
<body>
<div class="app-shell">

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
</body>
</html>