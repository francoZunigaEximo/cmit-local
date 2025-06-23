@extends('template')

@section('title', 'Editar Paquete Estudios')

@section('content')
<div class="row mb-4">
    <div class="col-12 text-end">
        <button onclick="window.history.back()" class="btn btn-warning"><i class="ri-arrow-left-line"></i>&nbsp;Volver</button>
    </div>
</div>
<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Editar Paquete de Estudios</h4>
    <a href="{{ route('paquetes.crearPaqueteExamen') }}?id={{$paquete->Id}}" class="btn botonGeneral">Clonar</a>
</div>

<div class="container-fluid">
    <div id="mensajeria"></div>
    @csrf
    <div class="row">
        <div class="col-12 p-3 border border-1 border-color" style="border-color: #666666;">
            <div class="row">
                <div class="col-2 p-1">
                    <div>
                        <label for="" class="form-label">Codigo <span class="required" aria-required="true">(*)</span>:</label>
                        <input type="text" class="form-control" id="codigo" value="{{$paquete->Id}}" disabled>
                    </div>
                </div>
                <div class="col-10 p-1">
                    <div>
                        <label for="" class="form-label">Nombre <span class="required" aria-required="true">(*)</span>:</label>
                        <input type="text" class="form-control" id="nombre" value="{{$paquete->Nombre}}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 p-1">
                    <div>
                        <label for="" class="form-label">Alias <span class="required" aria-required="true">(*)</span>:</label>
                        <input type="text" class="form-control" id="alias" value="{{$paquete->Alias}}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 p-1">
                    <div>
                        <label for="" class="form-label">Descripcion:</label>
                        <textarea class="form-control" id="descripcion">{{$paquete->Descripcion}}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 box-information mt-2 text-center">
            <button type="submit" id="btnRegistrar" class="btn botonGeneral">Editar</button>
        </div>
        <div class="col-12 p-3 border border-1 border-color mt-1" style="border-color: #666666;">
            <div class="row">
                <div class="col-5">
                    <div>
                        <label class="form-label">Paquete Estudios</label>
                        <select name="paqueteSelect2" class="form-control" id="paqueteSelect2">

                        </select>
                    </div>
                </div>
                <div class="col-1 p-1 d-flex align-items-end justify-content-center">
                    <div>
                        <button type="button" class="btn add-btn agregarPaquete" data-bs-toggle="offcanvas">
                            <i class="ri-play-list-add-line naranja" title="Añadir paquete completo" style="font-size: 2em;"></i>
                        </button>
                    </div>
                </div>
                <div class="col-5">
                    <div>
                        <label class="form-label">Examen</label>
                        <select name="examenSelect2" class="form-control" id="examenSelect2">

                        </select>
                    </div>
                </div>
                <div class="col-1 p-1 d-flex align-items-end justify-content-center">
                    <div>
                        <button type="button" class="btn add-btn agregarExamen" data-bs-toggle="offcanvas">
                            <i class="ri-add-circle-line align-bottom me-1 naranja" style="font-size: 2em;"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" value="{{ $paquete->Id }}" id="idPaquete" />
        <table id="listaExamenesPaquetes" class="table nowrap align-middle">
            <thead class="table-light">
                <tr>
                    <th class="sort">Codigo</th>
                    <th class="sort">Nombre</th>
                    <th>Descripcion</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="list form-check-all">

            </tbody>
        </table>
        
    </div>
</div>

<script>
    const TOKEN = "{{ csrf_token() }}";
    const paqueteId = "{{ route('paqueteId') }}";
    const getExamenId = "{{ route('examenes.getById') }}";
    const getPaquetes = "{{ route('getPaquetes') }}";
    const postPaquetesEstudios = "{{ route('paquetes.postPaqueteExamen')}}";
    const getExamenes = "{{ route('examenes.getExamenes') }}";
    const postPaquetesEstudiosEditar = "{{ route('paquetes.postEditPaqueteExamen') }}"
</script>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/paquetes/edit_estudios.js') }}?v={{ time() }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
@endpush

@endsection