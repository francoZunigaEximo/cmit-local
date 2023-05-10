<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="light" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title>@yield('title') | Sistema Ocupacional SRL</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link rel="mask-icon" href="{{ asset('images/safari-pinned-tab.svg') }}" color="#5bbad5">

    <link href="{{ asset('libs/jsvectormap/css/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />

    <script src="{{ asset('js/layout.js') }}"></script>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/custom.min.css') }}" rel="stylesheet" type="text/css" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
</head>

<body>
    <div id="layout-wrapper">

        <header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <div class="navbar-brand-box horizontal-logo">
                    <a href="{{ route('home') }}" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{ asset('images/logo-intranet-sm.png') }}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('images/logo-intranet.png') }}" alt="" height="17">
                        </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger" id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>

            </div>

            <div class="d-flex align-items-center">

                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user" src=" {{ asset('images/users/avatar-1.jpg') }}" alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ Auth::user()->name }}</span>
                                <span class="d-none d-xl-block ms-1 fs-12 text-muted user-name-sub-text">Administrador</span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <h6 class="dropdown-header">Bienvenido {{ Auth::user()->name }}!</h6>
                        <a class="dropdown-item" href="{{ route('changePassword')}}"><i class="mdi mdi-settings-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Cambiar Constraseña</span></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('logout')}}"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Salir</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

        <!-- ========== App Menu ========== -->
        <div class="app-menu navbar-menu">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <!-- Dark Logo-->
                <a href="{{ route('home') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('images/logo-intranet-sm.png') }}" alt="Salud Ocupacionl SRL" >
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('images/logo-intranet.png') }}" alt="Salud Ocupacionl SRL" >
                    </span>
                </a>
                <!-- Light Logo-->
                <a href="{{ route('home') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('images/logo-intranet-sm.png') }}" alt="Salud Ocupacionl SRL" >
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('images/logo-intranet.png') }}" alt="Salud Ocupacionl SRL" >
                    </span>
                </a>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
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
                            <a class="nav-link menu-link" href="#sidebarClientes" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarClientes">
                                <i data-feather="user" class="icon-dual"></i> <span data-key="t-user">Clientes</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarClientes">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ route('clientes') }}" class="nav-link" data-key="t-clientes"> Clientes </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('pacientes') }}" class="nav-link" data-key="t-pacientes"> Pacientes </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="{{ route('grupos') }}" class="nav-link"> <span data-key="t-grupos">Grupos</span></a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarProveedores" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarProveedores">
                                <i data-feather="package" class="icon-dual"></i> <span data-key="t-package">Proveedores</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarProveedores">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ route('profesionales') }}" class="nav-link" data-key="t-profesionales"> Profesionales </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('proveedores') }}" class="nav-link" data-key="t-proveedores"> Proveedores </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="{{ route('examenes') }}" class="nav-link"> <span data-key="t-examenes">Examenes</span></a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarPrestaciones" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarPrestaciones">
                                <i data-feather="check-square" class="icon-dual"></i> <span data-key="t-check-square">Prestaciones</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarPrestaciones">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ route('prestaciones') }}" class="nav-link" data-key="t-prestaciones"> Prestaciones </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('carnet') }}" class="nav-link" data-key="t-carnet"> Carnet </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('pcr') }}" class="nav-link"> <span data-key="t-pcr">PCR</span></a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('constancias') }}" class="nav-link"> <span data-key="t-pcr">Constancias</span></a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('placas') }}" class="nav-link"> <span data-key="t-placas">Placas</span></a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('entregadas') }}" class="nav-link"> <span data-key="t-entregadas">Entregadas</span></a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                       

                    </ul>
                </div>
                <!-- Sidebar -->
            </div>

            <div class="sidebar-background"></div>
        </div>
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                @yield('content')

            </div>
            <!-- End Page-content -->

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>document.write(new Date().getFullYear())</script> © Salud Ocupacional SRL.
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Desarrollado por Eximo SA
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- end main content-->

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



    <script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('js/pages/plugins/lord-icon-2.1.0.js') }}"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script type="text/javascript" src="{{ asset('libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('libs/flatpickr/flatpickr.min.js') }}"><\/script>"));

    <script src="{{ asset('libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('libs/jsvectormap/maps/world-merc.js') }}"></script>
    <script src="{{ asset('libs/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('js/pages/dashboard-ecommerce.init.js') }}"></script>

    <script src="{{ asset('js/app.js') }}"></script>

    @stack('scripts')
</body>

</html>