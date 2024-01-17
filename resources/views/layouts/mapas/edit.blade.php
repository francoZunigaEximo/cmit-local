@extends('template')

@section('title', 'Editar')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Mapas</h4>

    <div class="page-title-right">

    </div>
</div>

<h4 class="mb-4 mt-3"></span> {{ $mapa->artMapa->RazonSocial }} | {{ $mapa->empresaMapa->RazonSocial }} <span class="custom-badge original">{{$mapa->Nro }}</h4>

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
                <span class="custom-badge original">
                    <i class="ri-lock-2-line"></i>
                    Cerrar
                </span>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#finalizar" role="tab" aria-selected="false" tabindex="-1">
                <span class="custom-badge original">
                    <i class="ri-lock-2-line"></i>
                    Finalizar
                </span>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link text-info" data-bs-toggle="tab" href="#eenviar" role="tab" aria-selected="false" tabindex="-1">
                <span class="custom-badge original">
                    <i class="ri-lock-2-line"></i>
                    eEnviar
                </span>
            </a>
        </li>
    </ul>
</div>

<div class="card-body p-2">
    <div class="tab-content">

        <div class="tab-pane active" id="mapasitem" role="tabpanel">
            <div id="messageMapas"></div>
            <form id="form-update">
                <div class="row">
                    <div class="col-4 box-information">

                        <div class="input-group input-group-sm mb-2 size80porcent">
                            <span class="input-group-text">Cod Mapa&nbsp;<span class="required">(*)</span></span>
                            <input type="hidden" id="Id" value="{{ $mapa->Id }}">
                            <input type="text" class="form-control" id="Nro" name="Nro" value="{{ $mapa->Nro ?? '' }}">
                            <input type="hidden" id="verificador" value="{{ $mapa->Nro ?? '' }}">
                        </div>

                        <div class="input-group input-group-sm mb-2 selectSize">
                            <span class="input-group-text">ART&nbsp;<span class="required">(*)</span></span>
                            <select class="form-select" name="IdART" id="IdART">
                                @if(!empty($mapa->IdART))
                                    <option value="{{ $mapa->IdART }}">{{ $mapa->artMapa->RazonSocial }}</option>
                                @endif  
                            </select>
                        </div>

                        <div class="input-group input-group-sm mb-2 selectSize">
                            <span class="input-group-text">Empresa&nbsp;<span class="required">(*)</span></span>
                            <select class="form-select" id="IdEmpresa" name="IdEmpresa">
                                @if(!empty($mapa->IdEMpresa))
                                    <option value="{{ $mapa->IdEMpresa}}">{{ $mapa->empresaMapa->RazonSocial }}</option>
                                @endif
                            </select>
                        </div>

                        <div class="input-group input-group-sm size80porcent">
                            <span class="input-group-text">Cant Total de Pacientes&nbsp;<span class="required">(*)</span></span>
                            <input type="number" class="form-control" id="Cpacientes" name="Cpacientes" value="{{ $mapa->Cpacientes }}">
                        </div>

                    </div>
  
                    <div class="col-4 box-information">
                        <div class="input-group input-group-sm mb-2 size80porcent">
                            <span class="input-group-text">Fecha de Corte&nbsp;<span class="required">(*)</span></span>
                            <input type="date" class="form-control" id="FechaEdicion" name="FechaEdicion" value="{{ $mapa->Fecha }}">
                        </div>

                        <div class="input-group input-group-sm mb-2 size80porcent">
                            <span class="input-group-text">Fecha de Corte&nbsp;<span class="required">(*)</span></span>
                            <input type="date" class="form-control" id="FechaEEdicion" name="FechaEEdicion" value="{{ $mapa->FechaE }}">
                        </div>

                        <div class="input-group input-group-sm  size80porcent">
                            <span class="input-group-text">Fecha de Corte&nbsp;<span class="required">(*)</span></span>
                            <select class="form-select" name="Estado" id="Estado">
                                <option value="{{ $mapa->Inactivo }}" selected>{{ ($mapa->Inactivo == 0? 'Activo' : 'Inactivo') }}</option>
                                <option value="0">Activo</option>
                                <option value="1">Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-3 box-information">
                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text">Observaciones</span>
                            <textarea class="form-control" name="Obs" id="Obs" rows="8">{{ $mapa->Obs }}</textarea>
                        </div>
                    </div>

                    <div class="col-12 box-information mt-2 text-center">
                        <button type="button" id="updateMapa" class="btn botonGeneral">Actualizar</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive table-card mt-3 mb-1">
                <table id="listaMapas" class="display table table-bordered text-center" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>En proceso</th>
                            <th>Con estados</th>
                            <th>Completas</th>
                            <th>Cerradas</th>
                            <th>Finalizadas</th>
                            <th>Entregadas</th>
                            <th>Presentes</th>
                            <th>Ausentes</th>
                        </tr>
                    </thead>
                    <tbody class="list form-check-all">
                        <td id="totalEnProceso">{{ $enProceso }}</td>
                        <td id="totalConEstados">{{ $conEstado }}</td>
                        <td id="totalCompleta">{{ $completas }}</td>
                        <td id="totalCerradas">{{ $cerradas }}</td>
                        <td id="totalFinalizados">{{ $finalizados }}</td>
                        <td id="totalEntregados">{{ $entregados }}</td>
                        <td id="Presentes">{{ $presentes }}</td>
                        <td id="ausentes">{{ $ausentes }}</td>
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
                    <button type="button" class="btn botonGeneral buscarPresMapa">Buscar</button>
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
                            <th>eEnviado</th>
                            <th>Facturado</th> 
                            <th>INC</th>
                            <th>Ver</th>
                        </tr>
                    </thead>
                    <tbody class="list form-check-all" id="prestaMapa">
    
                    </tbody>
                </table>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">Observaciones privadas</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-card mt-3 mb-1">
                                    <table id="lstPrivPrestaciones" class="display table table-bordered" style="100%">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="sort">Fecha</th>
                                                <th class="sort">Nro prestación</th>
                                                <th>Usuario</th>
                                                <th>Rol</th>
                                                <th>Comentario</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all" id="privadoPrestaciones">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="tab-pane" id="remitos" role="tabpanel">
            
            <div class="table-responsive table-card mt-3 mb-1">
                <table id="listaRemito" class="display table table-bordered" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th class="sort">Remito Nro.</th>
                            <th>Cant Prestaciones</th>
                            <th>Estado</th>
                            <th>Obs.de Entrega</th>
                            <th>Acciones</th>
                            <th>Descargar</th>
                        </tr>
                    </thead>
                    <tbody class="list form-check-all" id="remitoMapa">
                  
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
                    <label for="EstadoCerrar" class="form-label font-weight-bold"><strong>Estado:</strong></label>
                    <select class="form-control" name="EstadoCerrar" id="EstadoCerrar">
                        <option value="" selected>Elija una opción...</option>
                        <option value="abierto">Abierto</option>
                        <option value="cerrado">Cerrado</option>
                    </select>
                </div>

                <div class="col-sm-4  d-flex justify-content-end align-self-center">
                    <button type="button" class="btn botonGeneral cerrarMapa">
                        <i class=" ri-lock-2-fill"></i> Cerrar    
                    </button>
                </div>
            </div> 

            <div class="table-responsive table-card mt-3 mb-1">
                <table id="listaCerrar" class="display table table-bordered" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th class="sort">Fecha</th>
                            <th>Nro. Prestación</th>
                            <th class="sort">Paciente</th>
                            <th>DNI</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                            <th><input type="checkbox" id="checkAllCerrar" name="Id_cerrar"></th>
                        </tr>
                    </thead>
                    <tbody class="list form-check-all" id="cerrarMapa">
    
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Observaciones privadas</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-card mt-3 mb-1">
                                <table id="lstPrivCerrados" class="display table table-bordered" style="100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort">Fecha</th>
                                            <th class="sort">Nro prestación</th>
                                            <th>Usuario</th>
                                            <th>Rol</th>
                                            <th>Comentario</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all" id="privadoCerrar">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
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

                <div class="col-sm-3 mb-3">
                    <label for="NroRemitoFinal" class="form-label font-weight-bold"><strong>Estados:</strong></label>
                    <select class="form-control" name="estadosFinalizar" id="estadosFinalizar">
                        <option value="" selected>Elija una opción</option>
                        <option value="aFinalizar">A finalizar</option>
                        <option value="finalizados">Finalizados</option>
                        <option value="finalizadosTotal">Finalizados Total</option>
                        <option value="todos">Todos</option>
                    </select>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-sm-8" style="text-align: left;">
                    <p style="font-size: small"><i class="ri-error-warning-line" style="color:rgb(0, 255, 255)"></i> No se podrán <span class="badge text-bg-warning">finalizar</span> las prestaciones con Preliminar RX, Sin Escanear, Forma o Devolución.</p>
                    <p style="font-size: small">Al presionar el botón <span class="badge badge-outline-warning">FINALIZAR</span>, se asignará a las prestaciones un Nro de Remito.</p>
                </div>
                <div class="col-sm-4" style="text-align: right;">
                    <button type="button" class="btn botonGeneral finalizarMap">
                        <i class="ri-lock-2-fill"></i> Finalizar    
                    </button>
                </div>
            </div>4

            <div class="table-responsive table-card mt-3 mb-1">
                <table id="listaFinalizar" class="display table table-bordered" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th class="sort">Remito Nro.</th>
                            <th>Fecha</th>
                            <th>Prestación</th>
                            <th class="sort">Paciente</th>
                            <th>DNI</th>
                            <th>Estado</th>
                            <th>Ver</th>
                            <th><input type="checkbox" id="checkAllFinalizar" name="Id_finalizar"><th>
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
                    <label for="NroPresRemito" class="form-label font-weight-bold"><strong>Nro Remito:</strong></label>
                    <input type="number" class="form-control" id="NroPresRemito" name="NroPresRemito" placeholder="Buscar por nro de remito">
                </div>

                <div class="col-sm-1 mb-3">
                    <label for="buscarEnviar" class="form-label font-weight-bold"><br /></label>
                    <button id="buscarEnviar" type="button" class="btn botonGeneral"> 
                        <i class="ri-search-2-line"></i> Buscar</button>
                </div>   
            </div>

            <div class="row mt-4">
                <div class="col-sm-8" style="text-align: left;">
                    <p style="font-size: small"> <span class="custom-badge nuevoAzul">eEnviar</span> solo las prestaciones Cerradas con todos los exámenes cerrados, adjuntados e informados (incluso sus anexos).</p>
                    <p style="font-size: small">Si el estado es <span class="custom-badge nuevoAzul">No eEnviado</span> y <span class="custom-badge nuevoAzul">Bloqueado</span> es porque la prestación no se encuentra cerrada, finalizada y correctamente efectuada e informada.</p>
                </div>
                <div class="col-sm-4" style="text-align: right;">
                    <button class="btn botonGeneral" type="button" id="vistaPreviaEnviar" ><i class="ri-file-text-line"></i> Vista Previa</button>
                    <button class="btn botonGeneral" type="button" id="exportarEnviar"><i class="ri-file-add-line"></i> Exportar</button>
                    <button type="button" class="btn botonGeneral eEnviarDatos" data-bs-toggle="modal" data-bs-target="#eEnviarModal">
                        <i class="ri-mail-send-line"></i> eEnviar    
                    </button>
                </div>
            </div>
            
            <div class="table-responsive table-card mt-3 mb-1">
                <table id="listaeenviar" class="display table table-bordered" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th class="sort">Remito Nro</th>
                            <th>Fecha</th>
                            <th>Prestación</th>
                            <th class="sort">Paciente</th>
                            <th>DNI</th>
                            <th>Estado</th>
                            <th>Ver</th>
                            <th><input type="checkbox" id="checkAllEnviar" name="Id_enviar"><th>
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
                <button type="button" class="btn btn-sm botonGeneral" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-sm botonGeneral confirmarEntrega">Guardar</button>
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
                        <button class="btn btn-sm botonGeneral"><i class="ri-printer-line"></i> Imprimir</button>
                        <button class="btn btn-sm botonGeneral"><i class="ri-file-text-line"></i> eEstudio</button>
                        <button data-id="" title="Observación de Estado" class="btn btn-sm botonGeneral mostrarObsEstado"><i class="ri-chat-1-line"></i> Agregar Obs</button>
                    </div>
                </div>

                <div class="comentarioObsEstado">
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="mb-0">Escriba una observación sobre el estado:</p>
                        <i style="font-size:1.5em" class="ri-close-circle-line cerrarObsEstado"></i>
                    </div>
                    <textarea class="form-control mt-2 ComObsEstado" name="ComObsEstado" rows="5"></textarea>
                    <div class="mt-2 text-center">
                        <button type="button" class="btn btn-sm botonGeneral saveComObsEstado mt-2">Guardar Obs</button>
                    </div>
                </div>

                <div class="table-responsive table-card mt-3 mb-1">
                    <table id="listaExaMapa" class="display table table-bordered" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th class="sort">Examen</th>
                                <th class="sort">Especialidad</th>
                                <th class="sort">Efector</th>
                                <th style="background-color: #eeeeee"></th>
                                <th style="background-color: #eeeeee"></th>
                                <th class="sort">Informador</th>
                                <th style="background-color: #eeeeee"></th>
                                <th>INC</th>
                                <th>Acciones</th>
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
                    <div class="col-sm-4" style="padding-left: 10%">
                        <input class="form-check-input enviarArt" type="checkbox"> ART
                    </div>
                    <div class="col-sm-4" style="padding-right: 10%">
                        <input class="form-check-input enviarEmpresa" type="checkbox"> Empresa 
                    </div>
                    <div class="col-sm-4" style="padding-right: 10%">
                        <input class="form-check-input adjuntarEnvio" type="checkbox"> Adjuntar 
                    </div>
                </div>

                <div class="d-flex justify-content-center">
                    <button type="button" class="btn botonGeneral me-2" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn botonGeneral saveEnviar">e-Enviar</button>
                </div>

            </div>
            <div class="modal-footer" class="text-center p-5"></div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="comentarioPrivado" class="modal fadeInUp" tabindex="-1" aria-labelledby="myModalLabel" aria-hidde="true" style="display: none">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"> Observación privada | Prestación <span class="custom-badge original" id="mostrarIdPrestacion"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body" class="text-center p-3">
                <div class="modal-body">
                    <h5>Paciente: <span class="custom-badge original" id="mostrarNombre"></span></h5>
                    <p>Escriba un comentario de la cuestión o situación:</p>
                   <textarea name="Comentario" id="Comentario" class="form-control" rows="10"></textarea>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="fase">
                    <button type="button" class="btn botonGeneral" id="reset" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn botonGeneral confirmarComentarioPriv">Confirmar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
//Rutas
const getClientes = "{{ route('getClientes') }}";
const fileExport = "{{ route('fileExport') }}";
const saveRemitos = "{{ route('saveRemitos') }}";
const updateMapa = "{{ route('updateMapa') }}";
const searchMapaPres = "{{ route('searchMapaPres') }}";
const getPacienteMapa = "{{ route('getPacienteMapa') }}";
const getExamenMapa = "{{ route('getExamenMapa') }}";
const serchInCerrar = "{{ route('serchInCerrar') }}";
const searchInFinalizar = "{{ route('searchInFinalizar') }}";
const saveEstado = "{{ route('saveEstado') }}"
const searchInEnviar = "{{ route('searchInEnviar') }}";
const saveEnviar = "{{ route('saveEnviar') }}";
const saveFinalizar = "{{ route('saveFinalizar') }}";
const saveCerrar = "{{ route('saveCerrar') }}";
const checkMapa = "{{ route('checkMapa') }}";
const getPrestaciones = "{{ route('getPrestaciones') }}";
const getCerrar = "{{ route('getCerrar') }}";
const getFMapa = "{{ route('getFMapa') }}";
const enviarMapa = "{{ route('enviarMapa') }}";
const changeEstado = "{{ route('changeEstado') }}";
const lnkItemsprestaciones = "{{ route('itemsprestaciones.edit', ['itemsprestacione' => '__item__']) }}";
const privateComment = "{{ route('comentariosPriv') }}";
const savePrivComent = "{{ route('savePrivComent') }}";
const getRemito = "{{ route('getRemito') }}";
const getComentarioPres = "{{ route('getComentarioPres') }}";
const setComentarioPres = "{{ route('setComentarioPres') }}";
const reverseRemito = "{{ route('reverseRemito') }}";
//Extras
const TOKEN = "{{ csrf_token() }}";
const MAPA = "{{ $mapa->Nro }}";
const IDMAPA = "{{ $mapa->Id }}";

</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/mapas/edit.js')}}?v={{ time() }}"></script>
<script src="{{ asset('js/mapas/utils.js')}}?v={{ time() }}"></script>
<script src="{{ asset('js/mapas/validaciones.js')}}?v={{ time() }}"></script>
<script src="{{ asset('libs/cleave.js/cleave.min.js') }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
<script src="{{ asset('js/fancyTable.js') }}"></script>
@endpush

@endsection