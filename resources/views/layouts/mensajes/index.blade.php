@extends('template')

@section('title', 'Mensajes')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Mensajes</h4>
</div>

<div class="card-header d-flex justify-content-between">
    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" data-bs-toggle="tab" href="#masivo" role="tab" aria-selected="true">
                <i class="ri-mail-send-line"></i>
                Enviar Mail Masivo
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#auditorias" role="tab" aria-selected="false" tabindex="-1">
                <i class="ri-mail-check-line"></i>
                Auditorias
            </a>
        </li>
    </ul>
</div>

<div class="card-body p-4">
    <div class="tab-content">

        <div class="tab-pane active" id="masivo" role="tabpanel">

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="listjs-table" id="customerList">
                                <div class="row g-4 mb-3">
            
                                    <div class="col-12 p-4 border border-1 border-color" style="border-color: #666666;">
                                        
                                        <div class="row">
                                            <div class="col-sm-2 mb-3">
                                                <label for="Corte" class="form-label font-weight-bold"><strong>Nro. Cliente:</strong></label>
                                                <input type="number" class="form-control" id="nroDesde" name="nroDesde" placeholder="nro desde">
                                                <input type="number" class="form-control" id="nroHasta" name="nroHasta" placeholder="nro hasta">
                                            </div>
            
            
                                            <div class="col-sm-2 mb-3">
                                                <label for="tipo" class="form-label font-weight-bold"><strong>Tipo:</strong></label>
                                                <select class="form-control" name="tipo" id="tipo">
                                                    <option value="" selected>Elija una opción...</option>
                                                    <option value="A">ART</option>
                                                    <option value="E">Empresa</option>
                                                    <option value="todos">Todos</option>
                                                </select>
                                            </div>
            
                                            <div class="col-sm-2 mb-3">
                                                <label for="pago" class="form-label font-weight-bold"><strong>Pago:</strong></label>
                                                <select name="pago" id="pago" class="form-control">
                                                    <option selected value="">Elija una opción...</option>
                                                    <option value="B">Contado</option>
                                                    <option value="C">Contado(CC Bloq)</option>
                                                    <option value="A">Cuenta corriente</option>
                                                    <option value="todos">Todos</option>
                                                </select>
                                            </div>
            
                                            <div class="col-sm-2 mb-3">
                                                <label for="bloqueado" class="form-label font-weight-bold"><strong>Estado:</strong></label>
                                                <select name="bloqueado" id="bloqueado" class="form-control">
                                                    <option selected value="">Elija una opción...</option>
                                                    <option value="bloqueado">Bloqueado</option>
                                                    <option value="noBloqueado">No bloqueado</option>
                                                    <option value="todos">Todos</option>
                                                </select>
                                            </div>
            
                                            <div class="col-sm-2 mb-3">
                                                <label for="Corte" class="form-label font-weight-bold"><strong>Actividad:</strong> <small>(desde/hasta)</small>&nbsp;<span class="bx bx-help-circle rojo" title="Ayuda: Solo busca prestaciones de los ultimos 30 días si la 'Fecha Desde' esta vacía."></span></label>
                                                <input type="date" class="form-control" id="fechaDesde" name="fechaDesde">
                                                <input type="date" class="form-control" id="fechaHasta" name="fechaHasta">
                                            </div>
            
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12 text-end">
                                                <button type="button" class="btn btn-sm botonGeneral modelos"><i class="ri-article-line"></i>Modelos</button>
                                                <button type="button" class="btn botonGeneral Testear"><i class="ri-settings-3-line"></i>Testear Conexión</button>
                                                <button type="type" class="btn btn-sm botonGeneral buscar"><i class="ri-search-line"></i>Buscar</button>
                                            </div>
                                        </div>
                                    </div>
            
                                </div>
            
                                <div class="row">
                                    <div class="col-sm-6 d-flex align-items-center">
                                        <span class="rojo small">El color rojo indica bloqueado</span>
                                    </div>
                                    <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                        <div class="p-2">
                                            <strong>Enviar a:</strong> <span class="required">(*)</span>
                                        </div>
                                        <div class="p-2">
                                            <input class="form-check-input" type="checkbox" value="" id="facturas" name="facturas">Facturas
                                        </div>
                                        <div class="p-2">
                                            <input class="form-check-input" type="checkbox" value="" id="masivos" name="masivos">Masivo
                                        </div>
                                        <div class="p-2">
                                            <input class="form-check-input" type="checkbox" value="" id="informes" name="informes">Informe
                                        </div>
                                        <div class="p-2">
                                            <button type="type" class="btn btn-sm botonGeneral enviar"><i class="ri-mail-send-line"></i>e-Enviar</button>
                                        </div>   
                                    </div>
                                </div>
                                
            
                                <div class="table-responsive table-card mt-3 mb-1 mx-auto">
                                    <table id="listaMensaje" class="display table table-bordered" style="width:100%">
                                        <thead class="table-light">
                                            <tr>
                                                
                                                <th class="sort">Código</th>
                                                <th class="sort">Razón Social</th>
                                                <th class="sort">Para Empresa</th>
                                                <th>CUIT</th>
                                                <th class="sort">Tipo</th>
                                                <th>Forma de Pago</th>
                                                <th>Mail Factura</th>
                                                <th>Mail Masivo</th>
                                                <th>Mail Informe</th>
                                                <th>Acciones</th>
                                                <th><input type="checkbox" id="checkAllMasivo" name="Id_masivo"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all" id="lstMensaje">
            
                                        </tbody>
                                    </table>
                                </div>           
                            </div>
                        </div>
                    </div>
                </div>
            
            </div>

        </div>

        <div class="tab-pane" id="auditorias" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <label for="fechaDesdeAu" class="form-label fw-bolder">Fecha Desde:</label>
                            <input class="form-control" type="date" id="fechaDesdeAu" name="fechaDesdeAu">
                        </div>
                        <div class="col-sm-3">
                            <label for="fechaHastaAu" class="form-label fw-bolder">Fecha Hasta:</label>
                            <input class="form-control" type="date" id="fechaHastaAu" name="fechaHastaAu">
                        </div>
                        <div class="col-sm-6 d-flex align-items-end justify-content-end">
                            <button class="btn btn-sm botonGeneral buscarAuditoria"><i class="ri-search-line"></i>Buscar</button>&nbsp;
                            <button class="btn btn-sm botonGeneral reiniciarAuditoria"><i class="ri-refresh-line"></i>Reiniciar</button>&nbsp;
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive table-card mt-3 mb-1 mx-auto">
                <table id="listaAuditorias" class="display table table-bordered" style="width:100%">
                    <thead class="table-light">
                        <tr>  
                            <th>Fecha</th>
                            <th class="sort">Asunto</th>
                            <th>Destinatarios</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="list form-check-all" id="lstAuditorias">
            
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>



<div id="modalEnviar" class="modal modal-lg fade" tabindex="-1" aria-labelledby="myModalLabel1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Datos del Mail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <label for="modelo" class="form-label fw-bolder">Modelo:</label>
                        <select class="form-control" name="modelo" id="modelo">
                        </select>
                    </div>
                    <div class="col-sm-12">
                        <label for="asunto" class="form-label fw-bolder">Asunto:</label>
                        <input class="form-control" type="text" id="Asunto" name="Asunto">
                    </div>
                    <div class="col-sm-12">
                        <label for="detalles" class="form-label fw-bolder">Detalles:</label>
                        <textarea class="form-control Cuerpo" name="Cuerpo" id="Cuerpo" row="10"></textarea>
                    </div>
                    <div class="col-sm-12 mt-2">
                        <button type="button" class="btn btn-sm botonGeneral modelos">Modelos</button>
                    </div>

                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn botonGeneral" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn botonGeneral Testear"><i class="ri-settings-3-line"></i>Testear Conexión</button>
                <button type="button" class="btn botonGeneral enviarMensaje">Enviar</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="envioIndividual" class="modal modal-lg fade" tabindex="-1" aria-labelledby="myModalLabel3" aria-hidden="true" style="display: none">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Mensaje enviado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="card p-2 text-center">
                        <div class="row">
                            <div class="col">
                                <input class="form-check-input" type="checkbox" value="" id="facturas2" name="facturas2">
                                <label class="form-check-label" for="facturas2">Facturas</label>
                            </div>
                            <div class="col">
                                <input class="form-check-input" type="checkbox" value="" id="masivos2" name="masivos2">
                                <label class="form-check-label" for="masivos2">Masivo</label>
                            </div>
                            <div class="col">
                                <input class="form-check-input" type="checkbox" value="" id="informes2" name="informes2">
                                <label class="form-check-label" for="informes2">Informe</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <label for="modelo2" class="form-label fw-bolder">Modelo:</label>
                        <select class="form-control" name="modelo2" id="modelo2">
                        </select>
                        <input type="hidden" id="Id2">
                    </div>
                    <div class="col-sm-12">
                        <label for="asunto2" class="form-label fw-bolder">Asunto:</label>
                        <input class="form-control" type="text" id="Asunto2" name="Asunto2">
                    </div>
                    <div class="col-sm-12">
                        <label for="detalles2" class="form-label fw-bolder">Detalles:</label>
                        <textarea class="form-control Cuerpo2" name="Cuerpo2" id="Cuerpo2" row="10"></textarea>
                    </div>
                    <div class="col-sm-12 mt-2">
                        <button type="button" class="btn btn-sm botonGeneral modelos">Modelos</button>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn botonGeneral" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn botonGeneral Testear"><i class="ri-settings-3-line"></i>Testear Conexión</button>
                <button type="button" class="btn botonGeneral enviarMensajeInd">Enviar</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="modalDestinatarios" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel3" aria-hidden="true" style="display: none">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Lista de Destinatarios</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="verDestinatarios">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn botonGeneral" data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="modalHistorial" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel3" aria-hidden="true" style="display: none">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Mensaje enviado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="verMensaje"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn botonGeneral" data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    const SEARCH = "{{ route('searchMensaje') }}";
    const loadModelos = "{{ route('loadModelos') }}";
    const loadMensaje = "{{ route('loadMensaje') }}";
    const sendEmails = "{{ route('sendEmails') }}";
    const testEmail = "{{ route('testEmail') }}";

    const SEARCHAUDITORIA = "{{ route('mensajes.auditoria') }}";
    const verAuditoria = "{{ route('verAuditoria') }}";
    
</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v=?v={{ time() }}">
<link rel="stylesheet" href="{{ asset('css/richtext.min.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script src="{{ asset('js/mensajeria/auditoria.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/mensajeria/paginacionAuditoria.js') }}?v={{ time() }}"></script>

<script src="{{ asset('js/mensajeria/index.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/mensajeria/paginacion.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/richText/jquery.richtext.js') }}?v={{ time() }}"></script>


@endpush

@endsection