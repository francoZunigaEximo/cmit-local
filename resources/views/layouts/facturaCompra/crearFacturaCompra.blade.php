@extends('template')

@section('title', 'Factura de Compra')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Factura de Compra</h4>

    <a class="btn btn-warning" type="button" href="{{ route('facturaCompra.index') }}"><i class="ri-arrow-left-line"></i> Volver</a>
</div>

<!--modales-->
<div class="modal fade" id="modalSubtotal" tabindex="-1" aria-labelledby="modalSubtotalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="modalSubtotalLabel">Facturar Compra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5>Examenes Efector</h5>
                <div class="table-responsive table-card mt-3 mb-1 mx-auto">
                    <table id="listadoCantidadExamenesEfector" class="display table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th class="sort">Cantidad</th>
                                <th class="sort">Examen</th>
                            </tr>
                        </thead>
                        <tbody class="list form-check-all">

                        </tbody>
                    </table>
                </div>
                <h5>Examenes Informador</h5>
                <div class="table-responsive table-card mt-3 mb-1 mx-auto">
                    <table id="listadoCantidadExamenesInformador" class="display table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th class="sort">Cantidad</th>
                                <th class="sort">Examen</th>
                            </tr>
                        </thead>
                        <tbody class="list form-check-all">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalFacturar" tabindex="-1" aria-labelledby="modalFacturarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="modalFacturarLabel">Subtotal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <label for="nroNotaCredito" class="form-label">Nro Factura Compra</label>
                        <div class="row">
                            <div class="col-2">
                                <input type="text" class="form-control" id="tipo" placeholder="A" required>
                            </div>
                            <div class="col-4">
                                <input type="number" class="form-control" id="sucursal" placeholder="0000" max="9999" min="0" required>
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control" id="nroFactura" placeholder="00000000" max="99999999" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="observacionFactura" class="form-label">Observacion</label>
                        <textarea class="form-control" id="observacionFactura" rows="3" placeholder="Ingrese una observación"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnFacturar">Facturar</button>
            </div>
        </div>
    </div>
</div>


<div class="row justify-content-md-center">
    <form id="form-index">
        <div class="row g-4 mb-3">
            <div class="col-12 p-4 border border-1 border-color" style="border-color: #666666;">

                <div class="row">

                    <div class="col-sm-2 mb-3">
                        <label for="fechaDesdeEfector" class="form-label fw-bolder">Fecha desde:</label>
                        <input type="date" class="form-control" id="fechaDesdeEfector" name="fechaDesde">
                    </div>

                    <div class="col-sm-2 mb-3">
                        <label for="fechaHastaEfector" class="form-label fw-bolder">Fecha hasta:</label>
                        <input type="date" class="form-control" id="fechaHastaEfector" name="fechaHasta">
                    </div>

                    <div class="col-sm-2 mb-3">
                        <label for="profesional" class="form-label fw-bolder">Profecional: </label>
                        <input type="text" class="form-control" id="profesional" name="profesional" value="{{ $profesional->Apellido }}, {{ $profesional->Nombre }}" disabled>
                    </div>
                    <div class="col-sm-2 mb-3">
                        <label for="cuit" class="form-label fw-bolder">Rol: </label>
                        <select class="form-select" id="rol" name="rol">
                            <option value="-1" selected>Todos</option>
                            <option value="1">Efector</option>
                            <option value="2">Informador</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 d-flex align-items-center justify-content-end">
                            <button type="button" id="buscarEfectores" class="btn botonGeneral"><i class="ri-zoom-in-line"></i>&nbsp;Buscar</button>&nbsp;
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="row">
                    <div class="row">
                        <div class="col-sm-12 d-flex align-items-center justify-content-end">
                            <button type="button" class="btn botonGeneral" id="subtotal"><i class="ri-money-dollar-circle-line"></i>&nbsp;Subtotal</button>&nbsp;
                            <button type="button" class="btn botonGeneral" id="modalFacturarBtn">Facturar</button>
                        </div>
                    </div>
                </div>
            </div>
    </form>

    <h4>Examenes Efector</h4>
    <div class="row justify-content-center">
        <div class="table-responsive table-card mt-3 mb-1 mx-auto">
            <table id="listadoExamenesEfector" class="display table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th><input type="checkbox" id="checkAllEfector" name="Id"></th>
                        <th class="sort">Prestacion</th>
                        <th class="sort">Fecha</th>
                        <th class="sort">Examen</th>
                        <th class="sort">Empresa</th>
                        <th class="sort">Cerrado</th>
                        <th class="sort">Paciente</th>
                        <th class="sort">Estados</th>
                    </tr>
                </thead>
                <tbody class="list form-check-all">

                </tbody>
            </table>
        </div>
    </div>

    <h4>Examenes Informador</h4>
    <div class="row justify-content-center">
        <div class="table-responsive table-card mt-3 mb-1 mx-auto">
            <table id="listadoExamenesInformador" class="display table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th><input type="checkbox" id="checkAllInformador" name="Id"></th>
                        <th class="sort">Prestacion</th>
                        <th class="sort">Fecha</th>
                        <th class="sort">Examen</th>
                        <th class="sort">Empresa</th>
                        <th class="sort">Cerrado</th>
                        <th class="sort">Paciente</th>
                        <th class="sort">Estados</th>
                    </tr>
                </thead>
                <tbody class="list form-check-all">

                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const ID_PROFESIONAL = "{{ $profesional->Id }}";
    const rutaListarExamenesEfector = "{{ route('facturaCompra.listarExamenesEfector') }}";
    const rutaListarExamenesInformador = "{{ route('facturaCompra.listarExamenesInformador') }}";
    const rutaCantidadExamenesEfector = "{{ route('facturaCompra.cantidadExamenesEfector') }}";
    const rutaCantidadExamenesInformador = "{{ route('facturaCompra.cantidadExamenesInformador') }}";

    const rutaFacturar = "{{ route('facturaCompra.facturar') }}";
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
<script src="https://cdn.datatables.net/rowgroup/1.3.1/js/dataTables.rowGroup.min.js"></script>

<script src="{{ asset('js/facturaCompra/crearFacturaCompra.js')}}?=v{{ time() }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>

@endpush
@endsection