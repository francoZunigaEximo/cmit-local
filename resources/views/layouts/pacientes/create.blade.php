@extends('template')

@section('title', 'Registrar un paciente')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Nuevo paciente</h4>

</div>

<div class="container-fluid">
    <form id="form-create" action="{{ route('pacientes.store') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
   <div class="row">
        <div class="col-12 text-center">

            <div class="row">
                <div class="col-4 box-information">
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">Apellido&nbsp;<span class="required">(*)</span></span>
                        <input type="text" class="form-control" id="Apellido" name="Apellido">
                    </div>
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">Nombre&nbsp;<span class="required">(*)</span></span>
                        <input type="text" class="form-control" id="Nombre" name="Nombre">
                    </div>
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">Documento&nbsp;<span class="required">(*)</span></span>
                        <select class="form-select" name="TipoDocumento" id="tipoDocumento">
                            <option selected value="">Elija una opción...</option>
                            <option value="DNI">DNI</option>
                            <option value="PAS">PAS</option>
                            <option value="LC">LC</option>
                            <option value="CF">CF</option>
                        </select>
                        <input type="text" class="form-control" id="documento" name="Documento">
                    </div>
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">CUIT/CUIL</span>
                        <select class="form-select" id="tipoIdentificacion" name="TipoIdentificacion">
                            <option selected value="">Elija una opción...</option>
                            <option value="CUIT">CUIT</option>
                            <option value="CUIL">CUIL</option>
                        </select>
                        <input type="text" class="form-control" id="identificacion" name="Identificacion">
                    </div>

                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">Fecha de nacimiento&nbsp;<span class="required">(*)</span></span>
                        <input type="date" class="form-control" id="fecha" name="FechaNacimiento">
                        <input type="text" class="form-control" id="edad" title="Edad">
                    </div>

                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Sexo</span>
                        <select class="form-select" id="Sexo" name="Sexo">
                            <option selected value="">Elija una opción... </option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                        </select>
                    </div>
                </div>

                <div class="col-4 box-information">
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">Dirección</span>
                        <input type="text" class="form-control" id="Direccion" name="Direccion">
                    </div>

                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">Telefono<span class="required">(*)</span></span>
                        <input type="text" class="form-control" placeholder="(xxx)xxx-xxxx" id="cleave-phone" name="NumeroTelefono">
                    </div>

                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">Email</span>
                        <input type="text" class="form-control" placeholder="example@gmail.com" id="correo" name="EMail">
                    </div>

                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">Provincia&nbsp;<span class="required">(*)</span></span>
                        <select id="provincia" class="form-select" name="Provincia">
                            <option selected value="">Elija una opción...</option>
                            @foreach ($provincias as $provincia)
                            <option value="{{ $provincia->Nombre }}">{{ $provincia->Nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Localidad&nbsp;<span class="required">(*)</span></span>
                        <select id="localidad" class="form-select" name="IdLocalidad">
                            <option selected value="">Elija una opción...</option>
                            <option>...</option>
                        </select>
                        <input type="text" class="form-control" id="codigoPostal" name="CP">
                    </div>

                </div>
    
                <div class="col-3 box-information mx-auto">
                    <div class="profile-user position-relative d-inline-block mx-auto mb-2">
                        <div id="profile-image-preview" class="img-thumbnail user-profile-image" style="width: 200px; height: 140px; background-image: url('{{ asset("archivos/fotos/foto-default.png") }}'); background-size: cover; background-position: center;"></div>
                    <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                            <input id="profile-img-file-input" type="button" class="profile-img-file-input" value="Tomar foto" onClick="takeSnapshot()">
                            <input type="hidden" name="Foto" class="image-tag">
                            <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                                <span class="avatar-title rounded-circle bg-light text-body">
                                    <i class="ri-camera-fill"></i>
                                </span>
                            </label>
                        </div> 
                    </div>
                    <div class="text-center d-block">
                        <span class="toggle-webcam-button text-center iconGeneral" onClick="toggleWebcam()" title="Activar Webcam">
                            <i class="ri-webcam-line"></i>
                        </span>
                    </div>
                </div>

                <div class="col-12 box-information mt-2">
                    <div class="input-group input-group-sm pt-1 pb-1">
                        <span class="input-group-text">Antecedentes</span>
                        <input type="text" class="form-control " id="antecedentes" name="Antecedentes">
                    </div>
                </div>

                <div class="col-12 box-information mt-2">
                    <div class="input-group input-group-sm pt-1 pb-1">
                        <span class="input-group-text">Observaciones</span>
                        <input type="text" class="form-control " id="Observaciones" name="Observaciones">
                    </div>
                </div>

                <div class="col-12 box-information mt-2 text-center">
                    <a href="{{ route('pacientes.index') }}" class="btn botonGeneral">Volver</a>
                    <button type="submit" id="btnRegistrar" class="btn botonGeneral">Registrar</button>
                </div>
            </div> 
        </div>
   </div>
    </form>
</div>

<!-- Default Modals -->
<div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body text-center p-5">
                <div class="mt-4">
                    <h4 class="mb-3">¡El número de documento ya se encuentra registrado!</h4>
                    <p class="text-muted mb-4">Actualice sus datos haciendo clíc en el botón.</p>
                    <p class="text-muted mb-4">El botón de registro se encontrará bloqueado hasta que cambie de Identificación.</p>
                    <div class="hstack gap-2 justify-content-center">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Utilizar otro número de documento</button>
                        <a href="#" id="editLink" class="btn btn-primary">Actualizar datos</a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
//Rutas
const verify = "{{ route('verify') }}";
const getLocalidades = "{{ route('getLocalidades') }}";
const getCodigoPostal = "{{ route('getCodigoPostal') }}";

//Extras
const TOKEN = "{{ csrf_token() }}";
let editUrl = "{{ route('pacientes.edit', ['paciente' => '__paciente__']) }}";

</script>

@push('styles')
<link href="{{ asset('css/hacks.css') }}?v=1.1" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/pacientes/validaciones.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/pacientes/create.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/pacientes/utils.js') }}?v={{ time() }}"></script>

<script src="{{ asset('js/webcam.min.js') }}?V={{ time() }}"></script>
<script src="{{ asset('js/webcam-picture.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/scripts.js') }}?v={{ time() }}"></script>
<script src="{{ asset('libs/cleave.js/cleave.min.js') }}"></script>
<script src="{{ asset('js/pages/form-masks.init.js') }}"></script>
@endpush

@endsection