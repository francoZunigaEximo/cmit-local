@extends('template')

@section('title', 'Paciente')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Paciente <span class="custom-badge original">Nro. {{ $paciente->Id }}</span></h4>

    <div class="page-title-right">
        <button type="button" class="btn botonGeneral" data-bs-toggle="modal" data-bs-target="#altaPrestacionModal">
            <i class="ri-add-line align-bottom me-1"></i> Nueva Prestación
        </button>
    </div>
</div>

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
                            <option value="DNI">DNI</option>
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

                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Fecha de nacimiento&nbsp;<span class="required">(*)</span></span>
                        <input type="date" class="form-control" id="fecha" name="FechaNacimiento" value="{{ $paciente->FechaNacimiento }}">
                        <input type="text" class="form-control" id="edad" value="Edad: {{ $suEdad }}" title="Edad">
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
    
                <div class="col-3 box-information mx-auto">
                    <div class="profile-user position-relative d-inline-block mx-auto mb-2">
                        <div id="profile-image-preview" class="img-thumbnail user-profile-image" style="width: 200px; height: 140px; background-image: url('{{ asset("archivos/fotos/" . (empty($paciente->Foto) ? "foto-default.png" : $paciente->Foto)) }}'); background-size: cover; background-position: center;"></div>
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
                    <button type="button" id="btnVolverPacientes" class="btn botonGeneral">Volver</button>
                    <button type="submit" id="actualizarPaciente" class="btn botonGeneral">Actualizar</button>
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
                            Imprimir&nbsp;<button type="button" id="excel" class="btn iconGeneral" title="Generar reporte en Excel">
                            <i class="ri-file-excel-line"></i>
                            </button>
                        </div>
                        <div class="col-sm-6">
                            <div class="" style="width: 100%;">
                                <div class="search-box ms-2">
                                        <input type="text" id="buscarPrestPaciente" class="form-control search" placeholder="Numero, Razon Social, ART, Para Empresa">
                                        <p class="small" id="search-instructions" style="color: #888;">Presione ENTER para buscar | ESC para reiniciar</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive table-card mt-3 mb-1 mx-auto">
                            <table id="listaPacientes" class="display table table-bordered" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th><input type="checkbox" id="checkAll" name="Id"></th>
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


<!-- Default Modals -->
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
            <div class="modal-header">
                <button type="button" class="btn-close eventDelete" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="row fichaLaboralModal">
                    <h3 class="ff-secondary fw-bold mt-1 text-center">Ficha Laboral</h3>
                    <div class="row">
                        <div class="col-9 mx-auto box-information">
                            <div class="row">
                                <div class="col-6">
                                    <div class="input-group input-group-sm mb-2">
                                        <span class="input-group-text">Paciente</span>
                                        <input type="text" class="form-control" id="Id" name="Id" value="{{ $paciente->Id }}" @readonly(true)>
                                        <input type="text" class="form-control" style="width: 50%" id="NombreCompleto" name="NombreCompleto" value="{{ $paciente->Apellido }} {{ $paciente->Nombre }}" @readonly(true)>
                                    </div>
    
                                    <div class="input-group input-group-sm mb-2 selectClientes2">
                                        <span class="input-group-text">Empresa</span>
                                        <select class="form-control-sm" id="selectClientes" s>
                                        </select>
                                    </div>
    
                                    
                                </div>
                            
                                <div class="col-6">
                                    <br /><br />
                                    <div class="input-group input-group-sm  mb-2 selectArt2">
                                        <span class="input-group-text">ART</span>
                                        <select class="form-control" id="selectArt">
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
                                        <input class="form-check-input" type="radio" name="TipoPrestacion" id="INGRESO" value="INGRESO">
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
                                        <input class="form-check-input" type="radio" name="TipoPrestacion" id="TipoPrestacion" id="OTRO" value="OTRO">
                                        <label class="form-check-label" for="otro">OTRO</label>
                                    </div>
                                    <div class="form-check form-check-inline" id="divtipoPrestacionPresOtros" style="display:none">
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

                            <div class="row mt-2">
                                <div class="col-6 ">

                                    <div class="input-group input-group-sm mb-2 TareaRealizar">
                                        <span class="input-group-text">Tareas a realizar</span>
                                        <input type="text" class="form-control" id="TareaRealizar" name="TareaRealizar">
                                    </div>

                                    <div class="input-group input-group-sm mb-2 UltimoPuesto">
                                        <span class="input-group-text">Última empresa y puesto</span>
                                        <input type="text" class="form-control" id="UltimoPuesto" name="UltimoPuesto">
                                    </div>

                                    <div class="input-group input-group-sm mb-2 PuestoActual">
                                        <span class="input-group-text">Puesto actual</span>
                                        <input type="text" class="form-control" id="PuestoActual" name="PuestoActual">
                                    </div>

                                    <div class="input-group input-group-sm mb-2 SectorActual">
                                        <span class="input-group-text">Sector Actual</span>
                                        <input type="text" class="form-control" id="SectorActual" name="SectorActual">
                                    </div>

                                    <div class="input-group input-group-sm mb-2 CCosto">
                                        <span class="input-group-text">C.Costos</span>
                                        <input type="text" class="form-control" id="CCostos" name="CCostos">
                                    </div>

                                    <div class="row">
                                        <div class="col-6">
                                            
                                            <div class="input-group input-group-sm mb-2 AntiguedadPuesto">
                                                <span class="input-group-text">Antig. Puesto</span>
                                                <input type="number" class="form-control" placeholder="0" id="AntiguedadPuesto">
                                            </div>

                                            <div class="input-group input-group-sm mb-2 AntiguedadEmpresa">
                                                <span class="input-group-text">Antig. Empresa</span>
                                                <input type="number" class="form-control" placeholder="0" id="AntiguedadEmpresa">
                                            </div>
                                        </div>

                                        <div class="col-6">

                                            <div class="input-group input-group-sm mb-2 FechaIngreso">
                                                <span class="input-group-text">Fecha Ingreso</span>
                                                <input type="date" class="form-control" id="FechaIngreso">
                                            </div>

                                            <div class="input-group input-group-sm mb-2 FechaEgreso">
                                                <span class="input-group-text">Fecha Egreso</span>
                                                <input type="date" class="form-control" id="FechaEgreso">
                                            </div>

                                        </div>
                                    </div>


                                </div>

                                <div class="col-6">
                                    <div class="input-group input-group-sm mb-2">
                                        <span class="input-group-text">Jornada</span>
                                        <select class="form-select" id="TipoJornada">
                                            <option value="NORMAL">Normal</option>
                                            <option value="PROLONGADA">Prolongada</option>
                                        </select>
                                        <select class="form-select" id="Horario">
                                            <option value="DIURNA">Diurna</option>
                                            <option value="NOCTURNO">Nocturno</option>
                                            <option value="ROTATIVO">Rotativo</option>
                                            <option value="FULLTIME">Fulltime</option>
                                    </select>
                                    </div>

                                    <div class="mt-3">
                                        <div class="input-group input-group-sm mb-2">
                                            <span class="input-group-text">Fecha Preocupacional</span>
                                            <input type="date" class="form-control" id="FechaPreocupacional">
                                        </div>

                                        <div class="input-group input-group-sm mb-2">
                                            <span class="input-group-text">Fecha Ult. Periodico Empresa</span>
                                            <input type="date" class="form-control"  id="FechaUltPeriod">
                                        </div>

                                        <div class="input-group input-group-sm mb-2">
                                            <span class="input-group-text">Fecha Ex ART</span>
                                            <input type="date" class="form-control" id="FechaExArt">
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <label for="Observaciones" class="form-label">Observaciones</label>
                                        <textarea class="form-control" style="height: 100px" placeholder="Observaciones de la jornada laboral" id="ObservacionesFicha"></textarea>
                                    </div>

                                </div>
                            </div>

                            <hr class="mt-1 mb-1">

                            <div class="row">
                                <div class="col-6">

                                    <div class="input-group input-group-sm mb-2">
                                        <span class="input-group-text">Forma de Pago</span>
                                        <select class="form-select" id="PagoLaboral">
                                            <option value="" selected>Elija una opción...</option>
                                            <option value="B">Contado</option>
                                            <option value="C">Cuenta Corriente</option>
                                            <option value="P">Exámen a Cuenta</option>
                                        </select>
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

                        <div class="row">
                            <div class="col-9 mx-auto box-information">
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

                        <div class="row">
                            <div class="col-9 mx-auto box-information">
                                <div class="messagePrestacion"></div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="input-group input-group-sm mb-2 Financiador">
                                            <span class="input-group-text">Financiador</span>
                                            <div class="updateFinanciador"></div>
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
                                            <select class="form-select" id="Pago">
                                                <option value="" selected>Elija una opción...</option>
                                                <option value="B">Contado</option>
                                                <option value="C">Cuenta Corriente</option>
                                                <option value="P">Examen a cuenta</option>
                                            </select>
                                        </div>

                                        <div class="input-group input-group-sm mb-2 SPago">
                                            <span class="input-group-text">Medio de pago</span>
                                            <select class="form-select" id="SPago">
                                            </select>
                                        </div>

                                        <div class="input-group input-group-sm mb-2 Factura">
                                            <span class="input-group-text">Numero Factura</span>
                                            <select class="form-select" id="Tipo">
                                                <option value="" selected>Elija una opción...</option>
                                                <option value="A">A</option>
                                                <option value="B">B</option>
                                                <option value="E">E</option>
                                                <option value="P">P</option>
                                                <option value="R">R</option>
                                                <option value="Z">Z</option>
                                            </select>
                                            <input type="text"  class="form-control" placeholder="nro sucursal" id="Sucursal">
                                            <input type="text"  class="form-control" placeholder="nro de factura" id="NroFactura">
                                        </div>

                                        <div class="input-group input-group-sm mb-2 Autoriza">
                                            <span class="input-group-text">Autorizado por</span>
                                            <select class="form-select" id="Autorizado">
                                                <option value="" selected>Elija una opción...</option>
                                                <option value="0">Lucas Grunmann</option>
                                            </select>
                                        </div>
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

                            <div class="row">
                                <div class="col-12 text-center mt-2">
                                    <hr class="mt-2 mb-2 d-block">
                                    <button type="button" class="btn botonGeneral eventDelete" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="button" id="guardarPrestacion" class="btn botonGeneral">Guardar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                   
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


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
const downPrestaActiva = "{{ route('downPrestaActiva') }}";
const getComentarioPres = "{{ route('getComentarioPres') }}";
const setComentarioPres = "{{ route('setComentarioPres') }}";
const searchPrestPacientes = "{{ route('searchPrestPacientes') }}";

const getMapas = "{{ route('getMapas') }}";

const deletePicture = "{{ route('deletePicture') }}";
const GOINDEX = "{{ route('pacientes.index') }}";

let url = "{{ route('prestaciones.edit', ['prestacione' => '__prestacion__']) }}";

//Extras
const editUrl = "{{ route('pacientes.edit', ['paciente' => '__paciente__']) }}";
const ID = "{{ $paciente->Id }}";
let checkFichaLaboral = "{{ $fichaLaboral->Id ?? ''}}";
const getTipoPrestacion = "{{ route('getTipoPrestacion') }}";
const TOKEN = "{{ csrf_token() }}";

const checkObs = "{{ route('checkObs') }}";
$('#excel').click(function(e) {
    e.preventDefault();

    ids     = "";
    filters = "";
    length  = $('input[name="Id"]:checked').length;

    $('input[name="Id"]:checked').each(function(index,element) {
        
        if($(this).val() == "on"){
            return;
        }

        if(index == (length - 1)){
            ids += $(this).val();    
        }
        else {
            ids += $(this).val() + ",";    
        }
    });

    if (!ids){
        toastr.info('No existen registros para exportar', 'Atención');
        return;
    }

    var exportExcel = "{{ route('excelPrestaciones', ['ids' =>  'idsContent', 'filters' => 'filtersContent']) }}";
    exportExcel     = exportExcel.replace('idsContent', ids);
    exportExcel     = exportExcel.replace('filtersContent', filters);
    exportExcel     = exportExcel.replaceAll('amp;', '');
    window.location = exportExcel;
});

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
<script src="{{ asset('js/pacientes/prestacionesComentarios.js') }}?v={{ time() }}"></script>

<script src="{{ asset('js/webcam.min.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/webcam-picture.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/actionsWebcam.js') }}?v={{ time() }}"></script>
<script src="{{ asset('libs/cleave.js/cleave.min.js') }}"></script>
<script src="{{ asset('js/pages/form-masks.init.js') }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>

@endpush

@endsection