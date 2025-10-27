@extends('template')

@section('title', 'Factura de Compra')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Editar Factura de Compra</h4>

    <a class="btn btn-warning" type="button" href="{{ route('facturaCompra.index') }}"><i class="ri-arrow-left-line"></i> Volver</a>
</div>


<div class="row justify-content-md-center">
    <form id="form-index">
        <div class="row g-4 mb-3">
            <div class="col-12 p-4 border border-1 border-color" style="border-color: #666666;">
                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <label for="fecha" class="form-label fw-bolder">Fecha: </label>
                        <input type="date" class="form-control" id="fecha" name="fecha" value="{{$facturaCompra->Fecha}}" required>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label for="profesional" class="form-label fw-bolder">Nro Factura: </label>
                        <div class="row">
                            <div class="col-2">
                                <input type="text" class="form-control" id="tipo" placeholder="A" value="{{$facturaCompra->Tipo}}" required>
                            </div>
                            <div class="col-4">
                                <input type="number" class="form-control" id="sucursal" placeholder="0000" max="9999" min="0" value="{{$facturaCompra->Sucursal}}" required>
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control" id="nroFactura" placeholder="00000000" max="99999999" min="0" value="{{$facturaCompra->NroFactura}}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="col-sm-6 mb-3">
                        <label for="profesional" class="form-label fw-bolder">Profesional:</label>
                        <input type="text" class="form-control" id="profesional" name="profesional" value="{{$profesional->Apellido .','. $profesional->Nombre}}" disabled>
                    </div>

                    <div class="col-sm-6 mb-3">
                        <label for="Tipo" class="form-label fw-bolder">Tipo:</label>
                        <input type="text" class="form-control" id="Tipo" name="Tipo" value="{{$pago}}" disabled>
                    </div>

                </div>
                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <label for="cuit" class="form-label fw-bolder">Prestacion Desde: </label>
                        <input type="date" class="form-control" id="prestacionDesde" name="prestacionDesde" value="{{$fechaDesde}}" disabled>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label for="cuit" class="form-label fw-bolder">Prestacion Hasta: </label>
                        <input type="date" class="form-control" id="prestacionHasta" name="prestacionHasta" value="{{$fechaHasta}}" disabled>
                    </div>
                </div>
                <div>
                    <div class="col-12 mb-3">
                        <label for="observaciones" class="form-label fw-bolder">Observaciones:</label>
                        <textarea class="form-control" id="observaciones" rows="3">{{$facturaCompra->Obs}}</textarea>
                    </div>
                </div>
                <div>
                    <div class="row">
                        <div class="col-sm-12 d-flex align-items-center justify-content-end">
                            <button type="button" id="editarFactura" class="btn botonGeneral">Editar</button>
                            <button type="button" class="btn botonGeneral" id="btnExportarPdf">Imprimir</button>
                            <button type="button" class="btn botonGeneral" id="btnreporteexcel">Excel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <h4>Examenes Efector</h4>
        <div class="row">
            <div class="col-12">
                <button type="button" class="btn botonGeneral" id="eliminarItemsEfector" onclick="eliminarItemsEfectorMasivo()">Eliminar Seleccionados</button>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="table-responsive table-card mt-3 mb-1 mx-auto">
                <table id="listadoExamenesEfector" class="display table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th><input type="checkbox" id="checkAllEfector" name="Id"></th>
                            <th class="sort">Prestacion</th>
                            <th class="sort">Fecha</th>
                            <th class="sort">Examen</th>
                            <th class="sort">Paciente</th>
                            <th class="sort">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="list form-check-all">

                    </tbody>
                </table>
            </div>
        </div>

        <h4>Examenes Informador</h4>
        <div class="row">
            <div class="col-12">
                <button type="button" class="btn botonGeneral" id="eliminarItemsInformador" onclick="eliminarItemsInformadorMasivo()">Eliminar Seleccionados</button>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="table-responsive table-card mt-3 mb-1 mx-auto">
                <table id="listadoExamenesInformador" class="display table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th><input type="checkbox" id="checkAllInformador" name="Id"></th>
                            <th class="sort">Prestacion</th>
                            <th class="sort">Fecha</th>
                            <th class="sort">Examen</th>
                            <th class="sort">Paciente</th>
                            <th class="sort">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="list form-check-all">

                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

<script>
const ID = '{{$facturaCompra->Id}}';

const rutaListarExamenesEfector = "{{ route('facturaCompra.listarExamenesFacturaEfector') }}";
const rutaListarExamenesInformador = "{{ route('facturaCompra.listarExamenesFacturaInformador') }}";
const rutaEditarFactura = "{{ route('facturaCompra.editarFacturaPost') }}";

const eliminarItemFacturaCompraEfector = "{{ route('facturaCompra.eliminarItemFacturaCompraEfector') }}";
const eliminarItemFacturaCompraInformador = "{{ route('facturaCompra.eliminarItemFacturaCompraInformador') }}";

const eliminarItemsFacturaCompraEfectorMasivo = "{{ route('facturaCompra.eliminarItemsFacturaCompraEfectorMasivo') }}";
const eliminarItemsFacturaCompraInformadorMasivo = "{{ route('facturaCompra.eliminarItemsFacturaCompraInformadorMasivo') }}";

const imprimirReporte = "{{ route('facturaCompra.imprimirReporte') }}";
const imprimirExcel = "{{route('facturaCompra.exportarExcelIndividual')}}"
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

<script src="{{ asset('js/facturaCompra/editarFacturaCompra.js')}}?v={{ time() }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>

@endpush
@endsection