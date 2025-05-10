<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!--<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="light" data-sidebar-size="sm-hover" data-sidebar-image="none" data-preloader="enabled" data-layout-width="fluid">-->
    <html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="light" data-sidebar-size="sm" data-sidebar-image="none" data-preloader="enable" data-sidebar-visibility="show" data-layout-style="default" data-layout-mode="light" data-layout-width="fluid" class="__web-inspector-hide-shortcut__">
<head>

    <meta charset="utf-8" />
    <title>@yield('title') | Salud Ocupacional SRL</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link rel="mask-icon" href="{{ asset('images/safari-pinned-tab.svg') }}" color="#5bbad5">

    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    @stack('styles')

    <link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('css/multi.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/autoComplete.css') }}">

    <script src="{{ asset('js/layout.js') }}"></script>
    <link href="{{ asset('css/bootstrap.min.css') }}?v={{ time() }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/icons.min.css') }}?v={{ time() }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/app.min.css') }}?v={{ time() }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/custom.min.css') }}?v={{ time() }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/screen.css') }}?v={{ time() }}" rel="stylesheet" type="text/css" />

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/basicos.js') }}?v={{ time() }}"></script>

    @vite('default')
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
                                    <img src="{{ asset('images/logo-intranet-sm.png') }}?v={{ time () }}" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('images/logo-intranet.png') }}?v={{ time () }}" alt="" height="17">
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

                        <div class="ms-1 header-item d-none d-sm-flex">
                            <button id="prestacionButton" type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle ms-2" title="Prestación rápida (ALT+ P)" data-bs-toggle="offcanvas" data-bs-target="#prestacionFast" aria-controls="offcanvas">
                                <img src="{{ asset('images/iconos/pacientes.svg')}}" alt="Alta prestación rápida" width="40px" height="40px">
                            </button>
                            <a class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle ms-2" title="Pacientes (ALT + A)" href="{{ route('prestaciones.index')}}">
                                <img src="{{ asset('images/iconos/prestaciones.svg')}}" alt="Grilla prestaciones" width="40px" height="40px">
                            </a>
                            <a class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle ms-2" title="Etapas (ALT + S)" href="{{ route('ordenesExamen.index')}}">
                                <img src="{{ asset('images/iconos/etapas.svg')}}" alt="Etapas" width="40px" height="40px">
                            </a>
                            <a class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle ms-2" title="Carnet" href="#">
                                <img src="{{ asset('images/iconos/carnet.svg')}}" alt="Carnet" width="40px" height="40px">
                            </a>
                            <a class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle ms-2" title="Efector" href="{{ route('llamador.efector') }}">
                                <img src="{{ asset('images/iconos/efector.svg')}}" alt="Efector" width="35px" height="35px">
                            </a>
                            <a class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle ms-2" title="Informador" href="{{ route('llamador.informador') }}">
                                <img src="{{ asset('images/iconos/informador.svg')}}" alt="Informador" width="35px" height="35px">
                            </a>
                            <a class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle ms-3" title="Combinado" href="#">
                                <img src="{{ asset('images/iconos/combinado.svg')}}" alt="Combinado" width="52px" height="52px">
                            </a>
                            <a class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle ms-3" title="Evaluador" href="{{ route('llamador.evaluador') }}">
                                <img src="{{ asset('images/iconos/evaluador.svg')}}" alt="Evaluador" width="40px" height="40px">
                            </a>
                        </div>

                        <div class="dropdown ms-sm-3 header-item topbar-user">
                            <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="d-flex align-items-center">
                                    <img class="rounded-circle header-profile-user" src="{{ asset('images/users/cmit.jpg') }}" alt="Header Avatar">
                                    <span class="text-start ms-xl-2">
                                        <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ ucfirst(Auth::user()->name) }} 
                                        </span>
                                    </span>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <h6 class="dropdown-header">Bienvenido {{ Auth::user()->name }}!</h6>
                                @foreach (Auth::user()->role as $rol)
                                    <h6><span class="dropdown-item badge text-bg-info small fw-bolder">{{ $rol->nombre }}</span></h6>
                                @endforeach
                                <a class="dropdown-item" href="{{ route('perfil')}}"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Perfil</span></a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Salir</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="app-menu navbar-menu">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <!-- Dark Logo-->
                <a href="{{route('noticias.index')}}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('images/logo-intranet-sm.png') }}" alt="Salud Ocupacionl SRL" >
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('images/logo-intranet.png') }}" alt="Salud Ocupacionl SRL" >
                    </span>
                </a>
                <a href="{{ route('home') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('images/logo-intranet-sm.png') }}" alt="Salud Ocupacionl SRL" >
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('images/logo-intranet.png') }}" alt="Salud Ocupacionl SRL" >
                    </span>
                </a>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
                    <i class="ri-anticlockwise-2-line" title="Fijar o Esconder menú"></i>
                </button>
            </div>

            <div id="scrollbar">
                <div class="container-fluid">

                    <div id="two-column-menu">
                    </div>
                    <ul class="navbar-nav" id="navbar-nav">
                        <li class="menu-title"><span data-key="t-menu">Menu</span></li>

                        <li class="nav-item">
                            <a class="nav-link menu-link collapsed" href="#sidebarOperaciones" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarConfiguracion">
                                <i data-feather="tool" class="icon-dual"></i> <span data-key="t-operaciones">Operaciones</span>
                            </a>
                            <div class="menu-dropdown mega-dropdown-menu collapse" id="sidebarOperaciones">
                                <ul class="nav nav-sm flex-column">
                                    @can('prestaciones_show')
                                    <li class="nav-item">
                                        <a href="{{ route('prestaciones.index') }}" class="nav-link enlace-blanco" data-key="t-prestaciones"> Prestaciones </a>
                                    </li>
                                    @endcan
                                    @can('etapas_show')
                                    <li class="nav-item">
                                        <a href="{{ route('ordenesExamen.index') }}" class="nav-link enlace-blanco" data-key="t-etapas"> Etapas </a>
                                    </li>
                                    @endcan
                                    @can("mapas_show")
                                    <li class="nav-item">
                                        <a href="{{ route('mapas.index') }}" class="nav-link enlace-blanco" data-key="t-mapas"> Mapas </a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link collapsed" href="#sidebarTablas" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarTablas">
                                <i data-feather="square" class="icon-dual"></i> <span data-key="t-tablas">Tablas</span>
                            </a>
                            <div class="menu-dropdown mega-dropdown-menu collapse" id="sidebarTablas">
                                <ul class="nav nav-sm flex-column">
                                    @can('pacientes_show')
                                    <li class="nav-item">
                                        <a href="{{ route('pacientes.index') }}" class="nav-link enlace-blanco" data-key="t-pacientes"> Pacientes </a>
                                    </li>
                                    @endcan
                                    @can("especialidades_show")
                                    <li class="nav-item">
                                        <a href="{{ route('especialidades.index') }}" class="nav-link enlace-blanco" data-key="t-especialidades"> Especialidades </a>
                                    </li>
                                    @endcan
                                    @can("examenes_show")
                                    <li class="nav-item">
                                        <a href="{{ route('examenes.index') }}" class="nav-link enlace-blanco" data-key="t-examenes"> Examenes </a>
                                    </li>
                                    @endcan
                                    @can('clientes_show')
                                    <li class="nav-item">
                                        <a href="{{ route('clientes.index') }}" class="nav-link enlace-blanco" data-key="t-cliente"> Clientes </a>
                                    </li>
                                    @endcan
                                    @can('boton_usuarios')
                                    <li class="nav-item">
                                        <a href="{{ route('usuarios.index') }}" class="nav-link enlace-blanco" data-key="t-usuarios"> Usuarios </a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link collapsed" href="#sidebarVentas" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarVentas">
                                <i data-feather="dollar-sign" class="icon-dual"></i> <span data-key="t-ventas">Ventas</span>
                            </a>
                            <div class="menu-dropdown mega-dropdown-menu collapse" id="sidebarVentas">
                                <ul class="nav nav-sm flex-column">
                                    @can("examenCta_show")
                                    <li class="nav-item">
                                        <a href="{{ route('examenesCuenta.index')}}" class="nav-link enlace-blanco" data-key="t-ExCuenta"> Ex. a Cta </a>
                                    </li>
                                    @endcan
                                    @can("facturacion_show")
                                    <li class="nav-item">
                                        <a href="{{ route('facturas.index') }}" class="nav-link enlace-blanco" data-key="t-factura"> Facturación </a>
                                    </li>
                                    @endcan
                                    @can("notaCredito_show")
                                    <li class="nav-item">
                                        <a href="#" class="nav-link enlace-blanco" data-key="t-notaCredito"> Nota de crédito </a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link collapsed" href="#sidebarGeneral" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarGeneral">
                                <i data-feather="folder" class="icon-dual"></i> <span data-key="t-general">General</span>
                            </a>
                            <div class="menu-dropdown mega-dropdown-menu collapse" id="sidebarGeneral">
                                <ul class="nav nav-sm flex-column">
                                    @can("mensajeria_show")
                                    <li class="nav-item">
                                        <a href="{{ route('mensajes.index') }}" class="nav-link enlace-blanco" data-key="t-Mensajeria"> Mensajeria </a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>

                        @can('noticias_edit')
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('noticias.edit', 1) }}" aria-expanded="false">
                                <i data-feather="message-circle" class="icon-dual"></i> <span data-key="t-layers" title="(ALT + N)">Noticias</span>
                            </a>
                        </li>
                        @endcan
                        
                    </ul>
                </div>
            </div>

            <div class="sidebar-background"></div>
        </div>

        <div class="vertical-overlay"></div>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>

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

    </div>



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

    @stack('modal')

    <div class="offcanvas offcanvas-top" tabindex="-1" id="prestacionFast" aria-labelledby="prestacionFastLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="prestacionFastLabel">Prestación rápida:</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="center">
                <div class="row">
                    <div class="col-xl-3"></div>
                    <div class="col-xl-6 d-flex justify-content-center align-items-center p-3 rounded" style="border: solid 1px #3c74b3">
                        <label class="form-label" style="color: #5484bc; font-size: 1.3em; margin:auto 1em;">DNI: </label>
                        <input type="number" class="form-control" placeholder="35458753" tabindex="1" id="dniPrestacion">
                        <button type="button" class="btn btn-primary d-inline-flex" id="btnWizardPrestacion" style="margin-left: 5px" tabindex="2"><i class="ri-search-2-line"></i>&nbsp;Buscar</button>
                    </div>
                <div class="col-xl-3"></div>
                </div>
            </div>
        </div>
    </div>

    <script>

        const choisePerfil = "{{ route('choisePerfil')}}";
        const choiseEspecialidad = "{{ route('choiseEspecialidad') }}";
        const savePrestador = "{{ route('savePrestador') }}";

        const lnkPacientes= "{{ route('pacientes.index') }}";
        const lnkClientes= "{{ route('clientes.index') }}";
        const lnkPrestaciones = "{{ route('prestaciones.index') }}";
        const lnkMapas = "{{ route('mapas.index') }}";
        const lnkEspecialidades = "{{ route('especialidades.index') }}";
        const lnkNoticias = "{{ route('noticias.index') }}";
        const lnkExamenes = "{{ route('examenes.index') }}";
        const lnkEtapas = "{{ route('ordenesExamen.index') }}";

        const lnkNuevoPaciente = "{{ route('pacientes.create') }}";
        const lnkExistePaciente = "{{ route('pacientes.edit', ['paciente' => '__paciente__']) }}";
        const verifyWizard = "{{ route('verifyWizard') }}";
        const TOKEN = "{{ csrf_token() }}";
        const tiempoSesion = {{ config('session.lifetime') * 60 * 1000 }};

        let idleTimeout;

        function resetIdleTimeout() {
            clearTimeout(idleTimeout);
            idleTimeout = setTimeout(function () {       
                cerrarSesion();
            }, tiempoSesion);
        }

        function cerrarSesion() {
            $.post(SALIR, {_token: TOKEN}, function(response){
                if (response.redirect) {
                    window.location.href = response.redirect;
                } else {
                    window.location.href = "{{ route('logout') }}";
                }
            })
        }

        // Reiniciar el temporizador
        $(document).on('mousemove keydown scroll', function () {
            resetIdleTimeout();
        });

        // Iniciar el temporizador con Hack
        $(document).ready(function () {
            resetIdleTimeout();
        });

    </script>

    <script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script src="{{ asset('js/toastify-js.js') }}"></script>
    <script src="{{ asset('js/choices.min.js') }}"></script>
    <script src="{{ asset('js/flatpickr.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>

    {{-- <script src="{{ asset('libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('libs/jsvectormap/maps/world-merc.js') }}"></script>
    <script src="{{ asset('libs/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('js/pages/dashboard-ecommerce.init.js') }}"></script> --}}

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/scripts.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/auth/template.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/atajos.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/wizardPrestaciones.js') }}?v={{ time() }}"></script>

    <script src="{{ asset('js/errores.js') }}?v={{ time() }}"></script>
    @stack('scripts')
</body>

</html>