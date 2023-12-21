<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="light" data-sidebar-size="sm-hover" data-sidebar-image="none" data-preloader="disable" data-layout-width="fluid">

<head>

    <meta charset="utf-8" />
    <title>@yield('title') | Sistema Ocupacional SRL</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link rel="mask-icon" href="{{ asset('images/safari-pinned-tab.svg') }}" color="#5bbad5">

    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">

    <link href="{{ asset('libs/jsvectormap/css/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    @stack('styles')

    <link href="{{ asset('libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('css/multi.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/autoComplete.css') }}">

    <script src="{{ asset('js/layout.js') }}"></script>
    <link href="{{ asset('css/bootstrap.min.css') }}?v={{ time() }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/icons.min.css') }}?v={{ time() }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/app.min.css') }}?v={{ time() }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/custom.min.css') }}?v={{ time() }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/screen.css') }}?v={{ time() }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">


    <script src="{{ asset('js/jquery.min.js') }}"></script>
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
                    <button id="prestacionButton" type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" title="Prestación rápida (Alt + P)" data-bs-toggle="offcanvas" data-bs-target="#prestacionFast" aria-controls="offcanvas">
                        <i data-feather="users"></i>
                    </button>
                </div>

                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ ucfirst(Auth::user()->name) }} <h6><span class="badge text-bg-info">{{ (session('choiseT') === '0' ? strtoupper(Auth::user()->Rol) : session('choiseT')) }}</span></h6>
                                
                                @if(Auth::user()->Rol === 'Prestador' && Auth::user()->profesional->TLP === 1)
                                    
                                <button type="button" data-bs-toggle="modal" data-bs-target="#choisePModal" class="btn btn-primary btn-label rounded-pill"><i class=" ri-anticlockwise-line label-icon align-middle rounded-pill fs-16 me-2"></i> Cambiar perfil</button>
                                @endif
                                </span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <h6 class="dropdown-header">Bienvenido {{ Auth::user()->name }}!</h6>
                        <a class="dropdown-item" href="{{ route('changePassword')}}"><i class="mdi mdi-settings-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Cambiar Constraseña</span></a>
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
                            <a class="nav-link menu-link" href="{{ route('pacientes.index') }}" aria-expanded="false">
                                <i data-feather="heart" class="icon-dual"></i> <span data-key="t-user" title="(ALT + A)">Pacientes</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('clientes.index') }}" aria-expanded="false">
                                <i data-feather="layers" class="icon-dual"></i> <span data-key="t-user" title="(ALT + C)">Clientes</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('prestaciones.index') }}" aria-expanded="false">
                                <i data-feather="users" class="icon-dual"></i> <span data-key="t-layers" title="(ALT + R)">Prestaciones</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('mapas.index') }}" aria-expanded="false">
                                <i data-feather="map" class="icon-dual"></i> <span data-key="t-layers" title="(ALT + M)">Mapas</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('profesionales.index') }}" aria-expanded="false">
                                <i data-feather="user-check" class="icon-dual"></i> <span data-key="t-layers" title="(ALT + O)">Profesionales</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('especialidades.index') }}" aria-expanded="false">
                                <i data-feather="list" class="icon-dual"></i> <span data-key="t-layers" title="(ALT + E)">Especialidades</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="{{ route('noticias.edit', 1) }}" aria-expanded="false">
                                <i data-feather="message-circle" class="icon-dual"></i> <span data-key="t-layers">Noticias</span>
                            </a>
                        </li>
                        
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

    <div id="choisePModal" class="modal fadeInUp" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Seleccione el perfil del profesional</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>   
                </div>
                <div class="modal-body">
                    <div class="message-sesion"></div>
                    <form>
                        <div class="mb-3">
                            <label for="choisePerfil" class="col-form-label">Perfil</label>
                            <select class="form-control" name="choisePerfil" id="choisePerfil">
                                <option value="" selected>Elija una opción...</option>
                               
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="choiseEspecialidad" class="col-form-label">Especialidad</label>
                            <select class="form-control" name="choiseEspecialidad" id="choiseEspecialidad">
                                <option value="" selected>Elija una opción...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <button type="button" class="btn btn-primary cargarPrestador">Seleccionar</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                </div>
    
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

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
        let mprof = "{{ session('mProf') }}";
        let choiseT = "{{ session('choiseT') }}";

        const IDSESSION = "{{ Auth::user()->IdProfesional }}";
        let tlp = "{{ Auth::user()->profesional->TLP }}";

        const choisePerfil = "{{ route('choisePerfil')}}";
        const choiseEspecialidad = "{{ route('choiseEspecialidad') }}";
        const savePrestador = "{{ route('savePrestador') }}";

        const lnkPacientes= "{{ route('pacientes.index') }}";
        const lnkClientes= "{{ route('clientes.index') }}";
        const lnkPrestaciones = "{{ route('prestaciones.index') }}";
        const lnkMapas = "{{ route('mapas.index') }}";
        const lnkProfesionales = "{{ route('profesionales.index') }}";
        const lnkEspecialidades = "{{ route('especialidades.index') }}";

        const lnkNuevoPaciente = "{{ route('pacientes.create') }}";
        const lnkExistePaciente = "{{ route('pacientes.edit', ['paciente' => '__paciente__']) }}";
        const verifyWizard = "{{ route('verifyWizard') }}";
        const SALIR = "{{ route('logout') }}";
        const tiempoSesion = {{ config('session.lifetime') * 60 * 1000 }};

        let idleTimeout;

        function resetIdleTimeout() {
            clearTimeout(idleTimeout);
            idleTimeout = setTimeout(function () {
                
                window.location.href = SALIR;
            }, tiempoSesion);
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

    <script src="{{ asset('libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('libs/jsvectormap/maps/world-merc.js') }}"></script>
    <script src="{{ asset('libs/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('js/pages/dashboard-ecommerce.init.js') }}"></script>

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/scripts.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/auth/template.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/atajos.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/wizardPrestaciones.js') }}?v={{ time() }}"></script>
    @stack('scripts')
</body>

</html>