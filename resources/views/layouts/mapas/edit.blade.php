@extends('template')

@section('title', 'Editar')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Edición mapa</h4>

    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="{{ route('mapas.index') }}">Mapas</a></li>
            <li class="breadcrumb-item active">Editar Mapa</li>
        </ol>
    </div>
</div>

<h4 class="mb-5 mt-4">Mapa ART <span class="custom-badge azul">{{$mapa->Nro }}</span> {{ $art }} - {{ $empresa }}</h4>

<div class="card-header">
    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" data-bs-toggle="tab" href="#mapasitem" role="tab" aria-selected="true">
                Mapa
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#prestaciones" role="tab" aria-selected="false" tabindex="-1">
                Prestaciones
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#remitos" role="tab" aria-selected="false" tabindex="-1">
                Remitos
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#cerrar" role="tab" aria-selected="false" tabindex="-1">
                <span class="custom-badge azul">
                    <i class="ri-lock-2-line"></i>
                    Cerrar
                </span>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#finalizar" role="tab" aria-selected="false" tabindex="-1">
                <span class="custom-badge nuevoAmarillo">
                    <i class="ri-lock-2-line"></i>
                    Finalizar
                </span>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link text-info" data-bs-toggle="tab" href="#eenviar" role="tab" aria-selected="false" tabindex="-1">
                <span class="custom-badge nuevoAzul">
                    <i class="ri-lock-2-line"></i>
                    eEnviar
                </span>
            </a>
        </li>
    </ul>
</div>

<div class="card-body p-4">
    <div class="tab-content">

        <div class="tab-pane active" id="mapasitem" role="tabpanel">
            <div id="messageMapas"></div>
            <form id="form-update">
                <div class="row">
                
                    <div class="col-2 p-2 mb-2">
                            <label for="Nro" class="form-label">ART</label>
                            <input type="hidden" id="Id" value="{{ $mapa->Id }}">
                            <input type="text" class="form-control" id="Nro" name="Nro" value="{{ $mapa->Nro ?? '' }}">
                            <input type="hidden" id="verificador" value="{{ $mapa->Nro ?? '' }}">
                    </div>
                    
                    <div class="col-4 p-2 mb-2">
                        <label for="ART" class="form-label"> <br>  </label>
                        <select class="form-select" name="IdART" id="IdART">
                            @if(!empty($mapa->IdART))
                                <option value="{{ $mapa->IdART }}">{{ $art }}</option>
                            @endif  
                        </select>
                    </div>

                    <div class="col-6 p-2 mb-2">
                        <label for="Empresa" class="form-label">Empresa</label>
                        <select class="form-select" id="IdEmpresa" name="IdEmpresa">
                            @if(!empty($mapa->IdEMpresa))
                                <option value="{{ $mapa->IdEMpresa}}">{{ $empresa }}</option>
                            @endif
                        </select>
                    </div>

                    <div class="col-6 p-2 mb-2">
                        <label for="Fecha" class="form-label">Fecha de Corte</label>
                        <input type="date" class="form-control" id="Fecha" name="Fecha" value="{{ $mapa->Fecha }}">
                    </div>

                    <div class="col-6 p-2 mb-2">
                        <label for="FechaE" class="form-label">Fecha de Entrega</label>
                        <input type="date" class="form-control" id="FechaE" name="FechaE" value="{{ $mapa->FechaE }}">
                    </div>

                    <div class="col-6 p-2 mb-2">
                        <label for="Estado" class="form-label">Estado </label>
                        <select class="form-select" name="Estado" id="Estado">
                            <option value="{{ $mapa->Inactivo }}" selected>{{ ($mapa->Inactivo == 0? 'Activo' : 'Inactivo') }}</option>
                            <option value="0">Activo</option>
                            <option value="1">Inactivo</option>
                        </select>
                    </div>


                    <div class="col-6 p-2 mb-2">
                        <label for="Cpacientes" class="form-label">Cantidad de pacientes </label>
                        <input type="number" class="form-control" id="Cpacientes" name="Cpacientes" value="{{ $mapa->Cpacientes }}">
                    </div>

                    <div class="col-12 p-2 mb-2">
                        <label for="Observaciones" class="form-label">Observaciones </label>
                        <textarea class="form-control" name="Obs" id="Obs" rows="4">{{ $mapa->Obs }}</textarea>
                    </div>

                    <div class="col-lg-12 mt-3">
                        <div class="hstack gap-2 justify-content-end">
                            
                            <button type="button" id="updateMapa" class="btn btn-success">Actualizar</button>
                        </div>
                    </div>

                </div>
            </form>

            <div class="table-responsive table-card mt-3 mb-1">
                <table id="listaMapas" class="display table table-bordered" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>En proceso</th>
                            <th>Con estados</th>
                            <th>Completas</th>
                            <th>Cerradas</th>
                            <th>Finalizadas</th>
                            <th>Entregadas</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody class="list form-check-all">
                        <td id="totalEnProceso">{{ $totalEnProceso }}</td>
                        <td id="totalConEstados">{{ $conteo->conEstados }}</td>
                        <td id="totalCompleta">{{ $conteo->completa }}</td>
                        <td id="totalCerradas">{{ $conteo->cerradas }}</td>
                        <td id="totalFinalizados">{{ $conteo->finalizados }}</td>
                        <td id="totalEntregados">{{ $conteo->entregados }}</td>
                        <td id="total"></td>
                    </tbody>
                </table>
            </div>

        </div>

        <div class="tab-pane" id="prestaciones" role="tabpanel">
            <div id="messagePrestaciones"></div>

            <div class="row">
                <div class="col-sm-3 mb-3">
                    <label for="NroPrestacion" class="form-label font-weight-bold"><strong>Nro Prestación:</strong></label>
                    <input type="number" class="form-control" id="NroPrestacion" name="NroPrestacion" placeholder="Buscar por prestación">
                </div>
    
                <div class="col-sm-3 mb-3">
                    <label for="NroRPrestacion" class="form-label font-weight-bold"><strong>Nro Remito:</strong></label>
                    <input type="number" class="form-control" id="NroRPrestacion" name="NroRprestacion" placeholder="Buscar por remito">
                </div>
    
                <div class="col-sm-3 mb-3">
                    <label for="etapaPrestacion" class="form-label font-weight-bold"><strong>Etapas:</strong></label>
                    <select class="form-control" name="etapaPrestacion" id="etapaPrestacion">
                        <option value="" selected>Elija una opción...</option>
                        <option value="completa">Completa</option>
                        <option value="incompleta">Incompleta</option>
                    </select>
                </div>
    
                <div class="col-sm-3 mb-3">
                    <label for="estadoPrestacion" class="form-label font-weight-bold"><strong>Estados:</strong></label>
                    <select class="form-control" name="estadoPrestacion" id="estadoPrestacion">
                        <option value="" selected>Elija una opción...</option>
                        <option value="finalizado">Finalizado</option>
                        <option value="cerrado">Cerrado</option>
                        <option value="abierto">Abierto</option>
                        <option value="eEnviado">eEnviado</option>
                        <option value="entregado">Entregado</option>
                        <option value="anulado">Anulado</option>
                    </select>
                </div>
            </div>
            
            <div class="col-lg-12 mt-3">
                <div class="hstack gap-2 justify-content-end">
                    
                   
                </div>
            </div>

            <div class="row">
                <div class="col-sm-9 mb-3">
                   <p style="font-size: small"><span class="custom-badge verde">Completas</span> Son las prestaciones con todos los exámenes efectuados e informados.</p>
                    <p style="font-size: small"><span class="custom-badge rojo">Incompletas</span> Son todas las prestaciones con algún exámen sin efectuar o informar.</p>
                </div>
                <div class="col-sm-3" style="text-align: right;">
                    <button type="button" class="btn btn-success buscarPresMapa">Buscar</button>
                </div>
            </div>

            <div class="table-responsive table-card mt-3 mb-1">
                <table id="listaPresMapa" class="display table table-bordered" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th class="sort">Nro</th>
                            <th class="sort">Fecha</th>
                            <th class="sort">Paciente</th>
                            <th class="sort">Remito</th>
                            <th class="sort">Etapas</th>
                            <th class="sort">Estado</th>
                            <th class="sort">eEnviado</th>
                            <th class="sort">Facturado</th> 
                            <th>Ver</th>
                        </tr>
                    </thead>
                    <tbody class="list form-check-all" id="prestaMapa">
    
                    </tbody>
                </table>
            </div>

        </div>

        <div class="tab-pane" id="remitos" role="tabpanel">
            
            <div class="table-responsive table-card mt-3 mb-1">
                <table id="listaMapas" class="display table table-bordered" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>Nro de Remito</th>
                            <th>Cantidad de prestaciones</th>
                            <th>Descarga</th>
                            <th>Entrega</th>
                        </tr>
                    </thead>
                    <tbody class="list form-check-all">
                        @forelse($remitos as $remito)
                            <tr>
                                <td>{{ $remito->NroCEE }}</td>
                                <td>{{ $remito->contadorRemitos }}</td>
                                <td>
                                    <button data-remito="{{ $remito->NroCEE }}" type="button" class="pdf btn btn-soft-secondary" title="Generar reporte en Pdf">
                                        <i class="ri-file-pdf-line"></i>
                                    </button>
                                    <button data-remito="{{ $remito->NroCEE }}" type="button" class="excel btn btn-soft-success" title="Generar reporte en Excel">
                                        <i class="ri-file-excel-line"></i>
                                    </button>
                                </td>
                                <td> 
                                    <button data-remito="{{ $remito->NroCEE }}" type="button" class="btn boton-azul entregarRemito" data-bs-toggle="modal" data-bs-target="#entregarModal">Entregar</button> 
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td>No hay remitos para este mapa</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tab-pane" id="cerrar" role="tabpanel">
            
            <div class="row">
                <div class="col-sm-4 mb-3">
                    <label for="NroPresCerrar" class="form-label font-weight-bold"><strong>Nro Prestación:</strong></label>
                    <input type="number" class="form-control" id="NroPresCerrar" name="NroPresCerrar" placeholder="Buscar por prestación">
                    <small>Presione ENTER para buscar</small>
                </div>
    
                <div class="col-sm-4 mb-3">
                    <label for="NroRemitoCerrar" class="form-label font-weight-bold"><strong>Nro Remito:</strong></label>
                    <input type="number" class="form-control" id="NroRemitoCerrar" name="NroRemitoCerrar" placeholder="Buscar por nro de remito">
                    <small>Presione ENTER para buscar</small>
                </div>

                <div class="col-sm-4 mb-3">
                    <label for="EstadoCerrar" class="form-label font-weight-bold"><strong>Estado:</strong></label>
                    <select class="form-control" name="EstadoCerrar" id="EstadoCerrar">
                        <option value="" selected>Elija una opción...</option>
                        <option value="finalizado">Finalizado</option>
                        <option value="abierto">Abierto</option>
                        <option value="eEnviado">eEnviado</option>
                        <option value="entregado">Entregado</option>
                        <option value="anulado">Anulado</option>
                    </select>
                </div>
            </div> 

            <div class="row">
                <div class="col-sm-12" style="text-align: right;">
                    <button type="button" class="btn btn-success cerrarMapa waves-effect waves-light">
                        <i class=" ri-lock-2-fill"></i> Cerrar    
                    </button>
                </div>
            </div>

            <div class="table-responsive table-card mt-3 mb-1">
                <table id="listaCerrar" class="display table table-bordered" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th class="sort">Nro</th>
                            <th class="sort">Fecha</th>
                            <th class="sort">Paciente</th>
                            <th class="sort">Estado</th>
                            <th>Ver</th>
                            <th><input type="checkbox" id="checkAll" name="Id"></th>
                        </tr>
                    </thead>
                    <tbody class="list form-check-all" id="cerrarMapa">
    
                    </tbody>
                </table>
            </div>

        </div>

        <div class="tab-pane" id="finalizar" role="tabpanel">
            
            <div class="row">
                <div class="col-sm-3 mb-3">
                    <label for="NroPresFinal" class="form-label font-weight-bold"><strong>Nro Prestación:</strong></label>
                    <input type="number" class="form-control" id="NroPresFinal" name="NroPresFinal" placeholder="Buscar por prestación">
                    <small>Presione ENTER para buscar</small>
                </div>
    
                <div class="col-sm-3 mb-3">
                    <label for="NroRemitoFinal" class="form-label font-weight-bold"><strong>Nro Remito:</strong></label>
                    <input type="number" class="form-control" id="NroRemitoFinal" name="NroRemitoFinal" placeholder="Buscar por nro de remito">
                    <small>Presione ENTER para buscar</small>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-sm-8" style="text-align: left;">
                    <p style="font-size: small"><i class="ri-error-warning-line" style="color:rgb(0, 255, 255)"></i> No se podrán <span class="badge text-bg-warning">finalizar</span> las prestaciones con Preliminar RX, Sin Escanear, Forma o Devolución.</p>
                    <p style="font-size: small">Al presionar el botón <span class="badge badge-outline-warning">FINALIZAR</span>, se asignará a las prestaciones un Nro de Remito.</p>
                </div>
                <div class="col-sm-4" style="text-align: right;">
                    <button type="button" class="btn btn-warning finalizarMap waves-effect waves-light">
                        <i class="ri-lock-2-fill"></i> Finalizar    
                    </button>
                </div>
            </div>

            <div class="table-responsive table-card mt-3 mb-1">
                <table id="listaFinalizar" class="display table table-bordered" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th class="sort">Nro</th>
                            <th class="sort">Fecha</th>
                            <th class="sort">Paciente</th>
                            <th class="sort">Estado</th>
                            <th>Ver</th>
                            <th><input type="checkbox" id="checkAll" name="Id"><th>
                        </tr>
                    </thead>
                    <tbody class="list form-check-all" id="finalizarMapa">
    
                    </tbody>
                </table>
            </div>

        </div>

        <div class="tab-pane" id="eenviar" role="tabpanel">
            <div class="row">

                <div class="col-sm-3 mb-3">
                    <label for="FechaPrestacion" class="form-label font-weight-bold"><strong>Fecha Prestación:</strong></label>
                    <input type="date" class="form-control" id="fDesde" name="fDesde">
                    <input type="date" class="form-control" id="fHasta" name="fHasta">
                </div>

                <div class="col-sm-3 mb-3">
                    <label for="NroPresEnviar" class="form-label font-weight-bold"><strong>Nro Prestación:</strong></label>
                    <input type="number" class="form-control" id="NroPresEnviar" name="NroPresEnviar" placeholder="Buscar por prestación">
                </div>

                <div class="col-sm-3 mb-3">
                    <label for="eEnviadoEnviar" class="form-label font-weight-bold"><strong>eEnviado:</strong></label>
                    <select class="form-control" name="eEnviadoEnviar" id="eEnviadoEnviar">
                        <option value="" selected>Elija una opción...</option>
                        <option value="eEnviadas">eEnviadas</option>
                        <option value="noEenviadas">No eEnviadas</option>
                    </select>
                </div>

                <div class="col-sm-2 mb-3">
                    <label for="eEnviadoEnviar" class="form-label font-weight-bold"><br /></label>
                    <button class="btn btn-primary waves-effect waves-light btn-sm" type="button" id="vistaPreviaEnviar" style="display:block;"><i class="ri-file-text-line"></i> Vista Previa</button>
                    <button class="btn boton-verde waves-effect waves-light btn-sm mt-1" type="button" id="exportarEnviar"><i class="ri-file-add-line"></i> Exportar</button>
                </div>

                <div class="col-sm-1 mb-3">
                    <label for="eEnviadoEnviar" class="form-label font-weight-bold"><br /></label>
                    <button id="buscarEnviar" type="button" class="btn btn-success waves-effect waves-light"> <i class="ri-search-2-line"></i> Buscar</button>
                </div>   
            </div>

            <div class="row mt-4">
                <div class="col-sm-8" style="text-align: left;">
                    <p style="font-size: small"> <span class="custom-badge nuevoAzul">eEnviar</span> solo las prestaciones Cerradas con todos los exámenes cerrados, adjuntados e informados (incluso sus anexos).</p>
                    <p style="font-size: small">Al presional el botón <span class="custom-badge nuevoAzul">eEnviar</span>se exportará el eEstudio a la carpeta designada en <strong>Mi Empresa</strong> y se marcará la Prestación como eEnviada</p>
                </div>
                <div class="col-sm-4" style="text-align: right;">
                    <button type="button" class="btn custom-badge nuevoAzulInverso eEnviarDatos" data-bs-toggle="modal" data-bs-target="#eEnviarModal" data-remito="{{ $remito->NroCEE ?? ''}}">
                        <i class="ri-mail-send-line"></i> eEnviar    
                    </button>
                </div>
            </div>
            
            <div class="table-responsive table-card mt-3 mb-1">
                <table id="listaeenviar" class="display table table-bordered" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th class="sort">Nro</th>
                            <th class="sort">Fecha</th>
                            <th class="sort">Tipo</th>
                            <th class="sort">Paciente</th>
                            <th class="sort">DNI</th>
                            <th>Ver</th>
                            <th><input type="checkbox" id="checkAll" name="Id"><th>
                        </tr>
                    </thead>
                    <tbody class="list form-check-all" id="eenviarMapa">
    
                    </tbody>
                </table>
            </div>


        </div>

    </div>
</div>

<!-- Modales -->
<div id="entregarModal" class="modal fadeInUp" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"> Entrega de Remito N° <span id="IdRemito"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="col-12 p-2 mb-2">
                    <label for="FechaE" class="form-label">Fecha de Entrega</label>
                    <input type="date" class="form-control" id="remitoFechaE" name="remitoFechaE">
                </div>
                <div class="col-12 p-2 mb-2">
                    <label for="Obs" class="form-label">Observaciones</label>
                    <textarea class="form-control" name="remitoObs" id="remitoObs" cols="30" rows="5"></textarea>
                    <span id="contadorRemitoObs"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary confirmarEntrega">Guardar</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="verPrestacionModal" class="modal fadeInUp" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"> Exámenes de Prestación N° <span id="IdPrestacion"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-8 mb-3">
                       <p><strong>Paciente: </strong><span id="nomPaciente"></span> <span id="apePaciente"></span> | <span id="tipoDocPaciente"></span> <span id="documentoPaciente"></span></p>
                    </div>
                    <div class="col-sm-4" style="text-align: right;">
                        <button class="btn btn-sm btn-primary"><i class="ri-printer-line"></i> Imprimir</button>
                        <button class="btn btn-sm boton-verde"><i class="ri-file-text-line"></i> eEstudios</button>
                    </div>
                </div>

                <div class="table-responsive table-card mt-3 mb-1">
                    <table id="listaExaMapa" class="display table table-bordered" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th class="sort">Examen</th>
                                <th class="sort">Estado</th>
                                <th class="sort">Proveedor</th>
                                <th class="sort">Efector</th>
                                <th class="sort">Informador</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="list form-check-all" id="examenMapa">
        
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="eEnviarModal" class="modal fadeInUp" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"></div>
            <div class="modal-body" class="text-center p-5">
                <h5 style="text-align:center !important" class="modal-title mb-4" id="myModalLabel"> eEnvío del Remito N° <span id="verIdRemito"></span></h5>
                
                <p style="text-align:center !important" class="text-muted mb-4">Puede seleccionar el envío del eEstudio por correo electrónico a la ART, a la Empresa o a ambos.</p>

                <div class="row mb-4 text-center">
                    <div class="col-sm-6" style="padding-left: 20%">
                        <input  class="form-check-input" type="checkbox" id="art" checked> ART
                    </div>
                    <div class="col-sm-6" style="padding-right: 20%">
                        <input class="form-check-input" type="checkbox" id="empresa" checked> Empresa 
                    </div>
                </div>

                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-soft-danger me-2" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-soft-success saveEnviar">e-Enviar</button>
                </div>

            </div>
            <div class="modal-footer" class="text-center p-5"></div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
//Rutas
const getClientes = "{{ route('getClientes') }}";
const exportExcelMapas = "{{ route('exportExcelMapas') }}";
const mapasPdf = "{{ route('mapasPdf') }}";
const saveRemitos = "{{ route('saveRemitos') }}";
const updateMapa = "{{ route('updateMapa') }}";
const searchMapaPres = "{{ route('searchMapaPres') }}";
const getPacienteMapa = "{{ route('getPacienteMapa') }}";
const getExamenMapa = "{{ route('getExamenMapa') }}";
const getCerrarMapa = "{{ route('getCerrarMapa') }}";
const getFinalizarMapa = "{{ route('getFinalizarMapa') }}";
const saveEstado = "{{ route('saveEstado') }}"
const getEnviarMapa = "{{ route('getEnviarMapa') }}";
const saveEnviar = "{{ route('saveEnviar') }}";
const saveFinalizar = "{{ route('saveFinalizar') }}";
const saveCerrar = "{{ route('saveCerrar') }}";
const checkMapa = "{{ route('checkMapa') }}";
const getPrestaciones = "{{ route('getPrestaciones') }}";
const getCerrar = "{{ route('getCerrar') }}";
const getFMapa = "{{ route('getFMapa') }}";
const enviarMapa = "{{ route('enviarMapa') }}";
//Extras
const TOKEN = "{{ csrf_token() }}";
const MAPA = "{{ $mapa->Nro }}";
</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">
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

<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/mapas/edit.js')}}?v={{ time() }}"></script>
<script src="{{ asset('js/mapas/utils.js')}}?v={{ time() }}"></script>
<script src="{{ asset('js/mapas/validaciones.js')}}?v={{ time() }}"></script>
<script src="{{ asset('js/scripts.js') }}?v={{ time() }}"></script>
<script src="{{ asset('libs/cleave.js/cleave.min.js') }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
<script src="{{ asset('js/fancyTable.js') }}"></script>
@endpush

@endsection