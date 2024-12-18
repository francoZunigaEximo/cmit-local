@extends('template')

@section('title', 'Mapas')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Mapas</h4>

    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="{{ route('mapas.index') }}">Mapas</a></li>
            <li class="breadcrumb-item active">Listado</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="listjs-table" id="customerList">
                    <div class="row g-4 mb-3">
                        <div class="col-12 p-4 border border-1 border-color" style="border-color: #666666;">
                            
                            <div class="row">
                                <div class="col-sm-3 mb-3">
                                    <label for="Nro" class="form-label font-weight-bold"><strong>Código:</strong></label>
                                    <input type="text" class="form-control" id="Nro" name="Nro" placeholder="Buscar por código">
                                </div>

                                <div class="col-sm-3 mb-3">
                                    <label for="ART" class="form-label font-weight-bold"><strong>ART:</strong></label>
                                    <select class="form-control" name="ART" id="ART"></select>
                                </div>

                                <div class="col-sm-3 mb-3">
                                    <label for="Empresa" class="form-label font-weight-bold"><strong>Empresa:</strong></label>
                                    <select class="form-control" name="Empresa" id="Empresa"></select>
                                </div>

                                <div class="col-sm-3 mb-3">
                                    <label for="Estado" class="form-label font-weight-bold"><strong>Estado:</strong></label>
                                    <select class="form-control name="Estado" id="Estado"> 
                                        <option value="" selected>Elija una opción...</option>
                                        <option value="abierto">Abierto</option>
                                        <option value="cerrado">Cerrado</option>
                                        <option value="eEnviado">E-enviado</option>
                                        <option value="enProceso">En proceso</option>
                                        <option value="terminado">Terminado</option>
                                        <option value="todos">Todos</option>
                                        <option value="vacio">Vacío</option>                                            
                                    </select>
                                </div>

                                <div class="col-sm-3 mb-3">
                                    <label for="Corte" class="form-label font-weight-bold"><strong>Corte:</strong></label>
                                    <input type="date" class="form-control" id="corteDesde" name="corteDesde">
                                    <input type="date" class="form-control" id="corteHasta" name="corteHasta">
                                </div>

                                <div class="col-sm-3 mb-3">
                                    <label for="Corte" class="form-label font-weight-bold"><strong>Entrega:</strong></label>
                                    <input type="date" class="form-control" id="entregaDesde" name="entregaDesde">
                                    <input type="date" class="form-control" id="entregaHasta" name="entregaHasta">
                                </div>

                                <div class="col-sm-3 mb-3">
                                    <label for="Vencimiento" class="form-label font-weight-bold"><strong>Vencimiento:</strong></label>
                                    <select class="js-example-basic-multiple" name="Vencimiento[]" multiple="multiple" id="Vencimiento" data-placeholder="Elija una opción..."> 
                                        <option value="corteVigente">Corte Vigente</option>
                                        <option value="entregaVigente">Entrega Vigente</option>
                                        <option value="corteVencido">Corte Vencido</option>
                                        <option value="entregaVencida">Entrega Vencida</option>
                                    </select>
                                </div>

                                <div class="col-sm-3 mb-3">
                                    <label for="Ver" class="form-label font-weight-bold"><strong>Ver:</strong></label>
                                    <select name="Ver" id="Ver" class="form-control">
                                        <option selected value="">Elija una opción...</option>
                                        <option value="activo">Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                    </select>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-sm-9 mb-3">
                                    <span class="custom-badge amarillo"> Entregas entre 15 a 11 días </span>
                                    <span class="custom-badge naranja">Entregas entre 10 a 1 día</span>
                                    <span class="custom-badge rojo">Entregas con 0 en adelante mientras no este eEnviado</span>
                                    <span class="custom-badge verde">Entregas eEnviadas</span>
                                </div>
                                <div class="col-sm-3" style="text-align: right;">
                                    <button type="button" id="reset" class="btn botonGeneral">Reiniciar</button>
                                    <button type="button" id="buscar" class="btn botonGeneral">Buscar</button>
                                </div>
                            </div>
                            
                        </div>
                    </div>

                    <div class="col-sm-9">
                        <div>
                            <a class="btn btn-sm botonGeneral" href="{{ route('mapas.create') }}">
                                <i class="ri-add-line align-bottom me-1"></i> 
                                Nuevo
                            </a>   
                            <button type="button" id="excel" class="btn btn-sm botonGeneral" title="Generar reporte en Excel">
                                <i class="ri-file-excel-line"></i>
                                Excel
                            </button>
                        </div>
                    </div>

                    <div class="table mt-3 mb-1 mx-auto">
                        <table id="listaMapas" class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th><input type="checkbox" id="checkAll" name="Id"></th>
                                    <th class="sort">Código</th>
                                    <th class="sort">ART</th>
                                    <th class="sort">Empresa</th>
                                    <th class="sort">Corte</th>
                                    <th class="sort">Entrega</th>
                                    <th class="sort">Presentes/Total</th>
                                    <th class="sort">Estado</th>
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
<script>
    //Rutas
    const SEARCH = "{{ route('mapas.index') }}"
    const routeMapas = "{{ route('deleteMapa', ['mapa' => '']) }}";
    const fileExport = "{{ route('mapas.exportar') }}";
    const deleteMapa = "{{ route('deleteMapa') }}";
    const getClientes = "{{ route('getClientes') }}";
    //Extras
    const TOKEN = "{{ csrf_token() }}";

</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v=?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">
@endpush


@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script src="{{ asset('js/mapas/index.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/mapas/paginacion.js') }}?v={{ time() }}"></script>


<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
@endpush

@endsection