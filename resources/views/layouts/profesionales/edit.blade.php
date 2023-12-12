@extends('template')

@section('title', 'Profesionales')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Editar Profesionales</h4>

    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="{{ route('profesionales.index') }}">Profesionales</a></li>
            <li class="breadcrumb-item active">Editar</li>
        </ol>
    </div>
</div>

<div class="col-xxl-12">
    <div class="card mt-xxl-n5">
        <div class="card-header">
            <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" data-bs-toggle="tab" href="#datosBasicos" role="tab" aria-selected="true">
                        <i class="fas fa-home"></i>
                        Datos Básicos
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" href="#opciones" role="tab" aria-selected="false" tabindex="-1">
                        <i class="ri-settings-3-line"></i>
                        Opciones
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" href="#seguro" role="tab" aria-selected="false" tabindex="-1">
                        <i class="far fa-envelope"></i>
                        Seguro
                    </a>
                </li>

            </ul>
        </div>
        <div class="card-body p-4">
            <div class="tab-content">
                <div class="tab-pane active" id="datosBasicos" role="tabpanel">
                    <form id="form-update" action="{{ route('profesionales.update', ['profesionale' => $profesionale->Id]) }}" method="POST" enctype="multipart/form-data" novalidate>
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div id="messageBasico"></div>
                            <div class="col-2">

                                <div class="mb-3">
                                    <label for="Documento" class="form-label"> Documento  <span class="required">(*)</span></label>
                                    <input type="text" class="form-control" placeholder="DNI" id="Documento" name="Documento" value="{{ $profesionale->Documento }}">
                                </div>
                            </div><!--end col-->

                            <div class="col-4">
                                <div class="mb-3">
                                    <label for="Apellido" class="form-label">Apellido <span class="required">(*)</span></label>
                                    <input type="text" class="form-control" placeholder="Apellido del profesional" id="Apellido" name="Apellido" value="{{ $profesionale->Apellido ?? ''}}">
                                </div>
                            </div><!--end col-->
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="Nombre" class="form-label">Nombre <span class="required">(*)</span></label>
                                    <input type="text" class="form-control" placeholder="Nombre del profesional" id="Nombre" name="Nombre" value="{{ $profesionale->Nombre ?? ''}}">
                                </div>
                            </div><!--end col-->


                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="Telefono" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" placeholder="2995896354" id="cleave-phone" name="Telefono" value="{{ $profesionale->telefono->NumeroTelefono ?? ''}}">
                                </div>
                            </div><!--end col-->
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="EMail" class="form-label">Email</label>
                                    <input type="text" class="form-control" value="{{ $profesionale->EMail ?? ''}}" placeholder="ejemplo@ejemplo.com" id="EMail" name="EMail">
                                </div>
                            </div><!--end col-->
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="Direccion" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" placeholder="Calle N° B°" id="Direccion" name="Direccion" value="{{ $profesionale->Direccion ?? ''}}">
                                </div>
                            </div><!--end col-->
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="Provincia" class="form-label">Provincia <span class="required">(*)</span></label>
                                    <select id="provincia" class="form-select" name="Provincia">
                                        <option selected value="{{ $profesionale->Provincia ?? ''}}">{{ $profesionale->Provincia ?? 'Elija una opción...'}}</option>
                                            @foreach ($provincias as $provincia)
                                                <option value="{{ $provincia->Nombre }}">{{ $provincia->Nombre }}</option>
                                            @endforeach
                                    </select>
                                </div>
                            </div><!--end col-->
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="IdLocalidad" class="form-label">Localidad</label>
                                    <select id="localidad" class="form-select" name="IdLocalidad">
                                        <option selected value="{{ $detailsLocalidad->Id ?? ''}}">{{ $detailsLocalidad->Nombre ?? ''}}</option>
                                        <option>...</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="CP" class="form-label">CP <span class="required"></span></label>
                                    <input type="text" class="form-control" id="codigoPostal" name="CP" value="{{ $detailsLocalidad->CP ?? ''}} " disabled>
                                </div>
                            </div><!--end col-->
                            <div class="col-3">
                                <!-- Esto debería ser un select múltiple. -->
                                <label for="estado" class="form-label">Estado <span class="required">(*)</span></label>
                                <select class="form-select" id="estado" name="estado">
                                    <option selected value="{{ $profesionale->Inactivo ?? ''}}">{{ ($profesionale->Inactivo == 0 ? 'Activo' : ($profesionale->Inactivo == 1 ? 'Inhabilitado' : ($profesionale->Inactivo == 2 ? 'Baja' : 'Elija una opción...'))) }}</option>
                                    <option value="0">Activo</option>
                                    <option value="2">Inhabilitado</option>
                                    <option value="1">Baja</option>


                                </select>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="Sello" class="form-label">Sello</label>
                                    <textarea class="form-control Firma" name="Firma" id="Firma">{{ $profesionale->Firma ?? ''}}</textarea>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="Foto" class="form-label">Firma</label>
                                    <input type="file" class="form-control-sm custom-file-input" id="Foto" name="Foto" accept="image/*" style="display: none;">
                                    <label class="custom-file-label" for="Foto" style="cursor: pointer;">Selecciona una imagen aquí</label>
                                    <img id="vistaPrevia" src="{{ asset('storage/profesionales/'.$profesionale->Foto) }}" alt="Previsualización de imagen" style="{{ $profesionale->Foto ? '' : 'display: none;' }}  max-width: 200px; max-height: 200px;">
                                    <input type="hidden" name="wImage" id="wImage" value="{{ $profesionale->wImage ?? ''}}">
                                    <input type="hidden" name="hImage" id="hImage"  value="{{ $profesionale->hImage ?? ''}}">
                                    <small style="display: block;">La imagen se edita en la "Vista Previa"</small>
                                </div>

                            </div>
                            <div class="col-3">
                                <div class="mb-3" style="text-align: right;">
                                    <label for="previsualizar" class="form-label"><br><br><br></label>
                                    <button type="button" class="previsualizar btn btn-soft-info waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#z">Vista Previa Firma</button>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-12">
                                <div class="hstack gap-2 justify-content-end">

                                    <button type="button" class="volverProfesionales btn botonGeneral">Volver</button>
                                    <button type="submit" class="btn botonGeneral">Guardar</button>
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </form>
                </div>

                <div class="tab-pane" id="opciones" role="tabpanel">
                        <div id="newlink">
                            <div id="1">
                                <div class="row">
                                    <div class="col-lg-3">

                                        <div class="form-check form-check-success mb-6">
                                            <input class="form-check-input" type="checkbox" id="T1" {{($profesionale->T1 == 1 ? 'checked' : '') ?? ''}}>
                                            <label class="form-check-label" for="T1">
                                                Efector
                                            </label>
                                        </div>

                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-3">
                                        <div class="form-check form-check-success mb-6">
                                            <input class="form-check-input" type="checkbox" id="T2" {{($profesionale->T2 == 1 ? 'checked' : '') ?? ''}}>
                                            <label class="form-check-label" for="T2">
                                                Informador
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-check form-check-success mb-6">
                                            <input class="form-check-input" type="checkbox" id="T4" {{($profesionale->T4 == 1 ? 'checked' : '') ?? ''}}>
                                            <label class="form-check-label" for="T4">
                                                Combinado
                                            </label>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-3">
                                        <div class="mb-3">
                                            <div class="form-check form-check-success mb-6">
                                                <input class="form-check-input" type="checkbox" id="T3" {{($profesionale->T3 == 1 ? 'checked' : '') ?? ''}}>
                                                <label class="form-check-label" for="T3">
                                                    Evaluador
                                                </label>
                                            </div>

                                            <!--end row-->
                                        </div>
                                    </div>
                                   

                                    <div class="col-lg-3">
                                        <div class="mb-3">
                                            <div class="form-check form-check-success mb-6">
                                                <input class="form-check-input" type="checkbox" id="Pago" {{($profesionale->Pago == 1 ? 'checked' : '') ?? ''}}>
                                                <label class="form-check-label" for="Pago">
                                                    Pago por hora
                                                </label>
                                            </div>

                                            <!--end row-->
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-check form-check-success mb-6">
                                            <input class="form-check-input" type="checkbox" id="InfAdj" {{($profesionale->proveedor->InfAdj == 1 ? 'checked' : '') ?? ''}}>
                                            <label class="form-check-label" for="InfAdj">
                                                Informe Adjunto
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <br>
                                        <div class="text-end">
                                            <button type="button" class="saveOpciones btn botonGeneral">Guardar</button>
                                        </div>
                                    </div>

                                    <hr class="mt-4">
                                    <div style="margin-block: 15px; ">
                                        
                                            <div class="row g-2">
                                                <div class="col-4">
                                                    <!-- Esto debería ser un select múltiple. -->
                                                    Especialidad
                                                    <select class="form-select" id="listaEspecialidad" name="listaEspecialidad">
                                                        <option selected value="">Elija una opción...</option>
                                                        @foreach ($listEspecialistas as $item)
                                                            <option value="{{ $item->Id ?? ''}}">{{ $item->Nombre ?? '' }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-4">
                                                    <!-- Esto debería ser un select múltiple. -->
                                                    Perfiles
                                                    <div class="d-flex align-items-center">
                                                        <select class="form-select" id="perfiles" name="perfiles">
                                                            <option selected value="">Elija una opción...</option>
                                                            
                                                        </select>
                                                        <i class="addPerfilProf ri-add-circle-line ml-2" title="Añadir perfil"></i>
                                                    </div>

                                            </div>
                                            </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title mb-0">Especialidades y Perfiles | Perfil principal: {{ $profesionale->proveedor->Nombre }}</h4>
                                                </div><!-- end card header -->

                                                <div class="card-body">
                                                    <div class="listjs-table">


                                                        <div class="table-responsive table-card mt-3 mb-1">
                                                            <table class="table align-middle table-nowrap" id="customerTable">
                                                                <thead class="table-light">
                                                                    <tr class="text-center">

                                                                        <th class="sort" data-sort="action">Especialidad</th>
                                                                        <th class="sort" data-sort="email">Perfiles</th>

                                                                        <th>Acciones</th>
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
                                    </div>
                                    <!--end row-->
                                </div>
                            </div>

                   </div>         <!--end col-->
                    
                </div>

                <div class="tab-pane" id="seguro" role="tabpanel">
                        <div class="row">

                            <div class="col-6">

                                <div class="mb-3">
                                    <label for="MN" class="form-label">N° Matrícula  </label>
                                    <input type="text" class="form-control" placeholder="Matrícula" id="MN" value="{{ $profesionale->MN}}">
                                </div>
                            </div><!--end col-->

                            <div class="col-6">
                                <br>
                                {!!                    
                                    ($profesionale->SeguroMP >= now()->format('Y-m-d') ? '<span class="badge badge-soft-success">Seguro Vigente</span>' : ($profesionale->SeguroMP < now()->format('Y-m-d') && $profesionale->SeguroMP <> '0000-00-00' ? '<span class="badge badge-soft-danger">Seguro Vencido</span>' : ($profesionale->SeguroMP === '0000-00-00' || $profesionale->SeguroMP === '' ? '<span class="badge badge-soft-warning">Seguro Pendiente</span>' : '')))
                                !!}
                            </div><!--end col-->
                            
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="MP" class="form-label">Seguro Mala Práxis</label>
                                    <input type="text" class="form-control" placeholder="N° seguro" id="MP" value="{{ $profesionale->MP}}">
                                </div>
                            </div><!--end col-->


                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="SeguroMP" class="form-label">Vigencia de Seguro</label>
                                    <input type="date" class="form-control" id="SeguroMP" value="{{ $profesionale->SeguroMP}}">
                                </div>
                            </div><!--end col-->
                            <!--end col-->
                            <div class="col-lg-12">
                                <div class="hstack gap-2 justify-content-end">

                                    <button type="button" class="saveSeguro btn botonGeneral">Guardar</button>
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
                <img id="imagenModal" src="#" width="{{ isset($profesionale->wImage) && $profesionale->wImage !== 0 ? $profesionale->wImage : '20px' }}" height="{{ isset($profesionale->hImage) && $profesionale->hImage !== 0 ? $profesionale->hImage : '20px' }}" style="display:block; max-width: 100%; max-height: 400px; border: 1px solid #eeeeee">
                <p id="selloModal"></p> 
            </div>
            <div class="modal-footer">
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    const checkP = "{{ route('checkProvincia') }}";
    const getLocalidades = "{{ route('getLocalidades') }}";
    const getCodigoPostal = "{{ route('getCodigoPostal') }}";
    const TOKEN = "{{ csrf_token() }}";
    const ID = "{{ $profesionale->Id}}";
    const setPerfiles = "{{ route('setPerfiles') }}";
    const getPerfiles = "{{ route('getPerfiles') }}";
    const delPerfil = "{{ route('delPerfil') }}";
    const opcionesProf = "{{ route('opcionesProf') }}";
    const seguroProf = "{{ route('seguroProf') }}";
    const GOINDEX = "{{ route('profesionales.index') }}";
    
</script>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">
<link rel="stylesheet" href="{{ asset('css/richtext.min.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/profesionales/validaciones.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/profesionales/edit.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/profesionales/utils.js') }}?v={{ time() }}"></script>

<script src="{{ asset('js/scripts.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
<script src="{{ asset('libs/cleave.js/cleave.min.js') }}"></script>

<script src="{{ asset('js/richText/jquery.richtext.js') }}?v={{ time() }}"></script>



@endpush
@endsection
