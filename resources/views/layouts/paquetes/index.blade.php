@extends('template')

@section('title', 'Pacientes')

@section('content')
<div class="row mb-4">
    <div class="col-12 text-end">
        <button onclick="window.history.back()" class="btn btn-warning"><i class="ri-arrow-left-line"></i>&nbsp;Volver</button>
    </div>
</div>
<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Paquetes</h4>

    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item active">Listado</li>
        </ol>
    </div>
</div>
<div class="card-header d-flex justify-content-between">
    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" data-bs-toggle="tab" href="#facturacion" role="tab" aria-selected="true">
                <i class="ri-window-line"></i>
                Facturacion
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#examenes" role="tab" aria-selected="true">
                <i class="ri-window-line"></i>
                Estudios
            </a>
        </li>

    </ul>
</div>
<div class="row">
    <div class="col-lg-12">
        
            <div class="tab-content">
                <div class="tab-pane active" id="facturacion" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <form id="form-index">
                                <div class="col-12 p-3 border border-1 border-color" style="border-color: #666666;">
                                    <div class="row">
                                        <div class="col-3 p-1">
                                            <div>
                                                <label for="paqueteFacturacionSelect2" class="form-label">Paquete:</label>
                                                <select name="paqueteFacturacionSelect2" class="form-control" id="paqueteFacturacionSelect2">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-3 p-1">
                                            <div>
                                                <label for="grupoSelect2" class="form-label">Grupo:</label>
                                                <select name="grupoSelect2" class="form-control" id="grupoSelect2">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-3 p-1">
                                            <div>
                                                <label for="fechaHasta" class="form-label">Empresa:</label>
                                                <select name="empresaSelect2" class="form-control" id="empresaSelect2">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-3 p-1">
                                            <div>
                                                <label for="fechaHasta" class="form-label">Cod:</label>
                                                <input type="text" class="form-control" id="codigoPaquete">
                                            </div>
                                        </div>
                                        <div class="col-12 p-1 d-flex align-items-center justify-content-end">
                                            <button type="button" class="btn botonGeneral buscarPaquetesFacturacion"><i class="ri-zoom-in-line"></i>&nbsp;Buscar</button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <a href="{{ route('paquetes.createPaqueteFacturacion') }}" class="btn botonGeneral">
                                                <i class="ri-add-line align-bottom me-1"></i> Nuevo
                                            </a>
                                            <a href="{{route('paquetes.detallesFacturacion')}}" class="btn botonGeneral">
                                                Detalles
                                            </a>
                                            <button type="button" class="btn botonGeneral btnExcelFacturacion">
                                                <i class="ri-file-excel-line"></i>&nbsp;Exportar Excel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="table w-100 mt-3 mb-1">
                                <table id="listaPaquetesFacturacion" class="table nowrap align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort">Nombre</th>
                                            <th class="sort">Examenes</th>
                                            <th class="sort">Empresa</th>
                                            <th class="sort">Grupo</th>
                                            <th class="sort">Codigo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="examenes" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <form id="form-index">
                                <div class="col-12 p-3 border border-1 border-color" style="border-color: #666666;">
                                    <div class="row">
                                        <div class="col-4 p-1">
                                            <label for="paqueteEstudioSelect2" class="form-label">Paquete:</label>
                                            <select name="paqueteEstudioSelect2" class="form-control" id="paqueteEstudioSelect2">
                                            </select>
                                        </div>
                                        <div class="col-4 p-1">
                                            <label for="codigopaquete" class="form-label">Codigo</label>
                                            <input type="number" class="form-control" id="codigopaquete">
                                        </div>
                                        <div class="col-4 p-1 d-none">
                                            <label for="aliaspaquete" class="form-label">Alias</label>
                                            <input type="text" class="form-control" id="aliaspaquete">
                                        </div>
                                        <div class="col-12 p-1 d-flex align-items-center justify-content-end">
                                            <button type="button" class="btn botonGeneral buscarPaquetesExamenes"><i class="ri-zoom-in-line"></i>&nbsp;Buscar</button>
                                        </div>
                                         <div class="col-12">
                                            <a href="{{ route('paquetes.crearPaqueteExamen') }}" class="btn botonGeneral">
                                                <i class="ri-add-line align-bottom me-1"></i> Nuevo
                                            </a>
                                            <button type="button" class="btn botonGeneral btnExcelEstudios">
                                                <i class="ri-file-excel-line align-bottom me-1"></i> Exportar
                                            </button>
                                            <a href="{{ route('paquetes.detalleEstudios') }}" class="btn botonGeneral">
                                                Detalles
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <br/>
                           
                            <div class="table w-100 mt-3 mb-1">
                                <table id="listaPaquetesExamenes" class="table nowrap align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort">Codigo</th>
                                            <th class="sort">Examenes</th>
                                            <th class="sort">Nombre</th>
                                            <th class="sort">Alias</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>
<script>
const TOKEN = "{{ csrf_token() }}";
const getClientes = "{{ route('getClientes') }}";
const SEARCH_EXAMENES = "{{ route('paquetes.searchExamenes')}}";
const exportarExcel = "{{ route('paquetes.exportExcel') }}";
const exportarExcelFacturacion = "{{ route('paquetes.exportExcelFacturacion') }}";
const eliminarPaqueteEstudioRoute = "{{ route('paquetes.eliminarPaqueteEstudio') }}";
const eliminarPaqueteFacturacionRoute = "{{route('paquetes.eliminarPaqueteFacturacion')}}"

const search_paquetes_studio = "{{route('paquetes.searchPaquetesFacturacion')}}";
const getGrupos = "{{route('getGrupos')}}";

const getPaqueteFact = "{{route('getPaqueteFact')}}";
const getPaqueteEstudio = "{{route('getPaquetes')}}";
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

<script src="{{ asset('js/paquetes/index.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/paquetes/paginacion.js') }}?v={{ time() }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
@endpush
@endsection