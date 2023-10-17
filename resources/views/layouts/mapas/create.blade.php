@extends('template')

@section('title', 'Crear un mapa')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Crear un nuevo mapa</h4>

    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="{{ route('mapas.index') }}">Mapas</a></li>
            <li class="breadcrumb-item active">Nuevo Mapa</li>
        </ol>
    </div>
</div>
         
<div class="card-header">
    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" data-bs-toggle="tab" href="#mapasitem" role="tab" aria-selected="true">
                Mapa
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#prestaciones" role="tab" aria-selected="false" tabindex="-1">
                Prestaciones
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#remitos" role="tab" aria-selected="false" tabindex="-1">
                Remitos
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#cerrar" role="tab" aria-selected="false" tabindex="-1">
                <span class="custom-badge azul">
                    <i class="ri-lock-2-line"></i>
                    Cerrar
                </span>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#finalizar" role="tab" aria-selected="false" tabindex="-1">
                <span class="custom-badge nuevoAmarillo">
                    <i class="ri-lock-2-line"></i>
                    Finalizar
                </span>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link text-info" data-bs-toggle="tab" href="#eenviar" role="tab" aria-selected="false" tabindex="-1">
                <span class="custom-badge nuevoAzul">
                    <i class="ri-lock-2-line"></i>
                    eEnviar
                </span>
            </a>
        </li>
    </ul>
</div>

<div class="card-body p-4">
    <div class="tab-content">

        <div class="tab-pane active" id="mapasitem" role="tabpanel">
            <div id="messageMapas"></div>

            <form id="form-create" action="{{ route('mapas.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            <div class="row">

                <div class="col-2 p-2 mb-2">
                        <label for="Nro" class="form-label">ART</label>
                        <input type="text" class="form-control" id="Nro" name="Nro" placeholder="#">
                </div>
                
                <div class="col-4 p-2 mb-2">
                    <label for="ART" class="form-label"> <br>  </label>
                    <select class="form-select" name="IdART" id="IdART"></select>
                </div>

                <div class="col-6 p-2 mb-2">
                    <label for="Empresa" class="form-label">Empresa</label>
                    <select class="form-select" id="IdEmpresa" name="IdEmpresa"></select>
                </div>

                <div class="col-6 p-2 mb-2">
                    <label for="Fecha" class="form-label">Fecha de Corte</label>
                    <input type="date" class="form-control" id="Fecha" name="Fecha">
                </div>

                <div class="col-6 p-2 mb-2">
                    <label for="FechaE" class="form-label">Fecha de Entrega</label>
                    <input type="date" class="form-control" id="FechaE" name="FechaE">
                </div>

                <div class="col-6 p-2 mb-2">
                    <label for="Estado" class="form-label">Estado </label>
                    <select class="form-select" name="Estado" id="Estado">
                        <option value="" selected>Elija una opción...</option>
                        <option value="0">Activo</option>
                        <option value="1">Inactivo</option>
                    </select>
                </div>


                <div class="col-6 p-2 mb-2">
                    <label for="Cpacientes" class="form-label">Cantidad de pacientes </label>
                    <input type="text" class="form-control" id="Cpacientes" name="Cpacientes">
                </div>

                <div class="col-12 p-2 mb-2">
                    <label for="Observaciones" class="form-label">Observaciones </label>
                    <textarea class="form-control" name="Obs" id="Obs" rows="4"></textarea>
                </div>

                <div class="col-lg-12 mt-3">
                    <div class="hstack gap-2 justify-content-end">
                        
                        <button type="submit" class="btn btn-success" id="crearMapa">Guardar</button>
                    </div>
                </div>

            </div>
            </form>

            <div class="table-responsive table-card mt-3 mb-1">
                <table id="listaMapas" class="display table table-bordered" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>En proceso</th>
                            <th>Con estados</th>
                            <th>Completas</th>
                            <th>Cerradas</th>
                            <th>Finalizadas</th>
                            <th>Entregadas</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody class="list form-check-all">

                    </tbody>
                </table>
            </div>

        </div>

        <div class="tab-pane" id="prestaciones" role="tabpanel">

            <div class="alert alert-dark" role="alert">
                <strong> ¡Atención! </strong> Debe generar un mapa para habilitar la opción o encontrarse en modo de visualización.
            </div>
        </div>

        <div class="tab-pane" id="remitos" role="tabpanel">
            <div class="alert alert-dark" role="alert">
                <strong> ¡Atención! </strong> Debe generar un mapa para habilitar la opción o encontrarse en modo de visualización.
            </div>
        </div>

        <div class="tab-pane" id="cerrar" role="tabpanel">
            <div class="alert alert-dark" role="alert">
                <strong> ¡Atención! </strong> Debe generar un mapa para habilitar la opción o encontrarse en modo de visualización.
            </div>
        </div>

        <div class="tab-pane" id="finalizar" role="tabpanel">
            <div class="alert alert-dark" role="alert">
                <strong> ¡Atención! </strong> Debe generar un mapa para habilitar la opción o encontrarse en modo de visualización.
            </div>
        </div>

        <div class="tab-pane" id="eenviar" role="tabpanel">
            <div class="alert alert-dark" role="alert">
                <strong> ¡Atención! </strong> Debe generar un mapa para habilitar la opción.
            </div>
        </div>

    </div>
</div>

<script>
//Rutas
const getClientes = "{{ route('getClientes') }}";
const checkMapa = "{{ route('checkMapa') }}";
</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/mapas/create.js')}}?v={{ time() }}"></script>
<script src="{{ asset('js/mapas/utils.js')}}?v={{ time() }}"></script>
<script src="{{ asset('js/mapas/validaciones.js')}}?v={{ time() }}"></script>
<script src="{{ asset('js/scripts.js') }}?v={{ time() }}"></script>
<script src="{{ asset('libs/cleave.js/cleave.min.js') }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
@endpush

@endsection