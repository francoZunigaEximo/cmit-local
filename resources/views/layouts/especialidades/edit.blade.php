@extends('template')

@section('title', 'Especialidades')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Especialidad <span class="badge text-bg-primary">{{ $especialidade->Id }}</span></h4>

    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="{{ route('especialidades.index') }}">Especialidades</a></li>
            <li class="breadcrumb-item active">Editar</li>
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
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#opciones" role="tab" aria-selected="false" tabindex="-1">
                <i class="las la-cog"></i>
                Opciones
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
                    <label for="Nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="Nombre" name="Nombre" placeholder="Nombre" value="{{ $especialidade->Nombre ?? ''}}">
                    <input type="hidden" value="{{ $especialidade->Id ?? '' }}" id="Id">
                </div> 
    
                <div class="col-3 mt-3">
                    <label for="cliente" class="form-label">Externo</label>
                    <select class="form-control" name="Externo" id="Externo">
                        <option value="{{ $especialidade->Externo ?? '' }}" selected>{{ $especialidade->Externo === 1 ? 'Sí' : ($especialidade->Externo === 0 ? 'No' : ($especialidade->Externo === null ? '' : 'Elija una opción...')) }}</option>
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>  
    
                <div class="col-3 mt-3">
                    <label for="Inactivo" class="form-label">Inactivo</label>
                    <select class="form-control" name="Inactivo" id="Inactivo">
                        <option value="{{ $especialidade->Inactivo ?? '' }}" selected>{{ $especialidade->Inactivo === 1 ? 'Sí' : ($especialidade->Inactivo === 0 ? 'No' : ($especialidade->Inactivo === null ? '' : 'Elija una opción...')) }}</option>
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>
    
                <div class="col-6 Telefono mt-3">
                    <label for="Telefono" class="form-label">Teléfono</label>
                    <input type="number" class="form-control" name="Telefono" id="Telefono" placeholder="2996547532" value="{{ $especialidade->Telefono ?? '' }}">
                </div>
    
                <div class="col-6 Direccion mt-3">
                    <label for="Direcion" class="form-label">Dirección</label>
                    <input type="text" class="form-control" name="Direccion" id="Direccion" placeholder="Calle N B" value=" {{ $especialidade->Direccion ?? '' }}">
                </div>
    
                <div class="col-4 Provincia mt-3">
                    <label for="Provincia" class="form-label">Provincia</label>
                    <select class="form-control" name="Provincia" id="Provincia">
                        <option value="{{ $detalleProv->Id ?? ''}}" selected>{{ $detalleProv->Nombre ?? 'Elija una opción...' }}</option>
                        @foreach($provincias as $provincia)
                            <option value="{{ $provincia->Nombre ?? ''}}">{{ $provincia->Nombre ?? ''}}</option>
                        @endforeach
                    </select>
                </div> 
                
                <div class="col-4 IdLocalidad mt-3">
                    <label for="IdLocalidad" class="form-label">Localidad</label>
                    <select class="form-control" name="IdLocalidad" id="IdLocalidad">
                        <option value="{{ $especialidade->IdLocalidad ?? '' }}" selected>{{ $especialidade->localidad->Nombre ?? 'Elija una opción...' }}</option>
                    </select>
                </div> 
    
                <div class="col-12 Obs mt-3">
                    <label for="Obs" class="form-label">Observaciones</label>
                    <textarea name="Obs" id="Obs" class="form-control">{{ $especialidade->Obs ?? '' }}</textarea>
                </div>
    
                <div class="col-lg-12 pt-4">
                    <div class="hstack gap-2 justify-content-end">
                        
                        <button type="button" id="btnVolverEspe" class="btn botonGeneral">Volver</button>
                        <button type="button" id="updateBasico" class="btn botonGeneral">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
        <!--end tab-pane-->
        <!--end tab-pane-->
        <div class="tab-pane" id="opciones" role="tabpanel">
            <div class="row">
    
                <div class="col-12">
                    <label for="MultiE" class="form-label"><br /></label> <!-- la selección se guarda en el campo Calificacion -->
                    <div class="form-check">
                        <label class="form-check-label" for="Multi">Multi Adjunto Efector (Un Adjunto puede corresponder a varios exámenes)</label>
                        <input class="form-check-input" type="checkbox" id="Multi" {{ $especialidade->Multi == 'null' || $especialidade->Multi == 0 ? '' : 'checked'}}>
                    </div>
                </div>
    
                <div class="col-12 mb-3">
                    <label for="Multi" class="form-label"><br /></label> <!-- la selección se guarda en el campo Calificacion -->
                    <div class="form-check">
                        <label class="form-check-label" for="MultiE">Multi Examen Informador (Un informe puede corresponder a varios exámenes)</label>
                        <input class="form-check-input" type="checkbox" id="MultiE" {{ $especialidade->MultiE == 'null' || $especialidade->MultiE == 0 ? '' : 'checked'}}>
                    </div>
                </div>
    
                <div class="col-6">
                    <label for="Min" class="form-label">Duración Turno (en minutos)</label>
                    <input type="number" class="form-control" name="Min" id="Min" placeholder="Mínutos" value="{{ $especialidade->Min ?? ''}}">
                </div>
    
                <div class="col-6">
                    <label for="PR" class="form-label">Máximo Pacientes (capacidad de llamar en simultáneo)</label>
                    <input type="number" class="form-control" name="PR" id="PR" placeholder="¿Cuántos pacientes es posible llamar en simultáneo?" value="{{ $especialidade->PR ?? ''}}">
                </div>
    
                <div class="col-lg-12 pt-4">
                    <div class="hstack gap-2 justify-content-end">
                        
                        <button type="button" id="updateOpciones" class="btn btn-success">Guardar</button>
                    </div>
                </div>
    
            </div>
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
    const TOKEN = "{{ csrf_token() }}";
    const updateProveedor = "{{ route('updateProveedor') }}";
    const GOINDEX = "{{ route('especialidades.index') }}";

</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v=?v={{ time() }}">
@endpush


@push('scripts')
<script src="{{ asset('js/especialidades/edit.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/scripts.js') }}?v={{ time() }}"></script>

@endpush

@endsection