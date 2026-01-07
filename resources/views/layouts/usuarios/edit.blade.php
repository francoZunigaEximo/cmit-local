@extends('template')

@section('title', 'Información del usuario')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Editar Usuario</h4>
        </div>

        <div class="alert alert-warning verAlerta" role="alert">
            <strong> Atención: </strong> Los actualización de los datos profesionales se encontrarán disponibles si el usuario es efector, informador, combinado o evaluador.
        </div>   
        <a class="btn btnSuccess " href="{{ route('usuarios.index') }}"><i class="ri-arrow-left-line"></i>&nbsp;Volver</a>
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

        <li class="nav-item verOpciones" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#opciones" role="tab" aria-selected="false" tabindex="-1">
                <i class="ri-window-line"></i>
                Opciones
            </a>
        </li>

        <li class="nav-item verOpciones" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#seguro" role="tab" aria-selected="false" tabindex="-1">
                <i class="ri-window-line"></i>
                Seguro
            </a>
        </li>

        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#sesiones" role="tab" aria-selected="false" tabindex="-1">
                <i class="ri-window-line"></i>
                Historial Sesiones
            </a>
        </li> 
    </ul>
</div>

<div class="p-4 tab-content">
    
    <div class="tab-pane active" id="datosUsuarios" role="tabpanel">
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
            
            <button class="btn btnDanger m-2 cambiarEmail" type="button" id="cambiarEmail">Actualizar</button>
        </div>
    </div>

    <div class="tab-pane" id="datosPersonales" role="tabpanel">
        <form id="form-update">
            <div class="col-12 d-flex  flex-wrap">
                <div class="p-2 col-md-6">
                    <label for="nombre" class="form-label font-weight-bold"><strong>Nombre <i class="text-danger">*</i></strong></label>
                    <input id="nombre" name="nombre" class="form-control" type="text" value="{{ $query->Nombre ?? '' }}">
                    <input type="hidden" id="UserId" value="{{ $query->UserId ?? 0 }}">
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
                    <label for="fechaNac" class="form-label font-weight-bold"><strong>Fecha Nacimiento</strong></label>
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
                    <input class="form-control" id="codPostal" name="codPostal" type="text" value="{{ $query->CP }}">
                </div>

                <div class="p-2 col-md-6">
                    <label for="direccion" class="form-label font-weight-bold">Dirección</label>
                    <input class="form-control" id="direccion" type="text" value="{{ $query->Direccion ?? ''}}">
                </div>
            </div>
        
            <div class="p-3 col-md-12 d-flex justify-content-end ">
                <button class="btn btnDanger m-2 updateDatos">Actualizar</button>
            </div>

        </form>
    </div>

     <div class="tab-pane" id="roles" role="tabpanel">
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

    <div class="tab-pane verOpciones" id="opciones" role="tabpanel">
        <div class="row">

            <div class="row mt-4 mb-4">
                <div class="col-6">
                    <label for="Sello" class="form-label fw-bold">Sello</label>
                    <textarea class="form-control Firma" name="Firma" id="Firma">{{ $query->Firma ?? ''}}</textarea>
                </div>

                <div class="col-6 text-center">
                    <label for="Foto" class="form-label fw-bold">Firma</label>
                    <input type="file" class="form-control-sm custom-file-input" id="Foto" name="Foto" accept="image/*" style="display: none;">
                    <label class="custom-file-label" for="Foto" style="cursor: pointer;">Selecciona una imagen aquí</label>
                    <button type="button" class="previsualizar btn btn-soft-info waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#z">Vista Previa Firma</button>

                    <div class="d-flex justify-content-center">
                        <img id="vistaPrevia" src="@fileUrl('lectura')/Prof/{{$query->Foto ?? 'foto-default.png' }}?v={{ time() }}" alt="Previsualización de imagen" style="{{ $query->Foto ? 'max-width: 150px; max-height: 150px;' : 'display: none; max-width: 150px; max-height: 150px;' }}">
                    </div>
                    
                    <input type="hidden" name="wImage" id="wImage" value="{{ $query->wImage ?? ''}}">
                    <input type="hidden" name="hImage" id="hImage"  value="{{ $query->hImage ?? ''}}">
                    <small style="display: block;">La imagen se edita en la "Vista Previa"</small>
                </div>
            </div>

            <div class="col-12 fondo-grisClaro">

                <div class="row p-4">
                    <div class="col-6">
                            <input class="form-check-input" type="checkbox" id="Pago" {{ ($query->Pago === 1 ? 'checked' : '') ?? ''}}>
                            <label class="form-check-label" for="Pago">Pago por hora</label>
                               
                            <input class="form-check-input" type="checkbox" id="InfAdj" {{ ($query->InfAdj === 1 ? 'checked' : '') ?? ''}}>
                            <label class="form-check-label" for="InfAdj"> Informe Adjunto </label>

                            <input class="form-check-input" type="checkbox" id="tlp" {{ ($query->TLP === 1 ? 'checked' : '') ?? ''}}>
                            <label class="form-check-label" for="InfAdj"> Multi Especialidad </label>
                    </div>
                </div>
                
            </div>

            <hr class="hr" />
                
            <div class="mt-4 mb-4">
                <div class="row">
                    <div class="col-sm-6">
                        <span class="fw-bold">Especialidad</span>
                        <select class="form-select" id="listaEspecialidad" name="listaEspecialidad">
                            <option selected value="">Elija una opción...</option>
                            @foreach($lstProveedor as $proveedor)
                                <option value="{{ $proveedor->Id ?? ''}}">{{ $proveedor->Nombre ?? ''}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-6">
                        <span class="fw-bold">Perfiles</span>
                        <div class="d-flex align-items-center">
                            <select class="form-select" id="perfiles" name="perfiles">
                            </select>
                            <i class="addPerfilProf ri-add-circle-line ml-2" title="Añadir perfil"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Especialidades y Perfiles: </h4>
                    </div><!-- end card header -->

                    <div class="card-body">
                        <div class="listjs-table">

                            <div class="table mt-3 mb-1">
                                <table class="table align-middle table-nowrap" id="customerTable">
                                    <thead class="table-light">
                                        <tr class="text-center">

                                            <th class="sort text-center">Especialidad</th>
                                            <th class="sort text-center">Perfiles</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all" id="listaProfesionales">
                                        
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div><!-- end card -->
                </div>
                <!-- end col -->
            </div>
            
            <div class="row">
                <div class="col-sm-12 text-center">
                    <button type="button" class="saveOpciones btn btnDanger">Confirmar</button>
                </div>
            </div>
       </div>
    </div>

    <div class="tab-pane verOpciones" id="seguro" role="tabpanel">
        <div class="row">

            <div class="col-6 mb-3">
                <label for="MN" class="form-label">N° Matrícula  </label>
                <input type="text" class="form-control" placeholder="Matrícula" id="MN" value="{{ $query->MN}}">
            </div>

            <div class="col-6 v-flex align-item-center">
                <br /><br />
                {!!                    
                    ($query->SeguroMP >= now()->format('Y-m-d') ? '<span class="badge badge-soft-success">Seguro Vigente</span>' : ($query->SeguroMP < now()->format('Y-m-d') && $query->SeguroMP <> '0000-00-00' ? '<span class="badge badge-soft-danger">Seguro Vencido</span>' : ($query->SeguroMP === '0000-00-00' || $query->SeguroMP === '' ? '<span class="badge badge-soft-warning">Seguro Pendiente</span>' : '')))
                !!}
            </div><!--end col-->
            
            <div class="col-6 mb-3">
                <label for="MP" class="form-label">Seguro Mala Práxis</label>
                <input type="text" class="form-control" placeholder="N° seguro" id="MP" value="{{ $query->MP}}">
            </div>

            <div class="col-6 mb-3">
                <label for="SeguroMP" class="form-label">Vigencia de Seguro</label>
                <input type="date" class="form-control" id="SeguroMP" value="{{ $query->SeguroMP}}">
            </div>
        
            <div class="col-lg-12">
                <div class="hstack gap-2 justify-content-end">

                    <button type="button" class="saveSeguro btn btnDanger">Guardar</button>
                </div>
            </div>
          
        </div>
    </div>

    <div class="tab-pane" id="sesiones" role="tabpanel">

        <h3>Historial de sesiones del usuario</h3>
        <p class="text muted">Listado ordenado cronologicamente</p>

        <table class="table table-nowrap" id="listaSesionesUsuario">
            <thead>
                <tr>
                    <th scope="col">IP</th>
                    <th scope="col">Dispositivo</th>
                    <th scope="col">Fecha/Hora Conexión</th>
                    <th scope="col">Fecha/Hora Desconexión</th>
                </tr>
            </thead>
            <tbody id="lstSesionesUsuario"></tbody>
        </table>
        
    </div>
</div>

<div id="previsualizarModal" class="modal fadeInUp" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-primary alert-dismissible alert-label-icon rounded-label fade show" role="alert">
                    <i class="ri-user-smile-line label-icon"></i><small>Coloquese en los bordes de la imagen para modificar el tamaño.</small>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <div class="alert alert-primary alert-dismissible alert-label-icon rounded-label fade show" role="alert">
                    <i class="ri-user-smile-line label-icon"></i><small>Cierre la ventana para confirmar.</small>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <img id="imagenModal" src="#" width="{{ !in_array($query->wImage, [0, '']) ? $query->wImage.'px' : '250px' }}" height="{{ !in_array($query->hImage, [0, '']) ? $query->hImage.'px' : '250px' }}" style="display:block; max-width: 100%; max-height: 400px; border: 1px solid #eeeeee">
                <p id="selloModal"></p> 
            </div>
            <div class="modal-footer">
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    const ROUTE = "{{ route('listadoRoles') }}";
    const getLocalidades = "{{ route('getLocalidades') }}";
    const getCodigoPostal = "{{ route('getCodigoPostal') }}";
    const actualizarDatos = "{{ route('actualizarDatos') }}";
    const ID = "{{ $query->UserId }}";
    const IDPROF = "{{ $query->IdProfesional }}";
    const lstRolAsignados = "{{ route('lstRolAsignados') }}";
    const checkEmailUpdate = "{{ route('checkEmailUpdate') }}";
    const verificarCorreo = "{{ $query->EMail ?? '' }}";
    const addRol = "{{ route('addRol') }}";
    const deleteRol = "{{ route('deleteRol') }}";
    const INDEX = "{{ route('usuarios.index') }}";
    const setPerfiles = "{{ route('setPerfiles') }}";
    const getPerfiles = "{{ route('getPerfiles') }}";
    const delPerfil = "{{ route('delPerfil') }}";
    const datosProf = "{{ route('usuarios.updateProfesional') }}";
    const checkRoles = "{{ route('checkRoles') }}";
    const seguroProf = "{{ route('profesionales.seguro') }}";
    const cargarSessiones = "{{ route('sesiones.listaSesiones') }}";
    const getProfesional = "{{ route('usuarios.getProfesional') }}";
</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/richtext.min.css') }}">
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
<script src="{{ asset('js/richText/jquery.richtext.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/profesionales/utils.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/auth/sesiones.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/basicos.js') }}?v={{ time() }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
<script src="{{ asset('js/fancyTable.js') }}"></script>
@endpush

@endsection


