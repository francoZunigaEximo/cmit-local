@extends('template')

@section('title', 'Editar Prestación')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Prestación <span class="custom-badge original">N° {{ $prestacione->Id }}</span>&nbsp;<span class="custom-badge verde">Financiador {{ ($prestacione->TipoPrestacion === 'ART' ? 'ART' : 'EMPRESA') }}</span> {!! ($prestacione->Anulado === 1) ? '<span class="custom-badge rojo">Bloqueado</span>' : '' !!}</h4>

    <div class="page-title-right">
        <button type="button" class="btn botonGeneral">
            <i class="ri-add-line align-bottom me-1"></i> Resultados
        </button>

        <button type="button" class="btn botonGeneral" data-bs-toggle="offcanvas" data-bs-target="#datosPaciente">
            <i class="ri-heart-line align-bottom me-1"></i> Paciente
        </button>

        <button type="button" class="btn botonGeneral">
            <i class="ri-add-line align-bottom me-1"></i> Ex. Cuenta
        </button>
    </div>
</div>

<div class="container-fluid">

    <div class="row text-center">
        <div class="col-12">

            <div class="col-12 box-information mb-2">
                <div class="row">
                    <div class="col-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Paciente</span>
                            <input type="text" class="form-control" id="Id" name="Id" value="{{ $prestacione->IdPaciente }}" @readonly(true) title="{{ $prestacione->IdPaciente }}">
                            <input type="text" class="form-control" style="width: 35%" id="NombreCompleto" name="NombreCompleto" value="{{ $prestacione->paciente->Apellido }} {{ $prestacione->paciente->Nombre }}" @readonly(true) title="{{ $prestacione->paciente->Apellido }} {{ $prestacione->paciente->Nombre }}">
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="input-group input-group-sm selectSize">
                            <span class="input-group-text">ART</span>
                            <select data-id="{{ $prestacione->art->Id}}" type="text" name="art" id="art" class="form-control">
                                <option value="{{ $prestacione->art->Id}}" selected>{{ $prestacione->art->RazonSocial }} - {{ $prestacione->art->Identificacion }}</option>
                            </select>
                        </div>
                    </div>
    
                    <div class="col-3">
                        <div class="input-group input-group-sm selectSize">
                            <span class="input-group-text">Empresa</span>
                            <select type="text" class="form-control" name="empresa" id="empresa" >
                                <option value="{{ $prestacione->empresa->Id}}" selected>{{ $prestacione->empresa->RazonSocial }} - {{ $prestacione->empresa->Identificacion }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="input-group input-group-sm selectSize">
                            <span class="input-group-text">Para Empresa</span>
                            <input type="text" name="paraEmpresa" id="paraEmpresa" class="class form-control" value="{{ $prestacione->empresa->RazonSocial }}" title="{{ $prestacione->empresa->RazonSocial }}" @readonly(true)>
                        </div>
                    </div>
    
                    
                </div>   
            </div>

            <div class="col-12 box-information mb-2">
                <div class="row">

                    <div class="col-4">
                        <div class="col-10 mb-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Fecha</span>
                                <input type="date" class="form-control" id="Fecha" value="{{ $prestacione->Fecha ?? ''}}">
                            </div>
                        </div>
                        <div class="col-10 mb-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Tipo Prestación</span>
                                <select class="form-control" name="TipoPrestacion" id="TipoPrestacion">
                                    <option value="{{ $prestacione->TipoPrestacion ? $prestacione->TipoPrestacion : '' }}">{{ $prestacione->TipoPrestacion ? $prestacione->TipoPrestacion : 'Elija una opción...' }}</option>
                                    @foreach($tipoPrestacion as $tipo)
                                        <option value="{{ $tipo->Nombre }}">{{$tipo->Nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-10 mapas">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Mapa</span>
                                <select class="form-control" name="mapas" id="mapas">
                                    <option value="{{ $prestacione->IdMapa ?? ''}}" selected>{{ $prestacione->mapa->Nro ?? ''}} {{ "| Empresa " . $prestacione->mapa->empresaMapa->RazonSocial ?? ''}} {{ " - ART: " . $prestacione->mapa->artMapa->RazonSocial ?? 'Elija una opción...' }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-4">
                        <div class="col-10">
                            <div class="input-group input-group-sm">
                                {!! ($prestacione->FechaCierre == '0000-00-00' || $prestacione->FechaCierre == null ? '<span class="input-group-text cerrar"><i class="ri-lock-unlock-line"></i>&nbsp;Cerrar' : '<span class="input-group-text cerrar"><i class="ri-lock-line"></i>&nbsp;Cerrado' ) !!}
                                </span>
                                <input type="text" class="form-control" id="cerrar" placeholder="dd/mm/aaaa" value="{{ ($prestacione->FechaCierre == '0000-00-00' || $prestacione->FechaCierre == null ? '': \Carbon\Carbon::parse($prestacione->FechaCierre)->format('d/m/Y')) }}" @readonly(true)>
                            </div>
                        </div>

                        <div class="col-10 FechaFinalizado mt-2">
                            <div class="input-group input-group-sm">
                                {!! ($prestacione->Cerrado == 1 ?  ($prestacione->Finalizado == 1 ? '<span class="input-group-text finalizar"><i class="ri-lock-line"></i>&nbsp;Finalizado</span>' : '<span class="input-group-text finalizar"><i class="ri-lock-unlock-line"></i>&nbsp;Finalizar</span>') : '<span class="input-group-text" title="Inhabilitado. Necesita cerrar la prestación."><i class="ri-lock-unlock-line"></i>&nbsp;Finalizar</span>') !!} 
                                <input type="text" class="form-control" id="finalizar" placeholder="dd/mm/aaaa" value="{{ ($prestacione->FechaFinalizado == '0000-00-00' || $prestacione->FechaFinalizado == null ? '' : \Carbon\Carbon::parse($prestacione->FechaFinalizado)->format('d/m/Y')) }}" @readonly(true)>
                            </div>
                        </div>

                        <div class="col-10 FechaEntrega mt-2">
                            <div class="input-group input-group-sm">
                                {!! ($prestacione->Finalizado == 1 ? ($prestacione->Entregado == 1 ? '<span class="input-group-text entregar"><i class="ri-lock-line"></i>&nbsp;Entregado</span>' : '<span class="input-group-text entregar"><i class="ri-lock-unlock-line"></i>&nbsp;Entregar</span>') : '<span class="input-group-text" title="Inhabilitado. Necesita finalizar la prestación."><i class="ri-lock-unlock-line"></i>&nbsp;Entregar</span>') !!}
                                <input type="text" class="form-control" id="entregar" placeholder="dd/mm/aaaa" value="{{ ($prestacione->FechaEntrega == '0000-00-00' || $prestacione->FechaEntrega == null ?'': \Carbon\Carbon::parse($prestacione->FechaEntrega)->format('d/m/Y')) }}" @readonly(true)>
                            </div>
                        </div>

                        <div class="col-10 FechaEnviado mt-2">
                            <div class="input-group input-group-sm">
                                {!! ($prestacione->Cerrado == 1 ? ($prestacione->eEnviado == 1 ? '<span class="input-group-text eEnviar"><i class="ri-lock-line"></i>&nbsp;E-enviado</span>' : '<span class="input-group-text eEnviar"><i class="ri-lock-unlock-line"></i>&nbsp;E-enviar</span>') : '<span class="input-group-text eEnviar"><i class="ri-lock-unlock-line"></i>&nbsp;E-enviar</span>') !!}
                                <input type="text" class="form-control" id="eEnviar" placeholder="dd/mm/aaaa" value="{{ ($prestacione->FechaEnviado == '0000-00-00' || $prestacione->FechaEnviado == null ? '' : \Carbon\Carbon::parse($prestacione->FechaEnviado)->format('d/m/Y')) }}" @readonly(true)>
                            </div>
                        </div>

                        <div class="col-10 mt-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Facturado</span>
                                <input type="date" class="form-control" id="FechaFact" value="{{ ($prestacione->FechaFact == '0000-00-00' || $prestacione->FechaFact == null ? '': $prestacione->FechaFact) }}" @readonly(true)>
                            </div>
                        </div>

                        <div class="col-10 FechaVto mt-2">
                            <div class="input-group input-group-sm">
                                @if($prestacione->FechaVto === null || $prestacione->FechaVto === '0000-00-00')
                                    <span class="input-group-text vigente" title="Sin Fecha">Vencimiento</span>
                                @elseif($prestacione->FechaVto > date('Y-m-d'))
                                    <span class="input-group-text text-verde vigente" title="Estado del vencimiento">Vigente</span>
                                @else 
                                    <span class="input-group-text text-rojo vigente" title="Estado del vencimiento">Vencido</span>
                                @endif
                                <input type="date" class="form-control" id="FechaVto" value="{{ $prestacione->FechaVto }}" @readonly(true)>
                            </div>
                        </div>
                        
                    </div>

                    <div class="col-4">
                        <div class="col-10">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Forma de Pago</span>
                                <select class="form-select" id="pago">
                                    <option value="{{ $prestacione->Pago ?? '' }}" selected>{{ ($prestacione->Pago === "B")? 'Contado' : (($prestacione->Pago === 'C')? 'Cuenta Corriente' : (($prestacione->Pago === 'P')? 'Pago a cuenta' : 'Elija una opción...')) }}</option>
                                    <option value="B">Contado</option>
                                    <option value="C">Cuenta Corriente</option>
                                    <option value="P">Examen a cuenta</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-10 mt-2 SPago">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Medio de Pago</span>
                                <select class="form-select" id="SPago">
                                    <option value="{{ $prestacione->SPago ?? '' }}" selected>
                                        {{ ($prestacione->SPago === "A") ? 'Efectivo' :
                                            (($prestacione->SPago === "B") ? "Débito" :
                                            (($prestacione->SPago === "C") ? "Crédito" :
                                            (($prestacione->SPago === "D") ? "Cheque" :
                                            (($prestacione->SPago === "E") ? "Otro" :
                                            (($prestacione->SPago === "F") ? "Transferencia" :
                                            (($prestacione->SPago === "G") ? "Sin Cargo" : 'Elija una opción...')))))) }}
                                    </option>
                                    <option value="A">Efectivo</option>
                                    <option value="B">Débito</option>
                                    <option value="C">Crédito</option>
                                    <option value="D">Cheque</option>
                                    <option value="E">Otro</option>
                                    <option value="F">Transferencia</option>
                                    <option value="G">Sin Cargo</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-10 mt-2 Factura">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">N. Factura</span>
                                <select class="form-select" id="Tipo" style="width: 4%">
                                    <option value="" selected>Tipo</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="E">E</option>
                                    <option value="P">P</option>
                                    <option value="R">R</option>
                                    <option value="Z">Z</option>
                                </select>
                                <input type="number" class="form-control" style="width: 20%" placeholder="nro sucursal" id="Sucursal">
                                <input type="number" class="form-control" style="width: 20%" placeholder="nro de factura" id="NroFactura">
                            </div>
                        </div>

                        <div class="col-10 mt-2 Autoriza">
                            <div class="input-group input-group">
                                <span class="input-group-text">Autorizado por</span>
                                <select class="form-select" id="Autorizado">
                                    <option value="" selected>Elija una opción...</option>
                                    <option value="lucas">Lucas Grunmann</option>
                                    <option value="martin">Martin</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-12 box-information mb-2">
                <div class="row">
                    <div class="col-6">

                        <div class="col-10">
                            <div class="input-group input-group-sm selectSize-lg">
                                <span class="input-group-text">Evaluador</span>
                                <select class="form-select" id="IdEvaluador" name="profesionales">
                                    <option value="{{ $prestacione->profesional->Id ?? ''}}" selected>{{ $prestacione->profesional->Apellido ?? ''}} {{ $prestacione->profesional->Nombre ?? ''}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-10 mt-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Evaluación</span>
                                <select class="form-select" id="Evaluacion" name="Evaluacion">
                                    <option value="{{ $prestacione->Evaluacion ? $prestacione->Evaluacion : ''}}" selected>{{ $prestacione->Evaluacion ? substr($prestacione->Evaluacion, 2) : 'Elija una opción...'}}</option>
                                    <option value="1.APTO SANO">Apto Sano</option>
                                    <option value="2.APTO CON PRE-EXISTENCIA(Sin interferencia en el desempeño laboral)">Apto con pre-existencia(Sin interferencia en el desempeño laboral)</option>
                                    <option value="3.APTO CON PRE-EXISTENCIA(Solo condiciones especiales de trabajo)">Apto con pre-existencia (Solo condiciones especiales de trabajo)</option>
                                    <option value="4.NO APTO">No apto</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-10 mt-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Calificación</span>
                                <select class="form-select" id="Calificacion" name="Calificacion">
                                    <option value ="{{ $prestacione->Calificacion ? $prestacione->Calificacion : '' }}" selected>{{ $prestacione->Calificacion ? substr($prestacione->Calificacion, 2) : 'Elija una opción...' }}</option>
                                    <option value="1.SANO">Sano</option>
                                    <option value="2.AFECCIÓN CONOCIDA PREVIAMENTE">Afección conocida previamente</option>
                                    <option value="3.AFECCIÓN DESCUBIERTA EN ESTE EXAMEN">Afección descubierta en este exámen</option>
                                    <option value="4.AFECCIÓN CONOCIDA PREVIAMENTE Y AFECCIÓN DESCUBIERTA EN ESTE EXAMEN">Afección conocida previamente y afección descubierta en este exámen</option>
                                </select>
                            </div>
                        </div>

                        
                        <div class="row">
                            <div class="col-12">
                                <div class="input-group input-group mt-2">
                                    <span class="input-group-text">Obs evaluación</span>
                                     <input type="text" class="form-control" placeholder="Observaciones de evaluación" id="Observaciones" value="{{ $prestacione->Observaciones ?? ''}}">
                                </div>
                            </div>
                        </div>
                        
                    </div>

                    <div class="col-6">
                        <div class="col-10">
                            <div class="form-check mb-3">
                                <label class="form-check-label" for="RxPreliminar">RX preliminar</label>
                                <input class="form-check-input" type="checkbox" id="RxPreliminar" {{ $prestacione->RxPreliminar == 'null' || $prestacione->RxPreliminar == 0 ? '' : 'checked'}}>
                            </div>
                        </div>

                        <div class="col-10">
                            <div class="form-check mb-3">
                                <label class="form-check-label" for="SinEval">Sin Evaluación</label>
                                <input class="form-check-input" type="checkbox" id="SinEval" {{ optional($prestacione->prestacionAtributo)->SinEval == 'null' || optional($prestacione->prestacionAtributo)->SinEval == 0 ? '' : 'checked'}}>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <div class="col-12 box-information mb-2">
                <div class="input-group input-group">
                    <span class="input-group-text">Obs exámenes</span>
                    <input type="text" class="form-control" placeholder="Observaciones" id="ObsExamenes" name="ObsExamenes" value="{{ $prestacione->ObsExamenes ?? ''}}">
                </div>

                <div class="input-group input-group mt-2">
                    <span class="input-group-text">Obs estado</span>
                    <input type="text" class="form-control" placeholder="Observaciones" id="Obs" name="Obs" value="{{ $prestacione->prestacionComentario->Obs ?? ''}}">
                </div>

            </div>

            <div class="row">
                <div class="col-12 text-center mt-2">
                    <hr class="mt-2 mb-2 d-block">
                    <button type="button" class="btn botonGeneral" id="actualizarPrestacion">Guardar</button>
                    <hr class="mt-2 mb-2 d-block">
                </div>
            </div>

            <div class="col-12 box-information mb-2">
                <div class="listjs-table" id="customerList">
                    <div class="row">
                    
                        <div class="col-6">
                            <label for="paquetes" class="form-label">Paquetes</label> <!-- select 2 de paquetes de exámenes -->
                            <div class="mb-3">
                                <div class="cajaExamenes">
                                    <select class="form-select" name="paquetes" id="paquetes"></select>
                                    <i class="addPaquete ri-play-list-add-line" title="Añadir paquete completo"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="examenes" class="form-label">Examen</label> <!-- select 2 de exámenes -->
                            <div class="mb-3">
                                <div class="cajaExamenes">
                                    <select class="form-select" name ="exam" id="exam"></select>
                                    <i class="addExamen ri-add-circle-line" title="Añadir examén de la busqueda"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive table-card mt-3 mb-1">

                        <table class="display table table-bordered" style="width:100%"  id="listado">
                            <thead class="table-light">
                                <th class="sort">Exámen</th>
                                <th title="Incompleto">Inc</th>
                                <th title="Ausente">Aus</th>
                                <th title="Forma">For</th>
                                <th>Esc</th>
                                <th>Dev</th>
                                <th>Efector</th>
                                <th>Informador</th>
                                <th>Factura</th>
                                <th>Acciones</th>
                            </thead>
                            <tbody id="listaExamenes" class="list form-check-all">

                            </tbody>
                        </table>

                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">Observaciones privadas</h4><button type="button" class="btn bt-sm botonGeneral" data-bs-toggle="modal" data-bs-target="#addObs">Añadir</button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive table-card mt-3 mb-1">
                                        <table id="lstPrivPrestaciones" class="display table table-bordered" style="100%">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="sort">Fecha</th>
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

                    <div class="row mt-2">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">Autorizados</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive table-card mt-3 mb-1">
                                        <table id="lstAutorizados" class="display table table-bordered" style="100%">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Nombre y Apellido</th>
                                                    <th>DNI</th>
                                                    <th>Tipo de Autorización</th>
                                                </tr>
                                            </thead>
                                            <tbody class="list form-check-all" id="autorizadosPres">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

        </div>
    </div>

</div>



<!-- Menu de Pacientes -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="datosPaciente" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasRightLabel">Paciente</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body text-center">
        <h4 class="fs-16 mb-1">{{ $prestacione->paciente->Apellido ?? '' }} {{ $prestacione->paciente->Nombre ?? '' }}</h4>

        <img src="{{ asset("archivos/fotos/" . (empty($prestacione->paciente->Foto) ? "foto-default.png" : $prestacione->paciente->Foto)) }}" alt="" width="200px">

        <div class="col-12 box-information mb-2">
            <p><strong>Documento:</strong> {{ $prestacione->paciente->Documento }}</p>
            <button type="button" onclick="window.open('{{ route('pacientes.edit', ['paciente' => $prestacione->paciente->Id ])}}', '_blank');" class="btn botonGeneral">
                <i class="ri-heart-line align-bottom me-1"></i> Ver Paciente
            </button>
        </div> 


    </div>
</div>

<div id="addObs" class="modal fadeInUp" tabindex="-1" aria-labelledby="myModalLabel" aria-hidde="true" style="display: none">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"> Observación privada </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body" class="text-center p-3">
                <div class="modal-body">
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
const updatePrestacion = "{{ route('updatePrestacion') }}";
const setEvaluador = "{{ route('setEvaluador') }}";
const getMapas = "{{ route('getMapas') }}";
const actualizarEstados = "{{ route('actualizarEstados') }}";
const GOPRESTACIONES = "{{ route('prestaciones.index') }}";
const GOPACIENTES = "{{ route('pacientes.index') }}";
const actualizarVto = "{{ route('actualizarVto') }}";
const getEvaluador = "{{ route('getEvaluador') }}";

const getPaquetes = "{{ route('getPaquetes') }}";
const searchExamen = "{{ route('searchExamen') }}";
const getItemExamenes = "{{ route('getItemExamenes') }}";
const checkItemExamen = "{{ route('checkItemExamen') }}";
const saveItemExamenes = "{{ route('saveItemExamenes') }}";
const getId = "{{ route('IdExamen') }}";
const deleteItemExamen = "{{ route('deleteItemExamen')}}";
const bloquearExamen ="{{ route('bloquearExamen') }}";
const getClientes = "{{ route('getClientes') }}";
const paqueteId = "{{ route('paqueteId') }}";
const itemExamen = "{{ route('itemExamen') }}";
const checkParaEmpresa = "{{ route('checkParaEmpresa') }}";
const getFactura = "{{route('getFactura') }}";
const getBloqueoPrestacion = "{{ route('getBloqueoPrestacion') }}";
const privateComment = "{{ route('comentariosPriv') }}";
const savePrivComent = "{{ route('savePrivComent') }}";
const getAutorizados = "{{ route('getAutorizados') }}";

//Extras
const TOKEN = "{{ csrf_token() }}";
const UBICACION = "{{ request()->query('location') }}";
const ID = "{{ $prestacione->Id }}";
const IDEMPRESA = "{{ $prestacione->empresa->Id }}";
const editUrl = "{{ route('itemsprestaciones.edit', ['itemsprestacione' => '__examen__'])}}";


//Select
const selectTipoPrestacion = "{{ $prestacione->TipoPrestacion }}";

</script>


@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/prestaciones/edit.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/prestaciones/utils.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/prestaciones/examenes.js') }}?v={{ time() }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('js/fancyTable.js') }}"></script>

@endpush

@endsection