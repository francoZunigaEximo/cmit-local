@extends('template')

@section('title', 'Detalles Paquete Facturacion')

@section('content')
<div class="row mb-4">
    <div class="col-12 text-end">
        <button onclick="window.history.back()" class="btn btn-warning"><i class="ri-arrow-left-line"></i>&nbsp;Volver</button>
    </div>
</div>
<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Detalle paquete facturacion</h4>

    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item active">Listado</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <form id="form-index">
            <div class="col-12 p-3 border border-1 border-color" style="border-color: #666666;">
                <div class="row">
                    <div class="col-6 p-1">
                        <label for="nombrepaquete" class="form-label">Paquete</label>
                        <select name="paqueteSelect2" class="form-control" id="paqueteSelect2">
                        </select>
                    </div>
                    <div class="col-6 p-1">
                        <label for="codigopaquete" class="form-label">Examen</label>
                        <select name="examenSelect2" class="form-control" id="examenSelect2">
                        </select>
                    </div>
                    <div class="col-6 p-1">
                        <label for="grupoSelect2" class="form-label">Grupo:</label>
                        <select name="grupoSelect2" class="form-control" id="grupoSelect2">
                        </select>
                    </div>
                    <div class="col-6 p-1">
                        <label for="fechaHasta" class="form-label">Empresa:</label>
                        <select name="empresaSelect2" class="form-control" id="empresaSelect2">
                        </select>
                    </div>
                    <div class="col-12 p-1 d-flex justify-content-end">
                        <button type="button" class="btn botonGeneral buscarDetallesEstudios"><i class="ri-zoom-in-line"></i>&nbsp;Buscar</button>
                    </div>
                </div>
            </div>
        </form>
        <div class="col-12">
            <div class="mt-3">
                <button type="button" class="btn botonGeneral btnExcelEstudios">
                    <i class="ri-file-excel-line align-bottom me-1"></i> Exportar
                </button>
            </div>
        </div>
        <div class="table w-100 mt-3 mb-1">
            <table id="listaPaquetesEstudiosDetalle" class="table nowrap align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="sort">Numero</th>
                        <th class="sort">Paquete</th>
                        <th class="sort">Especialidad</th>
                        <th class="sort">Examen</th>
                        <th class="sort">Empresa</th>
                        <th class="sort">Grupo</th>
                    </tr>
                </thead>
                <tbody class="list form-check-all">

                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    const getClientes = "{{ route('getClientes') }}";
    const getGrupos = "{{route('getGrupos')}}";
    const getExamenes = "{{ route('searchExamen')}}";
    const getPaquetesFact = "{{ route('getPaqueteFact') }}";

    const searchDetalleFacturacion = "{{ route('paquetes.searchDetalleFacturacion')}}"
    const exportarExcel = "{{route('paquetes.exportDetalleFacturacionExcel')}}";
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
<script src="{{ asset('js/paquetes/detalle_facturacion.js') }}?v={{ time() }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
@endpush
@endsection