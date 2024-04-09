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
    </ul>
</div>

<div class="card-body p-4">
    <div class="tab-content">

        <div class="tab-pane active" id="mapasitem" role="tabpanel">
            <div id="messageMapas"></div>
            <form id="form-create" action="{{ route('mapas.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                <div class="row">
                    <div class="col-4 box-information">

                        <div class="input-group input-group-sm mb-2 size80porcent">
                            <span class="input-group-text">Cod Mapa&nbsp;<span class="required">(*)</span></span>
                            <input type="text" class="form-control" id="Nro" name="Nro">
                        </div>

                        <div class="input-group input-group-sm mb-2 selectSize">
                            <span class="input-group-text">ART&nbsp;<span class="required">(*)</span></span>
                            <select class="form-select" name="IdART" id="IdART">
                            </select>
                        </div>

                        <div class="input-group input-group-sm mb-2 selectSize">
                            <span class="input-group-text">Empresa&nbsp;<span class="required">(*)</span></span>
                            <select class="form-select" id="IdEmpresa" name="IdEmpresa">
                            </select>
                        </div>

                        <div class="input-group input-group-sm size80porcent">
                            <span class="input-group-text">Cant Total de Pacientes&nbsp;<span class="required">(*)</span></span>
                            <input type="number" class="form-control" id="Cpacientes" name="Cpacientes">
                        </div>

                    </div>
  
                    <div class="col-4 box-information">
                        <div class="input-group input-group-sm mb-2 size80porcent">
                            <span class="input-group-text">Fecha de Corte&nbsp;<span class="required">(*)</span></span>
                            <input type="date" class="form-control" id="Fecha" name="Fecha">
                        </div>

                        <div class="input-group input-group-sm mb-2 size80porcent">
                            <span class="input-group-text">Fecha de Entrega&nbsp;<span class="required">(*)</span></span>
                            <input type="date" class="form-control" id="FechaE" name="FechaE">
                        </div>

                        <div class="input-group input-group-sm  size80porcent">
                            <span class="input-group-text">Estado&nbsp;<span class="required">(*)</span></span>
                            <select class="form-select" name="Estado" id="Estado">
                                <option value="" selected>Elija una opción...</option>
                                <option value="0">Activo</option>
                                <option value="1">Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-3 box-information">
                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text">Observaciones</span>
                            <textarea class="form-control" name="Obs" id="Obs" rows="8"></textarea>
                        </div>
                    </div>

                    <div class="col-12 box-information mt-2 text-center">
                        <button type="submit" class="btn botonGeneral" id="crearMapa"><i class="ri-save-line"></i>Guardar</button>
                    </div>
                </div>
            </form>
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