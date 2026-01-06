@extends('template')

@section('title', 'Especialidades')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Especialidad <span class="badge text-bg-primary">Nueva</span></h4>
    <a class="btn btn-sm botonGeneral" href="{{ route('especialidades.index')}}">Volver</a>
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
            <form id="form-create">
                <div class="row">

                    <div class="col-6 mt-3">
                        <label for="Nombre" class="form-label">Nombre <span class="required">(*)</span></label>
                        <input type="text" class="form-control" id="Nombre" name="Nombre" placeholder="Nombre">
                    </div> 
        
                    <div class="col-3 mt-3">
                        <label for="cliente" class="form-label">Externo <span class="required">(*)</span></label>
                        <select class="form-control" name="Externo" id="Externo">
                            <option value="" selected>Elija una opción...</option>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>  
        
                    <div class="col-6 Telefono mt-3">
                        <label for="Telefono" class="form-label">Teléfono</label>
                        <input type="number" class="form-control" name="Telefono" id="Telefono" placeholder="2996547532">
                    </div>
        
                    <div class="col-6 Direccion mt-3">
                        <label for="Direcion" class="form-label">Dirección</label>
                        <input type="text" class="form-control" name="Direccion" id="Direccion" placeholder="Calle N B">
                    </div>
        
                    <div class="col-4 Provincia mt-3">
                        <label for="Provincia" class="form-label">Provincia</label>
                        <select class="form-control" name="Provincia" id="Provincia">
                            <option value="" selected>Elija una opción...</option>
                            @foreach($provincias as $provincia)
                                <option value="{{ $provincia->Nombre ?? ''}}">{{ $provincia->Nombre ?? ''}}</option>
                            @endforeach
                        </select>
                    </div> 
                    
                    <div class="col-4 IdLocalidad mt-3">
                        <label for="IdLocalidad" class="form-label">Localidad</label>
                        <select class="form-control" name="IdLocalidad" id="IdLocalidad">
                            <option value="" selected>Elija una opción...</option>
                            @foreach($localidades as $localidad)
                                <option value="{{ $localidad->Id ?? ''}}">{{ $localidad->Nombre ?? ''}}</option>
                            @endforeach
                        </select>
                    </div> 
        
                    <div class="col-12 Obs mt-3">
                        <label for="Obs" class="form-label">Observaciones</label>
                        <textarea name="Obs" id="Obs" class="form-control"></textarea>
                    </div>
        
                    <div class="col-lg-12 pt-4">
                        <div class="hstack gap-2 justify-content-end">
                            
                            <button type="button" id="saveBasico" class="btn btn-sm botonGeneral">Registrar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Default Modals -->
<div id="advertencia" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body text-center p-5">
                <div class="mt-4">
                    <h4 class="mb-3">¡El nombre del especialidad ya se encuentra registrada!</h4>
                    <p class="text-muted mb-4">Actualice sus datos haciendo clíc en el botón.</p>
                    <p class="text-muted mb-4">El botón de guardar se encontrará bloqueado hasta que cambie de nombre.</p>
                    <div class="hstack gap-2 justify-content-center">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Utilizar otro nombre de especialidad</button>
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
    const getLocalidad ="{{ route('getLocalidades') }}";
    let editUrl = "{{ route('especialidades.edit', ['especialidade' => '__especialidades__']) }}";
    const checkProveedor = "{{ route('checkProveedor') }}";
    const saveBasico = "{{ route('saveBasico') }}";
</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v=?v={{ time() }}">
@endpush


@push('scripts')
<script src="{{ asset('js/especialidades/create.js') }}?v={{ time() }}"></script>

@endpush

@endsection