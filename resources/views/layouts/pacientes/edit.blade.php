@extends('template')

@section('title', 'Actualizar datos de un paciente')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Actualizar datos de un paciente</h4>

    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="{{ route('pacientes.index') }}">Pacientes</a></li>
            <li class="breadcrumb-item active">Editar Paciente</li>
        </ol>
    </div>
</div>

<div class="container-fluid">
    <form id="form-update" action="{{ route('pacientes.update', ['paciente' => $paciente->Id]) }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')
    <div class="position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg profile-setting-img" style="height: 50px">
            <img src="{{ asset('images/banner-top.jpeg') }}" class="profile-wid-img" alt="">
        </div>
    </div>

    <div class="row">
        <div class="col-3">
            <div class="card-body p-4 border-pic">
                <div class="text-center">
                    <div class="profile-user position-relative d-inline-block mx-auto mb-4">
                        <div id="profile-image-preview" class="img-thumbnail user-profile-image" style="width: 188px; height: 200px; background-image: url('{{ asset("archivos/fotos/" . (empty($paciente->Foto) ? "foto-default.png" : $paciente->Foto)) }}'); background-size: cover; background-position: center;"></div>
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
                    <p class="text-muted mb-0">Sacar una fotografía al paciente</p>
                </div>
                <div class="text-center mt-4">
                    <input id="toggle-webcam-button" type="button" class="btn btn-primary" value="Activar Webcam" onClick="toggleWebcam()">
                </div>
            </div>
            <!--end card-->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-5">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0">Antecedentes</h5>
                        </div>
                        
                    </div>
                    <textarea class="form-control" id="antecedentes" rows="3" name="Antecedentes">{{ $paciente->Antecedentes }}</textarea>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-5">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0">Observaciones</h5>
                        </div>
                        
                    </div>
                    <textarea class="form-control" id="meassageInput" rows="3" name="Observaciones">{{ $paciente->Observaciones ?? '' }}</textarea>
                </div>
            </div>
            <!--end card-->
        </div>
        <!--end col-->
        <div class="col-9">
            <div class="card mt-xxl-n5">
                <div class="card-header">
                    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#datosPersonales" role="tab" aria-selected="true">
                                <i class="fas fa-home"></i>
                                Datos Personales
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#fichaLaboral" role="tab" aria-selected="false" tabindex="-1">
                                <i class="far fa-user"></i>
                                Ficha Laboral
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#prestaciones" role="tab" aria-selected="false" tabindex="-1">
                                <i class="ri-hospital-line"></i>
                                Prestaciones
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content">
                        <div class="tab-pane active show" id="datosPersonales" role="tabpanel">
                                <div class="row">
                                    <div class="col-2">
                                        <div class="mb-3">
                                            <select class="form-select" name="TipoDocumento" id="tipoDocumento">
                                                <option selected value="{{ $paciente->TipoDocumento }}">{{ $paciente->TipoDocumento }}</option>
                                                <option value="DNI">DNI</option>
                                                <option value="PAS">PAS</option>
                                                <option value="LC">LC</option>
                                                <option value="CF">CF</option>
                                            </select>
                                        </div>
                                    </div>        
                                    <div class="col-4">
                                        <div class="mb-3">
                                            <input type="text" class="form-control" value="{{ $paciente->Documento }}" id="documento" name="Documento">
                                        </div>
                                    </div><!--end col-->
                                    <div class="col-2">
                                        <div class="mb-3">
                                            <select class="form-select" id="tipoIdentificacion" name="TipoIdentificacion">
                                                <option selected value="{{ $paciente->TipoIdentificacion }}">{{ $paciente->TipoIdentificacion ?? "Elija una opción..." }}</option>
                                                <option value="CUIT">CUIT</option>
                                                <option value="CUIL">CUIL</option>
                                            </select>
                                        </div>
                                    </div>        
                                    <div class="col-4">
                                        <div class="mb-3">
                                            <input type="text" class="form-control" value="{{ $paciente->Identificacion }}" id="identificacion" name="Identificacion">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="nombre" class="form-label">Nombre <span class="required">(*)</span></label>
                                            <input type="text" class="form-control" placeholder="Nombre del paciente" id="nombre" name="Nombre" value="{{ $paciente->Nombre }}">
                                        </div>
                                        <input type="hidden" type="text" name="Id" value="{{ $paciente->Id }}">
                                    </div><!--end col-->
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="apellido" class="form-label">Apellido <span class="required">(*)</span></label>
                                            <input type="text" class="form-control" value="{{ $paciente->Apellido }}" id="apellido" name="Apellido">
                                        </div>
                                    </div><!--end col-->
                                    <!--end col-->
        
                                    <div class="col-4">
                                        <div class="mb-3">
                                            <label for="fecha" class="form-label">Fecha de nacimiento <span class="required">(*)</span></label>
                                            <input type="date" class="form-control" data-provider="flatpickr" id="fecha" name="FechaNacimiento" value="{{ $paciente->FechaNacimiento }}">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="mb-3">
                                            <label for="edad" class="form-label">Edad</label>
                                            <input type="text" class="form-control" id="edad" value="{{ $suEdad }}">
                                        </div>
                                    </div><!--end col-->

                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="telefono" class="form-label">Teléfono <i class="ri-questionnaire-line" title="{{ $telefono->CodigoArea ?? '' }}{{ $telefono->NumeroTelefono ?? '' }}"></i> <span class="required">(*)</span></label>
                                            <input type="text" class="form-control" placeholder="(xxx)xxx-xxxx" id="cleave-phone" name="NumeroTelefono" value="{{ $telefono->CodigoArea ?? '' }}{{ $telefono->NumeroTelefono ?? '' }}">
                                        </div>
                                    </div><!--end col-->
                                
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="correo" class="form-label">Email</label>
                                            <input type="text" class="form-control" placeholder="example@gmail.com" id="correo" name="EMail" value="{{ $paciente->EMail }}">
                                        </div>
                                    </div><!--end col-->
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="direccion" class="form-label">Dirección</label>
                                            <input type="text" class="form-control" placeholder="Calle N° B°" id="direccion" name="Direccion" value="{{ $paciente->Direccion }}">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="mb-3">
                                            <label for="provincia" class="form-label">Provincia  <span class="required">(*)</span></label>
                                            <select id="provincia" class="form-select" name="Provincia">
                                                <option selected value="{{ $paciente->Provincia }}">{{ $paciente->Provincia }}</option>
                                                @foreach ($provincias as $provincia)
                                                <option value="{{ $provincia->Nombre }}">{{ $provincia->Nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div><!--end col-->
                                    <div class="col-4">
                                        <div class="mb-3">
                                            <label for="localidad" class="form-label">Localidad  <span class="required">(*)</span></label>
                                            <select id="localidad" class="form-select" name="IdLocalidad">
                                                <option selected value="{{ $paciente->IdLocalidad }}">{{ $paciente->localidad->Nombre }}</option>
                                                <option>...</option>
                                            </select>
                                        </div>
                                    </div>   
                                    <div class="col-2">
                                        <div class="mb-3">
                                            <label for="codigoPostal" class="form-label">CP</label>
                                            <input type="text" class="form-control" id="codigoPostal" name="CP" value="{{ $paciente->localidad->CP }}">
                                        </div>
                                    </div><!--end col-->
        
                                    
                                    <!--end col-->
                                    <div class="col-lg-12">
                                        <div class="hstack gap-2 justify-content-end">
                                            
                                            <button type="button" id="btnVolver" class="btn btn-danger">Volver</button>
                                            <button type="submit" id="actualizarPaciente" class="btn btn-success">Actualizar</button>
                                            
                                        </div>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </form>
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane" id="fichaLaboral" role="tabpanel">
                            <div class="row">   
                                
                                <div class="col-12 full">
                                    <div class="mb-3 fullData form-inline d-flex align-items-center">
                                        <input type="text" class="form-control mr-2 fullInput" value="" @readonly(true)><i id="editFull" class="ri-edit-line editFull" title="Modo editable"></i>
                                    </div>
                                </div>

                                <div class="col-6 selectClientes">  
                                    <div class="mb-3 ">
                                        <label for="Clientes" class="form-label"> Cliente  </label>
                                        <select class="form-select" id="selectClientes">
                                            <option value="{{ $dataCliente->Id ?? '' }}">{{ $dataCliente->RazonSocial ?? '' }}</option>
                                        </select>
                                    </div>
                                </div><!--end col-->

                                <div class="col-6">
            
                                    <div class="mb-3">
                                        <label for="TipoPrestacion" class="form-label"> Tipo de Prestación  </label>
                                        <select class="form-select" id="TipoPrestacion">
                                            <option value="{{ $fichaLaboral->TipoPrestacion ?? ''}}" selected>{{ $fichaLaboral->TipoPrestacion ?? 'Elija una opción...'}}</option>
                                            <option value="INGRESO">Ingreso</option>
                                            <option value="PERIODICO">Periódico</option>
                                            <option value="OCUPACIONAL">Ocupacional</option>
                                            <option value="EGRESO">Egreso</option>
                                            <option value="OTRO">Otro</option>
                                            <option value="CARNET">Carnet</option>
                                            <option value="RECMED">Recmed</option>
                                            <option value="S/C_OCUPACIO">S/C Ocupacional</option>
                                            <option value="ART">ART</option> 
                                        </select>
                                    </div>
                                </div><!--end col-->

                               
                                <div class="col-6 selectArt">
                                    <label for="Art" class="form-label"> ART  </label>
                                    <div class="mb-3">
                                        <select class="form-select" id="selectArt">
                                            <option value="{{ $dataArt->Id ?? '' }}">{{ $dataArt->RazonSocial ?? '' }}</option>
                                        </select>
                                    </div>
                                </div>       

                                <div class="col-6 TareaRealizar">
                                    <div class="mb-3">
                                        <label for="TareaRealizar" class="form-label">Tareas a realizar</label>
                                        <input type="text" class="form-control" placeholder="Descripción de tarea a realizar" id="TareaRealizar" value="{{ $fichaLaboral->Tareas ?? '' }}">
                                    </div>
                                </div><!--end col-->
                                <div class="col-6 Jornada">
                                    <div class="mb-3">
                                        <label for="Jornada" class="form-label">Jornada</label>
                                        <div class="row" style="border: 1px solid #cccccc; background-color: #eee; padding: 0.40em">
                                            <div class="col-6">
                                                <label for="Tipo" class="control-label">Tipo</label>
                                                <select class="form-select" id="Tipo">
                                                        <option selected value="{{ $fichaLaboral->TipoJornada ?? ''}}">{{ $fichaLaboral->TipoJornada ?? 'Elija una opción...'}}</option>
                                                        <option value="NORMAL">Normal</option>
                                                        <option value="PROLONGADA">Prolongada</option>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label for="Horario" class="control-label">Horario</label>
                                                <select class="form-select" id="Horario">
                                                        <option selected value="{{ $fichaLaboral->Jornada ?? '' }}">{{ $fichaLaboral->Jornada ?? 'Elija una opción...' }}</option>
                                                        <option value="DIURNA">Diurna</option>
                                                        <option value="NOCTURNO">Nocturno</option>
                                                        <option value="ROTATIVO">Rotativo</option>
                                                        <option value="FULLTIME">Fulltime</option>
                                                </select>
                                            </div>
                                        </div>        
                                    </div>
                                </div><!--end col-->
                                <div class="col-6 Observaciones">
                                    <div class="mb-3">
                                        <label for="Observaciones" class="form-label">Observaciones:</label>
                                        <textarea class="form-control" placeholder="Observaciones de la jornada laboral" id="Observaciones">{{ $fichaLaboral->Observaciones ?? '' }}</textarea>
                                    </div>
                                </div><!--end col-->
                                <div class="col-6 UltimoPuesto">
                                    <div class="mb-3">
                                        <label for="UltimoPuesto" class="form-label">Última empresa y puesto</label>
                                        <input type="text" class="form-control" placeholder="Nombre del último puesto" id="UltimoPuesto" value="{{ $fichaLaboral->TareasEmpAnterior ?? '' }}">
                                    </div>
                                </div><!--end col-->
                                
                                <div class="col-6 PuestoActual">
                                    <div class="mb-3">
                                        <label for="PuestoActual" class="form-label">Puesto actual</label>
                                        <input type="text" class="form-control" placeholder="Nombre del puesto actual" id="PuestoActual" value="{{ $fichaLaboral->Puesto ?? '' }}">
                                    </div>
                                </div><!--end col-->
    
                               
                                <div class="col-6 SectorActual">
                                    <div class="mb-3">
                                        <label for="SectorActual" class="form-label">Sector Actual</label>
                                        <input type="text" class="form-control" placeholder="Nombre del sector actual" id="SectorActual" value="{{ $fichaLaboral->Sector ?? '' }}">
                                    </div>
                                </div>
                                
                                <div class="col-6">
                                    <div>
                                        <label for="pago" class="form-label">Forma de pago </label>
                                        <select class="form-select" id="PagoLaboral">
                                            <option value="{{ $fichaLaboral->Pago ?? ''}}" selected>
                                                {{ 
                                                    ($fichaLaboral && $fichaLaboral->Pago === 'B' ? 'Contado' :
                                                    ($fichaLaboral && $fichaLaboral->Pago === 'C' ? 'Cuenta Corriente' :
                                                    ($fichaLaboral && $fichaLaboral->Pago === 'P' ? 'Pago a cuenta' :
                                                    'Elija una opción...')))
                                                }}
                                            </option>
                                            <option value="B">Contado</option>
                                            <option value="C">Cuenta Corriente</option>
                                            <option value="P">Pago a cuenta</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-6 CCostos">
                                    <div class="mb-3">
                                        <label for="CCostos" class="form-label">C.Costos</label>
                                        <input type="text" class="form-control" placeholder="Código de centro de costos" id="CCostos" value="{{ $fichaLaboral->CCosto ?? '' }}">
                                    </div>
                                  
                                </div><!--end col-->
                                
                                    <div class="col-3 AntiguedadPuesto">
                                        <div class="mb-3">
                                            <label for="AntiguedadPuesto" class="form-label">Antigüedad en el puesto</label>
                                            <input type="int" class="form-control" placeholder="00" id="AntiguedadPuesto" value="{{ $fichaLaboral->AntigPuesto ?? '' }}">
                                        </div>
                                    </div><!--end col-->
                                    <div class="col-3 FechaIngreso">
                                        <div class="mb-3">
                                            <label for="FechaIngreso" class="form-label">Ingreso</label>
                                            <input type="date" class="form-control" id="FechaIngreso" value="{{ (isset($fichaLaboral->FechaIngreso) && $fichaLaboral->FechaIngreso !== '0000-00-00') ? \Carbon\Carbon::parse($fichaLaboral->FechaIngreso)->format('Y-m-d') : '' }}">
                                        </div>
                                    </div><!--end col-->
                                    <div class="col-3 FechaEgreso">
                                        <div class="mb-3">
                                            <label for="FechaEgreso" class="form-label">Egreso</label>
                                            <input type="date" class="form-control" id="FechaEgreso" value="{{ (isset($fichaLaboral->FechaIngreso) && $fichaLaboral->FechaEgreso !== '0000-00-00') ? \Carbon\Carbon::parse($fichaLaboral->FechaEgreso)->format('Y-m-d') : '' }}">
                                        </div>
                                    </div>   
                                    <div class="col-3 AntiguedadEmpresa">
                                        <div class="mb-3">
                                            <label for="AntiguedadEmpresa" class="form-label">Antigüedad en la empresa</label>
                                            <input type="int" class="form-control" placeholder="00" id="AntiguedadEmpresa" readonly="">
                                        </div>
                                    </div><!--end col-->
                               
                                <!--end col-->
                                <div class="col-lg-12">
                                    <div class="hstack gap-2 justify-content-end">
                                        
                                        <button type="button" id="btnVolverFicha" class="btn btn-soft-secondary">Volver</button>
                                        <button type="button" id="guardarFicha" class="btn btn-success">Guardar</button>
                                    </div>
                                </div>
                                <!--end col-->
                            </div>
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane" id="prestaciones" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div id="mensajeFichaLaboral"></div>

                                        <div class="card-body">
                                            <div class="listjs-table" id="customerList">
                                                <div class="row g-4 mb-3">
                                                    <div class="col-sm-7">
                                                        <div>
                                                            <button id="pacientePrestacion" type="button" class="btn btn-primary add-btn" data-bs-toggle="modal" data-bs-target="#altaPrestacionModal">
                                                                <i class="ri-add-line align-bottom me-1"></i> Nuevo
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <div class="" style="width: 100%;">
                                                            <div class="search-box ms-2">
                                                                    <input type="text" id="buscarPrestPaciente" class="form-control search" placeholder="Numero, Razon Social, ART, Para Empresa">
                                                                    <p id="search-instructions" style="font-size: 12px; color: #888;">Presione ENTER para buscar | ESC para reiniciar</p>
                                                            </div>
                                                        </div>
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
                                                    <table id="listaPacientes" class="display table table-bordered" style="width:100%">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <td></td>
                                                                <th class="sort">N°</th>
                                                                <th class="sort">Alta</th>
                                                                <th class="sort">Empresa</th>
                                                                <th class="sort">Para Empresa</th>
                                                                <th class="sort">Cuit</th>
                                                                <th class="sort">Art</th>
                                                                <th class="sort">Situación</th>
                                                                <th class="sort">F.Pago</th>
                                                                <th>Acciones</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="list form-check-all">
                                                            @forelse($pacientePrestacion as $prespaciente)
                                                            <tr id="filapresId" data-filapres="{{ $prespaciente->Id }}">
                                                                <td>
                                                                    <div class="prestacionComentario" data-id="{{ $prespaciente->Id }}" data-bs-toggle="modal" data-bs-target="#prestacionModal">
                                                                            <i class="ri-chat-3-line"></i>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span 
                                                                        {!! ($prespaciente->Ausente === 1 ? 'style="padding: 0.5em; background-color: red; color: white;" title="Ausente"' : 
                                                                            ($prespaciente->Incompleto === 1 ? 'style="padding: 0.5em; background-color: orange; color: black;" title="Incompleto"' : 
                                                                                ($prespaciente->Devol === 1 ? 'style="padding: 0.5em; background-color: blue; color: white;" title="Devol"' : 
                                                                                    ($prespaciente->Forma === 1 ? 'style="padding: 0.5em; background-color: #0cb7f2; color: black;" title="Forma"' : 
                                                                                        ($prespaciente->SinEsc === 1 ? 'style="padding: 0.5em; background-color: yellow; color: black;" title="Sin Esc"': ''))))) !!}
                                                                    >
                                                                        {{ $prespaciente->Id }}
                                                                    </span>
                                                                </td>
                                                                <td>{{ \Carbon\Carbon::parse($prespaciente->FechaAlta)->format('d/m/Y') }}</td>
                                                                <td title="{{ $prespaciente->RazonSocial }}">{{ Illuminate\Support\Str::limit($prespaciente->RazonSocial,20,'...') }}</td>
                                                                <td title="{{ $prespaciente->ParaEmpresa }}">{{ Illuminate\Support\Str::limit($prespaciente->ParaEmpresa, 15, '...') }}</td>
                                                                <td>{{ $prespaciente->Identificacion }}</td>
                                                                <td title="{{ $prespaciente->Art }}">{{ Illuminate\Support\Str::limit($prespaciente->Art, 15, '...')  ?? '...'}}</td>
                                                                <td><span id="estadoBadge" class="badge badge-soft-{{ ($prespaciente->Anulado == 0)?'success':'danger' }} text-uppercase">{{ ($prespaciente->Anulado == 0)? 'Habilitado':'Bloqueado' }}</span></td>
                                                                <td>{{ $prespaciente->Pago == 'B' ? 'Ctdo' : ($prespaciente->Pago == 'C' ? 'CCorriente' : ($prespaciente->Pago == 'P' ? 'PC' : 'CCorriente')) }}
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex gap-2">
                                                                        <a title="Editar" href="{{ route('prestaciones.edit', ['prestacione' => $prespaciente->Id, 'location' => 'paciente' ])}}">
                                                                            <button type="button" class="btn btn-sm btn-primary edit-item-btn"><i class="ri-edit-line"></i></button>
                                                                        </a>
                                                                        <div class="bloquear">
                                                                            <button type="button" id="blockPrestPaciente" data-idprest="{{ $prespaciente->Id }}" class="btn btn-sm btn-warning remove-item-btn" title="{{ ($prespaciente->Anulado == 1)? 'Bloqueado':'Bloquear' }}" {{ ($prespaciente->Anulado == 1)?'disabled':'' }}><i class="ri-forbid-2-line"></i></button>
                                                                        </div>
                                                                            <button type="button" id="downPrestPaciente" data-idprest="{{ $prespaciente->Id }}" class="btn btn-sm btn-danger remove-item-btn" ><i class="ri-delete-bin-2-line"></i></button>
                                                                            
                                                                     </div>
                                                                </td>
                                                            </tr>
                                                            @empty
                                                            <tr>
                                                                <td>No hay registros en la base de datos de prestaciones para el paciente</td>
                                                            </tr>
                                                            @endforelse
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

                        <!--end tab-pane-->
                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->

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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Crear prestación para el paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="row g-2">

                    <div class="col-2">
                        <label for="Fecha" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="Fecha" name="Fecha">
                    </div>
                    
                    <div class="col-3 financiador">
                        <label for="financiadorPres" class="form-label" title="A quién se factura"> Financiador </label>
                        <div class="updateFinanciador mb-3">
                            
                        </div>
                    </div>    
                       
                    <div class="col-2">
                        <div>
                            <label for="tipoPrestacionPres" class="form-label">Tipo de prestación <span class="required">(*)</span></label>
                            <select class="form-select" id="tipoPrestacionPres">
                                <option selected value="">Elija una opción...</option>
                                @foreach ($tipoPrestacion as $tipo)
                                <option value="{{ $tipo->Nombre }}">{{ $tipo->Nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Este campo "Mapa" solo debe ser visible si se selecciona como tipo y "Financiador" ART -->
                    <div class="col-3 selectMapaPres">
                        <div>
                            <label for="selectMapaPres" class="form-label">Mapa </label>
                            <select class="form-control" name="mapas" id="mapas">
                               
                            </select>
                        </div>
                    </div>
                    <div class="col-2">
                        <div>
                            <label for="pago" class="form-label">Forma de pago </label>
                            <select class="form-select" id="Pago">
                                <option value="" selected>Elija una opción...</option>
                                <option value="B">Contado</option>
                                <option value="C">Cuenta Corriente</option>
                                <option value="P">Pago a cuenta</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-2 SPago">
                        <div>
                            <label for="SPago" class="form-label">Medio de pago </label>
                            <select class="form-select" id="SPago">
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
                    </div>

                  
                    <div class="col-3">
                        <div class="mb-3">
                            <label for="Observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control" placeholder="Observaciones prestación" id="Observaciones"></textarea>
                        </div>
                      
                    </div><!--end col-->
                    <div class="col-2 Autorizado">
                        <div class="mb-3">
                            <label for="Autorizado" class="form-label">Autorizado por</label>
                            <select class="form-select" id="Autorizado">
                                <option selected="">Lucas Grunmann</option>
                                
                            </select>
                        </div>
                    </div><!--end col-->

                    <!-- En el caso de que se haya seleccionado cualquier otro medio de pagose visualizará y se deberá solicitar oblgatoriamete el número de factura. -->
                    <div class="col-2">
                        <div class="mb-3">
                            <label for="NumeroFacturaVta" class="form-label">Factura N°</label>
                            <input type="text" class="form-control" placeholder="0-0000-322651" id="NumeroFacturaVta">
                        </div>
                    </div><!--end col-->
                    <hr>
                    <h5>Observaciones</h5>
                    <small class="mb-4">Estos datos son exclusivamente de referencia para la creación de la prestación.</small>
                    <div class="col-3">
                        <div class="mb-3">
                            <label for="Observaciones" class="form-label">Observaciones de Bloqueo</label>
                            <textarea style="color:#464242;  height:100px;" class="form-control" placeholder="Observaciones prestación" id="Observaciones" @readonly(true)>Empresa: {{ $fichaLaboral->empresa->Motivo ?? 'Sin datos de bloqueo art' }} | ART: {{ $fichaLaboral->art->Motivo ?? 'Sin datos de bloqueo art' }}</textarea>
                        </div>
                      
                    </div><!--end col-->

                    <div class="col-3">
                        <div class="mb-3">
                            <label for="Observaciones" class="form-label">Observaciones Empresa</label>
                            <textarea style="color:#464242;  height:100px;" class="form-control" placeholder="Observaciones prestación" id="Observaciones" @readonly(true)>{{ $fichaLaboral->empresa->Observaciones ?? 'Sin observaciones en la BD'}}</textarea>
                        </div>
                      
                    </div><!--end col-->

                    <div class="col-3">
                        <div class="mb-3">
                            <label for="Observaciones" class="form-label">Observaciones Paciente</label>
                            <textarea style="color:#464242;  height:100px;" class="form-control" placeholder="Observaciones prestación" id="Observaciones" @readonly(true)>{{ $paciente->Observaciones ?? 'Sin observaciones en la BD'}}</textarea>
                        </div>
                      
                    </div><!--end col-->
                    <hr>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <a class="btn btn-success" id="guardarPrestacion" disabled>Guardar</a>
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
const blockPrestPaciente = "{{ route('blockPrestPaciente') }}";
const downPrestPaciente = "{{ route('downPrestPaciente') }}";
const getComentarioPres = "{{ route('getComentarioPres') }}";
const setComentarioPres = "{{ route('setComentarioPres') }}";
const searchPrestPacientes = "{{ route('searchPrestPacientes') }}";

const updateFinanciador = "{{ route('updateFinanciador') }}";
const getMapas = "{{ route('getMapas') }}";

//Solucionamos fix de url
let prespacienteId = "{{ $prespaciente->Id ?? ''}}";
let ubicacion = "paciente"
let urlEdicion = "{{ route('prestaciones.edit', ['prestacione' => 'prespaciente_id', 'location' => 'ubicacion']) }}".replace('prespaciente_id', prespacienteId).replace('ubicacion', ubicacion);


//Extras
const editUrl = "{{ route('pacientes.edit', ['paciente' => '__paciente__']) }}";
const ID = "{{ $paciente->Id }}";
let checkFichaLaboral = "{{ $fichaLaboral->Id ?? ''}}";
const getTipoPrestacion = "{{ route('getTipoPrestacion') }}";
const TOKEN = "{{ csrf_token() }}";


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
<script src="{{ asset('js/pacientes/fichaLaboral.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/pacientes/prestacionesPacientes.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/pacientes/prestacionesComentarios.js') }}?v={{ time() }}"></script>

<script src="{{ asset('js/webcam.min.js') }}"></script>
<script src="{{ asset('js/webcam-picture.js') }}?v= {{ time() }}"></script>
<script src="{{ asset('libs/cleave.js/cleave.min.js') }}"></script>
<script src="{{ asset('js/pages/form-masks.init.js') }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>

@endpush

@endsection