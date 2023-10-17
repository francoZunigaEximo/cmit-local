@extends('template')

@section('title', 'Registrar un paciente')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Registrar un nuevo paciente</h4>

    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="{{ route('pacientes.index') }}">Pacientes</a></li>
            <li class="breadcrumb-item active">Nuevo Paciente</li>
        </ol>
    </div>
</div>

<div class="container-fluid">
        <form id="form-create" action="{{ route('pacientes.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
    <div class="position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg profile-setting-img">
            <img src="{{ asset('images/banner-top.jpeg') }}" class="profile-wid-img" alt="">
        </div>
    </div>

    <div class="row">
        <div class="col-3">
            <div class="card-body p-4 border-pic">
                <div class="text-center">
                    <div class="profile-user position-relative d-inline-block mx-auto mb-4">
                        <div id="profile-image-preview" class="img-thumbnail user-profile-image" style="width: 188px; height: 200px; background-image: url('{{ asset("images/icono-nuevo-usuario.png") }}'); background-size: cover; background-position: left;"></div>
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
                    <p class="text-muted mb-0">Sacar una fotografía al paciente</p>
                </div>
                <div class="text-center mt-4">
                    <input id="toggle-webcam-button" type="button" class="btn btn-primary" value="Activar Webcam" onClick="toggleWebcam()">
                </div>
            </div>
            <!--end card-->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-5">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0">Antecedentes</h5>
                        </div>
                        
                    </div>
                    <textarea class="form-control" id="meassageInput" rows="3" placeholder="" name="Antecedentes"></textarea>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-5">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0">Observaciones</h5>
                        </div>
                        
                    </div>
                    <textarea class="form-control" id="meassageInput" rows="3" placeholder="" name="Observaciones"></textarea>
                </div>
            </div>
            <!--end card-->
        </div>
        <!--end col-->
        <div class="col-9">
            <div class="card mt-xxl-n5">
                <div class="card-header">
                    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#AltaPaciente" role="tab" aria-selected="true">
                                <i class="fas fa-home"></i>
                                Datos Personales
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content">
                        <div class="tab-pane active show" id="AltaPaciente" role="tabpanel">
                                <div class="row">
                                    <div class="col-2">
                                        <div class="mb-3">
                                            <select class="form-select " name="TipoDocumento" id="tipoDocumento">
                                                <option selected value="">Elija una opción...</option>
                                                <option value="DNI">DNI</option>
                                                <option value="PAS">PAS</option>
                                                <option value="LC">LC</option>
                                                <option value="CF">CF</option>
                                            </select>
                                        </div>
                                    </div>        
                                    <div class="col-4">
                                        <div class="mb-3">
                                            <input type="text" class="form-control" placeholder="Documento del Paciente. Ej: 34256871" id="documento" name="Documento">
                                        </div>
                                    </div><!--end col-->
                                    <div class="col-2">
                                        <div class="mb-3">
                                            <select class="form-select" id="tipoIdentificacion" name="TipoIdentificacion">
                                                <option selected value="">CUIT/CUIL</option>
                                                <option value="CUIT">CUIT</option>
                                                <option value="CUIL">CUIL</option>
                                            </select>
                                        </div>
                                    </div>        
                                    <div class="col-4">
                                        <div class="mb-3">
                                            <input type="text" class="form-control" placeholder="xx-xxxxxxxx-x" id="identificacion" name="Identificacion">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="nombre" class="form-label">Nombre <span class="required">(*)</span></label>
                                            <input type="text" class="form-control" placeholder="Nombre del paciente" id="nombre" name="Nombre">
                                        </div>
                                    </div><!--end col-->
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="apellido" class="form-label">Apellido <span class="required">(*)</span></label>
                                            <input type="text" class="form-control " placeholder="Apellido del paciente" id="apellido" name="Apellido">
                                        </div>
                                    </div><!--end col-->
                                    <!--end col-->
        
                                    <div class="col-4">
                                        <div class="mb-3">
                                            <label for="fecha" class="form-label">Fecha de nacimiento <span class="required">(*)</span></label>
                                            <input type="date" class="form-control" data-provider="flatpickr" id="fecha" name="FechaNacimiento">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="mb-3">
                                            <label for="edad" class="form-label">Edad</label>
                                            <input type="text" class="form-control" id="edad" disabled>
                                        </div>
                                    </div><!--end col-->

                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="telefono" class="form-label">Teléfono <span class="required">(*)</span></label>
                                            <input type="text" class="form-control" placeholder="(xxx)xxx-xxxx" id="cleave-phone" name="NumeroTelefono">
                                        </div>
                                    </div><!--end col-->
 

                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="correo" class="form-label">Email </label>
                                            <input type="text" class="form-control" placeholder="example@gmail.com" id="correo" name="EMail">
                                        </div>
                                    </div><!--end col-->
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="direccion" class="form-label">Dirección</label>
                                            <input type="text" class="form-control" placeholder="Calle N° B°" id="direccion" name="Direccion">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="mb-3">
                                            <label for="provincia" class="form-label">Provincia  <span class="required">(*)</span></label>
                                            <select id="provincia" class="form-select" name="Provincia">
                                                <option selected value="">Elija una opción...</option>
                                                @foreach ($provincias as $provincia)
                                                <option value="{{ $provincia->Nombre }}">{{ $provincia->Nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div><!--end col-->
                                    <div class="col-4">
                                        <div class="mb-3">
                                            <label for="localidad" class="form-label">Localidad  <span class="required">(*)</span></label>
                                            <select id="localidad" class="form-select" name="IdLocalidad">
                                                <option selected value="">Elija una opción...</option>
                                                <option>...</option>
                                            </select>
                                        </div>
                                    </div>   
                                    <div class="col-2">
                                        <div class="mb-3">
                                            <label for="codigoPostal" class="form-label">CP</label>
                                            <input type="text" class="form-control" id="codigoPostal" name="CP">
                                        </div>
                                    </div><!--end col-->
        
                                    
                                    <!--end col-->
                                    <div class="col-lg-12">
                                        <div class="hstack gap-2 justify-content-end">
                                            
                                            <button type="button" class="btn btn-soft-danger">Cancelar</button>
                                            <button type="submit" id="btnRegistrar" class="btn btn-success">Registrar</button>
                                        </div>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </form>
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane" id="changePassword" role="tabpanel">
                            <!-- Code -->
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane" id="experience" role="tabpanel">
                            <!-- Code -->
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane" id="privacy" role="tabpanel">
                
                            <!-- Code -->
                        </div>
                        <!--end tab-pane-->
                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->

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

<script src="{{ asset('js/webcam.min.js') }}"></script>
<script src="{{ asset('js/webcam-picture.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/scripts.js') }}?v={{ time() }}"></script>
<script src="{{ asset('libs/cleave.js/cleave.min.js') }}"></script>
<script src="{{ asset('js/pages/form-masks.init.js') }}"></script>
@endpush

@endsection