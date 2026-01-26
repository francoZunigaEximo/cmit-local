@extends('template')

@section('title', 'Lista de Prestaciones - Llamador Evaluador')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0 capitalize">ordenes de examen <span class="custom-badge amarillo capitalize">evaluador</span></h4>
    <div class="page-title-right d-inline"></div>
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

                                    <div class="col-sm-3 mb-3">
                                        <label for="profesional" class="form-label fw-bolder">Profesional <span class="required">(*)</span></label>
                                        <select class="form-control" name="profesional" id="profesional">
                                            @if(!is_null($efectores) && $efectores->count() === 1)
                                                <option value="{{ $efectores->first()->Id ?? 0}}">{{ $efectores->first()->NombreCompleto ?? '' }}</option>
                                            @elseif(!is_null($efectores))
                                                <option value="" selected>Elija una opción...</option>

                                                @forelse($efectores as $efector)
                                                    <option value="{{ $efector->Id ?? 0}}">{{ $efector->NombreCompleto ?? '' }}</option>
                                                @empty
                                                    <option value="">Sin usuarios activos</option>
                                                @endforelse
                                            @else
                                                <option value="" selected disabled>No habilitado</option>
                                            @endif

                                            
                                        </select>
                                    </div>

                                    <div class="col-sm-2 mb-3">
                                        <label for="especialidad" class="form-label fw-bolder">Especialidad <span class="required">(*)</span></label>
                                        <input type="text" class="form-control" name="especialidad" id="especialidad">
                                    </div>

                                    <div class="col-sm-2 mb-3">
                                        <label for="fechaDesde" class="form-label fw-bolder">Fecha Desde <span class="required">(*)</span></label>
                                        <input type="date" class="form-control" id="fechaDesde">
                                    </div>

                                    <div class="col-sm-2 mb-3">
                                        <label for="fechaHasta" class="form-label fw-bolder">Fecha Hasta <span class="required">(*)</span></label>
                                        <input type="date" class="form-control" id="fechaHasta">
                                    </div>

                                    <div class="col-sm-2 mb-3">
                                        <label for="prestacion" class="form-label fw-bolder">Prestación</label>
                                        <input type="text" class="form-control" id="prestacion">
                                    </div>

                                    <div class="col-sm-2 mb-3">
                                        <label for="estado" class="form-label fw-bolder">Estado</label>
                                        <select name="estado" id="estado" class="form-control">
                                            <option value=""selected>Elija una opción...</option>
                                            <option value="abierto">Abiertos</option>
                                            <option value="cerrado">Cerrados</option>
                                            <option value="vacio">Vacíos</option>
                                            <option value="todos">Todos</option>
                                        </select>
                                    </div>

                                    <div class="col-sm-1 d-flex align-items-center justify-content-end">
                                        <button class="btn btn-sm botonGeneral" id="buscar">
                                            <i class="ri-zoom-in-line"></i>Buscar
                                        </button>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn btn-sm botonGeneral exportar"><i class="ri-file-excel-line"></i>&nbsp;Exportar</button>
                                        <button class="btn btn-sm botonGeneral detalles"><i class="ri-file-excel-line"></i>&nbsp;Detalles</button>
                                    </div>
                                </div>

                                <div class="table mt-3 mb-1 mx-auto">
                                    <table id="listaLlamadaEfector" class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Prestacion</th>
                                                <th>Empresa</th>
                                                <th>Para Empresa</th>
                                                <th>ART</th>
                                                <th>Paciente</th>
                                                <th>DNI</th>
                                                <th>Tipo</th>
                                                <th>Edad</th>
                                                <th>Telefono</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
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

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
@endpush

@push('scripts')


@endpush

@endsection