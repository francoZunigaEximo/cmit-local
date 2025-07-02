@extends('template')

@section('title', 'Paciente')

@section('content')

<div class="row mb-4">
    <div class="col-12 text-end">
        <button onclick="window.history.back()" class="btn btn-warning"><i class="ri-arrow-left-line"></i>&nbsp;Volver</button>
    </div>
</div>

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Paciente <span class="custom-badge original">Nro. {{ $paciente->Id }}</span></h4>

    <div class="page-title-right">
        <button type="button" class="btn botonGeneral" data-bs-toggle="modal" data-bs-target="#altaPrestacionModal">
            <i class="ri-add-line align-bottom me-1"></i> Nueva Prestación
        </button>
        <button type="button" class="btn botonGeneral" data-bs-toggle="modal" data-bs-target="#resultadosPaciente">
            <i class="ri-add-line align-bottom me-1"></i> Resultados
        </button>
    </div>
</div>

<input type="hidden" id="idPrestacion">

<div class="container-fluid">
    <form id="form-update" action="{{ route('pacientes.update', ['paciente' => $paciente->Id]) }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')
   <div class="row">
        <div class="col-12 text-center">

            <div class="row">
                <div class="col-4 box-information">
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">Apellido&nbsp;<span class="required">(*)</span></span>
                        <input type="text" class="form-control" id="Apellido" name="Apellido" aria-label="Apellido" aria-describedby="Apellido" value="{{ $paciente->Apellido }}">
                    </div>
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">Nombre&nbsp;<span class="required">(*)</span></span>
                        <input type="text" class="form-control" id="Nombre" name="Nombre" aria-label="Nombre" aria-describedby="Nombre" value="{{ $paciente->Nombre }}">
                    </div>
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">Documento&nbsp;<span class="required">(*)</span></span>
                        <select class="form-select" name="TipoDocumento" id="tipoDocumento">
                            <option selected value="{{ $paciente->TipoDocumento }}">{{ $paciente->TipoDocumento }}</option>
                            <option value="DNI">DNI</optiPsvQWXZeBDbMon>
                            <option value="PAS">PAS</option>
                            <option value="LC">LC</option>
                            <option value="CF">CF</option>
                        </select>
                        <input type="text" class="form-control" value="{{ $paciente->Documento }}" id="documento" name="Documento">
                    </div>
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">CUIT/CUIL</span>
                        <select class="form-select" id="tipoIdentificacion" name="TipoIdentificacion">
                            <option selected value="{{ $paciente->TipoIdentificacion }}">{{ $paciente->TipoIdentificacion ?? "Elija una opción..." }}</option>
                            <option value="CUIT">CUIT</option>
                            <option value="CUIL">CUIL</option>
                        </select>
                        <input type="text" class="form-control" value="{{ $paciente->Identificacion }}" id="identificacion" name="Identificacion">
                    </div>

                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">Fecha de nacimiento&nbsp;<span class="required">(*)</span></span>
                        <input type="date" class="form-control" id="fecha" name="FechaNacimiento" value="{{ $paciente->FechaNacimiento }}">
                        <input type="text" class="form-control" id="edad" value="Edad: {{ $suEdad }}" title="Edad">
                    </div>

                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Sexo</span>
                        <select class="form-select" id="Sexo" name="Sexo">
                            <option selected value="{{ $paciente->Sexo ?? ''}}">{{ !empty($paciente->Sexo) ? ($paciente->Sexo === 'M' ? 'Masculino' : 'Femenino') : 'Elija una opción...' }}</option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                            <option value="">Sin Sexo</option>
                        </select>
                    </div>
                </div>

                <div class="col-4 box-information">
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">Dirección</span>
                        <input type="text" class="form-control" id="Direccion" name="Direccion" aria-label="Direccion" aria-describedby="Direccion" value="{{ $paciente->Direccion }}">
                    </div>

                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">Telefono&nbsp;<i class="ri-questionnaire-line" title="{{ $telefono->CodigoArea ?? '' }}{{ $telefono->NumeroTelefono ?? '' }}"></i>&nbsp;<span class="required">(*)</span></span>
                        <input type="text" class="form-control" placeholder="(xxx)xxx-xxxx" id="cleave-phone" name="NumeroTelefono" value="{{ $telefono->CodigoArea ?? '' }}{{ $telefono->NumeroTelefono ?? '' }}">
                    </div>

                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">Email</span>
                        <input type="text" class="form-control" placeholder="example@gmail.com" id="correo" name="EMail" value="{{ $paciente->EMail }}">
                    </div>

                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">Provincia&nbsp;<span class="required">(*)</span></span>
                        <select id="provincia" class="form-select" name="Provincia">
                            <option selected value="{{ $paciente->Provincia }}">{{ $paciente->Provincia }}</option>
                            @foreach ($provincias as $provincia)
                            <option value="{{ $provincia->Nombre }}">{{ $provincia->Nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Localidad&nbsp;<span class="required">(*)</span></span>
                        <select id="localidad" class="form-select" name="IdLocalidad">
                            <option selected value="{{ $paciente->IdLocalidad }}">{{ $paciente->localidad->Nombre }}</option>
                            <option>...</option>
                        </select>
                        <input type="text" class="form-control" id="codigoPostal" name="CP" value="{{ $paciente->localidad->CP }}">
                    </div>

                </div>
                <div class="col-4 box-information mx-auto">
                    <div class="profile-user position-relative d-inline-block mx-auto mb-2">
                        <div id="profile-image-preview" class="img-thumbnail user-profile-image" style="background-size: cover; background-position: center; width: 200px; height: 140px; background-image: url('@fileUrl('lectura')/Fotos/{{ $paciente->Foto}}?v={{ time() }}') "></div>
                    <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                            <input id="profile-img-file-input" type="button" class="profile-img-file-input" value="Tomar foto" onClick="takeSnapshot()">
                            <input type="hidden" name="Foto" class="image-tag">
                            <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                                <span class="avatar-title rounded-circle bg-light text-body">
                                    <i class="ri-camera-fill"></i>
                                </span>
                            </label>
                        </div> 
                    </div>
                    <div class="text-center d-block">
                        <span class="toggle-webcam-button text-center iconGeneral" onClick="toggleWebcam()" title="Activar Webcam">
                            <i class="ri-webcam-line"></i>
                        </span>
                        <span></span>
                        <span id="deleteButton" class="text-center iconGeneral" data-id="{{ $paciente->Id }}" title="Eliminar Imagen">
                            <i class="ri-delete-bin-2-line"></i>
                        </span>
                    </div>
                </div>

                <div class="col-12 box-information mt-2">
                    <div class="input-group input-group-sm pt-1 pb-1">
                        <span class="input-group-text">Antecedentes</span>
                        <input type="text" class="form-control " id="antecedentes" name="Antecedentes" value="{{ $paciente->Antecedentes ?? '' }}">
                    </div>
                </div>

                <div class="col-12 box-information mt-2">
                    <div class="input-group input-group-sm pt-1 pb-1">
                        <span class="input-group-text">Observaciones</span>
                        <input type="text" class="form-control " id="Observaciones" name="Observaciones" value="{{ $paciente->Observaciones ?? '' }}">
                    </div>
                </div>

                <div class="col-12 box-information mt-2 text-center">
                    <button type="submit" class="btn btn-sm botonGeneral">Actualizar</button>
                </div>
            </div> 
        </div>
   </div>
    </form>

    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card">
                <div id="mensajeFichaLaboral"></div>

                <div class="card-body">
                    <div class="row listjs-table" id="customerList">

                        <div class="col-sm-6 small">
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
                        <div class="col-sm-6">
                            <div class="" style="width: 100%;">
                                <div class="search-box ms-2">
                                        <input type="text" id="buscarPrestPaciente" class="form-control search" placeholder="Numero, Razon Social, ART, Para Empresa">
                                        <p class="small" id="search-instructions" style="color: #888;">Presione ENTER para buscar | ESC para reiniciar</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive mt-3 mb-1 mx-auto">
                            <table id="listaPacientes" class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th class="sort">% Av</th>
                                        <th class="sort">Fecha</th>
                                        <th class="sort">Nro</th>
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
                                <tbody class="list form-check-all" id="grillaPacientes">
                                   
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
</div>


<!-- Default Modals Alerto de registro -->
<div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body text-center p-5">
                <div class="mt-4">
                    <h4 class="mb-3">¡El número de documento ya se encuentra registrado!</h4>
                    <p class="text-muted mb-4">Actualice sus datos haciendo clíc en el botón.</p>
                    <div class="hstack gap-2 justify-content-center">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Utilizar otro número de documento</button>
                        <a href="#" id="editLink" class="btn btn-primary">Actualizar datos</a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal de comentario prestación -->
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
                <button type="button" id="cancelar" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary guardarComentario" >Guardar Comentario</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="altaPrestacionModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header"></div>
            <div class="modal-body">
                 <div class="row fichaLaboralModal ">
                    <div class="row">
                        <div class="col-sm-4"></div>
                        <div class="col-sm-4 text-center"><h3 class="ff-secondary fw-bold mt-1 text-center">Ficha Laboral</h3></div>
                        <div class="col-sm-4 text-center">
                            <button type="button" class=" btn btn-sm botonGeneral verListadoExCta"><i class="ri-list-unordered"></i> Examenes a cuenta</button>
                            
                        </div>
                    </div>
                   
                    <div class="row d-flex justify-content-center">
                        <div class="col-9 box-information">
                            <div class="row">
                               
                                <div class="col-6">
                                    <div class="input-group input-group-sm mb-2">
                                        <span class="input-group-text">Paciente</span>
                                        <input type="text" class="form-control" id="Id" name="Id" value="{{ $paciente->Id }}" @readonly(true)>
                                        <input type="text" class="form-control" style="width: 50%" id="NombreCompleto" name="NombreCompleto" value="{{ $fichaLaboral->paciente->Apellido ?? $paciente->Apellido }} {{ $fichaLaboral->paciente->Nombre ?? $paciente->Nombre }}" @readonly(true)>
                                    </div>

                                    <small id="alertaExCta" class="fw-bolder rojo mb-2">Exa Cta Disponible</small>
    
                                </div>
                            
                                <div class="col-6">
                                    <div class="input-group input-group-sm mb-2 selectClientes2">
                                        <span class="input-group-text">Empresa</span>
                                        <select class="form-control-sm" id="selectClientes">
                                            <option value="{{ $fichaLaboral->empresa->Id ?? '' }}">{{ $fichaLaboral->empresa->RazonSocial ?? '' }}</option>
                                        </select>
                                    </div>
                                    <input type="hidden" id="IdFichaLaboral" value="{{ $fichaLaboral->Id ?? 0 }}">
                                    <div class="input-group input-group-sm  mb-2 selectArt2">
                                        <span class="input-group-text">ART</span>
                                        <select class="form-control" id="selectArt">
                                            <option value="{{ $fichaLaboral->art->Id ?? '' }}">{{ $fichaLaboral->art->RazonSocial ?? '' }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr class="mt-1 mb-1">
                            <div class="row text-center">
                                <div class="col-12">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="TipoPrestacion" id="ART" value="ART">
                                        <label class="form-check-label" for="ART">ART</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="TipoPrestacion" id="INGRESO" value="INGRESO" >
                                        <label class="form-check-label" for="ingreso">INGRESO</label>
                                    </div>
                            
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="TipoPrestacion" id="PERIODICO" value="PERIODICO">
                                        <label class="form-check-label" for="periodico">PERIODICO</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="TipoPrestacion" id="OCUPACIONAL" value="OCUPACIONAL">
                                        <label class="form-check-label" for="ocupacional">OCUPACIONAL</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="TipoPrestacion" id="EGRESO" value="EGRESO">
                                        <label class="form-check-label" for="egreso">EGRESO</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="TipoPrestacion" id="TipoPrestacion" id="MAS" value="MAS">
                                        <label class="form-check-label" for="mas">MAS</label>
                                    </div>
                                    <div class="form-check form-check-inline" id="divtipoPrestacionPresOtros" style="display: ">
                                        <select class="form-select" id="tipoPrestacionPresOtros">
                                            <option selected value="">Elija una opción...</option>
                                            @foreach ($tiposPrestacionOtros as $tipo)
                                            <option value="{{ $tipo->Nombre }}">{{ $tipo->Nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <hr class="mt-1 mb-1">

                            <div class="row text-center mt-2">
                                <div class="col-3"></div>
                                
                                <div class="col-6">
                                    <div class="input-group input-group-sm mb-2">
                                        <span class="input-group-text">Forma de Pago</span>
                                        <select class="form-control" id="PagoLaboral">
                                            <option selected value="">Elija una opción...</option>
                                            <option value="B">Contado</option>
                                            <option value="A">Cuenta Corriente</option>
                                            <option value="P">Exámen a Cuenta</option>
                                        </select>
                                    </div>

                                    @php
                                        $spagos = ["A" => "Efectivo", "B" => "Débito", "C" => "Crédito", "D" => "Cheque", "E" => "Otro", "F" => "Transferencia", "G" => "Sin Cargo"];    
                                    @endphp

                                    <div class="input-group input-group-sm mb-2 SPago">
                                        <span class="input-group-text">Medio de pago</span>
                                        <select class="form-select" id="SPago">
                                        </select>
                                    </div>

                                    <div class="input-group input-group-sm mb-2 Factura">
                                        <span class="input-group-text">Numero Factura</span>
                                        <select class="form-select" id="Tipo">
                                            <option value="{{ $fichaLaboral->Tipo ?? '' }}" selected>{{ $fichaLaboral->Tipo ?? 'Elija una opción...' }}</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="E">E</option>
                                            <option value="P">P</option>
                                            <option value="R">R</option>
                                            <option value="Z">Z</option>
                                        </select>
                                        <input type="text"  class="form-control" placeholder="nro sucursal" id="Sucursal" value="{{ $fichaLaboral->Sucursal ?? '' }}">
                                        <input type="text"  class="form-control" placeholder="nro de factura" id="NroFactura" value="{{ $fichaLaboral->NroFactura ?? '' }}">
                                    </div>

                                    <div class="input-group input-group-sm mb-2 NroFactProv">
                                        <span class="input-group-text">Nro Factura Provisoria</span>
                                        <input type="text" class="form-control" placeholder="Numero de factura provisoria" id="NroFactProv" value="{{ $fichaLaboral->NroFactProv ?? '' }}">
                                    </div>

                                    <div class="input-group input-group-sm mb-2 Autoriza">
                                        <span class="input-group-text">Autorizado por</span>
                                        <select class="form-select" id="Autorizado">
                                            <option value="" selected>Elija una opción...</option>
                                            <option value="Lucas Grunmann">Lucas Grunmann</option>
                                        </select>
                                    </div>

                                </div>
                                
                                <div class="col-3"></div>
                            </div>

                            <hr class="mt-1 mb-1">

                            <div class="row mt-2">
                                <div class="col-6 ">

                                    <div class="input-group input-group-sm mb-2 TareaRealizar">
                                        <span class="input-group-text">Tareas a realizar</span>
                                        <input type="text" class="form-control" id="TareaRealizar" name="TareaRealizar" value="{{ $fichaLaboral->Tareas ?? '' }}">
                                    </div>

                                    <div class="input-group input-group-sm mb-2 UltimoPuesto">
                                        <span class="input-group-text">Última empresa y puesto</span>
                                        <input type="text" class="form-control" id="UltimoPuesto" name="UltimoPuesto" value="{{ $fichaLaboral->TareasEmpAnterior ?? '' }}">
                                    </div>

                                    <div class="input-group input-group-sm mb-2 PuestoActual">
                                        <span class="input-group-text">Puesto actual</span>
                                        <input type="text" class="form-control" id="PuestoActual" name="PuestoActual" value="{{ $fichaLaboral->Puesto ?? '' }}">
                                    </div>

                                    <div class="input-group input-group-sm mb-2 SectorActual">
                                        <span class="input-group-text">Sector Actual</span>
                                        <input type="text" class="form-control" id="SectorActual" name="SectorActual" value="{{ $fichaLaboral->Sector ?? '' }}">
                                    </div>

                                    <div class="input-group input-group-sm mb-2 CCosto">
                                        <span class="input-group-text">C.Costos</span>
                                        <input type="text" class="form-control" id="CCostos" name="CCostos" value="{{ $fichaLaboral->CCosto ?? '' }}">
                                    </div>

                                    <div class="row">
                                        <div class="col-6">
                                            
                                            <div class="input-group input-group-sm mb-2 AntiguedadPuesto">
                                                <span class="input-group-text">Antig. Puesto</span>
                                                <input type="number" class="form-control" placeholder="0" id="AntiguedadPuesto" value="{{ $fichaLaboral->AntigPuesto ?? '' }}">
                                            </div>

                                            <div class="input-group input-group-sm mb-2 AntiguedadEmpresa">
                                                <span class="input-group-text">Antig. Empresa</span>
                                                <input type="number" class="form-control" placeholder="0" id="AntiguedadEmpresa" readonly="">
                                            </div>
                                        </div>

                                        <div class="col-6">

                                            <div class="input-group input-group-sm mb-2 FechaIngreso">
                                                <span class="input-group-text">Fecha Ingreso</span>
                                                <input type="date" class="form-control" id="FechaIngreso" value="{{ (isset($fichaLaboral->FechaIngreso) && $fichaLaboral->FechaIngreso !== '0000-00-00') ? \Carbon\Carbon::parse($fichaLaboral->FechaIngreso)->format('Y-m-d') : '' }}">
                                            </div>

                                            <div class="input-group input-group-sm mb-2 FechaEgreso">
                                                <span class="input-group-text">Fecha Egreso</span>
                                                <input type="date" class="form-control" id="FechaEgreso" value="{{ (isset($fichaLaboral->FechaIngreso) && $fichaLaboral->FechaEgreso !== '0000-00-00') ? \Carbon\Carbon::parse($fichaLaboral->FechaEgreso)->format('Y-m-d') : '' }}">
                                            </div>

                                        </div>
                                    </div>


                                </div>

                                <div class="col-6">
                                    <div class="input-group input-group-sm mb-2">
                                        <span class="input-group-text">Jornada</span>
                                        <select class="form-select" id="TipoJornada">
                                            <option selected value="{{ $fichaLaboral->TipoJornada ?? ''}}">{{ $fichaLaboral->TipoJornada ?? 'Elija una opción...'}}</option>
                                            <option value="NORMAL">Normal</option>
                                            <option value="PROLONGADA">Prolongada</option>
                                        </select>
                                        <select class="form-select" id="Horario">
                                            <option selected value="{{ $fichaLaboral->Jornada ?? '' }}">{{ $fichaLaboral->Jornada ?? 'Elija una opción...' }}</option>
                                            <option value="DIURNA">Diurna</option>
                                            <option value="NOCTURNO">Nocturno</option>
                                            <option value="ROTATIVO">Rotativo</option>
                                            <option value="FULLTIME">Fulltime</option>
                                    </select>
                                    </div>

                                    <div class="mt-3">
                                        <div class="input-group input-group-sm mb-2">
                                            <span class="input-group-text">Fecha Preocupacional</span>
                                            <input type="date" class="form-control" id="FechaPreocupacional" value="{{ (isset($fichaLaboral->FechaPreocupacional) && $fichaLaboral->FechaPreocupacional !== '0000-00-00') ? \Carbon\Carbon::parse($fichaLaboral->FechaPreocupacional)->format('Y-m-d') : '' }}">
                                        </div>

                                        <div class="input-group input-group-sm mb-2">
                                            <span class="input-group-text">Usuario 1</span>
                                            <input type="text" class="form-control"  id="FechaUltPeriod" value="{{ $fichaLaboral->FechaUltPeriod ?? '' }}">
                                        </div>

                                        <div class="input-group input-group-sm mb-2">
                                            <span class="input-group-text">Usuario 2</span>
                                            <input type="text" class="form-control" id="FechaExArt" value="{{ $fichaLaboral->FechaExArt ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <label for="Observaciones" class="form-label">Observaciones</label>
                                        <textarea class="form-control" style="height: 100px" placeholder="Observaciones de la jornada laboral" id="ObservacionesFicha">{{ $fichaLaboral->Observaciones ?? '' }}</textarea>
                                    </div>

                                </div>
                            </div>

                            <hr class="mt-1 mb-1">

                                <div class="row">
                                    <div class="col-12 text-center mt-2">
                                        <button type="button" class="btn botonGeneral eventDelete" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="button" id="guardarFicha" class="btn botonGeneral">Generar y continuar...</button>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    
                    <div class="row observacionesModal">
                        <h3 class="ff-secondary fw-bold mt-1 text-center">Alerta de prestación</h3>

                        <div class="row d-flex justify-content-center">
                            <div class="col-9 box-information">
                                <div class="row">
    
                                    <div class="col-12 ObBloqueoEmpresa">
                                        <h6 class="fs-16" style="color: red">Observaciones de Bloqueo</h6>
                                        <p class="text-muted mb-0"></p>
                                    </div>
                                    
                                    <div class="col-12 ObBloqueoArt">
                                        <hr class="mt-2 mb-2">
                                        <h6 class="fs-16" style="color: red">Observaciones de Bloqueo</h6>
                                        <p class="text-muted mb-0"></p>
                                    </div>
                                    
                                    <div class="col-12 ObEmpresa">
                                        <hr class="mt-2 mb-2">
                                        <h6 class="fs-16">Observaciones Empresa</h6>
                                        <p class="text-muted mb-0"></p>
                                    </div>

                                    <div class="col-12 ObArt">
                                        <hr class="mt-2 mb-2">
                                        <h6 class="fs-16">Observaciones Art</h6>
                                        <p class="text-muted mb-0"></p>
                                    </div>
                                    
                                    <div class="col-12 ObPaciente">
                                        <hr class="mt-2 mb-2">
                                        <h6 class="fs-16">Observaciones Paciente</h6>
                                        <p class="text-muted mb-0"></p>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 text-center mt-2">
                                            <hr class="mt-2 mb-2 d-block">
                                            <button type="button" class="btn botonGeneral eventDelete" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="button" class="btn botonGeneral seguirAl">Seguir</button>
                                        </div>
                                    </div>
    
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row nuevaPrestacion">
                        <h3 class="ff-secondary fw-bold mt-1 text-center">Alta Prestación</h3>

                        <div class="row d-flex justify-content-center">
                            <div class="col-9 box-information">
                                <div class="messagePrestacion"></div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="input-group input-group-sm mb-2 selectClientes2">
                                            <span class="input-group-text">Empresa</span>
                                            <input type="text" class="form-control" id="selectClientesPres" @readonly(true)>
                                        </div>

                                        <div class="input-group input-group-sm mb-2 selectArt2">
                                            <span class="input-group-text">ART</span>
                                            <input type="text" class="form-control" id="selectArtPres" @readonly(true)>
                                        </div>

                                        <input type="hidden" id="tipoPrestacionHidden"/>
                                        <div class="input-group input-group-sm mb-2 Tprestacion">
                                            <span class="input-group-text">Tipo Prestacion</span>
                                            <select class="form-select tipoPrestacionPres" id="tipoPrestacionPres">
                                                <option selected value="">Elija una opción...</option>
                                                @foreach ($tipoPrestacion as $tipo)
                                                <option value="{{ $tipo->Nombre }}">{{ $tipo->Nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="input-group input-group-sm mb-2 selectMapaPres">
                                            <span class="input-group-text">Mapas</span>
                                            <select class="form-control" name="mapas" id="mapas">
                                            </select>
                                        </div>

                                    </div>

                                    <div class="col-6">
                                        <div class="input-group input-group-sm mb-2">
                                            <span class="input-group-text">Paciente</span>
                                            <input type="text" class="form-control" id="Id" name="Id" value="{{ $paciente->Id }}" @readonly(true)>
                                            <input type="text" class="form-control" style="width: 50%" id="NombreCompleto" name="NombreCompleto" value="{{ $paciente->Apellido }} {{ $paciente->Nombre }}" @readonly(true)>
                                        </div>

                                        <div class="input-group input-group-sm mb-2">
                                            <span class="input-group-text">Fecha</span>
                                            <input type="date" class="form-control" id="Fecha" name="Fecha">
                                        </div>

                                        <hr class="mt-3 mb-3">

                                        <div class="input-group input-group-sm mb-2">
                                            <span class="input-group-text">Forma de Pago</span>
                                            <select class="form-select" id="ElPago">
                                                <option value="" selected>Elija una opción...</option>
                                                <option value="B">Contado</option>
                                                <option value="C">Cuenta Corriente</option>
                                                <option value="P">Examen a cuenta</option>
                                            </select>
                                        </div>

                                        <div class="input-group input-group-sm mb-2 SPago">
                                            <span class="input-group-text">Medio de pago</span>
                                            <select class="form-select" id="ElSPago">
                                                <option value="" selected>Elija una opción...</option>
                                                <option value="A">Efectivo</option>
                                                <option value="B">Débito</option>
                                                <option value="C">Crédito</option>
                                                <option value="D">Cheque</option>
                                                <option value="E">Otro</option>
                                                <option value="F">Transferencia</option>
                                                <option value="G">Sin Cargo</option>
                                            </select>
                                        </div>

                                        <div class="input-group input-group-sm mb-2 Factura">
                                            <span class="input-group-text">Numero Factura</span>
                                            <select class="form-select" id="ElTipo">
                                                <option value="" selected>Elija una opción...</option>
                                                <option value="A">A</option>
                                                <option value="B">B</option>
                                                <option value="E">E</option>
                                                <option value="P">P</option>
                                                <option value="R">R</option>
                                                <option value="Z">Z</option>
                                            </select>
                                            <input type="text"  class="form-control" placeholder="nro sucursal" id="ElSucursal" name="ElSucursal">
                                            <input type="text"  class="form-control" placeholder="nro de factura" id="ElNroFactura" name="ElNroFactura">
                                        </div>

                                        <div class="input-group input-group-sm mb-2 NroFactProv">
                                            <span class="input-group-text">Nro Factura Provisoria</span>
                                            <input type="text" class="form-control" placeholder="Numero de factura provisoria" id="ElNroFactProv" name="ElNroFactProv">
                                        </div>

                                        <div class="input-group input-group-sm mb-2 Autoriza">
                                            <span class="input-group-text">Autorizado por</span>
                                            <select class="form-select" id="ElAutorizado">
                                                <option value="" selected>Elija una opción...</option>
                                                <option value="Lucas Grunmann">Lucas Grunmann</option>
                                            </select>
                                        </div>
                                        <input type="hidden" id="facturacion_id">

                                    </div>
                            </div>
                            
                            <div class="row ObsPres">
                                <div class="col-12">
                                    <hr class="mt-3 mb-3">
                                    <div class="input-group input-group-sm mb-2">
                                        <span class="input-group-text">Observaciones</span>
                                        <input type="text" class="form-control" id="ObservacionesPres" name="ObservacionesPres">
                                    </div>
                                </div>
                            </div>

                            <hr class="mt-3 mb-3">

                            <div class="row">
                                <div class="col-12 text-center mt-2">
                                    <hr class="mt-2 mb-2 d-block">
                                    <button type="button" class="btn botonGeneral eventDelete" data-bs-dismiss="modal"><i class="ri-close-circle-line"></i>Cancelar</button>
                                    <button type="button" id="guardarPrestacion" class="btn botonGeneral"><i class="ri-save-line"></i>Guardar</button>
                                    <button type="button" id="siguienteExCta" class="btn botonGeneral"><i class="ri-arrow-right-line"></i>Siguiente</button>
                                </div>
                            </div>

                            <hr class="mt-3 mb-3 ultimasFacturadas">

                            <div class="row mt-2 ultimasFacturadas">
                                <div class="col-lg-12">
                                    <div class="card titulo-tabla">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h4 class="card-title mb-0">Ultimas prestaciones facturadas</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-resposive mb-1 col-sm-12">
                                                <table id="lstDisponibles" class="table table-bordered">
                                                    <thead class="table-light">
                                                    </thead>
                                                    <tbody class="list form-check-all" id="grillaFacturadas">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-2 ultimasFacturadas">
                                <div class="col-lg-12">
                                    <div class="card titulo-tabla">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h4 class="card-title mb-0">Examenes a cuenta disponibles <span class="small">(Total: <span id="totalCantidad"></span>)</span></h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive col-sm-12">
                                                <table id="lstDisponibles" class="table table-bordered dt-responsive nowrap table-striped align-middle dataTable no-footer dtr-inline collapsed">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Cantidad </th>
                                                            <th>Nombre de exámen</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="list form-check-all" id="disponiblesExamenes">
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

                <div class="row listadoExCta">
                    <div class="row ">
                        <div class="col-sm-4"></div>
                        <div class="col-sm-4 text-center"><h3 class="ff-secondary fw-bold mt-1 text-center">Listado de examenes a cuenta</h3></div>
                        <div class="col-sm-4 text-center"></div>
                    </div>
                   
                    <div class="row d-flex justify-content-center">
                        <div class="col-9  box-information">
                            <div class="row">
                                <div class="col-sm-12 text-end">
                                    <button type="button" class="btn btn-sm botonGeneral cerrarlstExCta">Cerrar</button>
                                </div>
                            </div>

                            <div class="row auto-mx mb-3">
                                <div class="table-responsive table-card mt-3 mb-1 mx-auto col-sm-8">
                                    <table id="lstExCta" class="display table table-bordered">
                                        <tbody class="list form-check-all" id="lstEx2">
                                            
                                        </tbody>
                                    </table>
                            
                                </div>
                            </div>
                        
                        </div>
                    </div>
                </div>

                <div class="row prestacionLimpia">
                    <h3 class="ff-secondary fw-bold mt-1 text-center tituloPrestacion">Alta Prestación</h3>
                    <div class="row d-flex justify-content-center">
                        <div class="col-9 box-information">
                            <div class="messagePrestacion"></div>
                            
                            <div class="row">
                                <div class="col-6">
                                    <div class="input-group input-group-sm mb-2 selectClientes2">
                                        <span class="input-group-text">Empresa</span>
                                        <input type="text" class="form-control" id="selectClientesPresN" @readonly(true)>
                                    </div>

                                    <div class="input-group input-group-sm mb-2 selectArt2">
                                        <span class="input-group-text">ART</span>
                                        <input type="text" class="form-control" id="selectArtPresN" @readonly(true)>
                                    </div>

                                    <input type="hidden" id="tipoPrestacionHidden"/>
                                    <div class="input-group input-group-sm mb-2 Tprestacion">
                                        <span class="input-group-text">Tipo Prestacion</span>
                                        <select class="form-select tipoPrestacionPresN" id="tipoPrestacionPresN">
                                            <option selected value="">Elija una opción...</option>
                                            @foreach ($tipoPrestacion as $tipo)
                                            <option value="{{ $tipo->Nombre }}">{{ $tipo->Nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="input-group input-group-sm mb-2 selectMapaPresN">
                                        <span class="input-group-text">Mapas</span>
                                        <select class="form-control" name="mapasN" id="mapasN">
                                        </select>
                                    </div>

                                </div>

                                <div class="col-6">
                                    <div class="input-group input-group-sm mb-2">
                                        <span class="input-group-text">Paciente</span>
                                        <input type="text" class="form-control" id="Id" name="Id" value="{{ $paciente->Id }}" @readonly(true)>
                                        <input type="text" class="form-control" style="width: 50%" id="NombreCompleto" name="NombreCompleto" value="{{ $paciente->Apellido }} {{ $paciente->Nombre }}" @readonly(true)>
                                    </div>

                                    <div class="input-group input-group-sm mb-2">
                                        <span class="input-group-text">Fecha</span>
                                        <input type="date" class="form-control" id="FechaN" name="FechaN">
                                    </div>

                                </div>
                            </div>
                        
                            <div class="row">
                                <div class="col-12">
                                    <hr class="mt-3 mb-3">
                                    <div class="input-group input-group-sm mb-2 d-none">
                                        <span class="input-group-text">Observaciones</span>
                                        <input type="text" class="form-control" id="ObservacionesPresN" name="ObservacionesPresN">
                                    </div>

                                    <div class="input-group input-group">
                                        <span class="input-group-text">Obs. prestacion</span>
                                        <input type="text" class="form-control" placeholder="Observaciones" id="ObsExamenesN" name="ObsExamenesN">
                                    </div>
                    
                                    <div class="input-group input-group mt-2">
                                        <span class="input-group-text">Obs estado</span>
                                        <input type="text" class="form-control" placeholder="Observaciones" id="ObsN" name="ObsB">
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 text-center mt-2">
                                    <hr class="mt-2 mb-2 d-block">
                                    <button type="button" id="finalizarWizzard" class="btn botonGeneral"><i class="ri-save-line"></i>Guardar</button>
                                    <button type="button" id="SalirWizzard" class="btn botonGeneral"><i class="ri-save-line"></i>Salir</button>
                                </div>
                            </div>

                            <hr class="mt-3 mb-3">

                            <div class="row text-start">
                                <div class="col-6 d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-sm botonGeneral imprimirReportes"><i class="bx bxs-file-pdf"></i>&nbsp;Imprimir</button>

                                    <button type="button" class="btn btn-sm botonGeneral deleteExamenes"><i class="ri-delete-bin-2-line"></i>&nbsp;Eliminar</button>

                                    <button type="button" class="btn btn-sm botonGeneral bloquearExamenes"><i class="ri-forbid-2-line"></i>&nbsp;Anular</button>

                                    <button type="button" class="btn btn-sm botonGeneral resulPaciente naranja"><i class="ri-add-line align-bottom me-1"></i>&nbsp;Resultados</button>
                                </div>

                                <div class="col-6 d-flex justify-content-end align-items-center">
                                    <span class="text-uppercase fw-bolder">Total de examenes: <span id="countExamenes">0</span></span>
                                </div>

                            </div>


                            <div class="row mt-2 paqueteExamen">
                        
                                <div class="col-5">
                                    <label for="paquetes" class="form-label">Paquetes</label> <!-- select 2 de paquetes de exámenes -->
                                    <div class="mb-3">
                                        <div class="cajaExamenes">
                                            <select class="form-select" name="paquetes" id="paquetes"></select>
                                            <i class="addPaquete ri-play-list-add-line naranja" title="Añadir paquete completo"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-5">
                                    <label for="examenes" class="form-label">Examen</label> <!-- select 2 de exámenes -->
                                    <div class="mb-3">
                                        <div class="cajaExamenes">
                                            <select class="form-select" name ="exam" id="exam"></select>
                                            <i class="addExamen ri-add-circle-line naranja" title="Añadir examén de la busqueda"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-2 d-flex justify-content-center align-items-center">
                                    <div class="mb-3">
                                        <div class="cajaExamenes">
                                            <button class="btn botonGeneral btnExamen">Examenes Masivo</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="table mt-3 mb-1 mx-auto col-sm-8">
                            
                                    <table class="table table-bordered" id="listado" >
                                        <thead class="table-light">
                                            <th><input type="checkbox" id="checkAllExa" name="Id_examenes"></th>
                                            <th class="sort">Exámen</th>
                                            <th>Acciones</th>
                                        </thead>
                                        <tbody id="listaExamenes" class="list form-check-all"></tbody>
                                    </table>
            
                                </div>
                            </div>

                            <hr class="mt-3 mb-3">

                            <div class="row paqueteExCta">
                                
                                <div class="d-flex justify-content-center align-items-center">
                                    <div class="col-sm-12 mt-5 mt-lg-0">
                                        <div class="row g-3 mb-0 justify-content-center">
                                            <div class="col-sm-6">
                                                <select name="examen" id="examen" class="form-control"></select>
                                            </div>
                                            <div class="col-auto">
                                                <button type="button" class="btn btn-sm botonGeneral buscarExamen"><i class="ri-search-line"></i> Buscar</button>
                                                <button type="button" class="btn btn-sm botonGeneral reiniciarExamen"><i class="ri-arrow-go-forward-fill"></i> Reiniciar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
    
                                <div class="row mx-auto">
                                    <div class="table-responsive table-card mt-3 mb-1 mx-auto col-sm-8">
                                        <table id="lstExCta" class="display table table-bordered">
                                            <tbody class="list form-check-all" id="lstEx">
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
    
                            </div>

                            <div class="row mb-2 paqueteExCta">
                                <div class="col-sm-12 text-center">
                                    <button type="button" class="btn btn-sm botonGeneral cargarExPrestacion"><i class="ri-save-line"></i>&nbsp;Añadir Ex a Cuenta</button>
                                </div>
                            </div>

                            <hr class="mt-3 mb-3">

                            <div class="row">
                                <div class="col-lg-12 mb-2">
                                    <p>ESCRIBA UN COMENTARIO O ALERTA:</p>
                                    <textarea name="Comentario" id="Comentario" class="form-control" rows="10"></textarea>
                                    <div class="text-center mt-2">
                                        <button type="button" class="btn botonGeneral confirmarComentarioPriv">Confirmar</button>
                                    </div>
                                </div>
                                
                                <hr class="mt-3 mb-3">

                                <div class="col-lg-12">
                                    <div class="card titulo-tabla">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h4 class="card-title mb-0">Observaciones privadas</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive table-card mb-1">
                                                <table id="lstPrivPrestaciones" class="table table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="sort">Fecha</th>
                                                            <th>Usuario</th>
                                                            <th>Rol</th>
                                                            <th>Comentario</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="list form-check-all" id="privadoPrestaciones"></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                        </div>
                    </div>
                </div>

                <div class="row editarComentario">
                    <h3 class="ff-secondary fw-bold mt-1 text-center">Modificar Comentario</h3>

                    <div class="row">
                        <div class="col-9 mx-auto box-information">
                            <button class="btn btn-sm botonGeneral volverPrestacionLimpia">
                                <i class="ri-arrow-left-line"></i> Volver a la prestación
                            </button>

                            <div class="row">
                                <div class="col-sm-4"></div>
                                <div class="col-sm-4">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <textarea class="form-control" name="ComentarioEditar" id="ComentarioEditar" cols="30" rows="10"></textarea>
                                                <input type="hidden" id="IdObservacion">
                                            </div>
                                        
                                            <div class="col-sm-12 text-center mt-2">
                                                <button class="btn btn-sm botonGeneral confirmarEdicion">Confirmar edición</button>
                                            </div>
                                    </div>
                                </div>
                                <div class="col-sm-4"></div>
                            </div>
                        </div>
                    </div>
                </div>
           

                <div class="row resultadosPaciente">
                    <h3 class="ff-secondary fw-bold mt-1 text-center">Resultados</h3>
                    <div class="col-9 mx-auto box-information">

                        <button class="btn btn-sm botonGeneral exportSimple" data-id="{{ $paciente->Id ?? '' }}"><i class="ri-file-excel-line"></i> Exportar Simple</button>
                        <button class="btn btn-sm botonGeneral exportDetallado" data-id="{{ $paciente->Id ?? '' }}"><i class="ri-file-excel-line"></i> Exportar Detallado</button>
                        <button class="btn btn-sm botonGeneral volverPrestacionLimpia">
                            <i class="ri-arrow-left-line"></i> Volver a la prestación
                        </button>
                        <div class="row auto-mx mb-3">
                            <div class="table-responsive mt-3 mb-1 mx-auto col-sm-12">
                                <table id="listadoResultadosPres" class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th class="sort">Prestacion</th>
                                            <th>Empresa</th>
                                            <th>Tipo</th>
                                            <th>Evaluación</th>
                                            <th>Calificación</th>
                                            <th>Observaciones</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all" id="lstResultadosPres">
                        
                                    </tbody>
                                </table>
                        
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row reportesPacientes">
                    <h3 class="ff-secondary fw-bold mt-1 text-center">Imprimir Opciones</h3>
                    <div class="col-9 mx-auto box-information">
                        <div class="text-end">
                            <button class="btn btn-sm botonGeneral volverPrestacionLimpia">
                                <i class="ri-arrow-left-line"></i> Volver a la prestación
                            </button>
                        </div>
                        
                        <div class="row mt-3">
                            <form>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="infInternos">
                                    <label class="form-check-label" for="infInternos">
                                        Informes internos
                                    </label>
                                </div>
            
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="pedProveedores">
                                    <label class="form-check-label" for="pedProveedores">
                                        Pedido a proveedores
                                    </label>
                                </div>
        
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="conPaciente">
                                    <label class="form-check-label" for="conPaciente">
                                        Control paciente
                                    </label>
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
            
                                <div class="mb-3 text-center">
                                    <button type="button" data-id="" class="btn btn-sm botonGeneral imprimirRepo"><i class="bx bxs-file-pdf"></i>Imprimir</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>
</div>

<div id="resultadosPaciente" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidde="true" style="display: none">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"> Resultados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body" class="text-center p-3">
                <button class="btn btn-sm botonGeneral exportSimple" data-id="{{ $paciente->Id ?? '' }}"><i class="ri-file-excel-line"></i> Exportar Simple</button>
                <button class="btn btn-sm botonGeneral exportDetallado" data-id="{{ $paciente->Id ?? '' }}"><i class="ri-file-excel-line"></i> Exportar Detallado</button>
                <div class="row auto-mx mb-3">
                    <div class="table mt-3 mb-1 mx-auto col-sm-7">
                        <table id="listadoResultadosPres2" class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th class="sort">Prestacion</th>
                                    <th>Empresa</th>
                                    <th>Tipo</th>
                                    <th>Evaluación</th>
                                    <th>Calificación</th>
                                    <th>Observaciones</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all" id="lstResultadosPres2">
                
                            </tbody>
                        </table>
                
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<div id="examenesCantidad" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidde="true" style="display: none">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"> Examenes por Cantidad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body" class="text-center p-3">
                <div class="search-box d-flex align-items-center gap-2">
                    <input type="text" class="form-control bg-light border-light" placeholder="Buscar examen" name="inputExCtd" id="inputExCtd">
                    <i class="ri-search-2-line search-icon"></i>
                    <button class="btn btn-sm botonGeneral" id="buscarExCtd">
                        <i class="ri-search-line"></i>
                        Buscar
                    </button>
                </div>

                <div class="row auto-mx mb-3">
                    <div class="table-responsive mt-3 mb-1 mx-auto col-sm-12">
                        <table id="listadoExamenesCtd" class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Nombre del examen</th>
                                    <th><input type="checkbox" name="examenCheck" id="examenCheck"></th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all" id="lstExamenesCtd">
                
                            </tbody>
                        </table>
                
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 text-center">
                        <button class="btn btn-sm botonGeneral" id="addExaCtd">
                            <i class=" ri-save-line"></i>
                            Añadir a la prestación
                        </button>
                    </div>
                </div>

            </div>            
        </div>
    </div>
</div>

<script>
//Rutas
const checkP = "{{ route('checkProvincia') }}";
const getLocalidades = "{{ route('getLocalidades') }}";
const getCodigoPostal = "{{ route('getCodigoPostal') }}";
const getParaEmpresas = "{{ route('getParaEmpresas') }}";
const verifyBlock = "{{ route('verifyBlock') }}";
const saveFichaAlta = "{{ route('saveFichaAlta') }}";
const getClientes = "{{ route('getClientes') }}";
const verificarAlta = "{{ route('verificarAlta') }}";
const savePrestacion = "{{ route('savePrestacion') }}";
const blockPrestacion = "{{ route('blockPrestacion') }}";
const downPrestaActiva = "{{ route('prestaciones.baja') }}";
const getComentarioPres = "{{ route('getComentarioPres') }}";
const setComentarioPres = "{{ route('setComentarioPres') }}";
const searchPrestPacientes = "{{ route('searchPrestPacientes') }}";
const lstExDisponibles = "{{ route('lstExDisponibles') }}";
const lstFacturadas = "{{ route('lstFacturadas')}}";
const lstExamenes = "{{ route('lstExamenes') }}";
const saldoNoDatatable = "{{ route('saldoNoDatatable') }}";
const lstClientes = "{{ route('lstClientes') }}";
const listPrecarga = "{{ route('listPrecarga') }}";
const listExCta = "{{ route('listExCta') }}";
const lstExClientes = "{{ route('lstExClientes') }}";
const searchExamen = "{{ route('searchExamen') }}";
const IDFICHA = "{{ $fichaLaboral->empresa->Id ?? ''}}";
const pagoInput = "{{ $fichaLaboral->Pago ?? ''}} ";
const saveItemExamenes = "{{ route('saveItemExamenes') }}";
const getExamenesEstandar = "{{ route('itemsprestaciones.lstExamenesEstandar') }}";
const getPaquetes = "{{ route('getPaquetes') }}";
const paqueteId = "{{ route('paqueteId') }}";
const deleteItemExamen = "{{ route('deleteItemExamen')}}";
const privateComment = "{{ route('comentariosPriv') }}";
const savePrivComent = "{{ route('savePrivComent') }}";
const bloquearItemExamen = "{{ route('bloquearItemExamen') }}";
const obsNuevaPrestacion = "{{ route('obsNuevaPrestacion') }}";
const getFormaPagoCli = "{{ route('clientes.formaPago') }}";

const getMapas = "{{ route('getMapas') }}";

const deletePicture = "{{ route('deletePicture') }}";
const GOINDEX = "{{ route('pacientes.index') }}";

let url = "{{ route('prestaciones.edit', ['prestacione' => '__prestacion__']) }}";

//Extras
const editUrl = "{{ route('pacientes.edit', ['paciente' => '__paciente__']) }}";
const ID = "{{ $paciente->Id }}";
let checkFichaLaboral = "{{ $fichaLaboral->Id ?? ''}}";
const getTipoPrestacion = "{{ route('getTipoPrestacion') }}";

const checkObs = "{{ route('checkObs') }}";
const excelPrestaciones = "{{ route('prestaciones.excel') }}";
const USER = "{{ Auth::user()->name }}";
const eliminarComentario = "{{ route('comentariosPriv.eliminar') }}";
const editarComentario = "{{ route('comentariosPriv.editar') }}";
const getComentario = "{{ route('comentariosPriv.data') }}";
const cacheDelete = "{{ route('prestaciones.cacheDelete') }}";

const loadlistadoAdjPres = "{{ route('prestaciones.listaAdjPres') }}";
const loadResultadosPres = "{{ route('prestaciones.resultados') }}";
const exResultado = "{{ route('prestaciones.exportarResultado') }}";
const impRepo = "{{ route('prestaciones.pdf') }}";
const sendExcel = "{{ route('prestaciones.excel') }}";
const contadorEx = "{{route('itemsprestaciones.contador')}}";
const getListaExCta = "{{ route('examenesCuenta.listado') }}";
const cargarExCta = "{{ route('examenesCuenta.cargar') }}";
</script>

@push('styles')
<link href="{{ asset('css/hacks.css') }}?v={{ time() }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/pacientes/validaciones.js') }}?v={{ time() }}"></script>

<script src="{{ asset('js/scripts.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/pacientes/edit.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/prestaciones/utils.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/utils.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/pacientes/fichaLaboral.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/pacientes/prestacionesPacientes.js') }}?v={{ time() }}"></script>


<script src="{{ asset('js/webcam.min.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/webcam-picture.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/actionsWebcam.js') }}?v={{ time() }}"></script>
<script src="{{ asset('libs/cleave.js/cleave.min.js') }}"></script>
<script src="{{ asset('js/pages/form-masks.init.js') }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
<script src="{{ asset('js/fancyTable.js') }}"></script>
@endpush

@endsection