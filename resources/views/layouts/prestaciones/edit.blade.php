@extends('template')

@section('title', 'Editar Prestación')

@section('content')

<div class="row mb-4">
    <div class="col-12 text-end">
        <button onclick="window.history.back()" class="btn btn-warning"><i class="ri-arrow-left-line"></i>&nbsp;Volver</button>
    </div>
</div>

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Prestación <span class="custom-badge original">N° {{ $prestacione->Id }}</span>&nbsp;<span class="custom-badge verde">Financiador {{ ($prestacione->TipoPrestacion === 'ART' ? 'ART' : 'EMPRESA') }}</span> {!! ($prestacione->Anulado === 1) ? '<span class="custom-badge rojo">Bloqueado</span>' : '' !!}</h4>

    <div class="page-title-right">
        <button type="button" class="btn botonGeneral">
            <i class="ri-add-line align-bottom me-1"></i> Resultados
        </button>

        <button type="button" class="btn botonGeneral" data-bs-toggle="offcanvas" data-bs-target="#datosPaciente">
            <i class="ri-heart-line align-bottom me-1"></i> Paciente
        </button>

        <button type="button" class="btn botonGeneral" data-bs-toggle="modal" data-bs-target="#exaCuenta">
            <i class="ri-add-line align-bottom me-1"></i> Ex. Cuenta
        </button>
    </div>
</div>

<div class="container-fluid">

    <div class="row text-center">
        <div class="col-12">

            <div class="col-12 box-information mb-2">
                <div class="row">
                    <div class="col-1">
                        <label class="form-label">Nro</label>
                        <input type="text" class="form-control" id="Id" name="Id" value="{{ $prestacione->IdPaciente }}" @readonly(true) title="{{ $prestacione->IdPaciente }}">   
                        <input type="hidden" id="idPrestacion" value="{{ $prestacione->Id }}">
                    </div>

                    <div class="col-1">
                        <label class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="Fecha" value="{{ $prestacione->Fecha ?? ''}}">
                    </div>

                    <div class="col-2">
                        <label class="form-label">Apellido y Nombre</label>
                        <input type="text" class="form-control" id="NombreCompleto" name="NombreCompleto" value="{{ $prestacione->paciente->Apellido }} {{ $prestacione->paciente->Nombre }}" @readonly(true) title="{{ $prestacione->paciente->Apellido }} {{ $prestacione->paciente->Nombre }}">  
                    </div>

                    <div class="col-2">
                        <label class="form-label">ART</label>
                        <select data-id="{{ $prestacione->art->Id}}" type="text" name="art" id="art" class="form-control">
                            <option value="{{ $prestacione->art->Id}}" selected>{{ $prestacione->art->RazonSocial }} - {{ $prestacione->art->Identificacion }}</option>
                        </select>
                    </div>
    
                    <div class="col-2">
                        <label class="form-label">Empresa</label>
                        <select type="text" class="form-control" name="empresa" id="empresa" >
                            <option value="{{ $prestacione->empresa->Id}}" selected>{{ $prestacione->empresa->RazonSocial }} - {{ $prestacione->empresa->Identificacion }}</option>
                        </select>
                    </div>

                    <div class="col-2">
                        <label class="form-label">Para Empresa</label>
                        <input type="text" name="paraEmpresa" id="paraEmpresa" class="class form-control" value="{{ $prestacione->empresa->RazonSocial }}" title="{{ $prestacione->empresa->RazonSocial }}" @readonly(true)>
                    </div>
    
                    <div class="col-2">
                        <label class="form-label">Tipo Prestación</label>
                        <select class="form-control" name="TipoPrestacion" id="TipoPrestacion">
                            <option value="{{ $prestacione->TipoPrestacion ? $prestacione->TipoPrestacion : '' }}">{{ $prestacione->TipoPrestacion ? $prestacione->TipoPrestacion : 'Elija una opción...' }}</option>
                            @foreach($tipoPrestacion as $tipo)
                                <option value="{{ $tipo->Nombre }}">{{$tipo->Nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                </div>   
            </div>

            <div class="col-12 box-information mb-2 ">
                <div class="row">

                    <div class="col-4 banderas">

                        <div class="col-10 mt-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Estado</span>
                                <input type="text" class="form-control {{ $prestacione->Incompleto === 1 ? 'rojo' : 'blanco'}}" id="Incompleto" name="Incompleto" value="INC">
                                <input type="text" class="form-control {{ $prestacione->Ausente === 1 ? 'rojo' : 'blanco'}}" id="Ausente" name="Ausente" value="AUS">
                                <input type="text" class="form-control {{ $prestacione->Forma === 1 ? 'rojo' : 'blanco'}}" id="Forma" name="Forma" value="FOR">
                                <input type="text" class="form-control grisClaro" id="SinEsc" name="SinEsc" value="ESC">
                                <input type="text" class="form-control {{ $prestacione->Devol === 1 ? 'rojo' : 'blanco'}}" id="Devol" name="Devol" value="DEV">
                            </div>
                        </div>

                        <div class="col-10 mt-2">
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

                    </div>
                    
                    <div class="col-4">

                        <div class="col-10 FechaEnviado">
                            <div class="input-group input-group-sm">
                                {!! ($prestacione->Cerrado == 1 ? ($prestacione->eEnviado == 1 ? '<span class="input-group-text eEnviar"><i class="ri-lock-line"></i>&nbsp;E-enviado</span>' : '<span class="input-group-text eEnviar"><i class="ri-lock-unlock-line"></i>&nbsp;E-enviar</span>') : '<span class="input-group-text eEnviar"><i class="ri-lock-unlock-line"></i>&nbsp;E-enviar</span>') !!}
                                <input type="text" class="form-control" id="eEnviar" placeholder="dd/mm/aaaa" value="{{ ($prestacione->FechaEnviado == '0000-00-00' || $prestacione->FechaEnviado == null ? '' : \Carbon\Carbon::parse($prestacione->FechaEnviado)->format('d/m/Y')) }}" @readonly(true)>
                            </div>
                        </div>

                        <div class="col-10 mt-2">
                            <div class="input-group input-group-sm"> 
                                <span class="input-group-text">Fecha Anulado</span>
                                <input type="date" class="form-control" id="FechaAnul" value="{{ in_array($prestacione->FechaAnul, [null, '0000-00-00']) ? '': $prestacione->FechaAnul }}" @readonly(true)>
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

                        <div class="col-10 mt-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Facturado</span>
                                <input type="date" class="form-control" id="FechaFact" value="{{ ($prestacione->FechaFact == '0000-00-00' || $prestacione->FechaFact == null ? '': $prestacione->FechaFact) }}" @readonly(true)>
                            </div>
                        </div>

                        <div class="col-10 mt-2 Autoriza">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Autorizado por</span>
                                <select class="form-select" id="Autorizado">
                                    <option value="" selected>Elija una opción...</option>
                                    <option value="lucas">Lucas Grunmann</option>
                                    <option value="martin">Martin</option>
                                </select>
                            </div>
                        </div>                     
                    </div>

                    <div class="col-4">
                        <div class="col-10 mapas">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Mapa</span>
                                <select class="form-control" name="mapas" id="mapas">
                                    <option value="{{ $prestacione->IdMapa ?? ''}}" selected>{{ $prestacione->mapa->Nro ?? ''}} {{ "| Empresa " . (empty($prestacione->mapa->empresaMapa->RazonSocial) ? '' : $prestacione->mapa->empresaMapa->RazonSocial) }} {{ " - ART: " . empty($prestacione->mapa->artMapa->RazonSocial) ? 'Elija una opción...' : $prestacione->mapa->artMapa->RazonSocial }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-10 mt-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Forma de Pago</span>
                                <select class="form-select" id="pago">
                                    <option value="{{ $prestacione->Pago ?? '' }}" selected>{{ ($prestacione->Pago === "B" || $prestacione->Pago === "C")? 'Contado' : (($prestacione->Pago === 'A' || empty($prestacione->Pago))? 'Cuenta Corriente' : (($prestacione->Pago === 'P')? 'Examenes a cuenta' : 'Elija una opción...')) }}</option>
                                    <option value="B">Contado</option>
                                    <option value="A">Cuenta Corriente</option>
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

                        <div class="col-10 mt-2 NroFactProv">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Nro Factura Provisoria</span>
                                <input type="text" class="form-control" id="NroFactProv" name="NroFactProv" value="{{ $prestacione->NroFactProv ?? ''}}">
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-12 box-information mb-2 evaluacion">
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
                                    <option value="1.APTO SANO">1.Apto Sano</option>
                                    <option value="2.APTO CON PRE-EXISTENCIA(Sin interferencia en el desempeño laboral)">2.Apto con pre-existencia(Sin interferencia en el desempeño laboral)</option>
                                    <option value="3.APTO CON PRE-EXISTENCIA(Solo condiciones especiales de trabajo)">3.Apto con pre-existencia (Solo condiciones especiales de trabajo)</option>
                                    <option value="4.NO APTO">4.No apto</option>
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
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="input-group input-group mt-2 mb-2">
                                <span class="input-group-text">Obs evaluación</span>
                                 <textarea class="form-control" id="Observaciones" rows="4">{{ $prestacione->Observaciones ?? ''}}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">

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
                    <textarea class="form-control" id="ObsExamenes" name="ObsExamenes" rows="4">{{ $prestacione->ObsExamenes ?? ''}}</textarea>
                </div>

                <div class="input-group input-group mt-2">
                    <span class="input-group-text">Obs estado</span>
                    <textarea class="form-control" id="Obs" name="Obs" rows="4">{{ $prestacione->prestacionComentario->Obs ?? ''}}</textarea>
                </div>

            </div>

            <div class="row">
                <div class="col-12 text-center mt-2">
                    <hr class="mt-2 mb-2 d-block">
                    <button type="button" class="btn btn-sm botonGeneral" id="actualizarPrestacion">Guardar</button>
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
                                    <i class="addPaquete ri-play-list-add-line naranja" title="Añadir paquete completo"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="examenes" class="form-label">Examen</label> <!-- select 2 de exámenes -->
                            <div class="mb-3">
                                <div class="cajaExamenes">
                                    <select class="form-select" name ="exam" id="exam"></select>
                                    <i class="addExamen ri-add-circle-line naranja" title="Añadir examén de la busqueda"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row text-left">
                        <div class="col-auto">
                            <button type="button" class="btn btn-sm botonGeneral deleteExamenes"><i class="ri-delete-bin-2-line"></i>&nbsp;Eliminar</button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-sm botonGeneral bloquearExamenes"><i class="ri-forbid-2-line"></i>&nbsp;Anular</button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-sm botonGeneral abrirExamenes">Abrir</button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-sm botonGeneral adjuntoExamenes"><i class="ri-attachment-line"></i>&nbsp;Adjuntado</button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-sm botonGeneral liberarExamenes">Liberar</button>
                        </div>
                    </div>

                    <div class="row mt-2 mb-2 text-left">
                        <div class="col-auto">
                            <span><i class="ri-file-mark-line verde"></i>&nbsp;Marca desde donde se cargo el archivo efector</span>
                            <span><i class="ri-file-mark-line naranja"></i>&nbsp;Marca desde donde se cargo el archivo informador</span>
                        </div>
                    </div>

                    <div class="table mt-3 mb-1">
                        
                        <table class="table table-bordered" id="listado">
                            <thead class="table-light">
                                <th><input type="checkbox" id="checkAllExamenes" name="Id_examenes"></th>
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

                    <div class="row mb-3">
                        <div class="col-sm-12 text-end ">
                            {{-- <button type="button" class="btn btn-sm botonGeneral" data-bs-toggle="modal" data-bs-target="#imprimir" ><i class="bx bxs-file-pdf"></i>&nbsp;Imprimir</button> --}}
                            <button type="button" class="btn btn-sm botonGeneral" data-bs-toggle="modal" data-bs-target="#opciones"><i class="ri-send-plane-line"></i>&nbsp;Opciones</button>
                            @can('prestaciones_eEnviar')
                            <button type="button" id="eEnviarReporte" class="btn btn-sm botonGeneral eEnviarReporte"><i class="ri-send-plane-line"></i>&nbsp;Enviar</button>
                            @endcan
                            @can('boton_todo')
                            <button type="button" class="btn btn-sm botonGeneral btnTodo"><i class="ri-stack-fill"></i>&nbsp;Todo</button>
                            @endcan
                            <button type="button" class="btn btn-sm botonGeneral resumenTotal"><i class="ri-file-excel-line"></i>&nbsp;Resumen</button>
                            <button type="button" class="btn btn-sm botonGeneral eEstudio"><i class="bx bxs-file-pdf"></i>&nbsp;e-Estudio</button>
                            <button type="button" class="btn btn-sm botonGeneral eAnexo"><i class="bx bxs-file-pdf"></i>&nbsp;e-Anexos</button>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card titulo-tabla">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">Observaciones privadas</h4><button type="button" class="btn bt-sm botonGeneral" data-bs-toggle="modal" data-bs-target="#addObs">Añadir</button>
                                </div>
                                <div class="card-body">
                                    <div class="table-card mb-1">
                                        <table id="lstPrivPrestaciones" class="table table-bordered">
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

                    <div class="row mt-2 autorizados">
                        <div class="col-lg-12">
                            <div class="card titulo-tabla">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">Autorizados</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table mb-1">
                                        <table id="lstAutorizados" class="table table-bordered">
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

                    <div class="row mt-2 adjPrestacion">
                        <div class="col-lg-12">
                            <div class="card titulo-tabla">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">Adjuntos Generales de la Prestación</h4><button type="button" class="btn bt-sm botonGeneral" data-bs-toggle="modal" data-bs-target="#addAdjPres">Añadir</button>
                                </div>
                                <div class="card-body">
                                    <div class="table mb-1">
                                        <table id="lstAuditorias" class="table table-bordered" style="100%">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Descripción</th>
                                                    <th>Archivo</th>
                                                </tr>
                                            </thead>
                                            <tbody class="list form-check-all" id="adjPrestacion">
                      
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2 auditoria">
                        <div class="col-lg-12">
                            <div class="card titulo-tabla">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">Auditoria</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table mb-1">
                                        <table id="lstAuditorias" class="table table-bordered" style="100%">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Fecha y hora</th>
                                                    <th>Accion</th>
                                                    <th>Usuario</th>
                                                </tr>
                                            </thead>
                                            <tbody class="list form-check-all" id="auditoriaPres">
                                                @forelse($auditorias as $auditoria)
                                                <tr>
                                                    <th>{{ isset($auditoria->Fecha) ? \Carbon\Carbon::parse($auditoria->Fecha)->format('d/m/Y H:i:s') : '' }}</th>
                                                    <th>{{ $auditoria->auditarAccion->Nombre ?? '' }}</th>
                                                    <th>{{ $auditoria->IdUsuario ?? '' }}</th>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <th>No hay registros actualmente</th>
                                                </tr>

                                                @endforelse
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

        <img src="@fileUrl('lectura')/Fotos/{{ $prestacione->paciente->Foto }}?v={{ time() }}" alt="" width="200px">

        <div class="col-12 box-information mb-2">
            <p><span>Documento:</spna> {{ $prestacione->paciente->Documento }}</p>
            <button type="button" onclick="window.open('{{ route('pacientes.edit', ['paciente' => $prestacione->paciente->Id ])}}', '_blank');" class="btn botonGeneral">
                <i class="ri-heart-line align-bottom me-1"></i> Ver Paciente
            </button>
        </div> 

        <div type="button" class="col-12 box-information mb-2" data-id="{{ $prestacione->paciente->Id}}" data-bs-toggle="modal" data-bs-target="#fichaLaboral">
            <button class="btn btn-sm botonGeneral">
                <i class="ri-heart-line align-bottom me-1"></i> Ver FichaLaboral
            </button>
        </div>
    </div>
</div>


<div id="fichaLaboral" class="modal fadeInUp" tabindex="-1" aria-labelledby="myModalLabel" aria-hidde="true" style="display: none">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"> Ficha Laboral</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body" class="text-center p-3">
                <div class="row">
                    <div class="col-12 mx-auto box-information">
                        <div class="row">
                            <div class="col-6">
                                <div class="input-group input-group-sm mb-2">
                                    <span class="input-group-text">Paciente</span>
                                    <input type="text" class="form-control" id="IdPaciente" name="IdPaciente" value="{{ $fichalaboral->paciente->Id ?? ''}}" @readonly(true)>
                                    <input type="text" class="form-control" style="width: 50%" id="NombreCompleto" name="NombreCompleto" value="{{ $fichalaboral->paciente->Apellido ?? ''}} {{ $fichalaboral->paciente->Nombre ?? ''}}" @readonly(true)>
                                    <input type="hidden" id="IdFichaLaboral" value="{{ $fichalaboral->Id}}">
                                </div>
                
                                <div class="input-group input-group-sm mb-2 selectClientes2">
                                    <span class="input-group-text">Empresa</span>
                                    <select class="form-control" id="selectClientes">
                                        <option value="{{ $fichalaboral->empresa->Id ?? '' }}">{{ $fichalaboral->empresa->RazonSocial ?? '' }}</option>
                                    </select>
                                </div>   
                            </div>
                        
                            <div class="col-6">
                                <br /><br />
                                <div class="input-group input-group-sm mb-2 selectArt2">
                                    <span class="input-group-text">ART</span>
                                    <select class="form-control" id="selectArt" >
                                        <option value="{{ $fichalaboral->art->Id ?? '' }}">{{ $fichalaboral->art->RazonSocial ?? '' }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr class="mt-1 mb-1">
                        <div class="row text-center">
                            <div class="col-12">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="TipoPrestacion" id="ART" value="ART" {{ isset($fichalaboral) && $fichalaboral->TipoPrestacion === 'ART' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ART">ART</label>
                                </div>
                
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="TipoPrestacion" id="INGRESO" value="INGRESO" {{ isset($fichalaboral) && $fichalaboral->TipoPrestacion === 'INGRESO' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ingreso">INGRESO</label>
                                </div>
                        
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="TipoPrestacion" id="PERIODICO" value="PERIODICO" {{ isset($fichalaboral) && $fichalaboral->TipoPrestacion === 'PERIODICO' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="periodico">PERIODICO</label>
                                </div>
                
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="TipoPrestacion" id="OCUPACIONAL" value="OCUPACIONAL" {{ isset($fichalaboral) && $fichalaboral->TipoPrestacion === 'OCUPACIONAL' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ocupacional">OCUPACIONAL</label>
                                </div>
                
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="TipoPrestacion" id="EGRESO" value="EGRESO" {{ isset($fichalaboral) && $fichalaboral->TipoPrestacion === 'EGRESO' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="egreso">EGRESO</label>
                                </div>
                
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="TipoPrestacion" id="TipoPrestacion" id="OTRO" value="OTRO" {{ isset($fichalaboral) && in_array($fichalaboral->TipoPrestacion, ['CARNET', 'NO ART', 'RECMED','S/C_OCUPACIONAL']) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="otro">OTRO</label>
                                </div>
                                <div class="form-check form-check-inline" id="divtipoPrestacionPresOtros" style="display: {{  isset($fichaLaboral) && in_array($fichaLaboral->TipoPrestacion, ['CARNET', 'NO ART', 'RECMED','S/C_OCUPACIONAL']) ? '' : 'none' }}">
                                    <select class="form-select" id="tipoPrestacionPresOtros">
                                        <option selected value="{{ isset($fichaLaboral) && in_array($fichalaboral->TipoPrestacion, ['CARNET', 'NO ART', 'RECMED','S/C_OCUPACIONAL']) ? $fichalaboral->TipoPrestacion : '' }}">{{ isset($fichaLaboral) && in_array($fichalaboral->TipoPrestacion, ['CARNET', 'NO ART', 'RECMED','S/C_OCUPACIONAL']) ? $fichalaboral->TipoPrestacion : 'Elija una opción...' }}</option>
                                        @foreach ($tiposPrestacionOtros as $tipo)
                                        <option value="{{ $tipo->Nombre }}">{{ $tipo->Nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                
                        <hr class="mt-1 mb-1">
                
                        <div class="row mt-2">
                            <div class="col-6 ">
                
                                <div class="input-group input-group-sm mb-2 TareaRealizar">
                                    <span class="input-group-text">Tareas a realizar</span>
                                    <input type="text" class="form-control" id="TareaRealizar" name="TareaRealizar" value="{{ $fichalaboral->Tareas ?? '' }}">
                                </div>
                
                                <div class="input-group input-group-sm mb-2 UltimoPuesto">
                                    <span class="input-group-text">Última empresa y puesto</span>
                                    <input type="text" class="form-control" id="UltimoPuesto" name="UltimoPuesto" value="{{ $fichalaboral->TareasEmpAnterior ?? '' }}">
                                </div>
                
                                <div class="input-group input-group-sm mb-2 PuestoActual">
                                    <span class="input-group-text">Puesto actual</span>
                                    <input type="text" class="form-control" id="PuestoActual" name="PuestoActual" value="{{ $fichalaboral->Puesto ?? '' }}">
                                </div>
                
                                <div class="input-group input-group-sm mb-2 SectorActual">
                                    <span class="input-group-text">Sector Actual</span>
                                    <input type="text" class="form-control" id="SectorActual" name="SectorActual" value="{{ $fichalaboral->Sector ?? '' }}">
                                </div>
                
                                <div class="input-group input-group-sm mb-2 CCosto">
                                    <span class="input-group-text">C.Costos</span>
                                    <input type="text" class="form-control" id="CCostos" name="CCostos" value="{{ $fichalaboral->CCosto ?? '' }}">
                                </div>
                
                                <div class="row">
                                    <div class="col-6">
                                        
                                        <div class="input-group input-group-sm mb-2 AntiguedadPuesto">
                                            <span class="input-group-text">Antig. Puesto</span>
                                            <input type="number" class="form-control" placeholder="0" id="AntiguedadPuesto" value="{{ $fichalaboral->AntigPuesto ?? '' }}">
                                        </div>
                
                                        <div class="input-group input-group-sm mb-2 AntiguedadEmpresa">
                                            <span class="input-group-text">Antig. Empresa</span>
                                            <input type="number" class="form-control" placeholder="0" id="AntiguedadEmpresa" readonly="">
                                        </div>
                                    </div>
                
                                    <div class="col-6">
                
                                        <div class="input-group input-group-sm mb-2 FechaIngreso">
                                            <span class="input-group-text">Fecha Ingreso</span>
                                            <input type="date" class="form-control" id="FechaIngreso" value="{{ (isset($fichalaboral->FechaIngreso) && $fichalaboral->FechaIngreso !== '0000-00-00') ? \Carbon\Carbon::parse($fichalaboral->FechaIngreso)->format('Y-m-d') : '' }}">
                                        </div>
                
                                        <div class="input-group input-group-sm mb-2 FechaEgreso">
                                            <span class="input-group-text">Fecha Egreso</span>
                                            <input type="date" class="form-control" id="FechaEgreso" value="{{ (isset($fichalaboral->FechaIngreso) && $fichalaboral->FechaEgreso !== '0000-00-00') ? \Carbon\Carbon::parse($fichalaboral->FechaEgreso)->format('Y-m-d') : '' }}">
                                        </div>
                
                                    </div>
                                </div>
                
                
                            </div>
                
                            <div class="col-6">
                                <div class="input-group input-group-sm mb-2">
                                    <span class="input-group-text">Jornada</span>
                                    <select class="form-select" id="TipoJornada">
                                        <option selected value="{{ $fichalaboral->TipoJornada ?? ''}}">{{ $fichalaboral->TipoJornada ?? 'Elija una opción...'}}</option>
                                        <option value="NORMAL">Normal</option>
                                        <option value="PROLONGADA">Prolongada</option>
                                    </select>
                                    <select class="form-select" id="Horario">
                                        <option selected value="{{ $fichalaboral->Jornada ?? '' }}">{{ $fichalaboral->Jornada ?? 'Elija una opción...' }}</option>
                                        <option value="DIURNA">Diurna</option>
                                        <option value="NOCTURNO">Nocturno</option>
                                        <option value="ROTATIVO">Rotativo</option>
                                        <option value="FULLTIME">Fulltime</option>
                                </select>
                                </div>
                
                                <div class="mt-3">
                                    <div class="input-group input-group-sm mb-2">
                                        <span class="input-group-text">Fecha Preocupacional</span>
                                        <input type="date" class="form-control" id="FechaPreocupacional" value="{{ (isset($fichalaboral->FechaPreocupacional) && $fichalaboral->FechaPreocupacional !== '0000-00-00') ? \Carbon\Carbon::parse($fichalaboral->FechaPreocupacional)->format('Y-m-d') : '' }}">
                                    </div>
                
                                    <div class="input-group input-group-sm mb-2">
                                        <span class="input-group-text">Fecha Ult. Periodico Empresa</span>
                                        <input type="date" class="form-control"  id="FechaUltPeriod" value="{{ (isset($fichalaboral->FechaUltPeriod) && $fichalaboral->FechaUltPeriod !== '0000-00-00') ? \Carbon\Carbon::parse($fichalaboral->FechaUltPeriod)->format('Y-m-d') : '' }}">
                                    </div>
                
                                    <div class="input-group input-group-sm mb-2">
                                        <span class="input-group-text">Fecha Ex ART</span>
                                        <input type="date" class="form-control" id="FechaExArt" value="{{ (isset($fichalaboral->FechaExArt) && $fichalaboral->FechaExArt !== '0000-00-00') ? \Carbon\Carbon::parse($fichalaboral->FechaExArt)->format('Y-m-d') : '' }}">
                                    </div>
                                </div>
                
                                <div class="mt-3">
                                    <label for="Observaciones" class="form-label">Observaciones</label>
                                    <textarea class="form-control" style="height: 100px" placeholder="Observaciones de la jornada laboral" id="ObservacionesFicha">{{ $fichalaboral->Observaciones ?? '' }}</textarea>
                                </div>
                
                            </div>
                        </div>
                
                        <hr class="mt-1 mb-1">
                
                        <div class="row">
                            <div class="col-sm-6">
                                @php
                                    $pago = ["A" => "Cuenta Corriente", "B" => "Contado", "P" => "Exámen a Cuenta"];
                                @endphp
                
                                <div class="input-group input-group-sm mb-2">
                                    <span class="input-group-text">Forma de Pago</span>
                                    <select class="form-select" id="PagoLaboral">
                                        <option value="{{ $fichalaboral->Pago ?? ''}}" selected>{{ isset($fichalaboral->Pago) && !in_array($fichalaboral->Pago, ['', null]) ? $pago[$fichalaboral->Pago] : "Elija una opción..."}}</option>
                                        <option value="B">Contado</option>
                                        <option value="A">Cuenta Corriente</option>
                                        <option value="P">Exámen a Cuenta</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                
                        <hr class="mt-1 mb-1">
            
                        <div class="row">
                            <div class="col-12 text-center mt-2">
                                <button type="button" id="guardarFicha" class="btn botonGeneral">Actualizar</button>
                            </div>
                        </div>
                            
                        
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="hidden" id="fase">
                <button type="button" class="btn botonGeneral" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn botonGeneral">Actualizar</button>
            </div>
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

<div id="addAdjPres" class="modal fadeInUp" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content"> 
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Adjuntar archivos prestación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>   
            </div>
            <div class="modal-body">
                <form id="form-efector">    
                    <input type="file" class="form-control" name="fileAdjPrestacion"/>
                
                    <div class="mt-3">
                        <label for="Descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" name="DescripcionAdjPrestacion" id="DescripcionAdjPrestacion" rows="5"></textarea>
                    </div>
                </form> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn botonGeneral" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn botonGeneral btnAdjFilePres">Guardar adjunto</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="opciones" class="modal fadeInUp" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
       <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Opciones</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>  
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-3">
                        <div class="input-group input-group mt-2">
                            <span class="input-group-text">Prestación</span>
                            <input type="text" class="form-control" value="{{ $prestacione->Id ?? 0}}" readonly="true">
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="input-group input-group mt-2">
                            <span class="input-group-text">Fecha</span>
                            <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($prestacione->Fecha)->format('d/m/Y') ?? '' }}" readonly="true">
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="input-group input-group mt-2">
                            <span class="input-group-text">Tipo de Prestación</span>
                            <input type="text" class="form-control" value="{{ $prestacione->TipoPrestacion ?? '' }}" readonly="true">
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-9">
                        <div class="input-group input-group mt-2">
                            <span class="input-group-text">Paciente</span>
                            <input type="text" class="form-control" value="{{ $prestacione->paciente->Apellido ?? ''}} {{ $prestacione->paciente->Nombre ?? ''}}" readonly="true">
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="input-group input-group mt-2">
                            <span class="input-group-text">Documento</span>
                            <input type="text" class="form-control" value="{{ $prestacione->paciente->Documento ?? 0}}" readonly="true">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <div class="input-group input-group mt-2">
                            <span class="input-group-text">Empresa</span>
                            <input title="{{ $prestacione->empresa->RazonSocial ?? ''}}" type="text" class="form-control" value="{{ $prestacione->empresa->RazonSocial ?? ''}}" readonly="true">
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="input-group input-group mt-2">
                            <span class="input-group-text">Para Empresa</span>
                            <input title="{{ $prestacione->empresa->ParaEmpresa ?? ''}}" type="text" class="form-control" value="{{ $prestacione->empresa->ParaEmpresa ?? ''}}" readonly="true">
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="input-group input-group mt-2">
                            <span class="input-group-text">ART</span>
                            <input title="{{ $prestacione->art->RazonSocial ?? ''}}" type="text" class="form-control" value="{{ $prestacione->art->RazonSocial ?? ''}}" readonly="true">
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="input-group input-group mt-2">
                            <span class="input-group-text">Email</span>
                            <input id="EMailInformes" title="{{ $prestacione->empresa->EMailInformes ?? ''}}" type="text" class="form-control" value="{{ $prestacione->empresa->EMailInformes ?? ''}}">
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <form>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="evaluacion">
                            <label class="form-check-label" for="evaluacion">
                                Evaluación resumen
                            </label>
                        </div>
                        
                        <hr class="mt-2 mb-2">

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="eEstudio">
                            <label class="form-check-label" for="eEstudio">
                                eEstudios
                            </label>
                            <p class="text-muted small">Solo disponible para impresión</p>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="eAnexo">
                            <label class="form-check-label" for="eAnexo">
                                eAnexos
                            </label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="eEnvio">
                            <label class="form-check-label" for="eEnvio">
                                eEnvio
                            </label>
                            <p class="text-muted small">Solo disponible para impresión</p>
                        </div>

                        <hr class="mt-2 mb-2">
    
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="adjDigitales">
                            <label class="form-check-label" for="adjDigitales">
                                Adjuntos digitales
                            </label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="adjFisicos">
                            <label class="form-check-label" for="adjFisicos">
                                Adjuntos físicos
                            </label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="adjPrestacion">
                            <label class="form-check-label" for="adjPrestacion">
                                Adjuntos prestación
                            </label>
                        </div>
    
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="adjFisicosDigitales">
                            <label class="form-check-label" for="adjFisicosDigitales">
                                Adjuntos fisicos y digitales
                            </label>
                            <p class="text-muted small">Solo disponible para impresión</p>
                        </div>

                        <hr class="mt-2 mb-2">

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="infInternos">
                            <label class="form-check-label" for="infInternos">
                                Informes internos
                            </label>
                            <p class="text-muted small">Solo disponible para impresión</p>
                        </div>
    
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="pedProveedores">
                            <label class="form-check-label" for="pedProveedores">
                                Pedido a proveedores
                            </label>
                            <p class="text-muted small">Solo disponible para impresión</p>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="conPaciente">
                            <label class="form-check-label" for="conPaciente">
                                Control paciente
                            </label>
                            <p class="text-muted small">Solo disponible para impresión</p>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="resAdmin">
                            <label class="form-check-label" for="resAdmin">
                                Resumen administrativo
                            </label>
                        </div>
    
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="caratula">
                            <label class="form-check-label" for="caratula">
                                Caratula
                            </label>
                            <p class="text-muted small">Solo disponible para impresión</p>
                        </div>

                        <hr class="mt-2 mb-2">
    
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="consEstDetallado">
                            <label class="form-check-label" for="consEstDetallado">
                                Constancia de estudio completo (Detallado)
                            </label>
                        </div>
    
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="consEstSimple">
                            <label class="form-check-label" for="consEstSimple">
                                Constancia de estudio completo (Simple)
                            </label>
                        </div>
    
                        <hr class="mt-2 mb-2">
    
                        <p class="fw-bold">Estudios: <small>(Solo disponible para impresión)</small></p>

                        <div id="estudios">

                        </div>
    
                        <div class="mb-3 text-center">
                            <button type="button" class="btn btn-sm botonGeneral imprimirReporte"><i class="bx bxs-file-pdf"></i>Imprimir</button>
                            <button type="button" class="btn btn-sm botonGeneral enviarReporte"><i class="ri-send-plane-line"></i>Enviar</button>
                        </div>
                    </form>
                </div>

            </div>
            <div class="modal-footer"></div>
            </div>
    </div>
</div>

<div id="exaCuenta" class="modal fadeInUp" tabindex="-1" aria-labelledby="myModalLabel" aria-hidde="true" style="display: none">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"> Disponibilidad Examenes a Cuenta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body" class="text-center p-3">
                <div class="row auto-mx mb-3">
                    <div class="table mt-3 mb-1 mx-auto col-sm-7">
                        <table id="listadoSaldos" class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th class="sort">Precarga</th>
                                    <th class="sort">Examen</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all" id="lstSaldos">
                
                            </tbody>
                        </table>
                
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modalExamen" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" style="display: none; z-index: 9998">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-11 mx-auto">

                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0">Exámen prestación <span id="ex-prestacionTitulo" class="custom-badge original"></span> | Paciente <span id="ex-paciente" class="custom-badge original"></span> <span id="ex-anulado"></span></h4>
                            <input type="hidden" value="" id="Id">
                            <div class="page-title-right d-inline">
                                <p><strong>QR: </strong><span id="ex-qr"></span></p>
                            </div>
                        </div>


                        <div class="container-fluid">

                            <div class="row mb-3">
                                <div class="col-12 mx-auto box-information">
                                    <div class="messageExamen"></div>

                                    <div class="row">
                                        <input type="hidden" id="ex-identificacion">
                                        <input type="hidden" id="ex-prestacion">
                        
                                        <div class="col-6">
                                            <div class="input-group input-group-sm mb-2 size50porcent">
                                                <span class="input-group-text">Fecha Estudio</span>
                                                <input type="date" name="ex-fecha" id="ex-fecha" class="form-control">
                                            </div>
                        
                                            <div class="input-group input-group-sm mb-2">
                                                <span class="input-group-text">Exámen</span>
                                                <input type="text" name="ex-examen" id="ex-examen" class="form-control" @readonly(true)>
                                            </div>
                                        </div>
                                        <div class="col-6"></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-6">
                                            <div class="input-group input-group-sm mb-2">
                                                <span class="input-group-text">Especialidad Efector</span>
                                                <input type="text" class="form-control" name="ex-provEfector" id="ex-provEfector" @readonly(true)>
                                                <input type="hidden" id="ex-IdEfector">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="input-group input-group-sm mb-2">
                                                <span class="input-group-text">Especialidad Informador</span>
                                                <input type="text" class="form-control" name="ex-provInformador" id="ex-provInformador" @readonly(true)>
                                                <input type="hidden" id="ex-IdInformador">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="input-group input-group-sm mb-2">
                                                <span class="input-group-text">Observaciones</span>
                                                <textarea class="form-control" style="height: 80px" id="ex-ObsExamen" name="ex-ObsExamen"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 mx-auto box-information">
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="input-group input-group-sm mb-2">
                                                <span class="input-group-text">Efector</span>
                                                <select name="ex-efectores" id="ex-efectores" class="form-control ex-efector">
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="input-group input-group-sm mb-2">
                                                <span class="input-group-text">Fecha asig.</span>
                                                <input type="date" class="form-control" id="ex-FechaAsignado" name="ex-FechaAsignado" @readonly(true)>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="input-group input-group-sm mb-2">
                                                <span class="input-group-text">Estado</span>
                                                <input type="text" class="form-control" id="ex-EstadoEx" name="ex-EstadoEx" @readonly(true)>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="input-group input-group-sm mb-2">
                                                <span class="input-group-text">Fecha Pago</span>
                                                <input type="date" class="form-control" id="ex-FechaPagado" name="ex-FechaPagado" @readonly(true)>
                                            </div>
                                        </div>

                                        <div class="col-md-2 text-end">
                                            <button type="button" id="ex-asignar" class="btn botonGeneral btn-sm ex-asignar">Asignar</button>
                                            <button type="button" id="ex-liberar" class="btn botonGeneral btn-sm ex-liberar">Liberar</button>
                                            <button type="button" id="ex-abrir" class="btn botonGeneral btn-sm ex-abrir">Abrir</button>
                                            <button type="button" id="ex-cerrar" class="btn botonGeneral btn-sm ex-cerrar">Cerrar</button>
                                           <input type="hidden" id="ex-CAdj">
                                        </div>
                        

                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="input-group input-group-sm mb-2">
                                                <span class="input-group-text">Adjunto</span>
                                                <input type="text" class="form-control" id="ex-Estado" name="ex-Estado" @readonly(true)>
                                            </div>
                                        </div>

                                        <div class="col-md-9 text-end">
                                            <button type="button" class="btn botonGeneral ex-adjuntarEfector" data-bs-toggle="offcanvas" data-bs-target="#modalEfector">Adjuntar archivo</button>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 mx-auto box-information">
                        
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="input-group input-group-sm mb-2 visualizarInformador">
                                                <span class="input-group-text">Informador</span>
                                                    <select name="ex-informadores" id="ex-informadores" class="form-control">
                                                        <option value="" selected></option>
                                                    </select>
                                            </div>
                                        </div>
                        
                                        <div class="col-md-2">
                                            <div class="input-group input-group-sm mb-2 visualizarInformador">     
                                                <span class="input-group-text">Estado</span>
                                                <input type="text" class="form-control" id="ex-EstadoI" name="ex-EstadoI" @readonly(true)>
                                            </div>
                                        </div>
                        
                                        <div class="col-md-2 visualizarInformador">
                                            <div class="input-group input-group-sm mb-2">
                                                <span class="input-group-text">Fecha Pago</span>
                                                <input type="date" id="ex-FechaPagado2" name="ex-FechaPagado2" class="form-control" @readonly(true)>
                                            </div>
                                        </div>
                        
                                        <div class="col-md-4 text-end visualizarInformador ">
                                            <button type="button" id="ex-asignarI" class="btn botonGeneral btn-sm ex-asignarI">Asignar</button>
                                            <button type="button" id="ex-liberarI" class="btn botonGeneral btn-sm ex-liberarI">Liberar</button>
                                            <button type="button" id="ex-cerrarI" class="btn botonGeneral btn-sm ex-cerrarI">Cerrar</button>
                                            <input type="hidden" id="ex-CInfo">
                                        </div>
                        
                                    </div>

                                    <div class="row visualizarInformador">
                                        <div class="col-md-3">
                                            <div class="input-group input-group-sm mb-2 ">
                                                <span class="input-group-text">Adjunto</span>
                                                <input type="text" class="form-control" id="ex-EstadoInf" name="ex-EstadoInf" @readonly(true)>
                                            </div>
                                        </div>

                                        <div class="col-md-9 text-end">
                                            <button type="button" class="btn botonGeneral ex-adjuntarInformador"  data-bs-toggle="offcanvas" data-bs-target="#modalInformador">Adjuntar archivo</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3 visualizarInformador">
                                <div class="col-12 mx-auto box-information">
                                    <div class="input-group input-group-sm mb-2">
                                        <span class="input-group-text">Observaciones Informador</span>
                                        <textarea class="form-control" style="height: 80px" name="ex-Obs" id="ex-Obs" disabled></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 mx-auto box-information">
                        
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="input-group input-group-sm mb-2">
                                                <span class="input-group-text">Fecha Factura</span>
                                                <input type="date" class="form-control" id="ex-FechaFacturaVta" name="ex-FechaFacturaVta" @readonly(true)>
                                            </div>
                                            <div class="input-group input-group-sm mb-2">
                                                <span class="input-group-text">Nro Factura</span>
                                                <input type="text" class="form-control" id="ex-NroFacturaVta" name=" ex-NroFacturaVta"  @readonly(true)>
                                            </div>
                                        </div>
                        
                                        <div class="col-md-4">
                                            <div class="input-group input-group-sm mb-2">
                                                <span class="input-group-text">Fecha NC</span>
                                                <input type="date" class="form-control" id="ex-FechaNC" name="ex-FechaNC" @readonly(true)>
                                            </div>
                                            <div class="input-group input-group-sm mb-2">
                                                <span class="input-group-text">Número NC</span>
                                                <input type="text" class="form-control" id="ex-NumeroNC" name="ex-NumeroNC" @readonly(true)>
                                            </div>
                                        </div>
                        
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3"> <!-- actualizarExamen -->
                                <div class="col-12 box-information text-center">
                                    <button type="button" class="btn botonGeneral" id="actExamenModal">Guardar</button>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 box-information text-center">
                        
                                    <div class="table-responsive table-card mt-3 mb-1">
                        
                                        <table class="display table table-bordered mb-4" style="width:100%"  id="listadoEfector">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="sort" title="Adjunto Efector">Adjunto Efector</th>
                                                    <th>Descripción</th>
                                                    <th>Adjuntar</th>
                                                    <th>Multi</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="listaefectores" class="list form-check-all">
                                    
                                            </tbody>
                                        </table>
                        
                                        <table class="display table table-bordered mt-4 visualizarInformador" style="width:100%"  id="listadoInformador">
                                            <thead class="table-light">
                                                <th class="sort" title="Adjunto Informador">Adjunto Informador</th>
                                                <th>Descripción</th>
                                                <th>Acciones</th>
                                            </thead>
                                            <tbody id="listainformadores" class="list form-check-all">
                                    
                                            </tbody>
                                        </table>
                                    </div>
                        
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="offcanvas offcanvas-end" tabindex="-1" id="replaceAdjunto" aria-labelledby="offcanvasRightLabel" style="z-index: 9999">
    <div class="offcanvas-header">
        <h5 id="offcanvasRightLabel">Reemplazar archivo</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body text-center">
        <form id="form-replace">
            <input type="file" class="form-control" name="fileReplace"/>
            <input type="hidden" name="replaceId" id="replaceId" value="">
            <input type="hidden" name="replaceTipo" id="replaceTipo" value="">
            <div class="mt-2 text-center">
                <button type="button" class="btn botonGeneral btnReplaceAdj">Guardar adjunto</button>
            </div>
        </form> 
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="modalEfector" aria-labelledby="offcanvasRightLabel" style="z-index: 9999">
    <div class="offcanvas-header">
        <h5 id="offcanvasRightLabel">Adjuntar archivo Efector</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body text-center">
        <form id="form-efector">

            <div class="multiEf">
                <div class="alert alert-info alert-border-left alert-dismissible fade show mb-2" role="alert">
                    Exámen con multi adjunto habilitado. Elija a que exámen quiere asociar el reporte.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
               
                <div class="list-group listaGrupoEfector">
                </div>
            </div>
            <input type="file" class="form-control fileA" name="fileEfector"/>
        
            <div class="mt-3">
                <label for="Descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" name="DescripcionE" id="DescripcionE" rows="5"></textarea>
                <input type="hidden" id="ex-multi">
            </div>
        </form> 
        <div class="mt-2 text-center">
            <button type="button" class="btn botonGeneral ex-btnAdjEfector">Guardar adjunto</button>
        </div>
        
        
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="modalInformador" aria-labelledby="offcanvasRightLabel" style="z-index: 9999">
    <div class="offcanvas-header">
        <h5 id="offcanvasRightLabel">Adjuntar archivo Informador</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body text-center">
       
        <form id="form-informador">
                    
            <div class="multiInf">
                <div class="alert alert-info alert-border-left alert-dismissible fade show mb-2" role="alert">
                    Exámen con multi adjunto habilitado. Elija el reporte que quiere asociar.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
           
                <div class="list-group listaGrupoInformador">
                </div>
            
            </div>
            <input type="file" class="form-control fileA" name="fileInformador"/>
        
            <div class="mt-3">
                <label for="Descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" name="DescripcionI" id="DescripcionI" rows="5"></textarea>
                <input type="hidden" id="ex-multiE">
            </div>
        </form> 
        <div class="mt-2 text-center">
            <button type="button" class="btn botonGeneral ex-btnAdjInformador">Guardar adjunto</button>
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
const updateEstadoItem = "{{ route('updateEstadoItem') }}";
const liberarExamen = "{{ route('liberarExamen') }}";
const marcarExamenAdjunto = "{{ route('marcarExamenAdjunto') }}";

const getPaquetes = "{{ route('getPaquetes') }}";
const searchExamen = "{{ route('searchExamen') }}";
const getItemExamenes = "{{ route('itemsprestaciones.listadoexamenes') }}";
const checkItemExamen = "{{ route('checkItemExamen') }}";
const saveItemExamenes = "{{ route('saveItemExamenes') }}";
const getId = "{{ route('IdExamen') }}";
const deleteItemExamen = "{{ route('deleteItemExamen')}}";
const bloquearItemExamen ="{{ route('bloquearItemExamen') }}";
const getClientes = "{{ route('getClientes') }}";
const paqueteId = "{{ route('paqueteId') }}";
const itemExamen = "{{ route('itemExamen') }}";
const checkParaEmpresa = "{{ route('checkParaEmpresa') }}";
const getFactura = "{{route('getFactura') }}";
const getBloqueoPrestacion = "{{ route('getBloqueoPrestacion') }}";
const privateComment = "{{ route('comentariosPriv') }}";
const savePrivComent = "{{ route('savePrivComent') }}";
const getAutorizados = "{{ route('getAutorizados') }}";
const lstExDisponibles = "{{ route('lstExDisponibles') }}";
const buscarEx = "{{ route('buscarEx')}}";
const saveFichaAlta = "{{ route('saveFichaAlta') }}";
const checkInc = "{{ route('prestaciones.checkIncompleto') }}";
const checkFirst = "{{ route('itemsprestaciones.checkId') }}";
const editModal = " {{ route('itemsprestaciones.editModal') }}";
const checkAdj = "{{ route('itemsprestaciones.checkAdjuntos') }}";
const paginacionGeneral = "{{ route('paginacionGeneral') }}";
const listGeneral = "{{ route('listGeneral') }}";

const fileUpload = "{{ route('uploadAdjunto') }}";
const descargaE = "@fileUrl('lectura')/AdjuntosEfector";
const descargaI = "@fileUrl('lectura')/AdjuntosInformador";
const deleteIdAdjunto = "{{ route('deleteIdAdjunto') }}";
const replaceIdAdjunto = "{{ route('replaceIdAdjunto') }}";
const updateAsignado = "{{ route('updateAsignado') }}";
const updateItem = "{{ route('updateItem') }}";
const updateItemExamen = "{{ route('updateItemExamen') }}";
const getBloqueoItemPrestacion = "{{ route('getBloqueoItemPrestacion') }}";
const exportPdf = "{{ route('prestaciones.pdf') }}";
const listadoEstudiosImp = "{{ route('prestaciones.estudioReporte') }}";
const enviarReporte = "{{ route('prestaciones.enviar') }}";
const eEnviarAviso = "{{ route('prestaciones.aviso') }}";
const exportXls = "{{ route('prestaciones.excelResumen') }}";
const btnVisibleEnviar = "{{ route('prestaciones.visibleEnviar') }}";
const CmdTodo = "{{ route('prestaciones.btnTodo') }}";
const fileUploadPres = "{{ route('prestaciones.uploadAdjPres') }}";
const loadlistadoAdjPres = "{{ route('prestaciones.listaAdjPres') }}";


//Extras
const TOKEN = "{{ csrf_token() }}";
const UBICACION = "{{ request()->query('location') }}";
const ID = "{{ $prestacione->Id }}";
const IDEMPRESA = "{{ $prestacione->empresa->Id }}";
const editUrl = "{{ route('itemsprestaciones.edit', ['itemsprestacione' => '__examen__'])}}";
const cacheDelete = "{{ route('prestaciones.cacheDelete') }}";
const descarga = "@fileUrl('lectura')/AdjuntosPrestacion";
const deleteAdjPrest = "{{ route('prestaciones.deleteAdjPres') }}";

//Select
const selectTipoPrestacion = "{{ $prestacione->TipoPrestacion }}";

</script>


@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/prestaciones/edit.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/prestaciones/utils.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/prestaciones/examenes.js')}}?v= {{ time() }}"></script>
<script src="{{ asset('js/fichalaboral/edit.js')}}?v={{ time() }}"></script>
<script src="{{ asset('js/prestaciones/modalEdit.js')}}?v={{ time() }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('js/fancyTable.js') }}"></script>
@endpush

@endsection