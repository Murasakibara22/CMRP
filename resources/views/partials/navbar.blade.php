 <!-- ========== App Menu ========== -->
 <div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="/home" class="logo logo-dark">
            <span class="logo-sm">
                <img src="assets/images/logo-sm.png" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="assets/images/logo-sm.png" alt="" height="22">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="/home" class="logo logo-light">
            <span class="logo-sm">
                <img src="assets/images/logo-sm.png" alt="" height="22">
            </span>
            <span class="logo-lg">
                 <img src="assets/images/logo-sm.png" alt="" height="22">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="/admin/dashboard">
                        <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Dashboards</span>
                    </a>
                </li> <!-- end Dashboard Menu -->
                 <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('admin.type-cotisations.index') ? 'active' : ''}}" href="{{ route('admin.type-cotisations.index') }}">
                            <i class="ri-timer-fill"></i> <span data-key="t-widgets">Types de Cotisations</span>
                        </a>
                    </li>
                 <li class="nav-item">
                        <a class="nav-link menu-link   {{ request()->routeIs('admin.cotisations.index') ? 'active' : ''}}" href="{{ route('admin.cotisations.index') }}">
                            <i class="ri-timer-fill"></i> <span data-key="t-widgets">Cotisations</span>
                        </a>
                    </li>


                    {{-- <li class="nav-item">
                        <a class="nav-link menu-link " href="">
                            <i class="ri-honour-line"></i> <span data-key="t-widgets">Caisse</span>
                        </a>
                    </li> --}}


                <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-pages">Services</span></li>


                    <li class="nav-item">
                        <a class="nav-link menu-link " href="#">
                            <i class="ri-table-fill"></i> <span data-key="t-widgets">Prêts</span>
                        </a>
                    </li>



                    {{-- <li class="nav-item">
                        <a class="nav-link menu-link " href="">
                            <i class="ri-briefcase-4-line"></i> <span data-key="t-widgets">Membres</span>
                        </a>
                    </li> --}}



                <li class="nav-item">
                    <a class="nav-link menu-link " href="#">
                        <i class=" ri-user-settings-fill"></i> <span data-key="t-widgets">Paiements</span>
                    </a>
                </li>




                    <li class="nav-item">
                        <a class="nav-link menu-link  " href="#">
                            <i class="r ri-menu-add-fill"></i> <span data-key="t-widgets"> Gestionnaires</span>
                        </a>
                    </li>



                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('admin.membres.index') ? 'active' : ''}}" href="{{ route('admin.membres.index') }}">
                            <i class="ri-team-fill"></i> <span data-key="t-widgets">Fidèles</span>
                        </a>
                    </li>






                    {{-- <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('admin.code_promo.index') ? 'active' : ''}}" href="{{ route('admin.code_promo.index') }}">
                            <i class="ri-gift-2-line"></i> <span data-key="t-widgets">Code Promo</span>
                        </a>
                    </li> --}}



                {{-- <li class="menu-title"><i class="ri-more-fill"></i> <span
                    data-key="t-components">FINANCES</span></li>

                    <li class="nav-item">
                        <a class="nav-link menu-link " href="#">
                            <i class=" ri-file-chart-line"></i> <span data-key="t-widgets">État financier</span>
                        </a>
                    </li> --}}


                <li class="menu-title"><i class="ri-more-fill"></i> <span
                        data-key="t-components">PARAMETRES SYSTÈME</span></li>


                {{-- <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('admin.admins.index') ? 'active' : ''}}" href="{{ route('admin.admins.index') }}">
                        <i class=" ri-user-star-fill"></i> <span data-key="t-widgets">Administrateurs</span>
                    </a>
                </li> --}}


                    {{-- <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('admin.roles.index') ? 'active' : '' }}" href="#sidebarUI" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarUI">
                            <i class="ri-pencil-ruler-2-line"></i> <span data-key="t-base-ui">Gestions des Comptes</span>
                        </a>
                        <div class="collapse menu-dropdown mega-dropdown-menu" id="sidebarUI">
                            <div class="row">
                                <div class="col-lg-4">
                                    <ul class="nav nav-sm flex-column">

                                        <li class="nav-item">
                                            <a href="{{ route('admin.admins.index') }}" class="nav-link">Administrateurs</a>
                                        </li>


                                        <li class="nav-item">
                                            <a href="{{ route('admin.roles.index') }}" class="nav-link" >Rôles</a>
                                        </li>
                                    </ul>
                            </div>
                        </div>
                    </li> --}}





                <li class="nav-item">
                    <a class="nav-link menu-link " href="#">
                        <i class=" ri-shield-user-fill"></i> <span data-key="t-widgets">Settings</span>
                    </a>
                </li>






                <li class="nav-item">
                    <a class="nav-link menu-link " href="#">
                        <i class="ri-slideshow-4-fill"></i> <span data-key="t-widgets">Audit et Logs</span>
                    </a>
                </li>



                <li class="nav-item">
                    <a class="nav-link menu-link " href="#">
                        <i class="ri-notification-4-fill"></i> <span data-key="t-widgets">Notifications</span>
                    </a>
                </li>




                {{-- <li class="nav-item">
                    <a class="nav-link menu-link " href="#sidebarForms" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarForms">
                        <i class="ri-file-list-3-line"></i> <span data-key="t-forms">Autres</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarForms">
                        <ul class="nav nav-sm flex-column">

                            <li class="nav-item">
                                <a href="forms-elements.html" class="nav-link" data-key="t-basic-elements">FAQ
                                    </a>
                            </li>



                            <li class="nav-item">
                                <a href="#" class="nav-link" data-key="t-basic-elements">Politiques de Confidentialités
                                    </a>
                            </li>


                        </ul>
                    </div>
                </li> --}}



                {{-- <li class="menu-title"><i class="ri-more-fill"></i> <span
                    data-key="t-components">PARAMETRES DU SITE</span></li>


                     <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('admin.presentation.index') ? 'active' : ''}}" href="{{ route('admin.presentation.index') }}">
                            <i class="ri-keynote-fill"></i> <span data-key="t-widgets">Présentation</span>
                        </a>
                    </li>





                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('admin.team.index') ? 'active' : ''}}" href="{{ route('admin.team.index') }}">
                            <i class="ri-team-fill"></i> <span data-key="t-widgets">Notre équipe</span>
                        </a>
                    </li>



                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('admin.partenaire.index') ? 'active' : ''}}" href="{{ route('admin.partenaire.index') }}">
                            <i class="ri-briefcase-5-fill"></i> <span data-key="t-widgets">Nos partenaires</span>
                        </a>
                    </li>




                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('admin.contacts.index') ? 'active' : ''}}" href="{{ route('admin.contacts.index') }}">
                            <i class=" ri-shield-user-fill"></i> <span data-key="t-widgets">contacts</span>
                        </a>
                    </li>




                <li class="nav-item">
                    <a class="nav-link menu-link" href="/deconnexion">
                        <i class="ri-logout-box-r-line"></i> <span data-key="t-widgets">Déconnexion</span>
                    </a>
                </li> --}}

            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->

<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
