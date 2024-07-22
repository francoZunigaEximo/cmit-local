@extends('template')

@section('title', 'Editar un cliente')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Cliente <span class="custom-badge original">Nro. {{ $cliente->Id }}</span> {!! ($cliente->Bloqueado === 1) ? '<span class="custom-badge rojo">Bloqueado</span>' : '' !!}</h4>

    <div class="page-title-right"></div>
</div>
                        
<div class="card-header d-flex justify-content-between">
    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" data-bs-toggle="tab" href="#datosBasicos" role="tab" aria-selected="true">
                <i class="fas fa-home"></i>
                Datos Básicos
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#opciones" role="tab" aria-selected="false" tabindex="-1">
                <i class="las la-cog"></i>
                Opciones
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#eMail" role="tab" aria-selected="false" tabindex="-1">
                <i class="far fa-envelope"></i>
                Emails
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#autorizados" role="tab" aria-selected="false" tabindex="-1">
                <i class="far fa-user"></i>
                Autorizados
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#obs" role="tab" aria-selected="false" tabindex="-1">
                <i class="las la-tasks"></i>
                Observaciones
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#paraEmpresa" role="tab" title="Ver otras empresas asociadas" aria-selected="false" tabindex="-1">
                <i class="las la-building"></i>
                Para Empresas
            </a>
        </li>

        <li class="nav-item" role="presentation">
            <a class="nav-link text-info" data-bs-toggle="tab" href="#examenCuenta" role="tab" title="Examenes a cuenta del cliente" aria-selected="false" tabindex="-1">
                <i class="ri-list-unordered"></i>
                Examenes a cuenta
            </a>
        </li>
    </ul>
    <div class="row">
        <div class="col-sm-12 text-end">
            @can('clientes_edit')
            <button type="button" class="btn botonGeneral" id="clonar"><i class="ri-file-copy-2-line"></i> Clonar</button>
            @endcan
        </div>
    </div>
    
</div>
<div class="card-body p-4">
    <div class="tab-content">
        <div id="messageClientes"></div>
        <div class="tab-pane active" id="datosBasicos" role="tabpanel">
            <form class="form-update" id="form-update" action="{{ route('clientes.update', ['cliente' => $cliente->Id]) }}"" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')
                <input type="hidden" name="Id" type="text" value="{{ $cliente->Id }}">
                <div class="row">
                    <div class="col-3 p-2 mb-2" style="background-color: #eeeeee">
                        <label for="cliente" class="form-label"> Cliente <span class="required">(*)</span></label>
                        <div class="mb-3">
                            <select class="form-select" name="TipoCliente" id="TipoCliente">
                                <option selected value="{{ $cliente->TipoCliente }}">{{ ($cliente->TipoCliente == 'E')? 'Empresa':'ART' }}</option>
                                @if($cliente->TipoCliente == 'E')
                                <option value="A">ART</option>
                                @else
                                <option value="E">Empresa</option>
                                @endif
                            </select>
                        </div>
                    </div>        
                    <div class="col-3 p-2 mb-2" style="background-color: #eeeeee">
                        <div class="mb-3">
                            <label for="Identificacion" class="form-label"> <br>  </label>
                            <input type="text" class="form-control " id="Identificacion" name="Identificacion" value="{{ $cliente->Identificacion }}">
                        </div>
                    </div><!--end col-->
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="ParaEmpresa" class="form-label">Para empresa <span class="required">(*)</span></label>
                            <input type="text" name="ParaEmpresa" class="form-control" value="{{ $cliente->ParaEmpresa }}" id="ParaEmpresa">
                        </div>
                    </div><!--end col-->
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="RazonSocial" class="form-label">Razón Social <span class="required">(*)</span></label>
                            <input type="text" class="form-control" value="{{ $cliente->RazonSocial }}" id="RazonSocial" name="RazonSocial">
                        </div>
                    </div><!--end col-->
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="NombreFantasia" class="form-label">Nombre de Fantasía</label>
                            <input type="text" class="form-control" value="{{ $cliente->NombreFantasia }}" id="NombreFantasia" name="NombreFantasia">
                        </div>
                    </div><!--end col-->
                    
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="CondicionIva" class="form-label">Condición</label>
                            <select class="form-select" name="CondicionIva" id="CondicionIva">
                                <option selected value="{{ $cliente->CondicionIva }}">{{ strtr(ucwords(strtolower($cliente->CondicionIva)), array_combine(['A', 'I'], ['a', 'i'])) }}</option>
                                <option value="RESPONSABLE INSCRIPTO">Responsable Inscripto</option>
                                <option value="EXENTO">Exento</option>
                                <option value="CONSUMIDOR FINAL">Consumidor Final</option>
                                <option value="NO RESPONSABLE">No Responsable</option>
                                <option value="MONOTRIBUTISTA">Monotributista</option>
                                <option value="DEL EXTERIOR">Del Exterior</option>
                            </select>
                        </div>
                    </div><!--end col-->

                    
                    <div class="col-6">
                        <div class="mb-3">
                            <label for="asignado" class="form-label">Asignado</label>
                            <select class="form-select" id="asignado">
                                <option selected value="">Elija una opción...</option>
                                <option value="1">...</option>
                                <option value="2">...</option>
                            </select>
                        </div>
                    </div><!--end col-->

                    <div class="col-6">
                        <div class="mb-3">
                            <label for="Telefono" class="form-label">Teléfono <i class="ri-questionnaire-line" title="{{ $cliente->Telefono}}"></i> <span class="required">(*)</span></label>
                            <input type="text" class="form-control" value="{{ $cliente->Telefono}}" name="Telefono" id="cleave-phone">
                        </div>
                    </div><!--end col-->

                    <div class="col-6">
                        <div class="mb-3">
                            <label for="Direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" value="{{ $cliente->Direccion }}" name="Direccion" id="Direccion">
                        </div>
                    </div><!--end col-->

                    <div class="col-6">
                        <div class="mb-3">
                            <label for="EMail" class="form-label">Email</label>
                            <input type="text" class="form-control" name="EMail" id="EMail" value="{{ $cliente->EMail }}">
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="mb-3">
                            <label for="ObsEMail" class="form-label">Observaciones</label>
                            <textarea name="ObsEMail" class="form-control" id="ObsEMail">{{ $cliente->ObsEMail }}</textarea>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="mb-3">
                            <label for="Provincia" class="form-label">Provincia</label>
                            <select class="form-select" name="Provincia" id="Provincia">
                                <option value="{{ $cliente->Provincia ?? '' }}" selected>{{ $cliente->Provincia ?? 'Elija una opción...'}}</option>
                                @foreach ($provincias as $provincia)
                                    <option value="{{ $provincia->Nombre }}">{{ $provincia->Nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div><!--end col-->
                    <div class="col-4">
                        <div class="mb-3">
                            <label for="IdLocalidad" class="form-label">Localidad</label>
                            <select class="form-select" id="IdLocalidad" name="IdLocalidad">
                                <option value="{{ $detailsLocalidad->Id }}">{{ $detailsLocalidad->Nombre }}</option>
                            </select>
                        </div>
                    </div>   
                    
                    <div class="col-2">
                        <div class="mb-3">
                            <label for="CP" class="form-label">CP</label>
                            <input type="text" class="form-control" placeholder="3300" id="CP" name="CP" value="{{ $detailsLocalidad->CP }}">
                        </div>
                    </div><!--end col-->

                    <div class="col-12" style="border: 1px solid #eeeeee; padding: 1em">
                        <div class="mb-3">
                            <label for="Telefonos" class="form-label">Teléfonos</label>
                            <div class="input-group mb-4">
                                <input name="prefijoExtra" id="prefijoExtra" type="text" class="form-control" placeholder="Prefijo">
                                <span class="input-group-addon">-</span>
                                <input name="numeroExtra" id="numeroExtra" type="text" class="form-control" placeholder="Numero">
                                <span class="input-group-addon">-</span>
                                <input name="obsExtra" id="obsExtra" type="text" class="form-control" placeholder="Observación">
                                <span class="input-group-addon">-</span>
                                <button type="button" class="btn botonGeneral" id="addNumero">Agregar Número adicional</button>
                            </div>
                            <table class="table table-nowrap" style="border: 1px solid #eeeeee">
                                <thead>
                                    <tr style="background-color: #eeeeee">
                                        <th scope="col">Prefijo</th>
                                        <th scope="col">Número</th>
                                        <th scope="col">Observación</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaTelefonos">

                                </tbody>
                            </table>
                            <div id="hiddens">

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 pt-4">
                        <div class="hstack gap-2 justify-content-end">
                            <a href="{{ route('clientes.index') }}" class="btn botonGeneral">Ir a Principal</a>
                            @can('clientes_edit')
                            <button type="submit" class="btn botonGeneral">Actualizar</button>
                            @endcan
                        </div>
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->
            </form>
        </div>
        
        <div class="tab-pane" id="paraEmpresa" role="tabpanel">                
                <div class="card mt-xxl-n5">
                <div class="card-body p-4">
                <div class="row g-2">
                    <div class="col-lg-12">
                        <div>
                            <label for="empresa" class="form-label" title="">Esta es la empresa para la cual se está editando esta información:</label>
                            <h5><strong>"{{ $cliente->ParaEmpresa }}"</strong></h5>
                        </div>
                    </div>
                    
                    <!--end col-->
                </div>
                </div>
                <!--end row-->
                </div>
            <div class="mt-4 mb-3 border-bottom pb-2">
                <h5 class="card-title">Estas son otras empresas asociadas al cliente "{{ $cliente->RazonSocial }}" "{{ $cliente->Identificacion }}":</h5>
            </div>
            <table class="table">
                <tbody>
                    @forelse($paraEmpresas as $empresa)
                    <tr>
                        <td class="align-middle">
                            <div class="avatar-sm">
                                <div class="avatar-title bg-light text-primary rounded-3 fs-18">
                                    <i class="ri-building-4-line"></i>
                                </div>
                            </div>
                        </td>
                        <td class="align-middle">
                            <h6>Empresa: {{ $empresa->ParaEmpresa}} </h6>
                        </td>
                        <td class="align-middle">
                            <h6>Razon Social: {{ $empresa->RazonSocial}} </h6>
                        </td>
                        <td class="align-middle">
                            <div>
                                <!-- Rounded Buttons -->
                                <a href="{{ route('clientes.edit', ['cliente' => $empresa->Id])}}" class="btn rounded-pill btn-primary waves-effect waves-light">Ver</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <p>No hay empresas asociadas</p>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="tab-pane" id="autorizados" role="tabpanel">
                <div class="row g-2">
                    <label for="oldpasswordInput" class="form-label">Autorizado <span class="required">(*)</span></label>
                    <div class="col-lg-6">
                        <div>
                            <input type="hidden" name="TipoEntidad" id="TipoEntidad" value="{{ $cliente->TipoCliente }}">
                            <input type="hidden" name="IdAutorizado" id="IdAutorizado" value="{{ $cliente->Id }}">
                            <input type="text" class="form-control" placeholder="Nombre" id="Nombre" name="Nombre" required title="El nombre del autorizado es obligatorio">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div>
                            <input type="text" class="form-control"  placeholder="Apellido" id="Apellido" name="Apellido" required title="El apellido del autorizado es obligatorio">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div>
                            <label for="DNI" class="form-label">DNI <span class="required">(*)</span></label>
                            <input type="number" class="form-control" name="DNI" placeholder="DNI" id="DNI" required title="El DNI del autorizado es obligatorio">
                            
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div>
                            <label for="Derecho" class="form-label ">Autorizado a <span class="required">(*)</span></label>
                            <select class="form-select" id="Derecho" name="Derecho" required title="Este campo es obligatorio">
                                <option  value="">Elija una opción...</option>
                                <option value="Información (solicitar resultados)">Información (solicitar resultados)</option>
                                <option value="Información + retiro de estudios">Información + retiro de estudios</option>
                                <option value="Retirar estudios">Retirar estudios</option>
                                <option value="Solicitar estudios">Solicitar estudios</option>
                                <option value="Solicitar estudios + Información">Solicitar estudios + Información</option>
                                <option value="Solicitar estudios + retirar estudios">Solicitar estudios + retirar estudios</option>
                                <option value="Total">Total</option>
                            </select>
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-lg-12">
                        <div class="text-end">
                            @can('clientes_edit')
                            <button type="button" id="btnAutorizado" class="btn botonGeneral">Autorizar</button>
                            @endcan
                        </div>
                        
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->
            <div class="mt-4 mb-3 border-bottom pb-2">
                <h5 class="card-title">Autorizados</h5>
            </div>
            <div class="body-autorizado">
                
            </div>
        </div>
        
        <div class="tab-pane" id="opciones" role="tabpanel">
            <form>
                <div id="newlink">
                    <div id="1">
                        <div class="row">
                            <div class="col-lg-3">
                                
                                    <div class="form-check form-check-success mb-6">
                                        <input class="form-check-input" type="checkbox" id="RF" {{ ($cliente->RF == 1)?'checked':'' }}>
                                        <label class="form-check-label" for="RF">
                                            Retira Físico
                                        </label>
                                    </div>
                                
                            </div>
                            <!--end col-->
                            <div class="col-lg-3">
                                <div class="form-check form-check-success mb-6">
                                    <input class="form-check-input" type="checkbox" id="mensajeriaItem" {{ (in_array($cliente->Entrega, [3, 2])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="mensajeriaItem">
                                        Mensajería
                                    </label>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <div class="form-check form-check-success mb-6">
                                        <input class="form-check-input" type="checkbox" id="correoItem" {{ ($cliente->Entrega == 4)?'checked':'' }}>
                                        <label class="form-check-label" for="correoItem">
                                            Correo
                                        </label>
                                    </div>
                                    <!--end row-->
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <div class="form-check form-check-success mb-6">
                                        <input class="form-check-input" type="checkbox" id="anexo" {{ ($cliente->Anexo == 1)?'checked':'' }}>
                                        <label class="form-check-label" for="anexo">
                                            Mail eAnexos
                                        </label>
                                    </div>
                                    <!--end row-->
                                </div>
                            </div>
                            <!--end col-->
                            
                            <div style="margin-block: 15px; "></div>

                            <div class="col-lg-3">
                                <div class="form-check form-check-success mb-6">
                                    <input class="form-check-input" type="checkbox" id="SinPF" {{ ($cliente->SinPF == 1)?'checked':'' }}>
                                    <label class="form-check-label" for="SinPF">
                                        Facturación sin paquetes
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-check form-check-success mb-6">
                                    <input class="form-check-input" type="checkbox" id="SinEval" {{ ($cliente->SinEval == 1)?'checked':'' }}>
                                    <label class="form-check-label" for="SinEval">
                                        Sin Evaluación
                                    </label>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="form-check form-check-success mb-6">
                                    <input class="form-check-input" type="checkbox" id="Bloqueado" {{ ($cliente->Bloqueado == 1)?'checked' : '' }}>
                                    <label class="form-check-label" for="Bloqueado">
                                        Bloquear
                                    </label>
                                </div>
                            </div>

                            <div class="col-lg-8 mt-3 p-3" style="background-color: #eeeeee;">
                                <label for="Motivo" class="form-label">Motivo Bloqueo <span class="required">(*)</span></label>
                                <textarea name="MotivoB" id="MotivoB" class="form-control" rows="10">{{ $cliente->Motivo ?? '' }}</textarea>
                            </div>

                            <!--end col-->
                            <div class="hstack gap-2 justify-content-end">
                                @can('clientes_edit')
                                <button type="button" class="btn botonGeneral" id="btnOpciones">Guardar</button>
                                @endcan
                            </div>
                        </div>
                        <!--end row-->
                    </div>
                </div>
                <div id="newForm" style="display: none;">

                </div>
                
                <!--end col-->
            </form>
        </div>
        
        <div class="tab-pane" id="eMail" role="tabpanel">
            <div class="mb-4 pb-2">
                <div class="mb-3">
                    <label for="EMailResultados" class="form-label">Masivos</label>
                    <input type="email" class="form-control" id="EMailResultados" value="{{ $cliente->EMailResultados }}">
                    <small style="color: #666666">Recuerde separar los correos con comas (,)</small>
                </div>
                <div class="mb-3">
                    <label for="EMailInformes" class="form-label">Informes</label>
                    <input type="email" class="form-control" id="EMailInformes" value="{{ $cliente->EMailInformes }}">
                    <small style="color: #666666">Recuerde separar los correos con comas (,)</small>
                </div>
                <div class="mb-3">
                    <label for="EMailFactura" class="form-label">Facturas</label>
                    <input type="email" class="form-control" id="EMailFactura" value="{{ $cliente->EMailFactura }}">
                    <small style="color: #666666">Recuerde separar los correos con comas (,)</small>
                </div>
                <div class="mb-3">
                    <label for="EMailAnexo" class="form-label">Solo anexos</label>
                    <input type="email" class="form-control" id="EMailAnexo" value="{{ $cliente->EMailAnexo }}">
                    <small style="color: #666666">Recuerde separar los correos con comas (,)</small>
                </div>
                <div class="form-check form-check-success mb-6">
                    <input class="form-check-input" type="checkbox" id="SEMail" {{ ($cliente->SEMail == 1)? 'checked':'' }}>
                    <label class="form-check-label" for="SEMail">
                        Sin Envío de emails
                    </label>
                </div>
                <div class="hstack gap-2 justify-content-end">
                    @can('clientes_edit')
                    <button type="button" class="btn botonGeneral" id="guardarEmail">Guardar</button>
                    @endcan
                </div>
            </div>
        </div>

        <div class="tab-pane" id="obs" role="tabpanel">
            <div class="mb-4 pb-2">
                <div class="mb-3">
                    <label for="Observacion" class="form-label">Recepción <i class="bx bx-show-alt" style="color: blue" title="Haz clic en el texto para ver todo"></i></label>
                    <textarea class="form-control auto-resize" placeholder="Observaciones recepción" id="Observaciones">{{ $cliente->Observaciones }}</textarea>

                </div>
                <div class="mb-3">
                    <label for="ObsEval" class="form-label">Evaluación <i class="bx bx-show-alt" style="color: blue" title="Haz clic en el texto para ver todo"></i></label>
                    <textarea class="form-control auto-resize" placeholder="Observaciones evaluación" id="ObsEval">{{ $cliente->ObsEval }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="ObsCE" class="form-label">Remito de estudios <i class="bx bx-show-alt" style="color: blue" title="Haz clic en el texto para ver todo"></i></label>
                    <textarea class="form-control auto-resize" placeholder="Remito de estudios del paciente" id="ObsCE">{{ $cliente->ObsCE }}</textarea> 
                    <span id="contadorObsCE"></span>
                </div>
                <div class="mb-3">
                    <label for="ObsCO" class="form-label">Cobranzas <i class="bx bx-show-alt" style="color: blue" title="Haz clic en el texto para ver todo"></i></label>
                    <textarea class="form-control auto-resize" placeholder="Observaciones cobranza" id="ObsCO">{{ $cliente->ObsCO }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="Motivo" class="form-label">Observaciones de Bloqueo <i class="bx bx-show-alt" style="color: blue" title="Haz clic en el texto para ver todo"></i></label>
                    <textarea class="form-control auto-resize" placeholder="Observaciones de bloqueo" id="Motivo">{{ $cliente->Motivo }}</textarea>
                </div>
            </div>
            <div class="hstack gap-2 justify-content-end">
                @can('clientes_edit')
                <button type="button" class="btn botonGeneral" id="btnObservaciones" >Guardar</button>
                @endcan
            </div>
        </div>

        <div class="tab-pane" id="examenCuenta" role="tabpanel">
            <div class="row">
                <div class="col-sm-12 text-end">
                    @can('examenCta_add')
                    <button data-id="{{ $cliente->Id }}" data-name="{{ $cliente->RazonSocial }}" class="btn btn-sm botonGeneral nuevoExamen"><i class="ri-add-line"></i> Alta Ex. a Cta</button>
                    @endcan
                </div>
            </div>

            <div class="row mx-auto">
                <div class="table-responsive table-card mt-3 mb-1 mx-auto col-sm-8">
                    <table id="lstFactCliente" class="display table table-bordered">
                        <tbody class="list form-check-all" id="lstFact">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
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
                    <h4 class="mb-3">¡El número de CUIT ya se encuentra registrado!</h4>
                    <p class="text-muted mb-4">Actualice sus datos haciendo clíc en el botón.</p>
                    <div class="hstack gap-2 justify-content-center">
                        <button type="button" class="btn botonGeneral" data-bs-dismiss="modal">Utilizar otro número de cuit</button>
                        <a href="#" id="editLink" class="btn botonGeneral">Actualizar datos</a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="editTelefonoModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Editar teléfono adicional</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body text-center p-5">
            
            
            </div>
            <div class="modal-footer">
                <button type="button" class="btn botonGeneral" data-bs-dismiss="modal">Cancelar edición</button>
                <button type="button" class="btn botonGeneral" id="saveCambiosEdit">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Default Modals -->

<script>

const ID = '{{ $cliente->Id }}';
const TOKEN = '{{ csrf_token() }}';
let contadorFilas = 1;
const GOINDEX = "{{ route('clientes.index') }}";
const GOCREATE = "{{ route('clientes.create') }}";
const RUTAEXAMEN = "{{ route('examenesCuenta.create') }}";

//Rutas
const deleteAutorizado = "{{ route('deleteAutorizado') }}";
const altaAutorizado = "{{ route('clientes.altaAutorizado') }}";
const getAutorizados = "{{ route('getAutorizados') }}";
const checkOpciones = "{{ route('checkOpciones') }}";
const checkEmail = "{{ route('checkEmail') }}";
const getLocalidad = "{{ route('getLocalidades') }}";
const getCodigoPostal = "{{ route('getCodigoPostal') }}";
const checkProvController = "{{ route('checkProvincia') }}";
const setObservaciones = "{{ route('clientes.setObservaciones') }}";
const getTelefonos = "{{ route('getTelefonos') }}";
const deleteTelefono = "{{ route('deleteTelefono') }}";
const saveTelefono = "{{ route('saveTelefono') }}";
const block = "{{ route('clientes.block') }}";
const getBloqueo = "{{ route('getBloqueo') }}";
const lstClientes = "{{ route('lstClientes') }}";
const listadoDni = "{{ route('listadoDni') }}";
const listadoEx = "{{ route('listadoEx') }}";

</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
@endpush

@push('scripts')
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/clientes/validaciones.js')}}?v={{ time() }}"></script>
<script src="{{ asset('js/clientes/edit.js')}}?=v{{ time() }}"></script>
<script src="{{ asset('js/clientes/utils.js')}}?=v{{ time() }}"></script>

<script src="{{ asset('js/scripts.js') }}?v={{ time() }}"></script>
<script src="{{ asset('libs/cleave.js/cleave.min.js') }}"></script>
<script src="{{ asset('js/pages/form-masks.init.js') }}"></script>
<script src="https://cdn.lordicon.com/bhenfmcm.js"></script>
@endpush

@endsection