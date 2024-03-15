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
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#pago" role="tab" aria-selected="false" tabindex="-1">
                <i class=" ri-window-line"></i>
                Pagos
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
                    <div class="card">
                        <div class="card-body">
                            <div class="listjs-table" id="customerList">
                                <div class="row g-4 mb-3">
            
                                    <form id="form-index">
                                        <div class="col-12 p-4 border border-1 border-color" style="border-color: #666666;">
                                            
                                            <div class="row">
            
                                                <div class="col-sm-2 mb-3">
                                                    <label for="fechaDesde" class="form-label font-weight-bold"><strong>Fecha desde: <span class="required">(*)</span></strong></label>
                                                    <input type="date" class="form-control" id="fechaDesde" name="fechaDesde">
                                                </div>
            
                                                <div class="col-sm-2 mb-3">
                                                    <label for="fechaDesde" class="form-label font-weight-bold"><strong>Fecha hasta: <span class="required">(*)</span></strong></label>
                                                    <input type="date" class="form-control" id="fechaHasta" name="fechaHasta">
                                                </div>
            
                                                <div class="col-sm-2 mb-3">
                                                    <label for="rangoDesde" class="form-label font-weight-bold"><strong>Factura Desde o Individual: </strong></label>
                                                    <input type="text" class="form-control" id="rangoDesde" name="rangoDesde">
                                                </div>
            
                                                <div class="col-sm-2 mb-3">
                                                    <label for="rangoDesde" class="form-label font-weight-bold"><strong>Factura Hasta: </strong></label>
                                                    <input type="text" class="form-control" id="rangoHasta" name="rangoHasta">
                                                </div>

                                                <div class="col-sm-2 mb-3">
                                                    <label for="empresa" class="form-label font-weight-bold"><strong>Empresa:</strong></label>
                                                    <select class="form-control" name="empresa" id="empresa"></select>
                                                </div>

                                                <div class="col-sm-2 mb-3">
                                                    <label for="examen" class="form-label font-weight-bold"><strong>Examen:</strong></label>
                                                    <select class="form-control" name="examen" id="examen"></select>
                                                </div>

                                            </div>
            
                                            <div class="row">
                                                
                                                <div class="col-sm-2 mb-3">
                                                    <label for="paciente" class="form-label font-weight-bold"><strong>Paciente / DNI:</strong></label>
                                                    <select class="form-control" name="paciente" id="paciente"></select>
                                                </div>
            
                                                
                                                <div class="col-sm-10 d-flex align-items-center justify-content-end">
                                                    <button type="button" id="buscar" class="btn botonGeneral"><i class="ri-zoom-in-line"></i>&nbsp;Buscar</button>&nbsp;
                                                    <button type="button" id="agregar" class="btn botonGeneral"><i class="ri-add-fill"></i>&nbsp;Agregar</button>
                                                </div>
                                                
            
                                            </div>
                                        
                                        </div>
                                    </form>
                                </div>

                                <div class="table-responsive table-card mt-3 mb-1 mx-auto">
                                    <table id="listadoExamenesCuentas" class="display table table-bordered" style="width:100%">
                                        <thead class="table-light">
                                            <tr>
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
                                </div>           
                            </div>
                        </div>
                    </div>
                </div>
            
            </div>
        </div>

        <div class="tab-pane" id="saldo" role="tabpanel">
        </div>

        <div class="tab-pane" id="pago" role="tabpanel">
        </div>
    </div>
</div>



<script>
    const getClientes = "{{ route('getClientes') }}";
    const searchExamen = "{{ route('searchExamen') }}";
    const getPacientes = "{{ route('getPacientes') }}";
    const TOKEN = "{{ @csrf_token() }}";
    const SEARCH = "{{ route('searchExCuenta') }}";
    const cambiarPago = "{{ route('cambiarPago') }}";
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

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
@endpush

@endsection