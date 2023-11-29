@extends('template')

@section('title', 'Prestaciones')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Prestaciones</h4>

    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="{{ route('prestaciones.index') }}">Prestaciones</a></li>
            <li class="breadcrumb-item active">Listado</li>
        </ol>
    </div>
</div>


<div class="row">
    <div class="col-lg-12">
        <div class="card">
 
            <div id="mensajeria"></div>

            <div class="card-body">
                <div class="listjs-table" id="customerList">
                    <div class="row g-4 mb-3">

                        <form id="form-index">
                            <div class="col-12 p-3 border border-1 border-color" style="border-color: #666666;">
                            <div class="row">
                                
                                <div class="col-sm-2 mb-3">
                                    <div>
                                        <label for="fechaHasta" class="form-label"><strong>Fecha desde: </strong><span class="required">(*)</span></label>
                                        <input type="date" class="form-control" id="fechaDesde">
                                    </div>
                                </div>
                                <div class="col-sm-2 mb-3">
                                    <div>
                                        <label for="fechaHasta" class="form-label"><strong>Fecha hasta: </strong><span class="required">(*)</span></label>
                                        <input type="date" class="form-control" id="fechaHasta">
                                    </div>
                                </div>

                                <div class="col-sm-2 mb-3">
                                    <label for="TipoPrestacion" class="form-label"><strong>Tipo de prestación:</strong></label>
                                    <select class="js-example-basic-multiple" name="tipoPrestacion[]" multiple="multiple" id="TipoPrestacion" data-placeholder="Elija una opción...">
                                        <option value="INGRESO">Ingreso</option>
                                        <option value="PERIODICO">Periódico</option>
                                        <option value="CARNET">Carnet</option>
                                        <option value="EGRESO">Egreso</option>
                                        <option value="ART">ART</option>
                                        <option value="NO_ART">NO ART</option>
                                        <option value="OCUPACIONAL">Ocupacional</option>
                                        <option value="RECMED">Redmec</option>
                                        <option value="S/C_OCUPACIO">S/C Ocupacional</option>
                                        <option value="OTRO">Otro</option>
                                    </select>
                                </div>
                                <div class="col-sm-2 mb-3">
                                    <label for="Pago" class="form-label"><strong>Pago:</strong></label>
                                    <select class="form-control" id="Pago">
                                        <option value="" selected>Elija una opción...</option>
                                        <option value="C">Cuenta corriente</option>
                                        <option value="P">Pago a cuenta</option>
                                        <option value="B">Contado</option>
                                        </select>
                                </div>
                                <div class="col-sm-2 mb-3">
                                    <label for="Pago" class="form-label"><strong>Forma de Pago:</strong></label>
                                    <select class="form-control" id="Spago">
                                        <option value="" selected>Elija una opción...</option>
                                        <option value="G">Sin cargo</option>
                                        <option value="F">Transferencia</option>
                                        <option value="E">Otra</option>
                                        </select>
                                </div>
                                
                                <div class="col-sm-2 mb-3">
                                    <div>
                                        <label for="Estado" class="form-label"><strong>Estado:</strong></label>
                                        <select class="js-example-basic-multiple" name="estados[]" multiple="multiple" id="Estado" data-placeholder="Elija una opción...">
                                            <option value="Anulado">Anulado</option>
                                            <option value="Incompleto">Incompleto</option>
                                            <option value="Ausente">Ausente</option>
                                            <option value="Forma">Forma</option>
                                            <option value="SinEsc">Sin Escanear</option>
                                            <option value="Devol">Devolución</option>
                                            <option value="RxPreliminar">Rx Preliminar</option>
                                            <option value="Cerrado">Cerrado</option>
                                            <option value="Abierto">Abierto</option>
                                            
                                        </select>
                                    </div>  
                                </div>
                                <div class="col-sm-2 mb-3">
                                    <div>
                                        <label for="eEnviado" class="form-label"><strong>eEnviado:</strong></label>
                                        <select id="eEnviado" class="form-control">
                                            <option value="" selected>Elija una opción...</option>
                                            <option value="1">Enviadas</option>
                                            <option value="0">No Enviadas</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-2 mb-3">
                                    <label for="nroprestacion" class="form-label font-weight-bold"><strong>Nro. Prestación</strong></label>
                                    <input type="text" placeholder="Nro." class="form-control" id="nroprestacion">
                                </div>

                                <div class="col-sm-2 mb-3">
                                    <label for="nroprestacion" class="form-label font-weight-bold"><strong>Buscar Empresa</strong></label>
                                    <input type="text" placeholder="Nombre Empresa." class="form-control" id="nomempresa">
                                </div>

                                <div class="col-sm-2 mb-3">
                                    <label for="nroprestacion" class="form-label font-weight-bold"><strong>Buscar ART</strong></label>
                                    <input type="text" placeholder="Nombre ART." class="form-control" id="nomart">
                                </div>

                               <!-- Filtros avanzados --> 
                               <div class="collapse" id="filtrosAvanzados">
                                <div class="card mb-3">
                                  <div class="card-body" style="background: #eaeef3">
                                    <div class="row">


                                      <div class="col-sm-2 mb-3">
                                        <div>
                                          <label for="Finalizado" class="form-label"><strong>Finalizado:</strong></label>
                                          <select id="Finalizado" class="form-control">
                                            <option value="" selected>Elija una opción...</option>
                                            <option value="1">Finalizado</option>
                                            <option value="0">No finalizado</option>
                                          </select>
                                        </div>
                                      </div>

                                      <div class="col-sm-2 mb-3">
                                        <div>
                                          <label for="Facturado" class="form-label"><strong>Facturado:</strong></label>
                                          <select id="Facturado" class="form-control">
                                            <option value="" selected>Elija una opción...</option>
                                            <option value="1">Sí</option>
                                            <option value="0">No</option>
                                          </select>
                                        </div>
                                      </div>

                                      <div class="col-sm-2 mb-3">
                                        <div>
                                          <label for="Entregado" class="form-label"><strong>Entregado:</strong></label>
                                          <select id="Entregado" class="form-control">
                                            <option value="" selected>Elija una opción...</option>
                                            <option value="1">Sí</option>
                                            <option value="0">No</option>
                                          </select>
                                        </div>
                                      </div>

                                      

                                    </div>
                                  </div>
                                </div>
                              </div>
                                <!-- Fin del filtro avanzado -->
                                <div class="hstack gap-2 justify-content-end">
                                    <a class="btn btn-danger" id="buscarReset">Mostrar Hoy</a>
                                    <a class="btn btn-success" id="buscarPrestaciones">Buscar</a>
                                </div>
                                
                            </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-sm-9">
                        <div>
                            <button type="button" class="btn btn-primary add-btn" data-bs-toggle="offcanvas" data-bs-target="#offcanvasTop" aria-controls="offcanvasTop">
                                    <i class="ri-add-line align-bottom me-1"></i> Nuevo
                            </button>
                            <button title="Filtros avanzados" class="btn btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosAvanzados" aria-expanded="false" aria-controls="filtrosAvanzados">
                                <i class="ri-filter-2-line"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-sm-9 mb-3 mt-4" style="font-size: small">
                        <span>Estados: </span>
                        <span title="Imcompleto" style="padding: 0.5em; background-color:orange; color: black; border-radius: 3px">Incompleto</span>
                        <span title="Devol" style="padding: 0.5em; background-color:blue; color: white; border-radius: 3px">Devol</span>
                        <span title="Forma" style="padding: 0.5em; background-color: #0cb7f2; color: black; border-radius: 3px">Forma </span>
                        <span evol</span>
                        <span title="Ausente" style="padding: 0.5em; background-color: red; color: black; border-radius: 3px"> Ausente</span>
                        <span title="Sin Esc" style="padding: 0.5em; background-color: yellow; color: black; border-radius: 3px"> Sin Esc</span>
                    </div>

                    <div class="table-responsive table-card mt-3 mb-1">
                        <table id="listaPrestaciones" class="display table table-bordered" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th></th>
                                    <th class="sort">N°</th>
                                    <th class="sort">Alta</th>
                                    <th class="sort">Empresa</th>
                                    <th class="sort">Para Empresa</th>
                                    <th class="sort">Cuit</th>
                                    <th class="sort">Paciente</th>
                                    <th class="sort">ART</th>
                                    <th class="sort">Situación</th>
                                    <th class="sort">F.pago</th>
                                    <th class="sort">Acciones</th>
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
                <button type="button" class="btn btn-primary guardarComentario" >Guardar Comentario</button>
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
                <button type="button" class="btn btn-primary d-inline-flex" id="checkPaciente" style="margin-left: 5px"><i class="ri-check-line me-2"></i>Crear</button>
                <a href="{{ route('pacientes.create') }}">
                    <button type="button" class="btn btn-warning d-inline-flex" style="margin-left: 10px"><i class="ri-user-add-line"></i>Nuevo</button>
                </a>
            </div>
        <div class="col-xl-3"></div>
        </div>
    </div>
</div>

<script>

//Rutas
const getPacientes = "{{ route('getPacientes') }}";
const getComentarioPres = "{{ route('getComentarioPres') }}";
const setComentarioPres = "{{ route('setComentarioPres') }}";
const searchPrestaciones = "{{ route('searchPrestaciones') }}";

//Extras
const TOKEN = "{{ csrf_token() }}";
const GOPACIENTES = "{{ route('pacientes.edit', ['paciente' => '__paciente__']) }}";
const downPrestaActiva = "{{ route('downPrestaActiva') }}";
const rutaBlock = "{{ route('blockPrestacion', ['Id' => '']) }}";
const ROUTE = "{{ route('prestaciones.index') }}";
const SEARCH = "{{ route('searchPrestaciones') }}";

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