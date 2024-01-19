@extends('template')

@section('title', 'Registrar un cliente')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Registrar un nuevo cliente</h4>

    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="{{ route('clientes.index') }}">Clientes</a></li>
            <li class="breadcrumb-item active">Nuevo Cliente</li>
        </ol>
    </div>
</div>
                        
<div class="card-header">
    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" data-bs-toggle="tab" href="#datosGenerales" role="tab" aria-selected="true">
                <i class="fas fa-home"></i>
                Datos Básicos
            </a>
        </li>
    </ul>
</div>
<div class="card-body p-4">
    <div class="tab-content">
        <div class="tab-pane active" id="datosGenerales" role="tabpanel">
            <div id="messageClientes"></div>
            <form id="form-create" action="{{ route('clientes.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                <div class="row">  
                    <div class="col-3 p-2 mb-2" style="background-color: #eeeeee">
                        <label for="cliente" class="form-label"> Cliente <span class="required">(*)</span></label>
                        <div class="mb-3">
                            <select class="form-select" id="TipoCliente" name="TipoCliente">
                                <option value="">Tipo cliente</option>
                                <option value="E">Empresa</option>
                                <option value="A">ART</option>
                            </select>
                        </div>
                    </div>        
                    <div class="col-3 p-2 mb-2" style="background-color: #eeeeee">
                        <div class="mb-3">
                            <label for="Identificacion" class="form-label"> <br>  </label>
                            <input type="text" class="form-control" id="Identificacion" name="Identificacion" placeholder="CUIT">
                        </div>
                    </div><!--end col-->
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="RazonSocial" class="form-label">Razón Social <span class="required">(*)</span></label>
                            <input type="text" class="form-control" placeholder="Nombre cliente" id="RazonSocial" name="RazonSocial">
                        </div>
                    </div><!--end col-->
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="ParaEmpresa" class="form-label">Para empresa <span class="required">(*)</span></label>
                            <input type="text" name="ParaEmpresa" class="form-control" placeholder="Nombre empresa para" id="ParaEmpresa">
                        </div>
                    </div><!--end col-->
                    
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="NombreFantasia" class="form-label">Alias</label>
                            <input type="text" class="form-control" placeholder="Alias cliente" id="NombreFantasia" name="NombreFantasia">
                        </div>
                    </div><!--end col-->
                    
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="CondicionIva" class="form-label">Condición</label>
                            <select class="form-select" name="CondicionIva" id="CondicionIva">
                                <option selected value="">Elija una opción...</option>
                                <option value="RESPONSABLE INSCRIPTO">Responsable Inscripto</option>
                                <option value="EXENTO">Exento</option>
                                <option value="CONSUMIDOR FINAL">Consumidor Final</option>
                                <option value="NO RESPONSABLE">No Responsable</option>
                                <option value="MONOTRIBUTISTA">Monotributista</option>
                                <option value="DEL EXTERIOR">Del Exterior</option>
                            </select>
                        </div>
                    </div><!--end col-->

                    
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="emailidInput" class="form-label">Asignado</label>
                            <select class="form-select" data-choices="" data-choices-sorting="true" id="autoSizingSelect">
                                <option selected value="">Juan Perez</option>
                                <option value="1">...</option>
                                <option value="2">...</option>
                            </select>
                        </div>
                    </div><!--end col-->

                    <div class="col-6">
                        <div class="mb-3">
                            <label for="Telefono" class="form-label">Teléfono <span class="required">(*)</span></label>
                            <input type="text" class="form-control" placeholder="(xxx)xxx-xxxx" id="cleave-phone" name="Telefono">
                            
                        </div>
                    </div><!--end col-->

                    <div class="col-6">
                        <div class="mb-3">
                            <label for="Direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" name="Direccion" id="Direccion">
                        </div>
                    </div><!--end col-->

                    <div class="col-6">
                        <div class="mb-3">
                            <label for="EMail" class="form-label">Email</label>
                            <input type="text" class="form-control" name="EMail" id="EMail">
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="mb-3">
                            <label for="ObsEMail" class="form-label">Observaciones</label>
                            <textarea name="ObsEMail" class="form-control" id="ObsEMail"></textarea>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="mb-3">
                            <label for="Provincia" class="form-label">Provincia <span class="required">(*)</span></label>
                            <select class="form-select" name="Provincia" id="Provincia">
                                <option selected value="">Elija una opción...</option>
                                @foreach ($provincias as $provincia)
                                    <option value="{{ $provincia->Nombre }}">{{ $provincia->Nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div><!--end col-->
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="IdLocalidad" class="form-label">Localidad <span class="required">(*)</span></label>
                            <select class="form-select" id="IdLocalidad" name="IdLocalidad">
                                <option selected value="">Elija una opción...</option>
                            </select>
                        </div>
                    </div>   
                    
                    <div class="col-2">
                        <div class="mb-3">
                            <label for="CP" class="form-label">CP</label>
                            <input type="text" class="form-control" placeholder="3300" id="CP" name="CP">
                        </div>
                    </div><!--end col-->

                    <div class="col-12" style="border: 1px solid #eeeeee; padding: 1em">
                        <div class="mb-3">
                            <label for="Telefonos" class="form-label">Teléfonos</label>
                            <div class="input-group mb-4">
                                <input name="prefijoExtra" id="prefijoExtra" type="number" class="form-control" placeholder="Prefijo">
                                <span class="input-group-addon">-</span>
                                <input name="numeroExtra" id="numeroExtra" type="number" class="form-control" placeholder="Numero">
                                <span class="input-group-addon">-</span>
                                <input name="obsExtra" id="obsExtra" type="text" class="form-control" placeholder="Observación">
                                <span class="input-group-addon">-</span>
                                <button type="button" class="btn botonGeneral" id="addNumero">Agregar Número adicional</button>
                            </div>
                            <table class="table table-nowrap" style="border: 1px solid #eeeeee">
                                <thead>
                                    <tr style="background-color: #eeeeee">
                                        <th scope="col">Prefijo</th>
                                        <th scope="col">Número</th>
                                        <th scope="col">Observación</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaTelefonos">

                                </tbody>
                            </table>
                            <div id="hiddens">

                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-12 mt-3">
                        <div class="hstack gap-2 justify-content-end">
                            
                            <button type="reset" class="btn botonGeneral">Cancelar</button>
                            <button type="submit" class="btn botonGeneral">Registrar</button>
                        </div>
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->
            </form>
        </div>
        <!--end tab-pane-->
    </div>
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
                    <h4 class="mb-3">¡El número de CUIT ya se encuentra asociado a esa "Para Empresa"!</h4>
                    <p class="text-muted mb-4">Actualice sus datos haciendo clíc en el botón.</p>
                    <div class="hstack gap-2 justify-content-center">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Utilizar otro "cuit" o "para empresa"</button>
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
let editUrl = "{{ route('clientes.edit', ['cliente' => '__cliente__']) }}";
let contadorFilas = 1;

//Rutas
const TOKEN = '{{ csrf_token() }}';
const getLocalidad = "{{ route('getLocalidades') }}";
const getCodigoPostal = "{{ route('getCodigoPostal') }}";
const checkProvincia = "{{ route('checkProvincia') }}";
const verifyIdentificacion = "{{ route('verifyIdentificacion') }}";
const verifycuitEmpresa = "{{ route('verifycuitEmpresa') }}";
const searchLocalidad = "{{ route('searchLocalidad') }}";


</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
@endpush

@push('scripts')
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>

<script src="{{ asset('js/clientes/validaciones.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/clientes/create.js')}}?v={{ time() }}"></script>
<script src="{{ asset('js/clientes/utils.js')}}?v={{ time() }}"></script>

<script src="{{ asset('js/scripts.js') }}?v={{ time() }}"></script>
<script src="{{ asset('libs/cleave.js/cleave.min.js') }}"></script>
<script src="{{ asset('js/pages/form-masks.init.js') }}"></script>

@endpush

@endsection