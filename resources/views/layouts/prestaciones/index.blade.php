@extends('template')

@section('title', 'Prestaciones')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-start">
    <h4 class="mb-sm-0">Prestaciones</h4>

    <div class="page-title-right">
        <!-- Button trigger modal -->
       <x-helper>
            {!!$helper!!}
       </x-helper>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="card">

            <div id="mensajeria"></div>

            <div class="card-body">
                <div class="listjs-table" id="customerList">
                    <div class="row g-4 mb-3 justify-content-center">

                        <form id="form-index">
                            <div class="col-12 p-3 border border-1 border-color" style="border-color: #666666;">
                                <div class="row">


                                    <div class="col-2 p-1">
                                        <div>
                                            <label for="fechaHasta" class="form-label"><strong>Fecha desde: </strong><span class="required">(*)</span></label>
                                            <input type="date" class="form-control" id="fechaDesde">
                                            <small class="text-muted">Obligatorio.</small>
                                        </div>
                                    </div>

                                    <div class="col-2 p-1">
                                        <div>
                                            <label for="fechaHasta" class="form-label"><strong>Fecha hasta: </strong><span class="required">(*)</span></label>
                                            <input type="date" class="form-control" id="fechaHasta">
                                            <small class="text-muted">Obligatorio.</small>
                                        </div>
                                    </div>

                                    <div class="col-2 p-1">
                                        <label for="TipoPrestacion" class="form-label"><strong>Tipo de prestación:</strong></label>
                                        <select class="js-example-basic-multiple" name="tipoPrestacion[]" multiple="multiple" id="TipoPrestacion" data-placeholder="Elija una opción...">
                                            <option value="CARNET">Carnet</option>
                                            <option value="EGRESO">Egreso</option>
                                            <option value="INGRESO">Ingreso</option>
                                            <option value="NO_ART">NO ART</option>
                                            <option value="OCUPACIONAL">Ocupacional</option>
                                            <option value="OTRO">Otro</option>
                                            <option value="PERIODICO">Periódico</option>
                                            <option value="RECMED">Redmec</option>
                                            <option value="S/C_OCUPACIO">S/C Ocupacional</option>
                                        </select>
                                    </div>

                                    <div class="col-2 p-1">
                                        <div>
                                            <label for="Estado" class="form-label"><strong>Estado:</strong></label>
                                            <select class="js-example-basic-multiple" name="estados[]" multiple="multiple" id="Estado" data-placeholder="Elija una opción...">
                                                <optgroup label="Estado">
                                                    <option value="Abierto">Abierto</option>
                                                    <option value="Cerrado">Cerrado</option>
                                                    <option value="Finalizado">Finalizado</option>
                                                    <option value="Entregado">Entregado</option>
                                                    <option value="eEnviado">eEnviado</option>
                                                    <option value="Facturado">Facturado</option>
                                                </optgroup>
                                                <optgroup label="Pago">
                                                    <option value="Pago-C">Cuenta corriente</option>
                                                    <option value="Pago-P">Examen a cuenta</option>
                                                    <option value="Pago-B">Contado</option>
                                                </optgroup>
                                                <optgroup label="Forma de Pago">
                                                    <option value="SPago-G">Sin cargo</option>
                                                    <option value="SPago-F">Transferencia</option>
                                                    <option value="SPago-E">Otra</option>
                                                </optgroup>
                                                <optgroup label="Filtros">
                                                    <option value="Anulado">Anulado</option>
                                                    <option value="Ausente">Ausente</option>
                                                    <option value="Devol">Devolución</option>
                                                    <option value="Forma">Forma</option>
                                                    <option value="Incompleto">Incompleto</option>
                                                    <option value="RxPreliminar">Rx Preliminar</option>
                                                    <option value="SinEsc">Sin Escanear</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-2 p-1">
                                        <label for="nroprestacion" class="form-label font-weight-bold"><strong>Nro. Prestación</strong></label>
                                        <input type="text" placeholder="Nro." class="form-control" id="nroprestacion">
                                        <small class="text-muted">Anula todos los filtros su uso.</small>
                                    </div>

                                    <div class="col-2 p-1 d-flex align-items-center justify-content-center">
                                        <button type="button" class="btn botonGeneral buscarPrestaciones"><i class="ri-zoom-in-line"></i>&nbsp;Buscar</button>&nbsp;
                                        <button type="button" class="btn botonGeneral hoyPrestaciones"><i class="ri-zoom-in-line"></i>&nbsp;Hoy</button>
                                    </div>


                                    <!-- Filtros avanzados -->
                                    <div class="collapse" id="filtrosAvanzados">
                                        <div class="card mb-3">
                                            <div class="card-body" style="background: #eaeef3">
                                                <div class="row">

                                                    <div class="col-3 p-2">
                                                        <div>
                                                            <label for="pacienteSelect2" class="form-label font-weight-bold"><strong>Paciente</strong></label>
                                                            <select name="pacienteSelect2" class="form-control" id="pacienteSelect2">
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-3 p-2">
                                                        <div>
                                                            <label for="empresaSelect2" class="form-label font-weight-bold"><strong>Empresa</strong></label>
                                                            <select name="empresaSelect2" class="form-control" id="empresaSelect2">
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-3 p-2">
                                                        <div>
                                                            <label for="artSelect2" class="form-label font-weight-bold"><strong>Art</strong></label>
                                                            <select name="artSelect2" class="form-control" id="artSelect2">
                                                            </select>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-12">
                        <div>
                            @can('prestaciones_add')
                            <button type="button" class="btn botonGeneral add-btn" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTop" aria-controls="offcanvasTop">
                                <i class="ri-add-line align-bottom me-1"></i> Nuevo
                            </button>
                            @endcan
                            <button title="Filtros avanzados" class="btn btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosAvanzados" aria-expanded="false" aria-controls="filtrosAvanzados">
                                Filtros <i class="ri-filter-2-line"></i>
                            </button>
                            @can('prestaciones_report')
                            <button type="button" class="btn iconGeneral exportExcel" data-id="simple" title="Reporte Simple">
                                Simple <i class="ri-file-excel-line"></i>
                            </button>
                            <button type="button" class="btn iconGeneral exportExcel" data-id="detallado" title="Reporte Detallado">
                                Detallado <i class="ri-file-excel-line"></i>
                            </button>
                            <button type="button" class="btn iconGeneral exportExcel" data-id="completo" title="Reporte Completo">
                                Completo <i class="ri-file-excel-line"></i>
                            </button>
                            @endcan
                        </div>
                    </div>

                    <div class="table-responsive w-100 mt-3 mb-1">

                        <table id="listaPrestaciones" class="table nowrap align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="sort">% Av</th>
                                    <th class="sort">Fecha</th>
                                    <th class="sort">Nro</th>
                                    <th class="sort">Paciente</th>
                                    <th class="sort">Tipo</th>
                                    <th class="sort">Empresa</th>
                                    <th class="sort">Para Empresa</th>
                                    <th class="sort">ART</th>
                                    <th class="sort">Estado</th>
                                    <th>eEnv</th>
                                    <th>INC</th>
                                    <th>AUS</th>
                                    <th>FOR</th>
                                    <th>DEV</th>
                                    <th>FP</th>
                                    <th>FAC</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div><!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end col -->
</div>


<!-- Default Modals -->
<div id="prestacionModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Comentario a prestación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <h5 class="fs-15">
                    Escriba un comentario de la prestación número <span id="IdComentarioEs"></span>
                </h5>
                <textarea id="comentario" rows="10" class="form-control"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" id="cancelar" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary guardarComentario">Guardar Comentario</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- top offcanvas -->
<div class="offcanvas offcanvas-top" tabindex="-1" id="offcanvasTop" aria-labelledby="offcanvasTopLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasTopLabel">Verificación de paciente para generar una prestación. Siga los pasos:</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="row">
            <div class="col-xl-3"></div>
            <div class="col-xl-6 d-flex justify-content-center align-items-center p-3 rounded" style="background-color: #d4e0ee">
                <label class="form-label" style="color: #5484bc; font-size: 1.3em; margin:auto 1em;">Paciente</label>
                <select class="form-select" id="paciente"></select>
                <button type="button" class="btn botonGeneral d-inline-flex" id="checkPaciente" style="margin-left: 5px"><i class="ri-check-line me-2"></i>&nbsp;Crear</button>
                <a href="{{ route('pacientes.create') }}">
                    <button type="button" class="btn botonGeneral d-inline-flex" style="margin-left: 10px"><i class="ri-user-add-line"></i>&nbsp;Nuevo</button>
                </a>
            </div>
            <div class="col-xl-3"></div>
        </div>
    </div>
</div>

<script>
    //Rutas
    const getPacientes = "{{ route('getPacientes') }}";
    const getComentarioPres = "{{ route('comentarios.obtener') }}";
    const setComentarioPres = "{{ route('comentarios.guardar') }}";
    const searchPrestaciones = "{{ route('prestaciones.index') }}";

    const GOPACIENTES = "{{ route('pacientes.edit', ['paciente' => '__paciente__']) }}";
    const downPrestaActiva = "{{ route('prestaciones.baja') }}";
    const blockPrestacion = "{{ route('blockPrestacion') }}";
    const SEARCH = "{{ route('prestaciones.index') }}";
    // const SEARCH = "{{ route('searchPrestaciones') }}";

    function exportExcel(tipo) {

        var listaPrestaciones = $('#listaPrestaciones').DataTable();
        
        if (!listaPrestaciones.data().any() ) {
            $('#listaPrestaciones').DataTable().destroy();
            toastr.info('No existen registros para exportar', 'Atención');
            return;
        }


        filters = "";
        length  = $('input[name="Id"]:checked').length;

        let data = listaPrestaciones.rows({ page: 'current' }).data().toArray();
        let ids = data.map(function(row) {
            return row.Id;
    });

        if(!['',0, null].includes(ids)) {
            filters += "nroprestacion:" + $('#nroprestacion').val() + ",";
            filters += "paciente:" + $('#pacienteSearch').val() + ",";
            filters += "empresa:" + $('#empresaSearch').val() + ",";
            filters += "art:" + $('#artSearch').val() + ",";
            filters += "tipoPrestacion:" + $('#TipoPrestacion').val() + ",";
            filters += "fechaDesde:" + $('#fechaDesde').val() + ",";
            filters += "fechaHasta:" + $('#fechaHasta').val() + ",";
            filters += "estado:" + $('#Estado').val() + ",";

            if((fechaDesde == '' || fechaHasta == '') && nroprestacion == ''){
                swal('Alerta','La fecha "Desde" y "Hasta" son obligatorias.', 'warning');
                return;
            }
        }

        var exportExcel = "{{ route('prestaciones.excel', ['ids' =>  'idsContent', 'filters' => 'filtersContent', 'tipo' => 'tipoContent']) }}";
        exportExcel     = exportExcel.replace('idsContent', ids);
        exportExcel     = exportExcel.replace('filtersContent', filters);
        exportExcel     = exportExcel.replace('tipoContent', tipo);
        exportExcel     = exportExcel.replaceAll('amp;', '');
        window.location = exportExcel;
    }

    const getClientes = "{{ route('getClientes') }}";
    const sendExcel = "{{ route('prestaciones.excel') }}";
</script>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">
@endpush

@push('scripts')
<!--datatable js-->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>


<script src="{{ asset('js/prestaciones/index.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/prestaciones/utils.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/prestaciones/paginacion.js') }}?v={{ time() }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
@endpush

@endsection