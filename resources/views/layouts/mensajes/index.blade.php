@extends('template')

@section('title', 'Mensajes')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Mensajes</h4>

    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="{{ route('mensajes.index') }}">Mensajes</a></li>
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
                                    <label for="Corte" class="form-label font-weight-bold"><strong>Actividad:</strong> <small>(desde/hasta)</small></label>
                                    <input type="date" class="form-control" id="fechaDesde" name="fechaDesde">
                                    <input type="date" class="form-control" id="fechaHasta" name="fechaHasta">
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-sm-12 text-end">
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
                        <button type="button" class="btn btn-sm botonGeneral auditoria">Auditoria</button>
                        <button type="button" class="btn btn-sm botonGeneral modelos">Modelos</button>
                    </div>

                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn botonGeneral" data-bs-dismiss="modal">Cerrar</button>
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
                        <button type="button" class="btn btn-sm botonGeneral auditoria">Auditoria</button>
                        <button type="button" class="btn btn-sm botonGeneral modelos">Modelos</button>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn botonGeneral" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn botonGeneral enviarMensajeInd">Enviar</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    const TOKEN = "{{ csrf_token() }}";
    const SEARCH = "{{ route('searchMensaje') }}";
    const loadModelos = "{{ route('loadModelos') }}";
    const loadMensaje = "{{ route('loadMensaje') }}";
    const sendEmails = "{{ route('sendEmails') }}";
    
</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v=?v={{ time() }}">
<link rel="stylesheet" href="{{ asset('css/richtext.min.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script src="{{ asset('js/mensajeria/index.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/mensajeria/paginacion.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/richText/jquery.richtext.js') }}?v={{ time() }}"></script>


@endpush

@endsection