<!doctype html>
<html lang="en" data-layout="vertical" data-layout-style="default" data-layout-position="fixed" data-topbar="light"
    data-sidebar="dark" data-sidebar-size="sm-hover" data-layout-width="fluid">


<head>

    <meta charset="utf-8" />
    <title>CMRP | @stack('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Tableau de bord CMRP, administration CMRP" name="description" />
    <meta content="joackim_clby" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="itv20t_EIkdJtaNJewNkkPEcIsSXaD0cNP7fUj1SVLM" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('logo.png')}}">

    <!-- swiper slider css -->
    <link href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" />

     <!-- Sweet Alert css-->
     <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Layout config Js -->
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

    @stack('styles')

      @livewireStyles




            <style>
                /* Overlay pour griser complètement le modal et désactiver les inputs */
                .overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.7); /* Assure que le fond est bien grisé et opaque */
                    z-index: 10; /* S'assure que l'overlay est bien au-dessus des inputs */
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }


                .overlay .spinner {
                    pointer-events: all; /* Active les interactions uniquement pour le spinner si nécessaire */
                }

                /* Spinner pour indiquer le chargement */
                .spinner {
                    border: 5px solid rgba(255, 255, 255, 0.3);
                    border-top: 5px solid #fff;
                    border-radius: 50%;
                    margin-inline: auto;
                    margin-block: auto;
                    width: 50px;
                    height: 50px;
                    animation: spin 1s linear infinite;
                }

                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }

            </style>



</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        @include('partials.header')

        @include('partials.navbar')

        <div class="main-content">

            {{ $slot }}


            @include('partials.footer')
        </div>

    </div>
    <!-- END layout-wrapper -->


     <!--start back-to-top-->
     <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>
    <!--end back-to-top-->

    <!--preloader-->
    <div id="preloader">
        <div id="status">
            <div class="spinner-border text-primary avatar-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>



     @livewireScripts

@stack('scripts')


 <!-- JAVASCRIPT -->
 <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
 <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
 <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
 <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
 <script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
 <script src="{{ asset('assets/js/plugins.js') }}"></script>


 <!-- apexcharts -->
 <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

  <!-- Vector map-->
  <script src="{{ asset('assets/libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
  <script src="{{ asset('assets/libs/jsvectormap/maps/world-merc.js') }}"></script>
 <!-- Swiper Js -->
 <script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>

  <!-- Sweet Alerts js -->
  <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

  <!-- Sweet alert init js-->
  <script src="{{ asset('assets/js/pages/sweetalerts.init.js') }}"></script>
  <script src="{{ asset('assets/js/NotifSweet.js') }}"></script>

 <!-- CRM js -->
  <!-- Dashboard init -->
 <script src="{{ asset('assets/js/pages/dashboard-ecommerce.init.js') }}"></script>

 <!-- App js -->
 <script src="{{ asset('assets/js/app.js') }}"></script>

<script src="{{ asset('js/app.js') }}"></script>


</body>


<!-- Mirrored from themesbrand.com/velzon/html/default/layouts-vertical-hovered.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 23 Jun 2023 15:34:50 GMT -->

</html>
