@extends('template')

@section('title', 'Crear factura')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Crear factura</h4>
</div>

<div class="card-header d-flex justify-content-between">
    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" data-bs-toggle="tab" href="#altaFactura" role="tab" aria-selected="true">
                <i class="ri-window-line"></i>
                Alta Factura
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#altaFacturaMasiva" role="tab" aria-selected="true">
                <i class="ri-window-line"></i>
                Alta Factura Masiva
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#manualArt" role="tab" aria-selected="false" tabindex="-1">
                <i class="ri-window-line"></i>
                Manual ART
            </a>
        </li>
    </ul>
</div>

<div class="card-body p-4">
    <div class="tab-content">
        
        <div class="tab-pane active" id="altaFactura" role="tabpanel">

            <div class="card">

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-2 mt-3">
                            <label for="fechaDesdeA" class="form-label fw-bolder">Fecha Desde <span class="required">(*)</span></label>
                            <input type="date" class="form-control" id="fechaDesdeA" name="fechaDesdeA">
                        </div>

                        <div class="col-sm-2 mt-3">
                            <label for="fechaHastaA" class="form-label fw-bolder">Fecha Hasta <span class="required">(*)</span></label>
                            <input type="date" class="form-control" id="fechaHastaA" name="fechaHastaA">
                        </div>

                        <div class="col-sm-2 mt-3">
                            <label for="empresa" class="form-label fw-bolder">Empresa <span class="required">(*)</span></label>
                            <select name="empresa" id="empresa" class="form-control"></select>
                        </div>

                        <div class="col-sm-2 mt-3">
                            <label for="pago" class="form-label fw-bolder">Pago</label>
                            <select name="pago" id="pago" class="form-control">
                                <option value="" selected>Elija una opción...</option>
                                <option value="todo">Todas</option>
                                <option value="sincargo">Sin cargo</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="otro">Otras</option>
                            </select>
                        </div>

                        <div class="col-sm-2 mt-3">
                            <label for="tipo" class="form-label fw-bolder">Tipo</label>
                            <select name="tipo" id="tipo" class="form-control">

                            </select>
                        </div>
                    </div>

                    <div class="row mt-2 fondo-grisClaro p-2">
                        <div class="col-sm-3">
                            <label for="nroFactura" class="form-label fw-bolder">Numero de Factura</label>
                            <input class="form-control" type="text" id="nroFactura" name="nroFactura">
                        </div>
        
                        <div class="col-sm-3">
                            <label for="fecha" class="form-label fw-bolder">Fecha</label>
                            <input class="form-control" type="date" id="fecha" name="fecha">
                        </div>
        
                        <div class="col-sm-3">
                            <label for="obs" class="form-label fw-bolder">Observación</label>
                            <input class="form-control" type="text" id="obs" name="obs">
                        </div>
        
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-6 mt-2 text-start">
                            <button type="button" class="btn btn-sm botonGeneral generar">Generar factura</button>
                        </div>
                        <div class="col-sm-6 mt-2 text-end">
                            <button type="button" class="btn btn-sm botonGeneral Contado">Contado</button>
                            <button type="button" class="btn btn-sm botonGeneral buscarAlta"><i class="ri-search-line"></i>Buscar</button>
                        </div>
                    </div>
                    

                </div>
            </div>

            <div class="row mt-2">
                <div class="col-sm-6">
                    <button type="button" class="btn btn-sm botonGeneral"><i class="ri-printer-line"></i>Imprimir</button>
                    <button type="button" class="btn btn-sm botonGeneral"><i class="ri-file-excel-2-line"></i>Exportar</button>
                    <button type="button"class="btn btn-sm botonGeneral"><i class="ri-money-dollar-circle-line"></i>Precios</button>
                </div>
                <div class="col-sm-6 text-end">
                    <button type="button" class="btn btn-sm botonGeneral"><i class="ri-file-search-line"></i>Totales</button>
                    <button type="button" class="btn btn-sm botonGeneral"><i class="ri-add-line"></i>G. Individual</button>
                    <button type="button" class="btn btn-sm botonGeneral"><i class="ri-add-line"></i>G. Totales</button>
                    <button type="button" class="btn btn-sm botonGeneral"><i class="ri-file-excel-2-line"></i>Finneg</button>
                </div>
            </div>

            <div class="table-responsive table-card mb-1 mt-3">
                <table id="listaFacturasAlta" class="display table table-bordered" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th><input type="checkbox" id="checkAllFacturaAlta" name="Id_factura_alta"></th>
                            <th>Alta</th>
                            <th>Prestación</th>
                            <th class="sort">Empresa</th>
                            <th class="sort">ART</th>
                            <th>Tipo</th>
                            <th>Paciente</th>
                            <th>Pago</th>
                            <th>C.Costo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="list form-check-all" id="lstFacturasAlta">
                        
                    </tbody>
                </table>
            </div>

        </div>

        <div class="tab-pane" id="altaFacturaMasiva" role="tabpanel">

            <div class="card">

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-2 mt-3">
                            <label for="fechaDesdeMasivo" class="form-label fw-bolder">Fecha Desde</label>
                            <input type="date" class="form-control" id="fechaDesdeMasivo" name="fechaDesdeMasivo">
                        </div>

                        <div class="col-sm-2 mt-3">
                            <label for="fechaHastaMasivo" class="form-label fw-bolder">Fecha Hasta</label>
                            <input type="date" class="form-control" id="fechaHastaMasivo" name="fechaHastaMasivo">
                        </div>

                        <div class="col-sm-2 mt-3">
                            <label for="clienteMasivo" class="form-label fw-bolder">Cliente</label>
                            <select name="clienteMasivo" id="clienteMasivo" class="form-control"></select>
                        </div>

                        <div class="col-sm-2 mt-3">
                            <label for="remitoMasivo" class="form-label fw-bolder">Remito</label>
                            <select name="remitoMasivo" id="remitoMasivo" class="form-control"></select>
                        </div>

                        <div class="col-sm-2 mt-3">
                            <label for="asignadoMasivo" class="form-label fw-bolder">Asignado</label>
                            <select name="asignadoMasivo" id="asignadoMasivo" class="form-control"></select>
                        </div>
                    </div>

                    <div class="row mt-2 fondo-grisClaro p-2">
                        <div class="col-sm-3">
                            <label for="nroFacturaMasivo" class="form-label fw-bolder">Numero de Factura</label>
                            <input class="form-control" type="text" id="nroFacturaMasivo" name="nroFacturaMasivo">
                        </div>
        
                        <div class="col-sm-3">
                            <label for="fechaMasivo" class="form-label fw-bolder">Fecha</label>
                            <input class="form-control" type="date" id="fechaMasivo" name="fechaMasivo">
                        </div>
        
                        <div class="col-sm-3">
                            <label for="obsMasivo" class="form-label fw-bolder">Observación</label>
                            <input class="form-control" type="text" id="obsMasivo" name="obsMasivo">
                        </div>
        
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-12 mt-2 text-end">
                            <button type="button" class="btn btn-sm botonGeneral buscar"><i class="ri-search-line"></i>Buscar</button>
                        </div>
                    </div>
                    

                </div>
            </div>

            <div class="row mt-2 p-2">
                <div class="col-sm-12 text-end">
                    <button type="button" class="btn btn-sm botonGeneral"><i class="ri-add-line"></i>Generar Factura</button>
                    <button type="button" class="btn btn-sm botonGeneral"><i class="ri-file-excel-line"></i>Finneg</button>
                    <button type="button" class="btn btn-sm botonGeneral"><i class="ri-file-search-line"></i>Totales</button>
                </div>

            </div>

            <div class="table-responsive table-card mb-1 mt-3">
                <table id="listaFacturasAltaMasivo" class="display table table-bordered" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th><input type="checkbox" id="checkAllFacturaAltaMasivo" name="Id_factura_alta_masivo"></th>
                            <th>Cliente</th>
                            <th>Asignado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="list form-check-all" id="lstFacturasAltaMasivo">
                        
                    </tbody>
                </table>
            </div>

        </div>

        <div class="tab-pane" id="manualArt" role="tabpanel">

            <div class="card">

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-2 mt-3">
                            <label for="fechaDesdeArt" class="form-label fw-bolder">Fecha Desde</label>
                            <input type="date" class="form-control" id="fechaDesdeArt" name="fechaDesdeArt">
                        </div>

                        <div class="col-sm-2 mt-3">
                            <label for="fechaHastaArt" class="form-label fw-bolder">Fecha Hasta</label>
                            <input type="date" class="form-control" id="fechaHastaArt" name="fechaHastaArt">
                        </div>

                        <div class="col-sm-2 mt-3">
                            <label for="art" class="form-label fw-bolder">Cliente</label>
                            <select name="art" id="art" class="form-control"></select>
                        </div>

                        <div class="col-sm-2 mt-3">
                            <label for="remitoMasivo" class="form-label fw-bolder">Remito</label>
                            <input type="text" class="form-control" id="remitoArt" name="remitoArt">
                        </div>
                    </div>

                    <div class="row mt-2 fondo-grisClaro p-2">
                        <div class="col-sm-3">
                            <label for="nroFacturaArt" class="form-label fw-bolder">Numero de Factura</label>
                            <input class="form-control" type="text" id="nroFacturaArt" name="nroFacturaArt">
                        </div>
        
                        <div class="col-sm-3">
                            <label for="fechaArt" class="form-label fw-bolder">Fecha</label>
                            <input class="form-control" type="date" id="fechaArt" name="fechaArt">
                        </div>
        
                        <div class="col-sm-3">
                            <label for="obsArt" class="form-label fw-bolder">Observación</label>
                            <input class="form-control" type="text" id="obsArt" name="obsArt">
                        </div>
        
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-12 mt-2 text-end">
                            <button type="button" class="btn btn-sm botonGeneral"><i class="ri-search-line"></i>Buscar</button>
                        </div>
                    </div>

                    <div class="row mt-2 p-2">
                        <div class="col-sm-12 text-end">
                            <button type="button" class="btn btn-sm botonGeneral"><i class="ri-file-search-line"></i>Alta de Factura</button>
                        </div>
        
                    </div>

                    <div class="table-responsive table-card mb-1 mt-3">
                        <table id="listaManualArt" class="display table table-bordered" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th><input type="checkbox" id="checkAllManualArt" name="Id_art"></th>
                                    <th>Cliente</th>
                                    <th>Asignado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all" id="lstManualArt">
                                
                            </tbody>
                        </table>
                    </div>
                    

                </div>
            </div>

        </div>

    </div>
</div>

<div id="ver" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Prestación <span id="nroPrestacion"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body text-center p-5">
                
            </div>
            <div class="modal-footer">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    const TOKEN = "{{ csrf_token() }}";
    const getClientes = "{{ route('getClientes') }}";
    const lstTipoPrestacion = "{{ route('lstTipoPrestacion') }}";
    const paginacionAlta = "{{ route('facturas.paginacion')}}";
    const SEARCHALTA = "{{ route('facturas.paginacion') }}";
    const verDetalle = "{{ route('facturas.detalle') }}";
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

<script src="{{ asset('js/facturacion/create.js')}}?=v{{ time() }}"></script>
<script src="{{ asset('js/facturacion/paginacionAlta.js')}}?=v{{ time() }}"></script>
<script src="{{ asset('js/facturacion/paginacionMasivo.js')}}?=v{{ time() }}"></script>
<script src="{{ asset('js/facturacion/paginacionArt.js')}}?=v{{ time() }}"></script>

<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('js/init.select2.js') }}"></script>
@endpush
@endsection