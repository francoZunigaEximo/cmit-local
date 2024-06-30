@extends('template')

@section('title', 'Facturas')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Facturación</h4>
</div>

<div class="card">

    <div class="card-body">
        <div class="row">

                <div class="col-sm-3 mt-3">
                    <label for="fechaDesde" class="form-label fw-bolder">Fecha Desde</label>
                    <input type="date" class="form-control" id="fechaDesde" name="fechaDesde">
                </div>

                <div class="col-sm-3 mt-3">
                    <label for="fechaHasta" class="form-label fw-bolder">Fecha Hasta</label>
                    <input type="date" class="form-control" id="fechaHasta" name="fechaHasta">
                </div>
                
                <div class="col-sm-3 mt-3">
                    <label for="facturaDesde" class="form-label fw-bolder">Factura Desde</label>
                    <input type="text" class="form-control" id="facturaDesde" name="facturaDesde">
                </div>

                <div class="col-sm-3 mt-3">
                    <label for="facturaHasta" class="form-label fw-bolder">Factura Hasta</label>
                    <input type="text" class="form-control" id="facturaHasta" name="facturaHasta">
                </div>

                <div class="col-sm-2 mt-3">
                    <label for="empresa" class="form-label fw-bolder">Empresa</label>
                    <select class="form-control" name="empresa" id="empresa"></select>
                </div>

                <div class="col-sm-2 mt-3">
                    <label for="art" class="form-label fw-bolder">ART</label>
                    <select class="form-control" name="art" id="art"></select>
                </div>

                <div class="col-sm-2 mt-3">
                    <label for="tabla" class="form-label fw-bolder">Ver Tabla <span class="required">(*)</span></label>
                    <select name="tabla" id="tabla" class="form-control">
                        <option value="" selected>Elija una opción...</option>
                        <option value="facturas">Facturas</option>
                        <option value="exacuenta">Facturas Exa. a Cuenta</option>
                        <option value="todo">Todo</option>
                    </select>
                </div>

                <div class="col-sm-12 mt-3 text-end">
                    <button type="button" class="btn btn-sm botonGeneral buscar"><i class="ri-search-line"></i>Buscar</button>
                    <a class="btn btn-sm botonGeneral" href="{{ route('facturas.index') }}"><i class="ri-refresh-line"></i>Reiniciar</a>
                </div>
        </div>
    </div>
</div>

<div class="row fondo-grisClaro mt-2 p-2">
    <div class="col-sm-6">
        <button type="button" class="btn btn-sm botonGeneral"><i class="ri-file-excel-line"></i>Finneg</button>
        <button type="button" class="btn btn-sm botonGeneral"><i class="ri-file-excel-line"></i>FinnegART</button>
    </div>
    <div class="col-sm-6 text-end">
        Filtros:  <button type="button" class="btn btn-sm botonGeneral Hoy">Hoy</button>
        <button type="button" class="btn btn-sm botonGeneral FacturasSN">Factura S/N</button>
    </div>
</div>
      
<div class="row mt-2">
    <div class="col-sm-12 text-end">
        <button type="button" class="btn btn-sm botonGeneral" data-bs-toggle="modal" data-bs-target="#opcionesEnvio"><i class="ri-send-plane-line"></i>Enviar</button>
        <button type="button" class="btn btn-sm botonGeneral eliminarMultiple"><i class="ri-delete-bin-6-line"></i>Eliminar</button>
        <button type="button" class="btn btn-sm botonGeneral"><i class="ri-money-dollar-circle-line"></i>Precio</button>
        <a class="btn btn-sm botonGeneral" href="{{ route('facturas.create') }}"><i class="ri-add-line"></i>Nuevo</a>
    </div>
</div>

<div class="table-responsive table-card mb-1 mt-3">
    <table id="listaFacturas" class="display table table-bordered" style="width:100%">
        <thead class="table-light">
            <tr>
                <th class="text-center"><input type="checkbox" id="checkAllFactura" name="Id_factura"></th>
                <th>Numero</th>
                <th>Factura</th>
                <th>Fecha Fac</th>
                <th class="sort">Empresa</th>
                <th>Cuit</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody class="list form-check-all" id="lstFacturas">
            
        </tbody>
    </table>
</div>

<div id="opcionesEnvio" class="modal fadeInUp" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Seleccione una opción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="row fondo-grisClaro p-2">
                    <div class="col-sm-6 text-center">
                        <div class="mb-1">
                            <button class="btn btn-sm botonGeneral enviar"><i class="ri-send-plane-line"></i>Enviar</button>
                        </div>
                        <small class="text-muted">Envio de email con el detalle de la futura factura.</small>
                    </div>
                    <div class="col-sm-6 text-center">
                        <div class="mb-1">
                            <button class="btn btn-sm botonGeneral imprimir"><i class="ri-printer-line"></i>Imprimir</button>
                        </div>
                        <small class="text-muted">Descargar en PDF los detalles de las facturas.</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn botonGeneral" data-bs-dismiss="modal">Cancelar</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    const TOKEN = "{{ csrf_token() }}";
    const SEARCH = "{{ route('facturas.search') }}";
    const getClientes = "{{ route('getClientes') }}";
    const checkNotaCredito = "{{ route('nota-de-credito.check') }}";

    const lnkFactura = "{{ route('facturas.edit', '__id__') }}";
    const lnkExamenCuenta = "{{ route('examenesCuenta.edit', '__id__') }}";

    const eliminarFactura = "{{ route('facturas.delete')}}";
    const exportar = "{{ route('facturas.export')}}";
    const enviarDetalle = "{{ route('facturas.enviar')}}";
</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v=?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/inputmask/dist/jquery.inputmask.min.js"></script>
<!--datatable js-->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

<script src="{{ asset('js/facturacion/index.js')}}?=v{{ time() }}"></script>
<script src="{{ asset('js/facturacion/paginacion.js')}}?=v{{ time() }}"></script>

<script src="{{ asset('js/select2.min.js') }}"></script>
@endpush
@endsection