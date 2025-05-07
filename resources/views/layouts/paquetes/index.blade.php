@extends('template')

@section('title', 'Pacientes')

@section('content')

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
                Examenes
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
                                        <div class="col-2 p-1">
                                            <div>
                                                <label for="fechaHasta" class="form-label">Paquete:</label>
                                                <input type="text" class="form-control" id="nombrepaquetefacturacion">
                                            </div>
                                        </div>
                                        <div class="col-2 p-1">
                                            <div>
                                                <label for="fechaHasta" class="form-label">Grupo:</label>
                                                <select name="grupoSelect2" class="form-control" id="grupoSelect2">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-2 p-1">
                                            <div>
                                                <label for="fechaHasta" class="form-label">Empresa:</label>
                                                <select name="empresaSelect2" class="form-control" id="empresaSelect2">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-2 p-1">
                                            <div>
                                                <label for="fechaHasta" class="form-label">Cod:</label>
                                                <input type="text" class="form-control" id="codigoPaquete">
                                            </div>
                                        </div>
                                        <div class="col-2 p-1 d-flex align-items-center justify-content-center">
                                            <button type="button" class="btn botonGeneral buscarPaquetesFacturacion"><i class="ri-zoom-in-line"></i>&nbsp;Buscar</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="table w-100 mt-3 mb-1">
                                <table id="listaPaquetesFacturacion" class="table nowrap align-middle">
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
                                        <div class="col-10 p-1">
                                            <label for="fechaHasta" class="form-label">Paquete</label>
                                            <input type="text" class="form-control" id="nombrepaquete">
                                        </div>
                                        <div class="col-2 p-1 d-flex align-items-center justify-content-center">
                                            <button type="button" class="btn botonGeneral buscarPaquetesExamenes"><i class="ri-zoom-in-line"></i>&nbsp;Buscar</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <br/>
                            <div class="col-12">
                                <div>
                                    <button type="button" class="btn botonGeneral add-btn" data-bs-toggle="offcanvas">
                                        <i class="ri-add-line align-bottom me-1"></i> Nuevo
                                    </button>
                                </div>
                            </div>
                            <div class="table w-100 mt-3 mb-1">
                                <table id="listaPaquetesExamenes" class="table nowrap align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort">Codigo</th>
                                            <th class="sort">Nombre</th>
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

const getClientes = "{{ route('getClientes') }}";
const SEARCH_EXAMENES = "{{ route('paquetes.searchExamenes')}}";

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