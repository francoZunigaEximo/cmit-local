@extends('template')

@section('title', 'Factura de Compra')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Factura de Compra</h4>

    <div class="page-title-right"></div>
</div>

<div class="card-header d-flex justify-content-between">
    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" data-bs-toggle="tab" href="#gestion" role="tab" aria-selected="true">
                <i class="ri-window-line"></i>
                Gestion Facturas
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#factura" role="tab" aria-selected="false" tabindex="-1">
                <i class="ri-window-line"></i>
                Factura Masivo
            </a>
        </li>
    </ul>
</div>

<div class="card-body p-4">
    <div class="tab-content">
        <div id="messageClientes"></div>

        <div class="tab-pane active" id="gestion" role="tabpanel">
            <div class="row">
                <div class="col-lg-12">

                    <div class="row">
                        <div class="small col-sm-12 mb-2"><span class="required">(*)</span> El campo es obligatorio.</div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="listjs-table" id="customerList">
                                <form id="form-index">
                                    <div class="row g-4 ">
                                        <div class="col-12 p-4 border border-1 border-color" style="border-color: #666666;">

                                            <div class="row">

                                                <div class="col-sm-2 me-3">
                                                    <label for="fechaDesde" class="form-label fw-bolder">Fecha desde: <span class="required">(*)</span></label>
                                                    <input type="date" class="form-control" id="fechaDesde" name="fechaDesde">
                                                </div>

                                                <div class="col-sm-2 me-3">
                                                    <label for="fechaDesde" class="form-label fw-bolder">Fecha hasta: <span class="required">(*)</span></label>
                                                    <input type="date" class="form-control" id="fechaHasta" name="fechaHasta">
                                                </div>

                                                <div class="col-sm-2 me-3">
                                                    <label for="profesional" class="form-label fw-bolder">Profesional: </label>
                                                    <select class="form-control" name="Profesional" id="Profesional"></select>
                                                </div>

                                                <div class="col-sm-2 me-3">
                                                    <label for="nroDesde" class="form-label fw-bolder">Nro Desde: </label>
                                                    <input type="number" class="form-control" id="nroDesde" name="nroDesde">
                                                </div>

                                                <div class="col-sm-2 me-3">
                                                    <label for="examen" class="form-label fw-bolder">Nro Hasta:</label>
                                                    <input type="number" class="form-control" id="nroHasta" name="nroHasta">
                                                </div>

                                            </div>

                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-sm-12 d-flex align-items-center justify-content-end">
                                            <button type="button" id="buscarFacturas" class="btn botonGeneral"><i class="ri-zoom-in-line"></i>&nbsp;Buscar</button>&nbsp;
                                            <a id="agregar" class="btn botonGeneral m-1" href="{{ route('examenesCuenta.create') }}"><i class="ri-add-fill"></i>&nbsp;Agregar</a>
                                            <button type="button" id="btnreporte" class="btn botonGeneral"><i class="ri-file-excel-2-fill"></i>&nbsp;Exportar</button>
                                        </div>
                                    </div>

                                    <div class="table-responsive table-card mt-3 mb-1 mx-auto">
                                        <table id="listadoFacturas" class="display table table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th><input type="checkbox" id="checkAll" name="Id"></th>
                                                    <th class="sort">Número</th>
                                                    <th class="sort">Fecha</th>
                                                    <th class="sort">Factura</th>
                                                    <th class="sort">Profesional</th>
                                                    <th class="sort">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody class="list form-check-all">

                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="tab-pane" id="factura" role="tabpanel">
            <div class="row">
                <div class="col-lg-12">

                    <div class="row">
                        <div class="small col-sm-12 mb-2"><span class="required">(*)</span> El campo es obligatorio.</div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="listjs-table" id="customerList">
                                <form id="form-index">
                                    <div class="row g-4 ">
                                        <div class="col-12 p-4 border border-1 border-color" style="border-color: #666666;">

                                            <div class="row">

                                                <div class="col-sm-2 me-3">
                                                    <label for="fechaDesdeEfector" class="form-label fw-bolder">Fecha desde: <span class="required">(*)</span></label>
                                                    <input type="date" class="form-control" id="fechaDesdeEfector" name="fechaDesde">
                                                </div>

                                                <div class="col-sm-2 me-3">
                                                    <label for="fechaHastaEfector" class="form-label fw-bolder">Fecha hasta: <span class="required">(*)</span></label>
                                                    <input type="date" class="form-control" id="fechaHastaEfector" name="fechaHasta">
                                                </div>

                                                <div class="col-sm-2 me-3">
                                                    <label for="profesional" class="form-label fw-bolder">Tipo: </label>
                                                    <select class="form-control" name="tipo" id="tipo">
                                                        <option value="">Seleccione</option>
                                                        <option value="0">No Hora</option>
                                                        <option value="1">Hora</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-sm-12 d-flex align-items-center justify-content-end">
                                            <button type="button" id="buscarEfectores" class="btn botonGeneral"><i class="ri-zoom-in-line"></i>&nbsp;Buscar</button>&nbsp;
                                            <a id="agregar" class="btn botonGeneral m-1" href="{{ route('examenesCuenta.create') }}"><i class="ri-add-fill"></i>&nbsp;Agregar</a>
                                        </div>
                                    </div>

                                    <div class="table-responsive table-card mt-3 mb-1 mx-auto">
                                        <table id="listadoEfectores" class="display table table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th><input type="checkbox" id="checkAll" name="Id"></th>
                                                    <th class="sort">Profesional</th>
                                                    <th class="sort">Pago</th>
                                                    <th class="sort">Cantidad</th>
                                                    <th class="sort">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody class="list form-check-all">

                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    const buscarEfectoresUrl = "{{ route('facturaCompra.buscarEfectores') }}";
    const buscarFacturas = "{{ route('facturaCompra.buscarFacturasCompras') }}";
    const rutaCrearFacturaCompra = "{{ route('facturaCompra.crearFacturaCompra', ['id' => 'ID_PROFESIONAL']) }}";
    const rutaEditarFacturaCompra = "{{ route('facturaCompra.editarFactura', ['id' => 'ID_FACTURA']) }}";

    const rutaEliminarFacturaCompra = "{{ route('facturaCompra.eliminarFacturaCompra') }}";

    const imprimirReporte = "{{ route('facturaCompra.exportarExcel') }}";

    const buscarProfesional = "{{ route('searchProfesionalesComun') }}";
</script>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/inputmask/dist/jquery.inputmask.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script src="{{ asset('js/facturaCompra/index.js')}}?=v{{ time() }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
<script src="{{ asset('js/fancyTable.js') }}"></script>
@endpush
@endsection