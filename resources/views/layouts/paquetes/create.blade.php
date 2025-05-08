@extends('template')

@section('title', 'Registrar un paciente')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Nuevo Paquete de Examenes</h4>

</div>

<div class="container-fluid">
    <div class="col-12 p-3 border border-1 border-color" style="border-color: #666666;">
        <div class="row">
            <div class="col-2 p-1">
                <div>
                    <label for="" class="form-label">Codigo:</label>
                    <input type="text" class="form-control" id="codigo">
                </div>
            </div>
            <div class="col-4 p-1">
                <div>
                    <label for="" class="form-label">Nombre:</label>
                    <input type="text" class="form-control" id="nombre">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 p-1">
                <div>
                    <label for="" class="form-label">Descripcion:</label>
                    <textarea class="form-control" id="descripcion"></textarea>
                </div>
            </div>
        </div>

    </div>
    <div class="col-12 p-3 border border-1 border-color mt-1" style="border-color: #666666;">
        <div class="row">
            <div class="col-8">
                <div>
                    <label class="form-label">Paquete Estudios</label>
                    <select name="paqueteSelect2" class="form-control" id="paqueteSelect2">

                    </select>
                </div>
            </div>
            <div class="col-2">
                <button type="button" class="btn botonGeneral add-btn agregarExamen" data-bs-toggle="offcanvas">
                    <i class="ri-add-line align-bottom me-1"></i> Copiar
                </button>
            </div>
        </div>
    </div>
    <div class="col-12 p-3 border border-1 border-color mt-1" style="border-color: #666666;">
        <div class="row">
            <div class="col-8">
                <div>
                    <label class="form-label">Examen</label>
                    <select name="examenSelect2" class="form-control" id="examenSelect2">

                    </select>
                </div>
            </div>
            <div class="col-2 p-1 d-flex align-items-center justify-content-center">
                <div>
                    <button type="button" class="btn botonGeneral add-btn agregarExamen" data-bs-toggle="offcanvas">
                        <i class="ri-add-line align-bottom me-1"></i> Agregar
                    </button>
                </div>
            </div>
        </div>
    </div>
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
<script>
    const getExamenes = "{{ route('examenes.getExamenes') }}";
    const getExamenId = "{{ route('examenes.getById') }}"
    const getPaquetes = "{{ route('getPaquetes') }}";
</script>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/paquetes/create.js') }}?v={{ time() }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
@endpush

@endsection