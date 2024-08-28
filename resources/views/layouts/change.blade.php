@extends('template')

@section('title', 'Perfil')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Perfil de usuario</h4>
</div>

<div class="col-12">
    <div class="card mt-xxl-n5">
        
        <div class="card-header">
            <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" data-bs-toggle="tab" href="#personalDetails" role="tab" aria-selected="true">
                        <i class="fas fa-home"></i>
                        Datos personales
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" href="#changePassword" role="tab" aria-selected="false" tabindex="-1">
                        <i class="far fa-user"></i>
                        Cambiar password
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="card-body p-4">
            <div class="tab-content">
                <div class="tab-pane active" id="personalDetails" role="tabpanel">
                    <form id="form-updatePerfil">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre <span class="required">(*)</span></label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $query->Nombre ?? '' }}" @readonly(true)>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="apellido" class="form-label">Apellido <span class="required">(*)</span></label>
                                    <input type="text" class="form-control" id="apellido" name="apellido" value="{{ $query->Apellido ?? '' }}" @readonly(true)>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="tipoDoc" class="form-label">Tipo Documento <span class="required">(*)</span></label>
                                    <select id="tipoDoc" name="tipoDoc" class="form-select font-weight-bold" @readonly(true)>
                                        <option value="{{ $query->TipoDocumento ?? '' }}" selected>{{ $query->TipoDocumento ?? 'Elija una opción...' }}</option>
                                        <option value="DNI">DNI</option>
                                        <option value="CF">CF</option>
                                        <option value="LC">LC</option>
                                        <option value="LE">LE</option>
                                        <option value="PS">PS</option>
                                    </select>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="numeroDoc" class="form-label">Numero de documento <span class="required">(*)</span></label>
                                    <input type="text" class="form-control" id="numeroDoc" name="numeroDoc" value="{{ $query->Documento ?? '' }}" @readonly(true)>
                                    <input type="hidden" id="Id" value="{{ $query->Id ?? 0 }}">
                                    <input type="hidden" id="name" value="{{ $query->Name ?? 0 }}">
                                </div>
                            </div>
                            
                            <div class="p-2 col-md-6">
                                <label for="cuil" class="form-label font-weight-bold"><strong>CUIL <i class="text-danger">*</i></strong></label>
                                <select id="cuil" name="cuil" class="form-select font-weight-bold" @readonly(true)>
                                    <option value="{{ $query->TipoIdentificacion ?? '' }}" selected>{{ $query->TipoIdentificacion ?? 'Elija una opción...' }}</option>
                                    <option value="CUIT" >CUIT</option>
                                    <option value="CUIL" selected>CUIL</option>   
                                </select>
                            </div>
                
                            <div class="p-2 col-md-6">
                                <label for="numeroCUIL" class="form-label font-weight-bold"><strong>Numero de Cuil/Cuit <i class="text-danger">*</i></strong></label>
                                <input id="numeroCUIL" name="numeroCUIL" class="form-control" type="text" value="{{ $query->Identificacion ?? '' }}" placeholder="xx-xxxxxxxx-x" @readonly(true)>
                            </div>
                
                            <div class="p-2 col-md-6">
                                <label for="numTelefono" class="form-label font-weight-bold"><strong>Número de Telefono</strong></label>
                                <input id="numTelefono" name="numTelefono" class="form-control" type="text" value="{{ $query->Telefono ?? '' }}">
                            </div>
                            <div class="p-2 col-md-6">
                                <label for="fechaNac" class="form-label font-weight-bold"><strong>Fecha de Naciemiento</strong></label>
                                <input id="fechaNac" name="fechaNac" class="form-control" type="date" value="{{ $query->FechaNacimiento ?? '' }}" @readonly(true)>
                            </div>
                            
                            <!-- Agregar las provincias en base al lugar de nacimiento -->
                            <div class="p-2 col-md-4">
                                <label for="provincia" class="form-label font-weight-bold"><strong>Provincia </strong></label>
                                <select id="provincia" name="provincia" class="form-select font-weight-bold" @readonly(true)>
                                    <option value="{{ $query->Provincia ?? ''}}" selected>{{ $query->Provincia ?? 'Seleccionar una opción...' }}</option>
                                    @foreach ($provincias as $provincia)
                                        <option value="{{ $provincia->Nombre }}">{{ $provincia->Nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                
                            <div class="p-2 col-md-4">
                                <label for="localidad" class="form-label font-weight-bold"><strong>Localidad </strong></label>
                                <select id="localidad" name="localidad" class="form-select font-weight-bold" @readonly(true)>
                                    <option value="{{ $query->ILocalidad ?? ''}}" selected>{{ $query->NombreLocalidad ?? 'Seleccionar una opción...' }}</option>
                                </select>
                            </div>
                
                            <div class="p-2 col-md-4">
                                <label for="codPostal" class="form-label font-weight-bold">Codigo Postal</label>
                                <input class="form-control" id="codPostal" type="text" value="{{ $query->CP ?? '' }}" @readonly(true)>
                            </div>
                
                            <div class="p-2 col-md-6">
                                <label for="direccion" class="form-label font-weight-bold">Dirección</label>
                                <input class="form-control" id="direccion" type="text" value="{{ $query->Direccion ?? ''}}" @readonly(true)>
                            </div>

                            <div class="p-2 col-md-6">
                                <label for="email" class="form-label font-weight-bold">Email</label>
                                <input class="form-control" name="email" id="email" type="text" value="{{ $query->EMail ?? ''}}">
                            </div>

                            <div class="col-lg-12">
                                <div class="hstack gap-2 justify-content-end">
                                    <button type="submit" class="btn btn-sm botonGeneral updateDatos">Actualizar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="tab-pane" id="changePassword" role="tabpanel">
                    <div class="row">
                        <div class="col-sm-12">
                            <form id="form-updatePass">
                                <div class="row g-2">
                                    <div class="col-lg-4">
                                        <div>
                                            <label for="oldpasswordInput" class="form-label">Contraseña actual <span class="required">(*)</span></label>
                                            <input type="password" class="form-control" id="password" name="password">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-4">
                                        <div>
                                            <label for="newPass" class="form-label">Nueva contraseña <span class="required">(*)</span></label>
                                            <input type="password" class="form-control" id="newPass" name="newPass">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-4">
                                        <div>
                                            <label for="confirmpasswordInput" class="form-label">Confirmar contraseña <span class="required">(*)</span></label>
                                            <input type="password" class="form-control" id="confirmPass" name="confirmPass">
                                        </div>
                                    </div>
        
                                    <div class="col-lg-12">
                                        <div class="text-end">
                                            <button type="button" class="btn btn-sm botonGeneral actPass">Actualizar</button>
                                        </div>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    const ROUTE = "{{ route('listadoRoles') }}";
    const getLocalidad = "{{ route('getLocalidades') }}";
    const getCodigoPostal = "{{ route('getCodigoPostal') }}";
    const TOKEN = '{{ csrf_token() }}';
    const actualizarDatos = "{{ route('actualizarDatos') }}";
    const actualizarPass = "{{ route('actualizarPass') }}";
    const checkPassword = "{{ route('checkPassword') }}";
    const checkTelefono = "{{ route('checkTelefono') }}";
    const checkCorreo = "{{ route('checkCorreo') }}";
</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
@endpush

@push('scripts')
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/perfiles/perfil.js')}}?v={{ time() }}"></script>
@endpush

@endsection