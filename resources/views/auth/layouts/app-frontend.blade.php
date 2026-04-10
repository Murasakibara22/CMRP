<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
  <meta name="theme-color" content="#405189"/>
  <title>Connexion — Espace Fidèle</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css"/>
  <link rel="stylesheet" href="{{ asset('frontend/css/pwa.css') }}"/>
  <link rel="stylesheet" href="{{ asset('frontend/css/shell.css') }}"/>
  <link rel="stylesheet" href="{{ asset('frontend/auth/login.css') }}"/>

        @livewireStyles

</head>
<body>

<div class="auth-root">

    @stack('pre-content')

  <!-- ── Panneau droit ── -->
    {{ $slot}}
</div>

        @livewireScripts

        @stack('scripts')
<script src="{{ asset('frontend/auth/login.js') }}"></script>

</body>
</html>