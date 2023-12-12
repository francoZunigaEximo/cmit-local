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

                        <form id="form-index">
                            <div class="col-12 p-4 border border-1 border-color" style="border-color: #666666;">
                                
                                <div class="row">
                                    <div class="col-sm-3 mb-3">
                                        <label for="Nro" class="form-label font-weight-bold"><strong>Código:</strong></label>
                                        <input type="text" class="form-control" id="Nro" name="Nro" placeholder="Buscar por código">
                                    </div>

                                    <div class="col-sm-3 mb-3">
                                        <label for="ART" class="form-label font-weight-bold"><strong>ART:</strong></label>
                                        <input type="text" class="form-control" id="ART" name="ART" placeholder="Buscar por ART">
                                    </div>

                                    <div class="col-sm-3 mb-3">
                                        <label for="Empresa" class="form-label font-weight-bold"><strong>Empresa:</strong></label>
                                        <input type="text" class="form-control" id="Empresa" name="Empresa" placeholder="Buscar por Empresa">
                                    </div>

                                    <div class="col-sm-3 mb-3">
                                        <label for="Estado" class="form-label font-weight-bold"><strong>Estado:</strong></label>
                                        <select name="Estado" id="Estado" class="form-control">
                                            <option selected value="">Elija una opción...</option>
                                            <option value="NOeEnviado">No e-enviado</option>
                                            <option value="terminado">Terminado</option>
                                            <option value="abierto">Abierto</option>
                                            <option value="enProceso">En proceso</option>
                                            <option value="cerrado">Cerrado</option>
                                            <option value="eEnviado">E-enviado</option>
                                            <option value="conEenviados">Con e-enviados</option>
                                            <option value="todos">Todos</option>
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
                                        <select name="Vencimiento" id="Vencimiento" class="form-control">
                                            <option selected value="">Elija una opción...</option>
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
                                        <span class="custom-badge violeta">Entregas vencidas</span>
                                        <span class="custom-badge verde">Entregas vencidas eEnviadas</span>
                                        <span class="custom-badge rojo">Entregas a 10 días de vencer</span>
                                        <span class="custom-badge amarillo"> Entregas a mas de 10 dias de vencer, que ya cortaron</span>
                                    </div>
                                    <div class="col-sm-3" style="text-align: right;">
                                        <button type="button" id="reset" class="btn botonGeneral">Reiniciar</button>
                                        <button type="button" id="buscar" class="btn botonGeneral">Buscar</button>
                                    </div>
                                </div>
                               
                            </div>
                        </form>

                    </div>

                    <div class="col-sm-9">
                        <div>
                            <a href="{{ route('mapas.create') }}">
                                <button type="button" class="btn botonGeneral add-btn">
                                    <i class="ri-add-line align-bottom me-1"></i> Nuevo
                                </button>
                            </a>   
                            <button type="button" id="excel" class="btn iconGeneral" style="" data-bs-toggle="tooltip" data-bs-placement="top" title="Generar reporte en Excel">
                                <i class="ri-file-excel-line"></i>
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive table-card mt-3 mb-1">
                        <table id="listaMapas" class="display table table-bordered" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th><input type="checkbox" id="checkAll" name="Id"></th>
                                    <th class="sort">Código</th>
                                    <th class="sort">ART</th>
                                    <th class="sort">Empresa</th>
                                    <th class="sort">Corte</th>
                                    <th class="sort">Entrega</th>
                                    <th class="sort">Prestaciones</th>
                                    <th class="sort">Saldo</th>
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
    const SEARCH = "{{ route('searchMapas') }}"
    const routeMapas = "{{ route('deleteMapa', ['mapa' => '']) }}";
    const exportExcelMapas = "{{ route('exportExcelMapas') }}";
    const deleteMapa = "{{ route('deleteMapa') }}";
    //Extras
    const TOKEN = "{{ csrf_token() }}";

</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v=?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
@endpush


@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

<script src="{{ asset('js/mapas/index.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/mapas/paginacion.js') }}?v={{ time() }}"></script>

<script src="{{ asset('js/scripts.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
@endpush

@endsection