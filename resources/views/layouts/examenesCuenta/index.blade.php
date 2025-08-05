@extends('template')

@section('title', 'Examenes a cuenta')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Examenes a cuenta </h4>

    <div class="page-title-right"></div>
</div>

<div class="card-header d-flex justify-content-between">
    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" data-bs-toggle="tab" href="#exCuenta" role="tab" aria-selected="true">
                <i class="ri-window-line"></i>
                Examenes a Cuenta
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#saldo" role="tab" aria-selected="false" tabindex="-1">
                <i class="ri-window-line"></i>
                Saldos
            </a>
        </li>        
    </ul>
</div>

<div class="card-body p-4">
    <div class="tab-content">
        <div id="messageClientes"></div>

        <div class="tab-pane active" id="exCuenta" role="tabpanel">
            <div class="row">
                <div class="col-lg-12">

                    <div class="row">
                        <div class="small col-sm-12 mb-2"><span class="required">(*)</span> El campo es obligatorio.</div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="listjs-table" id="customerList">
                                <div class="row g-4 mb-3">
            
                                    <form id="form-index">
                                        <div class="col-12 p-4 border border-1 border-color" style="border-color: #666666;">

                                            <div class="row">
            
                                                <div class="col-sm-2 mb-3">
                                                    <label for="fechaDesde" class="form-label fw-bolder">Fecha desde: <span class="required">(*)</span></label>
                                                    <input type="date" class="form-control" id="fechaDesde" name="fechaDesde">
                                                </div>
            
                                                <div class="col-sm-2 mb-3">
                                                    <label for="fechaDesde" class="form-label fw-bolder">Fecha hasta: <span class="required">(*)</span></label>
                                                    <input type="date" class="form-control" id="fechaHasta" name="fechaHasta">
                                                </div>
            
                                                <div class="col-sm-2 mb-3">
                                                    <label for="rangoDesde" class="form-label fw-bolder">Factura Desde o Individual: </label>
                                                    <input type="text" class="form-control" id="rangoDesde" name="rangoDesde">
                                                </div>
            
                                                <div class="col-sm-2 mb-3">
                                                    <label for="rangoDesde" class="form-label fw-bolder">Factura Hasta: </label>
                                                    <input type="text" class="form-control" id="rangoHasta" name="rangoHasta">
                                                </div>

                                                <div class="col-sm-2 mb-3">
                                                    <label for="empresa" class="form-label fw-bolder">Empresa:</label>
                                                    <select class="form-control" name="empresa" id="empresa"></select>
                                                </div>

                                                <div class="col-sm-2 mb-3">
                                                    <label for="examen" class="form-label fw-bolder">Examen:</label>
                                                    <select class="form-control" name="examen" id="examen"></select>
                                                </div>

                                            </div>
            
                                            <div class="row">
                                                
                                                <div class="col-sm-2 mb-3">
                                                    <label for="paciente" class="form-label fw-bolder">Paciente / DNI:</label>
                                                    <select class="form-control" name="paciente" id="paciente"></select>
                                                </div>

                                                <div class="col-sm-2 mb-3">
                                                    <label for="estado" class="form-label fw-bolder">Estado: </label>
                                                    <select class="form-control" name="estado" id="estado">
                                                        <option value="" selected>Elija una opción...</option>
                                                        <option value="">Impagos</option>
                                                        <option value="pago">Pagos</option>
                                                        <option value="todos">Todos</option>
                                                    </select>
                                                </div>
            
                                                
                                                <div class="col-sm-8 d-flex align-items-center justify-content-end">
                                                    <button type="button" id="buscar" class="btn botonGeneral"><i class="ri-zoom-in-line"></i>&nbsp;Buscar</button>&nbsp;
                                                    <a id="agregar" class="btn botonGeneral" href="{{ route('examenesCuenta.create') }}"><i class="ri-add-fill"></i>&nbsp;Agregar</a>
                                                </div>
                                                
            
                                            </div>
                                        
                                        </div>
                                </div>

                                <div class="row">
                                    <div class="col-8 text-start">
                                        <span class="fw-normal">Busquedas rápidas:</span>
                                        <button type="button" class="btn btn-sm botonGeneral facturasHoy"><i class="ri-calendar-2-line"></i>&nbsp;Hoy</button>
                                        <button type="button" class="btn btn-sm botonGeneral sieteDias"><i class="ri-calendar-2-line"></i>&nbsp;Ultimos 7 días</button>
                                        <button type="button" class="btn btn-sm botonGeneral tresMeses"><i class="ri-calendar-2-line"></i>&nbsp;Ultimos 3 meses</button>
                                    </div>
                                    
                                    <div class="col-4 text-end">
                                        <button type="button" class="btn btn-sm botonGeneral botonPagar"><i class=" ri-money-dollar-circle-line"></i>&nbsp;Pagar masivo</button>
                                        <button type="button" class="btn btn-sm botonGeneral quitarPago"><i class=" ri-money-dollar-circle-line"></i>&nbsp;Quitar pago masivo</button>
                                    </div>
                                    
                                </div>

                                <div class="table-responsive table-card mt-3 mb-1 mx-auto">
                                    <table id="listadoExamenesCuentas" class="display table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th><input type="checkbox" id="checkAll" name="Id"></th>
                                                <th class="sort">Número</th>
                                                <th class="sort">Factura</th>
                                                <th class="sort">Fecha</th>
                                                <th class="sort">Empresa</th>
                                                <th class="sort">Para Empresa</th>
                                                <th class="sort">Pagado</th>
                                                <th>Detalles</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all">
            
                                        </tbody>
                                    </table>
                                </form>
                                </div>           
                            </div>
                        </div>
                    </div>
                </div>
            
            </div>
        </div>

        <div class="tab-pane" id="saldo" role="tabpanel">

            <div class="row justify-content-md-center">
                <form id="form-index">
                    <div class="col p-2 border border-1 border-color" style="border-color: #666666;">
                        
                        <div class="row justify-content-center">
                            <div class="col-sm-3 mb-3">
                                <label for="empresaSaldo" class="form-label font-weight-bold"><strong>Empresa: </strong></label>
                                <select class="form-control" name="empresaSaldo" id="empresaSaldo"></select>
                            </div>

                            <div class="col-sm-3 mb-3">
                                <label for="examenSaldo" class="form-label font-weight-bold"><strong>Examen: </strong></label>
                                <select class="form-control" name="examenSaldo" id="examenSaldo"></select>
                            </div>

                            <div class="col-sm-3 mb-3">
                                <label for="examenSaldo2" class="form-label font-weight-bold"><strong>Examen 2: </strong></label>
                                <input type="text" class="form-control" id="examenSaldo2" name="examenSaldo2">
                            </div>

                            <div class="col-sm-3 mb-3 d-flex align-items-end justify-content-center">
                                <label class="form-label" for=""></label>
                                <button type="button" class="btn btn-sm botonGeneral" id="buscarSaldo"><i class="ri-zoom-in-line"></i>&nbsp;Buscar</i></button>
                            </div>

                        </div>
                    </div>
                    <div class="row p-3">
                        <div class="col-2 p-1">
                            <button type="button" class="btn btn-sm botonGeneral excelDetalleSaldos" >Detalle</button>
                            <button type="button" class="btn btn-sm botonGeneral excelExcel" >Excel</button>
                        </div>
                    </div>
                </form>  
            </div> 

            <div class="row auto-mx">

                <div class="table-responsive table-card mt-3 mb-1 mx-auto">
                    <table id="listadoExCtasSaldos" class="display table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th class="sort">Empresa</th>
                                <th class="sort">Examen</th>
                                <th>Saldo</th>
                                <th>Reportes</th>
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



<script>
    const getClientes = "{{ route('getClientes') }}";
    const searchExamen = "{{ route('searchExamen') }}";
    const getPacientes = "{{ route('getPacientes') }}";
    const SEARCH = "{{ route('searchExCuenta') }}";
    const INDEX = "{{ route('examenesCuenta.index') }}";
    const SALDOS = "{{ route('searchSaldo') }}";
    const cambiarPago = "{{ route('cambiarPago') }}";
    const detallesExamenes = "{{ route('detallesExamenes') }}";
    const eliminarExCuenta = "{{ route('eliminarExCuenta') }}";
    const exportarDetalle = "{{ route('notasCredito.exportarDetalle') }}";
    const exportGeneral = "{{ route('notasCredito.exportarExcel') }}";
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


<script src="{{ asset('js/examenescuenta/index.js')}}?=v{{ time() }}"></script>
<script src="{{ asset('js/examenescuenta/paginacion.js')}}?=v{{ time() }}"></script>
<script src="{{ asset('js/examenescuenta/paginacionSaldos.js')}}?=v{{ time() }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
<script src="{{ asset('js/fancyTable.js') }}"></script>
@endpush

@endsection