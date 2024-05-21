@extends('template')

@section('title', 'Información del usuario')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Alta Usuario</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">Usuario</li>
                    <li class="breadcrumb-item active">Nuevo</li>
                </ol> 
            </div>
        </div>
    </div>
</div>

<div class="card-header card-header-tabs d-flex justify-content-between">
    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" data-bs-toggle="tab" href="#datosUsuarios" role="tab" aria-selected="true">
                <i class="ri-window-line"></i>
                Datos de usuario
            </a>
        </li>
        
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#datosPersonales" role="tab" aria-selected="false" tabindex="-1">
                <i class="ri-window-line"></i>
                Datos Personales
            </a>
        </li> 
        
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#roles" role="tab" aria-selected="false" tabindex="-1">
                <i class="ri-window-line"></i>
                Roles
            </a>
        </li> 
        
    </ul>
</div>

<div class="p-4 tab-content">
    <div class="card row tab-pane active" id="datosUsuarios" role="tabpanel">
        <div class="col-12 d-flex  flex-wrap">
            <div class="p-2 col-md-6">
                <label for="usuario" class="form-label font-weight-bold"><strong>Usuario</strong></label>
                <input id="usuario" name="usuario" class="form-control" type="text" value="{{ $query->Name ?? '' }}" @disabled(true)>
            </div>
            <div class="p-2 col-md-6">
                <label for="email" class="form-label font-weight-bold"><strong>Email</strong></label>
                <input id="email" name="email" class="form-control" type="email" value="{{ $query->EMail ?? '' }}">
            </div>
        </div>
        
        <div class="p-3 col-md-12 d-flex justify-content-end ">
            <button class="btn botonGeneral m-2" type="button" id="volver">Volver</button>
            <button class="btn botonGeneral m-2" type="button" id="cambiarEmail">Actualizar</button>
        </div>
    </div>

    <div class="card row tab-pane" id="datosPersonales" role="tabpanel">
        <form id="form-update">
        <div class="col-12 d-flex  flex-wrap">
            <div class="p-2 col-md-6">
                <label for="nombre" class="form-label font-weight-bold"><strong>Nombre <i class="text-danger">*</i></strong></label>
                <input id="nombre" name="nombre" class="form-control" type="text" value="{{ $query->Nombre ?? '' }}">
                <input type="hidden" id="Id" value="{{ $query->Id ?? 0 }}">
            </div>
            <div class="p-2 col-md-6">
                <label for="apellido" class="form-label font-weight-bold"><strong>Apellido  <i class="text-danger">*</i></strong></label>
                <input id="apellido" name="apellido" class="form-control" type="text" value="{{ $query->Apellido ?? '' }}">
            </div>


            <div class="p-2 col-md-6">
                <label for="tipoDoc" class="form-label font-weight-bold"><strong>Tipo Documento <i class="text-danger">*</i></strong></label>
                <select id="tipoDoc" name="tipoDoc" class="form-select font-weight-bold" >
                    <option value="{{ $query->TipoDocumento ?? '' }}" selected>{{ $query->TipoDocumento ?? 'Elija una opción...' }}</option>
                    <option value="DNI">DNI</option>
                    <option value="CF">CF</option>
                    <option value="LC">LC</option>
                    <option value="LE">LE</option>
                    <option value="PS">PS</option>
                </select>
            </div>

            <div class="p-2 col-md-6">
                <label for="numeroDoc" class="form-label font-weight-bold"><strong>Numero de Documento <i class="text-danger">*</i></strong></label>
                <input id="numeroDoc" name="numeroDoc" class="form-control" type="text" value="{{ $query->Documento ?? '' }}">
            </div>
            
            <div class="p-2 col-md-6">
                <label for="cuil" class="form-label font-weight-bold"><strong>CUIL <i class="text-danger">*</i></strong></label>
                <select id="cuil" name="cuil" class="form-select font-weight-bold" >
                    <option value="{{ $query->TipoIdentificacion ?? '' }}" selected>{{ $query->TipoIdentificacion ?? 'Elija una opción...' }}</option>
                    <option value="CUIT" >CUIT</option>
                    <option value="CUIL" selected>CUIL</option>   
                </select>
            </div>

            <div class="p-2 col-md-6">
                <label for="numeroCUIL" class="form-label font-weight-bold"><strong>Numero de Cuil/Cuit <i class="text-danger">*</i></strong></label>
                <input id="numeroCUIL" name="numeroCUIL" class="form-control" type="text" value="{{ $query->Identificacion ?? '' }}" placeholder="xx-xxxxxxxx-x">
            </div>

            <div class="p-2 col-md-6">
                <label for="numTelefono" class="form-label font-weight-bold"><strong>Número de Telefono</strong></label>
                <input id="numTelefono" name="numTelefono" class="form-control" type="text" value="{{ $query->Telefono ?? '' }}">
            </div>
            <div class="p-2 col-md-6">
                <label for="fechaNac" class="form-label font-weight-bold"><strong>Fecha de Naciemiento</strong></label>
                <input id="fechaNac" name="fechaNac" class="form-control" type="date" value="{{ $query->FechaNacimiento ?? '' }}">
            </div>
            
            <!-- Agregar las provincias en base al lugar de nacimiento -->
            <div class="p-2 col-md-4">
                <label for="provincia" class="form-label font-weight-bold"><strong>Provincia </strong></label>
                <select id="provincia" name="provincia" class="form-select font-weight-bold" >
                    <option value="{{ $query->Provincia ?? ''}}" selected>{{ $query->Provincia ?? 'Seleccionar una opción...' }}</option>
                    @foreach ($provincias as $provincia)
                        <option value="{{ $provincia->Nombre }}">{{ $provincia->Nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="p-2 col-md-4">
                <label for="localidad" class="form-label font-weight-bold"><strong>Localidad </strong></label>
                <select id="localidad" name="localidad" class="form-select font-weight-bold" >
                    <option value="{{ $query->ILocalidad ?? ''}}" selected>{{ $query->NombreLocalidad ?? 'Seleccionar una opción...' }}</option>
                </select>
            </div>

            <div class="p-2 col-md-4">
                <label for="codPostal" class="form-label font-weight-bold">Codigo Postal</label>
                <input class="form-control" id="codPostal" type="text" value="{{ $query->CP }}" @readonly(true)>
            </div>

            <div class="p-2 col-md-6">
                <label for="direccion" class="form-label font-weight-bold">Dirección</label>
                <input class="form-control" id="direccion" type="text" value="{{ $query->Direccion ?? ''}}">
            </div>
        </div>
        
        <div class="p-3 col-md-12 d-flex justify-content-end ">
            <button class="btn botonGeneral m-2 updateDatos">Actualizar</button>
        </div>

    </form>
    </div>

     <div class="card row tab-pane" id="roles" role="tabpanel">
        <div class="row">
            <div class="col-sm-9"></div>
            <div class="col-sm-3 p-2 d-flex justify-content-end">
                <select class="form-control" name="listaRoles" id="listaRoles">
                    <option value="" selected>Elija un rol para aplicar...</option>
                    @foreach($roles as $rol)
                        <option value="{{ $rol->Id ?? 0}}">{{ $rol->nombre ?? ''}}</option>
                    @endforeach
                </select>
                <button class="btn btn-sm botonGeneral agregarRol" type="button" data-id="{{ $query->UserId ?? 0 }}"> Agregar</button>
            </div>
        </div>
        <div class="table-card table-responsive mt-3 mb-1 mx-auto">
            <table id="listadoRolesAsignados" class="display table table-bordered ">
                <thead class="table-light">
                    <tr>
                        <th class="sort">Rol</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="list form-check-all" id="lstRolesAsignados">
                </tbody>
            </table>
        </div>

          
        <!-- End Roles permisos -->
    </div>
</div>

<script>
    const ROUTE = "{{ route('listadoRoles') }}";
    const getLocalidad = "{{ route('getLocalidades') }}";
    const getCodigoPostal = "{{ route('getCodigoPostal') }}";
    const TOKEN = '{{ csrf_token() }}';
    const actualizarDatos = "{{ route('actualizarDatos') }}";
    const ID = "{{ $query->UserId }}";
    const lstRolAsignados = "{{ route('lstRolAsignados') }}";
    const checkEmailUpdate = "{{ route('checkEmailUpdate') }}";
    const verificarCorreo = "{{ $query->EMail ?? '' }}";
    const addRol = "{{ route('addRol') }}";
    const deleteRol = "{{ route('deleteRol') }}";
    const INDEX = "{{ route('usuarios.index') }}";
</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">
@endpush

@push('scripts')
<!--datatable js-->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/auth/edit.js')}}?v={{ time() }}"></script>
<script src="{{ asset('js/auth/validaciones.js')}}?v={{ time() }}"></script>


<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>

@endpush

@endsection


