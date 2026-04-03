<!DOCTYPE html>
<html lang="fr" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>
  <meta charset="utf-8" />
  <title>Connexion | Mosquée – Gestion</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta content="Plateforme de gestion de la mosquée" name="description" />
  <link rel="shortcut icon" href="assets/images/favicon.ico">

  <!-- Bootstrap -->
  <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
  <!-- Icons -->
  <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
  <!-- App CSS -->
  <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

  <link href="{{ asset('auth/login.css') }}" rel="stylesheet" type="text/css" />

  <style>
    /* ─── Alert Error ──────────────────────────────────────── */
    .alert-error {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        background: #FCEBEB;
        border: 0.5px solid #F09595;
        border-left: 3px solid #E24B4A;
        border-radius: 0 8px 8px 0;
        padding: 12px 14px;
        margin-bottom: 16px;
        animation: alertSlideIn 0.25s ease;
    }

    .alert-error .alert-icon {
        font-size: 16px;
        color: #A32D2D;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .alert-error .alert-title {
        font-size: 13px;
        font-weight: 500;
        color: #501313;
        margin: 0 0 2px;
        line-height: 1.4;
    }

    .alert-error .alert-message {
        font-size: 13px;
        color: #A32D2D;
        margin: 0;
        line-height: 1.5;
    }

    /* ─── Alert Warning (rate limit) ───────────────────────── */
    .alert-warning {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        background: #FAEEDA;
        border: 0.5px solid #FAC775;
        border-left: 3px solid #BA7517;
        border-radius: 0 8px 8px 0;
        padding: 12px 14px;
        margin-bottom: 16px;
        animation: alertSlideIn 0.25s ease;
    }

    .alert-warning .alert-icon { color: #854F0B; }
    .alert-warning .alert-title { color: #412402; font-size: 13px; font-weight: 500; margin: 0 0 2px; }
    .alert-warning .alert-message { color: #854F0B; font-size: 13px; margin: 0; }

    /* ─── Input invalide ───────────────────────────────────── */
    .input-group-custom.is-invalid input {
        border-color: #E24B4A !important;
        background: #FCEBEB;
        box-shadow: 0 0 0 3px rgba(226, 75, 74, 0.12);
    }

    .input-error-hint {
        font-size: 12px;
        color: #A32D2D;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* ─── Animation ────────────────────────────────────────── */
    @keyframes alertSlideIn {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

  </style>

        @livewireStyles

</head>

<body>

    <!-- Confetti wrapper -->
    <div class="confetti-wrapper" id="confettiWrapper"></div>


    {{ $slot }}


        @livewireScripts
<script src="{{ asset('auth/login.js') }}"></script>
</body>
</html>
