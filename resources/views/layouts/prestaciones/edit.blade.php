@extends('template')

@section('title', 'Editar Prestación')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Editar Prestaciones <span class="badge text-bg-primary">N° {{ $prestacione->Id }}</span> <span class="badge text-bg-success">{{ $prestacione->TipoPrestacion }}</span> <span class="badge text-bg-info">{{ \Carbon\Carbon::parse($prestacione->Fecha)->format('d/m/Y') }}</span></h4>

    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="{{ route('prestaciones.index') }}">Prestaciones</a></li>
            <li class="breadcrumb-item active">Editar</li>
        </ol>
    </div>
</div>

<div class="col-xl-12">
    <div class="card">
        <div class="card-body form-steps">


            <div class="row">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            
                </div>
                <!--end col-->
                <div class="col-xxl-12">
                    <div class="card mt-xxl-2">
                        <div class="card-header">
                        </div>
                        
                        <div class="card-body p-4">
                            <div class="tab-content">

                                <div class="row">
                                    <div class="col-2">
                                        <label for="nroPrestacion" class="form-label"> Fecha alta</label>
                                        <div class="mb-3">
                                            <input type="date" class="form-control" id="Fecha" value="{{ $prestacione->Fecha ?? ''}}">
                                        </div>
                                    </div>

                                    <div class="col-1">
                                        <label for="nroPrestacion" class="form-label"> Número  </label>
                                        <div class="mb-3">
                                            <input type="hidden" name="Id" id="Id" value="{{ $prestacione->Id}}">
                                            <input type="text" class="form-control" placeholder="Nro Prestacion" id="Id" value="{{ $prestacione->Id }}"  @readonly(true)>
                                        </div>
                                    </div>

                                    <div class="col-3">
                                        <div class="mb-3">
                                            <label for="paciente" class="form-label"> Paciente  </label> <a href="{{ route('pacientes.edit', ['paciente' => $prestacione->IdPaciente])}}" target="_blank"><span class="badge text-bg-info" style="margin-left:20px">Ver paciente</span></a><!--link al perfil del paciente - se abre en una nueva pestaña-->
                                            <input type="text" class="form-control" placeholder="Apellido y Nombre del paciente" value="{{ $prestacione->paciente->Apellido }} {{ $prestacione->paciente->Nombre }}" id="apellidoNombre"  @readonly(true)>
                                            <input type="hidden" name="IdPaciente" id="IdPaciente" value="{{ $prestacione->IdPaciente }}">
                                        </div>
                                    </div>

                                    <div class="col-3">
                                        <div class="mb-3">
                                            <label for="Financiador" class="form-label">Financiador</label>
                                            <select name="Financiador" id="Financiador" name="Financiador" class="form-control" title="{{ $financiador->RazonSocial ?? '' }} - {{ $financiador->Identificacion ?? '' }}">
                                                <option value="{{ $financiador->Id ?? '' }}"selected>{{ $financiador->RazonSocial ?? '' }} - {{ $financiador->Identificacion ?? '' }}</option>
                                                <option value="{{ $prestacione->art->Id}}">ART: {{ $prestacione->art->RazonSocial }} - {{ $prestacione->art->Identificacion }}</option>
                                                <option value="{{ $prestacione->empresa->Id}}">Empresa: {{ $prestacione->empresa->RazonSocial }} - {{ $prestacione->empresa->Identificacion }}</option>
                                            </select>
                                        </div>
                                    </div><!--end col-->
                                    <div class="col-3">
                                        <div class="mb-3">
                                            <label for="empresa" class="form-label">Empresa</label>
                                            <select type="text" class="form-control" name="empresa" id="empresa" >
                                                <option value="{{ $prestacione->empresa->Id}}" selected>Empresa: {{ $prestacione->empresa->RazonSocial }} - {{ $prestacione->empresa->Identificacion }}</option>
                                            </select>
                                        </div>
                                    </div><!--end col-->

                                    <div class="col-3">
                                        <div class="mb-3">
                                            <label for="art" class="form-label">ART</label>
                                            <select data-id="{{ $prestacione->art->Id}}" type="text" name="art" id="art" class="form-control" >
                                                <option value="{{ $prestacione->art->Id}}" selected>ART: {{ $prestacione->art->RazonSocial }} - {{ $prestacione->art->Identificacion }}</option>
                                            </select>
                                        </div>
                                    </div><!--end col-->

                                    <div class="col-2">
                                        <div class="mb-3">
                                            <label for="TipoPrestacion" class="form-label">Tipo de Prestación</label>
                                            <select class="form-control" name="TipoPrestacion" id="TipoPrestacion">
                                                <option value="{{ $prestacione->TipoPrestacion ? $prestacione->TipoPrestacion : '' }}">{{ $prestacione->TipoPrestacion ? $prestacione->TipoPrestacion : 'Elija una opción...' }}</option>
                                                @foreach($tipoPrestacion as $tipo)
                                                    <option value="{{ $tipo->Nombre }}">{{$tipo->Nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div><!--end col-->

                                    <div class="col-3 mapas">
                                        <div class="mb-3">
                                            <label for="mapa" class="form-label">Mapa</label>
                                            <select class="form-control" name="mapas" id="mapas">
                                                <option value="{{ $prestacione->mapa->Id ?? ''}} " selected>{{ $prestacione->mapa->Nro ?? ''}} ART: {{ $prestacione->mapa->artMapa->RazonSocial ?? ''}} | Empresa: {{ $prestacione->mapa->empresaMapa->RazonSocial ?? ''}}</option>
                                            </select>
                                        </div>
                                    </div><!--end col-->
                                    
                                    <div class="col-2">
                                        <div class="mb-3">
                                            <label for="NumeroFacturaVta" class="form-label">Nro de Factura de Venta</label>
                                            <input type="text" class="form-control" placeholder="Nro Factura de venta" value="{{ $prestacione->NumeroFacturaVta ?? ''}}" id="NumeroFacturaVta" name="NumeroFacturaVta">
                                        </div>
                                    </div><!--end col-->

                                    <div class="col-2">
                                        <div class="mb-3">
                                            <label for="fechaCierre" class="form-label">Fecha Cierre</label> 
                                            <span class="badge text-bg-primary cerrar">{!!($prestacione->FechaCierre == '0000-00-00' || $prestacione->FechaCierre == null ? 'Cerrar' : '<i class="ri-lock-line"></i> Cerrado' )!!}</span> <!--este botón cerrar es el que marcará la prestación como cerrada (campo Cerrado = 1) y guardará la fecha en el campo FechaCierre -->
                                            <input type="text" class="form-control" id="cerrar" placeholder="dd/mm/aaaa" value="{{ ($prestacione->FechaCierre == '0000-00-00' || $prestacione->FechaCierre == null ? '': \Carbon\Carbon::parse($prestacione->FechaCierre)->format('d/m/Y')) }}" @readonly(true)>

                                        </div>
                                    </div><!--end col-->
                                    <div class="col-2">
                                        <div class="mb-3 FechaFinalizado">
                                            <label for="FechaFinalizado" class="form-label">F. Finalización</label> 
                                            
                                            {!! ($prestacione->Cerrado == 1 ?  ($prestacione->Finalizado == 1 ? '<span class="badge text-bg-warning finalizar"><i class="ri-lock-line"></i> Finalizado</span>' : '<span class="badge text-bg-warning finalizar">Finalizar</span>') : '<span class="badge text-bg-dark" title="Inhabilitado. Necesita cerrar la prestación.">Finalizar</span>') !!} 
                                            
                                            <!--este botón finalizar, sólo debe estar habilitado si la prestación está cerrada.  es el que marcará la prestación como finalizada (campo Finalizado = 1) y guardará la fecha en el campo FechaFinalizado -->
                                            <input type="text" class="form-control" id="finalizar" placeholder="dd/mm/aaaa" value="{{ ($prestacione->FechaFinalizado == '0000-00-00' || $prestacione->FechaFinalizado == null ? '' : \Carbon\Carbon::parse($prestacione->FechaFinalizado)->format('d/m/Y')) }}" @readonly(true)>

                                        </div>
                                    </div><!--end col-->

                                    <div class="col-2">
                                        <div class="mb-3 FechaEntrega">
                                            <label for="FechaEntrega" class="form-label">F Entrega</label> 
                                            
                                            {!! ($prestacione->Finalizado == 1 ? ($prestacione->Entregado == 1 ? '<span class="badge text-bg-success entregar"><i class="ri-lock-line"></i> Entregado</span>' : '<span class="badge text-bg-success entregar">Entregar</span>') : '<span class="badge text-bg-dark" title="Inhabilitado. Necesita finalizar la prestación.">Entregar</span>') !!} 
                                            
                                            <!--este botón entregar, sólo debe estar habilitado si la prestación está finalizada, es el que marcará la prestación como entregada (campo Entregado = 1) y guardará la fecha en el campo FechaEntrega -->
                                            <input type="text" class="form-control" id="entregar" placeholder="dd/mm/aaaa" value="{{ ($prestacione->FechaEntrega == '0000-00-00' || $prestacione->FechaEntrega == null ?'': \Carbon\Carbon::parse($prestacione->FechaEntrega)->format('d/m/Y')) }}" @readonly(true)>

                                        </div>
                                    </div><!--end col-->

                                    <div class="col-2">
                                        <div class="mb-3 FechaEnviado">
                                            <label for="FechaEnviado" class="form-label">F E-envío</label> 

                                            {!! ($prestacione->Cerrado == 1 ? ($prestacione->eEnviado == 1 ? '<span class="badge text-bg-info eEnviar"><i class="ri-lock-line"></i> E-enviado</span>' : '<span class="badge text-bg-info eEnviar">E-enviar</span>') : '<span class="badge text-bg-info eEnviar">E-enviar</span>') !!}
                                            
                                            <!--este botón e-enviar, sólo debe estar habilitado si la prestación está finalizada, es el que marcará la prestación como e-enviada (campo eEnviado = 1) y guardará la fecha en el campo FechaEnviado. -->
                                            <input type="text" class="form-control" id="eEnviar" placeholder="dd/mm/aaaa" value="{{ ($prestacione->FechaEnviado == '0000-00-00' || $prestacione->FechaEnviado == null ? '' : \Carbon\Carbon::parse($prestacione->FechaEnviado)->format('d/m/Y')) }}" @readonly(true)>

                                        </div>
                                    </div><!--end col-->
                                    <div class="col-2">
                                        <div class="mb-3">
                                            <label for="dateInput" class="form-label">Fecha Facturación</label> <!--este campo solo muestra el contenido del campo FechaFact, no es editable. -->
                                            <input type="date" class="form-control" id="FechaFact" value="{{ ($prestacione->FechaFact == '0000-00-00' || $prestacione->FechaFact == null ? '': $prestacione->FechaFact) }}" @readonly(true)>

                                        </div>
                                    </div><!--end col-->
                                    <div class="col-2">
                                        <div class="mb-3 FechaVto">
                                            <label for="FechaVto" class="form-label">Fecha Vencimiento</label> 
                                            
                                            @if($prestacione->FechaVto === null || $prestacione->FechaVto === '0000-00-00')
                                                
                                            @elseif($prestacione->FechaVto > date('Y-m-d'))
                                                <span class="badge text-bg-success vigente">Vigente</span>
                                            @else 
                                                <span class="custom-badge rojo vigente">Vencido</span>
                                            @endif
                                            <!--campo FechaVto muestra el span vigente verde o vencido rojo de acuerdo a la comparación con la fecha actual en el caso de que esté vencido, se guarda el valor 1 en el campo Vto-->
                                            <input type="date" class="form-control" id="FechaVto" value="{{ $prestacione->FechaVto }}" @readonly(true)>

                                        </div>
                                    </div><!--end col-->
                                    <div class="col-2">
                                        <div>
                                            <label for="pago" class="form-label">Forma de pago</label> <!-- campo Pago-->
                                            <select class="form-select" id="pago">
                                                <option value="{{ $prestacione->Pago ?? '' }}" selected>{{ ($prestacione->Pago === "B")? 'Contado' : (($prestacione->Pago === 'C')? 'Cuenta Corriente' : (($prestacione->Pago === 'P')? 'Pago a cuenta' : 'Elija una opción...')) }}
                                                </option>
                                                <option value="B">Contado</option>
                                                <option value="C">Cuenta Corriente</option>
                                                <option value="P">Pago a cuenta</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div>
                                            <label for="SPago" class="form-label">Medio de pago</label><!-- campo sPago-->
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

                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="Observaciones" class="form-label">Observaciones</label><!-- campo observaciones-->
                                            <textarea class="form-control" placeholder="Observaciones recepción" id="Observaciones">{{ $prestacione->Observaciones }}</textarea>
                                        </div>
                                    </div><!--end col-->

                                    <!--end col-->
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-3">
                                        <label for="Profesional" class="form-label"> Evaluador  </label> <!--este select contiene Apellido + Nombre de la tabla profesionales que tengan el campo T3 con valor 1 -->
                                        <div class="mb-3">
                                            <select class="form-select" id="IdEvaluador" name="profesionales">
                                                <option value="{{ $prestacione->profesional->Id ?? ''}}" selected>{{ $prestacione->profesional->Apellido ?? ''}} {{ $prestacione->profesional->Nombre ?? ''}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <label for="Evaluacion" class="form-label"> Evaluación  </label> <!-- la selección se guarda en el campo Evaluacion -->
                                        <div class="mb-3">
                                            <select class="form-select" id="Evaluacion" name="Evaluacion">
                                                <option value="{{ $prestacione->Evaluacion ? $prestacione->Evaluacion : ''}}" selected>{{ $prestacione->Evaluacion ? substr($prestacione->Evaluacion, 2) : 'Elija una opción...'}}</option>
                                                <option value="1.APTO SANO">Apto Sano</option>
                                                <option value="2.APTO CON PRE-EXISTENCIA(Sin interferencia en el desempeño laboral)">Apto con pre-existencia(Sin interferencia en el desempeño laboral)</option>
                                                <option value="3.APTO CON PRE-EXISTENCIA(Solo condiciones especiales de trabajo)">Apto con pre-existencia (Solo condiciones especiales de trabajo)</option>
                                                <option value="4.NO APTO">No apto</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <label for="Calificacion" class="form-label"> Calificación  </label> <!-- la selección se guarda en el campo Calificacion -->
                                        <div class="mb-3">
                                            <select class="form-select" id="Calificacion" name="Calificacion">
                                                <option value ="{{ $prestacione->Calificacion ? $prestacione->Calificacion : '' }}" selected>{{ $prestacione->Calificacion ? substr($prestacione->Calificacion, 2) : 'Elija una opción...' }}</option>
                                                <option value="1.SANO">Sano</option>
                                                <option value="2.AFECCIÓN CONOCIDA PREVIAMENTE">Afección conocida previamente</option>
                                                <option value="3.AFECCIÓN DESCUBIERTA EN ESTE EXAMEN">Afección descubierta en este exámen</option>
                                                <option value="4.AFECCIÓN CONOCIDA PREVIAMENTE Y AFECCIÓN DESCUBIERTA EN ESTE EXAMEN">Afección conocida previamente y afección descubierta en este exámen</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <label for="RxPreliminar" class="form-label"><br /></label> <!-- la selección se guarda en el campo Calificacion -->
                                        <div class="form-check mb-3">
                                            <label class="form-check-label" for="RxPreliminar">RX preliminar</label>
                                            <input class="form-check-input" type="checkbox" id="RxPreliminar" {{ $prestacione->RxPreliminar == 'null' || $prestacione->RxPreliminar == 0 ? '' : 'checked'}}>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <label for="SinEvaluacion" class="form-label"><br /> </label> <!-- la selección se guarda en el campo Calificacion -->
                                        <div class="form-check mb-3">
                                            <label class="form-check-label" for="SinEval">Sin Evaluación</label>
                                            <input class="form-check-input" type="checkbox" id="SinEval" {{ optional($prestacione->prestacionAtributo)->SinEval == 'null' || optional($prestacione->prestacionAtributo)->SinEval == 0 ? '' : 'checked'}}>

                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="ObsExamenes" class="form-label">Observaciones de exámenes</label><!-- campo obsexamenes-->
                                            <textarea class="form-control" placeholder="Observaciones" id="ObsExamenes" name="ObsExamenes"> {{ $prestacione->ObsExamenes }}</textarea>
                                        </div>
                                    </div><!--end col-->

                                </div>
                                <hr>
                                <div class="listjs-table" id="customerList">
                                    <div class="row">
                                        
                                    <br>
                                    <div class="row">

                                        <div class="col-6">
                                            <label for="paquetes" class="form-label"> Paquetes  </label> <!-- select 2 de paquetes de exámenes -->
                                            <div class="mb-3">
                                                <div class="cajaExamenes">
                                                    <select class="form-select" name="paquetes" id="paquetes"></select>
                                                    <i class="addPaquete ri-play-list-add-line" title="Añadir paquete completo"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label for="examenes" class="form-label"> Examen  </label> <!-- select 2 de exámenes -->
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
                                                <th class="sort" title="Exámen">Exa</th>
                                                <th title="Incompleto">Inc</th>
                                                <th title="Ausente">Aus</th>
                                                <th title="Forma">For</th>
                                                <th>Esc</th>
                                                <th title="Devolución">Dev</th>
                                                <th title="Efector">Efe</th>
                                                <th title="Informador">Inf</th>
                                                <th title="Factura">Fac</th>
                                                <th title="Acciones">Acciones</th>
                                            </thead>
                                            <tbody id="listaExamenes" class="list form-check-all">

                                            </tbody>
                                        </table>

                                    </div>

                                </div>
                                <div class="col-lg-12">
                                    <div class="hstack gap-2 justify-content-end">

                                        <button type="button" class="btn btn-soft-secondary" id="btnVolver">Volver</button>
                                        <a class="btn btn-success" id="actualizarPrestacion">Guardar</a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!--end col-->
            </div>
            <!--end row-->
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
const getExamenes = "{{ route('getExamenes') }}";
const checkExamen = "{{ route('checkExamen') }}";
const saveExamenes = "{{ route('saveExamenes') }}";
const getId = "{{ route('IdExamen') }}";
const deleteExamen = "{{ route('deleteExamen')}}";
const bloquearExamen ="{{ route('bloquearExamen') }}";
const getClientes = "{{ route('getClientes') }}";
const paqueteId = "{{ route('paqueteId') }}";
const itemExamen = "{{ route('itemExamen') }}";

//Extras
const TOKEN = "{{ csrf_token() }}";
const UBICACION = "{{ request()->query('location') }}";
const Id = "{{ $prestacione->Id }}";
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