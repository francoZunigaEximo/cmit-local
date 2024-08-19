@extends('template')

@section('title', 'Etapas')

@section('content')

@can('etapas_show')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Etapas </h4>

    <div class="page-title-right"></div>
</div>

<div class="card-header d-flex justify-content-between">
    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" data-bs-toggle="tab" href="#prestacion" role="tab" aria-selected="true">
                <i class="ri-window-line"></i>
                Prestaciones
            </a>
        </li>
        @can("etapas_efector")
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#efector" role="tab" aria-selected="true">
                <i class="ri-window-line"></i>
                Efector
            </a>
        </li>
        @endcan
        @can("etapas_informador")
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#informador" role="tab" aria-selected="false" tabindex="-1">
                <i class="ri-window-line"></i>
                Informador
            </a>
        </li>
        @endcan
        @can('etapas_eenviar')
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#eenviar" role="tab" aria-selected="false" tabindex="-1">
                <i class=" ri-window-line"></i>
                eEnviar
            </a>
        </li>
        @endcan
        
    </ul>
</div>

<div class="card-body p-4">
    <div class="tab-content">
        <div id="messageClientes"></div>

        <div class="tab-pane active" id="prestacion" role="tabpanel">

            <div class="row">
                <div class="col-sm-12">
                    <span class="small">
                        <span class="required">(*)</span> El campo es obligatorio
                        <span class="required">(**)</span> Debe definir la especialidad para listar
                    </span>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="listjs-table" id="customerList">
                                <div class="row g-4 mb-3">
            
                                    <form id="form-index">
                                        <div class="col-12 p-4 border border-1 border-color" style="border-color: #666666;">
                                            
                                            <div class="row">

                                                <div class="col-sm-2 mb-3">
                                                    <label for="fechaDesdePres" class="form-label font-weight-bold"><strong>Fecha desde: <span class="required">(*)</span></strong></label>
                                                    <input type="date" class="form-control" id="fechaDesdePres" name="fechaDesdePres" max="9999-12-31">
                                                </div>
            
                                                <div class="col-sm-2 mb-3">
                                                    <label for="fechaHastaPres" class="form-label font-weight-bold"><strong>Fecha hasta: <span class="required">(*)</span></strong></label>
                                                    <input type="date" class="form-control" id="fechaHastaPres" name="fechaHastaPres" max="9999-12-31">
                                                </div>

                                                <div class="col-sm-2 mb-3">
                                                    <label for="especialidadPres" class="form-label font-weight-bold"><strong>Especialidad: <span class="required"></span></strong></label>
                                                    <select class="form-control especialidadPres" name="especialidadPres" id="especialidadPres"></select>
                                                </div>

                                                <div class="col-sm-2 mb-3">
                                                    <label for="estadoPres" class="form-label font-weight-bold"><strong>Estado prestación: </strong></label>
                                                    <select name="estadoPres" id="estadoPres" class="form-control">
                                                        <option value="" selected>Elija una opción...</option>
                                                        <option value="abierto">Abierto</option>
                                                        <option value="cerrado">Cerrado</option>
                                                        <option value="finalizado">Finalizado</option>
                                                        <option value="entregado">Entregado</option>
                                                        <option value="eenviado">eEnviado</option>
                                                    </select>
                                                </div>

                                                <div class="col-sm-2 mb-3">
                                                    <label for="efectorPres" class="form-label font-weight-bold"><strong>Efector: </strong></label>
                                                    <select name="efectorPres" id="efectorPres" class="form-control">
                                                        <option value="" selected>Elija una opción...</option>
                                                        <option value="pendientes">Pendientes</option>
                                                        <option value="cerrados">Cerrados</option>
                                                    </select>
                                                </div>

                                                <div class="col-sm-2 mb-3">
                                                    <label for="informadorPres" class="form-label font-weight-bold"><strong>Informador: </strong></label>
                                                    <select name="informadorPres" id="informadorPres" class="form-control">
                                                        <option value="" selected>Elija una opción...</option>
                                                        <option value="pendientes">Pendientes</option>
                                                        <option value="borrador">Borrador</option>
                                                        <option value="pendienteYborrador">Pendiente y borrador</option>
                                                        <option value="todos">Todos</option>
                                                    </select>
                                                </div>

                                                <div class="col-sm-2 mb-3">
                                                    <label for="profEfePres" class="form-label font-weight-bold"><strong>Prof. Efector: <span class="required">(**)</span></strong></label>
                                                    <select name="profEfePres" id="profEfePres" class="form-control">
                                                        <option value="" selected>Elija una opción...</option>
                                                    </select>
                                                </div>

                                                <div class="col-sm-2 mb-3">
                                                    <label for="profInfPres" class="form-label font-weight-bold"><strong>Prof. Informador: <span class="required">(**)</span></strong></label>
                                                    <select name="profInfPres" id="profInfPres" class="form-control">
                                                        <option value="" selected>Elija una opción...</option>
                                                    </select>
                                                </div>

                                                <div class="col-sm-2 mb-3">
                                                    <label for="tipoPres" class="form-label font-weight-bold"><strong>Tipo prov: </strong></label>
                                                    <select name="tipoPres" id="tipoPres" class="form-control">
                                                        <option value="" selected>Elija una opción...</option>
                                                        <option value="interno">Interno</option>
                                                        <option value="externo">Externo</option>
                                                        <option value="todos">Todos</option>
                                                    </select>
                                                </div>

                                                <div class="col-sm-2 mb-3">
                                                    <label for="adjuntoPres" class="form-label font-weight-bold"><strong>Adjunto: </strong></label>
                                                    <select name="adjuntoPres" id="adjuntoPres" class="form-control">
                                                        <option value="" selected>Elija una opción...</option>
                                                        <option value="fisico">Físico</option>
                                                        <option value="digital">Digital</option>
                                                    </select>
                                                </div>

                                                <div class="col-sm-2 mb-3">
                                                    <label for="examenPres" class="form-label font-weight-bold"><strong>Examen:</strong></label>
                                                    <select class="form-control" name="examenPres" id="examenPres"></select>
                                                </div>

                                                <div class="col-sm-2 mb-3 d-flex align-items-center justify-content-center">
                                                    <input type="checkbox" class="form-check-input" name="pendientePres" name="pendientePres" id="pendientePres">&nbsp;
                                                    <label for="pendientePres" class="form-check-label font-weight-bold"><strong>Con pendiente</strong></label>
                                                </div>

                                                <div class="col-sm-2 mb-3 d-flex align-items-center justify-content-center">
                                                    <input type="checkbox" class="form-check-input" name="vencidoPres" id="vencidoPres">&nbsp;
                                                    <label for="vencidoPres" class="form-check-label font-weight-bold"><strong>Exámen vencido</strong></label>
                                                </div>
                                            </div>
                                            <div class="row mt-2 mb-2 fondo-base">
                                                <div class="col-sm-3 p-2 text-start">
                                                    <button type="button" class="btn btn-sm botonGeneral Exportar"><i class="ri-file-excel-line"></i>&nbsp;Exportar</button>
                                                </div>
                                                <div class="col-sm-9 p-2 text-end">
                                                    Filtros:
                                                    <button type="button" title="Filtros: efector pendiente - tipo interno - no ausentes" class="btn btn-sm botonGeneral hoyDias"><i class="ri-calendar-2-fill"></i>&nbsp;Hoy</button>
                                                    <button type="button" title="Filtros: tipo todos - no ausentes - con pendientes" class="btn btn-sm botonGeneral treintaDias"><i class="ri-calendar-2-fill"></i>&nbsp;30 días</button>
                                                    <button type="button" title="Filtros: tipo todos - no ausentes - con pendientes" class="btn btn-sm botonGeneral sesentaDias"><i class="ri-calendar-2-fill"></i>&nbsp;60 días</button>
                                                    <button type="button" title="Filtros: no ausentes - con pendientes" class="btn btn-sm botonGeneral noventaDias"><i class="ri-calendar-2-fill"></i>&nbsp;90 días</button>
                                                    <button type="button" title="Filtros: tipo todos - ausentes todos - con pendientes" class="btn btn-sm botonGeneral totalDias"><i class="ri-calendar-2-fill"></i>&nbsp;Total</button>
                                                    <button type="button" title="Filtros: tipo todos - con ausentes - con pendientes" class="btn btn-sm botonGeneral ausenteDias"><i class="ri-calendar-2-fill"></i>&nbspAusente</button>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-12" style="text-align: right;">
                                                    <button type="button" id="resetPres" class="btn botonGeneral"><i class="ri-refresh-line"></i>&nbsp;Reiniciar</button>
                                                    <button type="button" id="buscarPres" class="btn botonGeneral"><i class="ri-zoom-in-line"></i>&nbsp;Buscar</button>
                                                </div>
                                            </div>
                                        
                                        </div>
                                    </form>
            
                                </div>
            
                                <div class="table mt-3 mb-1 mx-auto">
                                    <table id="listaOrdenesPrestaciones" class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="sort">Especialidad</th>
                                                <th class="sort">Fecha</th>
                                                <th class="sort">Prestación</th>
                                                <th class="sort">Empresa</th>
                                                <th class="sort">Paciente</th>
                                                <th>DNI</th>
                                                <th class="sort">E_Presta</th>
                                                <th>eEnv</th>
                                                <th class="sort">Examen</th>
                                                <th class="sort">Efector</th>
                                                <th class="sort">E_EFE</th>
                                                <th class="sort">ADJ</th>
                                                <th class="sort">Informador</th>
                                                <th class="sort">E_INF</th>
                                                <th>Fecha Vto</th>
                                                <th class="sort">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all">
            
                                        </tbody>
                                    </table>
                                </div>           
                            </div>
                        </div>
                    </div>
                </div>
            
            </div>


        </div>

        @can("etapas_efector")
        <div class="tab-pane" id="efector" role="tabpanel">
            
            <div class="card-header d-flex justify-content-between">
                <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" href="#asignarEfector" role="tab" aria-selected="true">
                            <i class="ri-calendar-check-line"></i>
                            Asignar efector
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#asignadosEfector" role="tab" aria-selected="false" tabindex="-1">
                            <i class="ri-calendar-todo-line"></i>
                            Efectores asignados
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#adjuntarEfector" role="tab" aria-selected="false" tabindex="-1">
                            <i class="ri-file-upload-line"></i>
                            Adjuntar a examenes efectores
                        </a>
                    </li>
                    
                </ul>
            </div>

            <div class="tab-content">
                <div class="tab-pane active" id="asignarEfector" role="tabpanel">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="listjs-table" id="customerList">
                                        <div class="row g-4 mb-3">
                    
                                            <form id="form-index">
                                                <div class="col-12 p-4 border border-1 border-color" style="border-color: #666666;">
                                                    
                                                    <div class="row">
        
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="fechaDesde" class="form-label font-weight-bold"><strong>Fecha desde: <span class="required">(*)</span></strong></label>
                                                            <input type="date" class="form-control" id="fechaDesde" name="fechaDesde" max="9999-12-31">
                                                        </div>
                    
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="fechaDesde" class="form-label font-weight-bold"><strong>Fecha hasta: <span class="required">(*)</span></strong></label>
                                                            <input type="date" class="form-control" id="fechaHasta" name="fechaHasta" max="9999-12-31">
                                                        </div>
        
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="especialidad" class="form-label font-weight-bold"><strong>Especialidad: <span class="required">(*)</span></strong></label>
                                                            <select class="form-control especialidad" name="especialidad" id="especialidad"></select>
                                                        </div>

                                                        <div class="col-sm-2 mb-3">
                                                            <label for="examen" class="form-label font-weight-bold"><strong>Examen:</strong></label>
                                                            <select class="form-control" name="examen" id="examen"></select>
                                                        </div>
                                                        
                                                        <div class="col-sm-2 mb-3 d-flex align-items-end">
                                                            <button title="Filtros avanzados" class="btn btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosAvanzados" aria-expanded="false" aria-controls="filtrosAvanzados">
                                                                <i class="ri-filter-2-line"></i>
                                                            </button>
                                                        </div>
        
                                                        <div class="collapse" id="filtrosAvanzados">
                                                            <div class="card mb-3">
                                                              <div class="card-body" style="background: #eaeef3">
                                                                <div class="row">

                                                                    <div class="col-sm-2 mb-3">
                                                                        <label for="prestacion" class="form-label font-weight-bold"><strong>Nro prestación:</strong></label>
                                                                        <input type="number" class="form-control" id="prestacion" name="prestacion">
                                                                    </div>
                        
                                                                    <div class="col-sm-2 mb-3">
                                                                        <label for="empresa" class="form-label font-weight-bold"><strong>Empresa:</strong></label>
                                                                        <select class="form-control" name="empresa" id="empresa"></select>
                                                                    </div>
                    
                                                                    <div class="col-sm-2 mb-3">
                                                                        <label for="paciente" class="form-label font-weight-bold"><strong>Paciente / DNI:</strong></label>
                                                                        <select class="form-control" name="paciente" id="paciente"></select>
                                                                    </div>
                                                                </div>
                                                              </div>
                                                            </div>
                                                        </div>


                                                    </div>
        
                                                    <div class="row">
                                                        <div class="col-sm-9 mb-3 p-2 rounded" style="background-color: #eee">
                                                            <div class="col-sm-6 d-flex align-items-center justify-content-center">
                                                                <span class="font-weight-bold"><strong>Profesional:</strong></span>&nbsp;
                                                                <select class="form-control" name="efectores" id="efectores"></select>&nbsp;
                                                                <button type="button" id="asigEfector" class="btn btn-sm botonGeneral"><i class="ri-arrow-right-line"></i>&nbsp;Asignar</button>
                                                            </div> 
                                                        </div>
                                                        
                                                        
                                                        
                                                        <div class="col-sm-3" style="text-align: right;">
                                                            <button type="button" id="reset" class="btn botonGeneral">Reiniciar</button>
                                                            <button type="button" id="buscar" class="btn botonGeneral"><i class="ri-zoom-in-line"></i>&nbsp;Buscar</button>
                                                        </div>
                                                    </div>
                                                
                                                </div>
                                            </form>
                    
                                        </div>
                    
                                        <div class="table mt-3 mb-1 mx-auto">
                                            <table id="listaOrdenesEfectores" class="table table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="sort">Fecha</th>
                                                        <th class="sort">Especialidad</th>
                                                        <th class="sort">Prestación</th>
                                                        <th class="sort">Empresa</th>
                                                        <th class="sort">Paciente</th>
                                                        <th class="sort">DNI</th>
                                                        <th class="sort">Examen</th>
                                                        <th><input type="checkbox" id="checkAllAsignar" name="Id_asignar"></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="list form-check-all">
                    
                                                </tbody>
                                            </table>
                                        </div>           
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                    </div>
        
                </div>

                <div class="tab-pane" id="asignadosEfector" role="tabpanel">
                    
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="listjs-table" id="customerList">
                                        <div class="row g-4 mb-3">
                    
                                            <form id="form-index">
                                                <div class="col-12 p-4 border border-1 border-color" style="border-color: #666666;">  
                                                    <div class="row">
        
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="fechaDesdeAsignados" class="form-label font-weight-bold"><strong>Fecha desde: <span class="required">(*)</span></strong></label>
                                                            <input type="date" class="form-control" id="fechaDesdeAsignados" name="fechaDesdeAsignados" max="9999-12-31">
                                                        </div>
                    
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="fechaDesdeAsignados" class="form-label font-weight-bold"><strong>Fecha hasta: <span class="required">(*)</span></strong></label>
                                                            <input type="date" class="form-control" id="fechaHastaAsignados" name="fechaHastaAsignados" max="9999-12-31">
                                                        </div>
        
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="especialidadAsignados" class="form-label font-weight-bold"><strong>Especialidad: <span class="required">(*)</span></strong></label>
                                                            <select class="form-control especialidadAsignados" name="especialidadAsignados" id="especialidadAsignados"></select>
                                                        </div>
        
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="efectorAsignado" class="form-label font-weight-bold"><strong>Profesional:</strong></label>
                                                            <select class="form-control" name="efectorAsignado" id="efectorAsignado"></select>
                                                        </div>

                                                        <div class="col-sm-2 mb-3">
                                                            <label for="examenAsignados" class="form-label font-weight-bold"><strong>Examen:</strong></label>
                                                            <select class="form-control" name="examenAsignados" id="examenAsignados"></select>
                                                        </div>

                                                        <div class="col-sm-2 mb-3">
                                                            <label for="estadoAsignados" class="form-label font-weight-bold"><strong>Estado: <span class="required">(*)</span></strong></label>
                                                            <select class="form-control" name="estadoAsignados" id="estadoAsignados">
                                                                <option value="" selected>Elija una opción...</option>
                                                                <option value="cerrados">Cerrados</option>
                                                                <option value="abiertos">Abiertos</option>
                                                                <option value="asignados">Asignados</option>
                                                            </select>
                                                        </div>

                                                        <div class="collapse" id="filtrosAvanzadosAsignados">
                                                            <div class="card mb-3">
                                                              <div class="card-body" style="background: #eaeef3">
                                                                <div class="row">
        
                                                                    <div class="col-sm-2 mb-3">
                                                                        <label for="prestacionAsignados" class="form-label font-weight-bold"><strong>Nro prestación:</strong></label>
                                                                        <input type="number" class="form-control" id="prestacionAsignados" name="prestacionAsignados">
                                                                    </div>
                                
                                                                    <div class="col-sm-2 mb-3">
                                                                        <label for="empresaAsignados" class="form-label font-weight-bold"><strong>Empresa:</strong></label>
                                                                        <select class="form-control" name="empresaAsignados" id="empresaAsignados"></select>
                                                                    </div>
                    
                                                                    <div class="col-sm-2 mb-3">
                                                                        <label for="pacienteAsignados" class="form-label font-weight-bold"><strong>Paciente / DNI:</strong></label>
                                                                        <select class="form-control" name="pacienteAsignados" id="pacienteAsignados"></select>
                                                                    </div>
                                                                </div>
                                                              </div>
                                                            </div>
                                                        </div>
        
                                                    </div>
        
                                                    <div class="row">
                                                        <div class="col-sm-9 mb-3" style="text-align: right;">
                                                            <button type="button" id="Liberar" class="btn botonGeneral btnLiberar"><i class="ri-arrow-right-line"></i>&nbsp;Desasignar</button>
                                                            <button type="button" id="Cerrar" class="btn botonGeneral btnCerrar"><i class="ri-arrow-right-line"></i>&nbsp;Cerrar</button>
                                                            <button type="button" id="Abrir" class="btn botonGeneral btnAbrir"><i class="ri-arrow-right-line"></i>&nbsp;Abrir</button>
                                                        </div>
                                                        <div class="col-sm-3" style="text-align: right;">
                                                            <button title="Filtros avanzados" class="btn btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosAvanzadosAsignados" aria-expanded="false" aria-controls="filtrosAvanzados">
                                                                Filtros <i class="ri-filter-2-line"></i>
                                                            </button>
                                                            <button type="button" id="resetAsignado" class="btn botonGeneral">Reiniciar</button>
                                                            <button type="button" id="buscarAsignados" class="btn botonGeneral"><i class="ri-zoom-in-line"></i>&nbsp;Buscar</button>
                                                        </div>
                                                    </div>
                                                
                                                </div>
                                            </form>
                    
                                        </div>
                    
                                        <div class="table mt-3 mb-1 mx-auto">
                                            <table id="listaOrdenesEfectoresAsig" class="table table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="sort">Fecha</th>
                                                        <th class="sort">Especialidad</th>
                                                        <th class="sort">Prestación</th>
                                                        <th class="sort">Empresa</th>
                                                        <th class="sort">Paciente</th>
                                                        <th class="sort">DNI</th>
                                                        <th class="sort">Examen</th>
                                                        <th></th>
                                                        <th><input type="checkbox" id="checkAllAsignado" name="Id_asignado"></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="list form-check-all">
                    
                                                </tbody>
                                            </table>
                                        </div>           
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                    </div>

                </div>

                <div class="tab-pane" id="adjuntarEfector" role="tabpanel">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="listjs-table" id="customerList">
                                        <div class="row g-4 mb-3">
                    
                                            <form id="form-index">
                                                <div class="col-12 p-4 border border-1 border-color" style="border-color: #666666;">
                                                    
                                                    <div class="row">
        
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="fechaDesdeAdjunto" class="form-label font-weight-bold"><strong>Fecha desde: <span class="required">(*)</span></strong></label>
                                                            <input type="date" class="form-control" id="fechaDesdeAdjunto" name="fechaDesdeAdjunto" max="9999-12-31">
                                                        </div>
                    
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="fechaHastaAdjunto" class="form-label font-weight-bold"><strong>Fecha hasta: <span class="required">(*)</span></strong></label>
                                                            <input type="date" class="form-control" id="fechaHastaAdjunto" name="fechaHastaAdjunto" max="9999-12-31">
                                                        </div>
        
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="especialidadAdjunto" class="form-label font-weight-bold"><strong>Especialidad: <span class="required">(*)</span></strong></label>
                                                            <select class="form-control especialidadAdjunto" name="especialidadAdjunto" id="especialidadAdjunto"></select>
                                                        </div>
        
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="efectorAdjunto" class="form-label font-weight-bold"><strong>Profesional:</strong></label>
                                                            <select class="form-control" name="efectorAdjunto" id="efectorAdjunto"></select>
                                                        </div>
                    
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="empresaAdjunto" class="form-label font-weight-bold"><strong>Empresa:</strong></label>
                                                            <select class="form-control" name="empresaAdjunto" id="empresaAdjunto"></select>
                                                        </div>
        
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="artAdjunto" class="form-label font-weight-bold"><strong>ART:</strong></label>
                                                            <select class="form-control" name="artAdjunto" id="artAdjunto"></select>
                                                        </div>

                                                    </div>
        
                                                    <div class="row">
                                                        <div class="col-sm-9 mb-3" style="text-align: right;">
                                                            <button type="button" class="btn btn-sm botonGeneral automaticUpload" data-forma="masivo"><i class="ri-arrow-right-line"></i>&nbsp;Asignar masivo</button>
                                                        </div>
                                                        <div class="col-sm-3" style="text-align: right;">
                                                            <button type="button" id="resetAdjunto" class="btn botonGeneral">Reiniciar</button>
                                                            <button type="button" id="buscarAdjunto" class="btn botonGeneral"><i class="ri-zoom-in-line"></i>&nbsp;Buscar</button>
                                                        </div>
                                                    </div>
                                                
                                                </div>
                                            </form>
                    
                                        </div>

                                        <div class="row" id="qrExamen">
                                            <div class="col-12 mt-2 mb-2 text-center generarQr p-3 fondo-grisClaro">
                                                
                                            </div>
                                        </div>

                                        <div id="preloader-overlay" class="preloader-overlay" style="display: none;">
                                            <div class="preloader"></div>
                                        </div>
                    
                                        <div class="table mt-3 mb-1 mx-auto">
                                            <table id="listaOrdenesEfectoresAdj" class="table table-bordered"">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="sort">Fecha</th>
                                                        <th class="sort">Especialidad</th>
                                                        <th class="sort">Prestación</th>
                                                        <th class="sort">Empresa</th>
                                                        <th class="sort">Paciente</th>
                                                        <th class="sort">DNI</th>
                                                        <th class="sort">Examen</th>
                                                        <th class="sort">Efector</th>
                                                        <th class="sort">Estado</th>
                                                        <th><input type="checkbox" id="checkAllAdj" name="Id_adjunto"></th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="list form-check-all">
                    
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
        @endcan

        @can("etapas_informador")
        <div class="tab-pane" id="informador" role="tabpanel">
            <div class="card-header d-flex justify-content-between">
                <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" href="#asignarInformador" role="tab" aria-selected="true">
                            <i class="ri-calendar-check-line"></i>
                            Asignar informador
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#asignadosInformador" role="tab" aria-selected="false" tabindex="-1">
                            <i class="ri-calendar-todo-line"></i>
                            Informadores asignados
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#adjuntarInformador" role="tab" aria-selected="false" tabindex="-1">
                            <i class="ri-file-upload-line"></i>
                            Adjuntar a examenes informadores
                        </a>
                    </li>
                    
                </ul>
            </div>

            <div class="tab-content">
                <div class="tab-pane active" id="asignarInformador" role="tabpanel">
    
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="listjs-table" id="customerList">
                                        <div class="row g-4 mb-3">
                    
                                            <form id="form-index">
                                                <div class="col-12 p-4 border border-1 border-color" style="border-color: #666666;">
                                                    
                                                    <div class="row">
        
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="fechaDesdeInf" class="form-label font-weight-bold"><strong>Fecha desde: <span class="required">(*)</span></strong></label>
                                                            <input type="date" class="form-control" id="fechaDesdeInf" name="fechaDesdeInf" max="9999-12-31">
                                                        </div>
                    
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="fechaDesde" class="form-label font-weight-bold"><strong>Fecha hasta: <span class="required">(*)</span></strong></label>
                                                            <input type="date" class="form-control" id="fechaHastaInf" name="fechaHastaInf" max="9999-12-31">
                                                        </div>
        
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="especialidad" class="form-label font-weight-bold"><strong>Especialidad: <span class="required">(*)</span></strong></label>
                                                            <select class="form-control especialidadInf" name="especialidadInf" id="especialidadInf"></select>
                                                        </div>
    
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="examen" class="form-label font-weight-bold"><strong>Examen:</strong></label>
                                                            <select class="form-control" name="examenInf" id="examenInf"></select>
                                                        </div>
                                                        
                                                        <div class="col-sm-2 mb-3 d-flex align-items-end">
                                                            <button title="Filtros avanzados" class="btn btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosAvanzados" aria-expanded="false" aria-controls="filtrosAvanzados">
                                                                <i class="ri-filter-2-line"></i>
                                                            </button>
                                                        </div>
        
                                                        <div class="collapse" id="filtrosAvanzados">
                                                            <div class="card mb-3">
                                                              <div class="card-body" style="background: #eaeef3">
                                                                <div class="row">
    
                                                                    <div class="col-sm-2 mb-3">
                                                                        <label for="prestacion" class="form-label font-weight-bold"><strong>Nro prestación:</strong></label>
                                                                        <input type="number" class="form-control" id="prestacionInf" name="prestacionInf">
                                                                    </div>
                        
                                                                    <div class="col-sm-2 mb-3">
                                                                        <label for="empresa" class="form-label font-weight-bold"><strong>Empresa:</strong></label>
                                                                        <select class="form-control" name="empresaInf" id="empresaInf"></select>
                                                                    </div>
            
                                                                    <div class="col-sm-2 mb-3">
                                                                        <label for="paciente" class="form-label font-weight-bold"><strong>Paciente / DNI:</strong></label>
                                                                        <select class="form-control" name="pacienteInf" id="pacienteInf"></select>
                                                                    </div>
                                                                </div>
                                                              </div>
                                                            </div>
                                                        </div>
    
    
                                                    </div>
        
                                                    <div class="row">
                                                        <div class="col-sm-9 mb-3 p-2 rounded" style="background-color: #eee">
                                                            <div class="col-sm-6 d-flex align-items-center justify-content-center">
                                                                <span class="font-weight-bold"><strong>Profesional:</strong></span>&nbsp;
                                                                <select class="form-control" name="informadores" id="informadores"></select>&nbsp;
                                                                <button type="button" id="asigInf" class="btn btn-sm botonGeneral"><i class="ri-arrow-right-line"></i>&nbsp;Asignar</button>
                                                            </div> 
                                                        </div>
                                                        
                                                        
                                                        
                                                        <div class="col-sm-3" style="text-align: right;">
                                                            <button type="button" id="resetInf" class="btn botonGeneral">Reiniciar</button>
                                                            <button type="button" id="buscarInf" class="btn botonGeneral"><i class="ri-zoom-in-line"></i>&nbsp;Buscar</button>
                                                        </div>
                                                    </div>
                                                
                                                </div>
                                            </form>
                    
                                        </div>
                    
                                        <div class="table mt-3 mb-1 mx-auto">
                                            <table id="listaOrdenesInformadores" class="table table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="sort">Fecha</th>
                                                        <th class="sort">Especialidad</th>
                                                        <th class="sort">Prestación</th>
                                                        <th class="sort">Empresa</th>
                                                        <th class="sort">Paciente</th>
                                                        <th class="sort">DNI</th>
                                                        <th class="sort">Examen</th>
                                                        <th><input type="checkbox" id="checkAllAsigInf" name="Id_asigInf"></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="list form-check-all">
                    
                                                </tbody>
                                            </table>
                                        </div>           
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                    </div>
        
                </div>
    
                <div class="tab-pane" id="asignadosInformador" role="tabpanel">
                    
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="listjs-table" id="customerList">
                                        <div class="row g-4 mb-3">
                    
                                            <form id="form-index">
                                                <div class="col-12 p-4 border border-1 border-color" style="border-color: #666666;">  
                                                    <div class="row">
        
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="fechaDesdeAsignadosInf" class="form-label font-weight-bold"><strong>Fecha desde: <span class="required">(*)</span></strong></label>
                                                            <input type="date" class="form-control" id="fechaDesdeAsignadosInf" name="fechaDesdeAsignadosInf" max="9999-12-31">
                                                        </div>
                    
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="fechaDesdeAsignadosInf" class="form-label font-weight-bold"><strong>Fecha hasta: <span class="required">(*)</span></strong></label>
                                                            <input type="date" class="form-control" id="fechaHastaAsignadosInf" name="fechaHastaAsignadosInf" max="9999-12-31">
                                                        </div>
        
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="especialidadAsignadosInf" class="form-label font-weight-bold"><strong>Especialidad: <span class="required"></span></strong></label>
                                                            <select class="form-control especialidadAsignadosInf" name="especialidadAsignadosInf" id="especialidadAsignadosInf"></select>
                                                        </div>
        
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="informadorAsignadoInf" class="form-label font-weight-bold"><strong>Profesional:</strong></label>
                                                            <select class="form-control" name="informadorAsignadoInf" id="informadorAsignadoInf"></select>
                                                        </div>
    
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="examenAsignadosInf" class="form-label font-weight-bold"><strong>Examen:</strong></label>
                                                            <select class="form-control" name="examenAsignadosInf" id="examenAsignadosInf"></select>
                                                        </div>
    
                                                        <div class="collapse" id="filtrosAvanzadosAsignados">
                                                            <div class="card mb-3">
                                                              <div class="card-body" style="background: #eaeef3">
                                                                <div class="row">
        
                                                                    <div class="col-sm-2 mb-3">
                                                                        <label for="prestacionAsignadosInf" class="form-label font-weight-bold"><strong>Nro prestación:</strong></label>
                                                                        <input type="number" class="form-control" id="prestacionAsignadosInf" name="prestacionAsignadosInf">
                                                                    </div>
                                
                                                                    <div class="col-sm-2 mb-3">
                                                                        <label for="empresaAsignados" class="form-label font-weight-bold"><strong>Empresa:</strong></label>
                                                                        <select class="form-control" name="empresaAsignadosInf" id="empresaAsignadosInf"></select>
                                                                    </div>
                    
                                                                    <div class="col-sm-2 mb-3">
                                                                        <label for="pacienteAsignados" class="form-label font-weight-bold"><strong>Paciente / DNI:</strong></label>
                                                                        <select class="form-control" name="pacienteAsignadosInf" id="pacienteAsignadosInf"></select>
                                                                    </div>
                                                                </div>
                                                              </div>
                                                            </div>
                                                        </div>
        
                                                    </div>
        
                                                    <div class="row">
                                                        <div class="col-sm-9 mb-3" style="text-align: right;">
                                                            <button type="button" id="LiberarInf" class="btn botonGeneral btnLiberarInf"><i class="ri-arrow-right-line"></i>&nbsp;Desasignar</button>
                                                        </div>
                                                        <div class="col-sm-3" style="text-align: right;">
                                                            <button title="Filtros avanzados" class="btn btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosAvanzadosAsignados" aria-expanded="false" aria-controls="filtrosAvanzados">
                                                                Filtros <i class="ri-filter-2-line"></i>
                                                            </button>
                                                            <button type="button" id="resetAsignadoInf" class="btn botonGeneral">Reiniciar</button>
                                                            <button type="button" id="buscarAsignadosInf" class="btn botonGeneral"><i class="ri-zoom-in-line"></i>&nbsp;Buscar</button>
                                                        </div>
                                                    </div>
                                                
                                                </div>
                                            </form>
                    
                                        </div>
                    
                                        <div class="table mt-3 mb-1 mx-auto">
                                            <table id="listaOrdenesInformadoresAsig" class="table table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="sort">Fecha</th>
                                                        <th class="sort">Especialidad</th>
                                                        <th class="sort">Prestación</th>
                                                        <th class="sort">Empresa</th>
                                                        <th class="sort">Paciente</th>
                                                        <th class="sort">DNI</th>
                                                        <th class="sort">Examen</th>
                                                        <th></th>
                                                        <th><input type="checkbox" id="checkAllAsignadoInf" name="Id_asignadoInf"></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="list form-check-all">
                    
                                                </tbody>
                                            </table>
                                        </div>           
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                    </div>
    
                </div>
    
                <div class="tab-pane" id="adjuntarInformador" role="tabpanel">
    
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="listjs-table" id="customerList">
                                        <div class="row g-4 mb-3">
                    
                                            <form id="form-index">
                                                <div class="col-12 p-4 border border-1 border-color" style="border-color: #666666;">
                                                    <div class="row mb-4">
                                                        <span class="small"><i class="ri-information-line rojo"></i>&nbsp;El icono indica que la prestación ya se encuentra cerrada.</span>
                                                    </div>
                                                    <div class="row">
        
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="fechaDesdeAdjuntoInf" class="form-label font-weight-bold"><strong>Fecha desde: <span class="required">(*)</span></strong></label>
                                                            <input type="date" class="form-control" id="fechaDesdeAdjuntoInf" name="fechaDesdeAdjuntoInf" maxlength="10">
                                                        </div>
                    
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="fechaHastaAdjuntoInf" class="form-label font-weight-bold"><strong>Fecha hasta: <span class="required">(*)</span></strong></label>
                                                            <input type="date" class="form-control" id="fechaHastaAdjuntoInf" name="fechaHastaAdjuntoInf" maxlength="10">
                                                        </div>
        
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="especialidadAdjuntoInf" class="form-label font-weight-bold"><strong>Especialidad: <span class="required"></span></strong></label>
                                                            <select class="form-control especialidadAdjuntoInf" name="especialidadAdjuntoInf" id="especialidadAdjuntoInf"></select>
                                                        </div>
        
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="informadorAdjuntoInf" class="form-label font-weight-bold"><strong>Profesional:</strong></label>
                                                            <select class="form-control" name="informadorAdjuntoInf" id="informadorAdjuntoInf"></select>
                                                        </div>
                    
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="empresaAdjuntoInf" class="form-label font-weight-bold"><strong>Empresa:</strong></label>
                                                            <select class="form-control" name="empresaAdjuntoInf" id="empresaAdjuntoInf"></select>
                                                        </div>
        
                                                        <div class="col-sm-2 mb-3">
                                                            <label for="artAdjuntoInf" class="form-label font-weight-bold"><strong>ART:</strong></label>
                                                            <select class="form-control" name="artAdjuntoInf" id="artAdjuntoInf"></select>
                                                        </div>
    
                                                    </div>
        
                                                    <div class="row">
                                                        <div class="col-sm-9 mb-3">
                                                            <div class="col-sm-12 mb-3" style="text-align: right;">
                                                                <button type="button" class="btn btn-sm botonGeneral automaticUploadI" data-forma="masivo"><i class="ri-arrow-right-line"></i>&nbsp;Asignar masivo</button>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3" style="text-align: right;">
                                                            <button type="button" id="resetAdjuntoInf" class="btn botonGeneral">Reiniciar</button>
                                                            <button type="button" id="buscarAdjuntoInf" class="btn botonGeneral"><i class="ri-zoom-in-line"></i>&nbsp;Buscar</button>
                                                        </div>
                                                    </div>
                                                
                                                </div>
                                            </form>
                    
                                        </div>
    
                                        <div class="row" id="qrExamen">
                                            <div class="col-12 mt-2 mb-2 text-center generarQr p-3 fondo-grisClaro">
                                                
                                            </div>
                                        </div>
                    
                                        <div class="table mt-3 mb-1 mx-auto">
                                            <table id="listaOrdenesInformadoresAdj" class="table table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="sort">Fecha</th>
                                                        <th class="sort">Especialidad</th>
                                                        <th class="sort">Prestación</th>
                                                        <th class="sort">Empresa</th>
                                                        <th class="sort">Paciente</th>
                                                        <th class="sort">DNI</th>
                                                        <th class="sort">Examen</th>
                                                        <th class="sort">Efector</th>
                                                        <th class="sort">Est Efector</th>
                                                        <th><input type="checkbox" id="checkAllAdjInf" name="Id_adjuntoInf"></th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="list form-check-all">
                    
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
        @endcan

        @can('etapas_eenviar')
        <div class="tab-pane" id="eenviar" role="tabpanel">
            
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="listjs-table" id="customerList">
                                <div class="row g-4 mb-3">

                                    <form id="form-index">

                                        <div class="col-12 p-4 border border-1 border-color" style="border-color: #666666;">
                                            
                                            <div class="row">

                                                <div class="col-sm-2 mb-3">
                                                    <label for="fechaDesdeEEnviar" class="form-label font-weight-bold"><strong>Fecha desde: <span class="required">(*)</span></strong></label>
                                                    <input type="date" class="form-control" id="fechaDesdeEEnviar" name="fechaDesdeEEnviar" max="9999-12-31">
                                                </div>
            
                                                <div class="col-sm-2 mb-3">
                                                    <label for="fechaHastaEEnviar" class="form-label font-weight-bold"><strong>Fecha hasta: <span class="required">(*)</span></strong></label>
                                                    <input type="date" class="form-control" id="fechaHastaEEnviar" name="fechaHastaEEnviar" max="9999-12-31">
                                                </div>

                                                <div class="col-sm-2 mb-3">
                                                    <label for="eEnviarEEnviar" class="form-label font-weight-bold"><strong>eEnviado:</strong></label>
                                                    <select class="form-control" name="eEnviarEEnviar" id="eEnviarEEnviar">
                                                        <option value="" selected>Elija una opción...</option>
                                                        <option value="eenviado">eEnviado</option>
                                                        <option value="noeenviado">No eEnviado</option>
                                                        <option value="todos">Todos</option>
                                                    </select>
                                                </div>

                                                <div class="col-sm-2 mb-3">
                                                    <label for="empresaEEnviar" class="form-label font-weight-bold"><strong>Empresa:</strong></label>
                                                    <select class="form-control" name="empresaEEnviar" id="empresaEEnviar"></select>
                                                </div>

                                                <div class="col-sm-2 mb-3">
                                                    <label for="pacienteEEnviar" class="form-label font-weight-bold"><strong>Paciente / DNI:</strong></label>
                                                    <select class="form-control" name="pacienteEEnviar" id="pacienteEEnviar"></select>
                                                </div>

                                            </div>

                                            <div class="row">
                                                <div class="col-sm-12" style="text-align: right;">
                                                    <button type="button" id="buscarEEnviar" class="btn botonGeneral"><i class="ri-zoom-in-line"></i>&nbsp;Buscar</button>
                                                </div>
                                            </div>

                                            <div class="row mb-2 mt-2">
                                                <div class="col-sm-6 text-start">
                                                    <button type="button" class="btn btn-sm botonGeneral completo">Completo</button>
                                                    <button type="button" class="btn btn-sm botonGeneral abierto">Abiertos</button>
                                                    <button type="button" class="btn btn-sm botonGeneral cerrado">Cerrados</button>
                                                </div>
                                                <div class="col-sm-6 text-end">
                                                    <button type="button" class="btn btn-sm botonGeneral"><i class="ri-article-line"></i>&nbsp;Vista previa</button>
                                                    <button type="button" class="btn btn-sm botonGeneral"><i class="ri-file-info-line"></i>&nbsp;Aviso</button>
                                                    <button type="button" class="btn btn-sm botonGeneral"><i class="ri-send-plane-line"></i>&nbsp;eEnviar</button>
                                                </div>
                                            </div>

                                            <div class="table mt-3 mb-1 mx-auto">
                                                <table id="listaOrdenesEEnviar" class="table table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="sort">Fecha</th>
                                                            <th class="sort">Prestación</th>
                                                            <th class="sort">Empresa</th>
                                                            <th class="sort">Paciente</th>
                                                            <th class="sort">DNI</th>
                                                            <th class="sort">Examen</th>
                                                            <th>F eEnviado</th>
                                                            <th>Email</th>
                                                            <th>ExCta Imp</th>
                                                            <th><input type="checkbox" id="checkAllEEnviar" name="Id_EEnviar"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="list form-check-all">
                        
                                                    </tbody>
                                                </table>
                                            </div> 

                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        @endcan
    </div>
</div>

<script>
    const TOKEN = "{{ @csrf_token() }}";

    const lstProveedores = "{{ route('lstProveedores') }}";
    const listGeneral = "{{ route('listGeneral') }}";
    const getClientes = "{{ route('getClientes') }}";
    const searchExamen = "{{ route('searchExamen') }}";
    const getPacientes = "{{ route('getPacientes') }}";

    const linkPrestaciones = "{{ route('prestaciones.index') }}";
    const linkItemPrestacion = "{{ route('itemsprestaciones.index') }}";

    const SEARCH = "{{ route('seachOrdenesExamen')}}";
    const SEARCHASIG = "{{ route('searchOrExaAsignados') }}";
    const SEARCHADJ = "{{ route('searchOrExaAdjunto') }}";
    const SEARCHINF = "{{ route('seachOrExInf')}}";
    const SEARCHASIGINF = "{{ route('seachOrExAsigInf')}}";
    const SEARCHADJINF = "{{ route('searchOrExaAdjInf') }}";
    const SEARCHPRESTACION = "{{ route('searchPrestacion') }}";
    const SEARCHEENVIAR = "{{ route('searchEenviar') }}";
    const asignarProfesional = "{{ route('asignarProfesional') }}";
    const updateItem = "{{ route('updateItem') }}";
    const fileUpload = "{{ route('uploadAdjunto') }}";
    const archivosAutomatico = "{{ route('archivosAutomatico') }}";
    const archivosAutomaticoI = "{{ route('archivosAutomaticoI') }}";

    const exportarOrdExa = "{{ route('exportarOrdExa') }}";
</script>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/ordenesexamenes/validaciones.js')}}?v={{ time() }}"></script>
<script src="{{ asset('js/ordenesexamenes/index.js')}}?=v{{ time() }}"></script>
<script src="{{ asset('js/ordenesexamenes/paginacion.js')}}?=v{{ time() }}"></script>
<script src="{{ asset('js/ordenesexamenes/paginacionPrestacion.js')}}?=v{{ time() }}"></script>
<script src="{{ asset('js/ordenesexamenes/paginacionInf.js')}}?=v{{ time() }}"></script>
<script src="{{ asset('js/ordenesexamenes/paginacionAsignados.js')}}?=v{{ time() }}"></script>
<script src="{{ asset('js/ordenesexamenes/paginacionAsignadosInf.js')}}?=v{{ time() }}"></script>
<script src="{{ asset('js/ordenesexamenes/paginacionAdjunto.js')}}?=v{{ time() }}"></script>
<script src="{{ asset('js/ordenesexamenes/paginacionAdjuntoInf.js')}}?=v{{ time() }}"></script>
<script src="{{ asset('js/ordenesexamenes/paginacionEnviar.js')}}?=v{{ time() }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
@endpush

@endcan

@endsection