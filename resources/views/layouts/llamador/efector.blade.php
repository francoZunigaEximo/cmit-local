@extends('template')

@section('title', 'Lista de Prestaciones - LLamador Efector')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0 capitalize">ordenes de examen <span class="custom-badge verde capitalize">efector</span></h4>
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

                                    <div class="col-sm-1 mb-3 d-flex align-items-center justify-content-end">
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

<!-- Modales -->
<div id="atenderEfector" class="modal fadeInUp" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"> Atender Paciente - Efector</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <hr size="1">
                <div class="row p-2 fondo-grisClaro">
                    <div class="col-6 text-start">
                        <button class="btn btn-sm botonGeneral">Liberar</button>
                    </div>
                    <div class="col-6 text-end">
                        <button class="btn btn-sm botonGeneral">Llamar todo</button>
                    </div>
                </div>
                <hr size="1">

                <div class="card card-h-100">
                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-9">

                                <div class="row">

                                    <div class="col-md-4">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Profesional</span>
                                            <input type="text" class="form-control" id="profesionalEfector" name="profesionalEfector" readonly="true">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Prestación</span>
                                            <input type="text" class="form-control" id="prestacionEfector" name="prestacionEfector" readonly="true">
                                        </div>
                                    </div>
        
                                    <div class="col-md-4">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Tipo Exámen</span>
                                            <input type="text" class="form-control" id="tipoEfector" name="tipoEfector" readonly="true">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">ART</span>
                                            <input type="text" class="form-control" id="artEfector" name="artEfector" readonly="true">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Empresa</span>
                                            <input type="text" class="form-control" id="empresaEfector" name="empresaEfector" readonly="true">
                                        </div>
                                    </div>

                                    <div class="col-md-4 mt-1">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Para Empresa</span>
                                            <input type="text" class="form-control" id="paraEmpresaEfector" name="paraEmpresaEfector" readonly="true">
                                        </div>
                                    </div>

                                    <div class="col-md-4 mt-1">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Paciente</span>
                                            <input type="text" class="form-control" id="pacienteEfector" name="pacienteEfector" readonly="true">
                                        </div>
                                    </div>

                                    <div class="col-md-4 mt-1">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Edad</span>
                                            <input type="text" class="form-control" id="edadEfector" name="edadEfector" readonly="true">
                                        </div>
                                    </div>

                                    <div class="col-md-4 mt-1">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Fecha Prestación</span>
                                            <input type="text" class="form-control" id="fechaEfector" name="fechaEfector" readonly="true">
                                        </div>
                                    </div>

                                </div>

                            </div>

                            
                            <div class="col-md-3">
                                <img class="round mx-auto d-block img-fluid" id="fotoEfector" src="" alt="Foto del paciente" width="150px">
                            </div>
                            
                        </div>
                   
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button class="btn btn-sm botonGeneral">Resultados</button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12" id="tablasExamenes">

                            </div>
                        </div>

                    </div>
                </div>

            </div>
            <div class="modal-footer text-center">
                <button type="button" class="btn btn-sm botonGeneral" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-sm botonGeneral terminarAtencion">Terminar</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    const SEARCH = "{{ route('llamador.buscarEfector') }}";
    const lnkPrestaciones = "{{ route('prestaciones.edit', ['prestacione' => '__item__']) }}";
    const printExportar = "{{ route('llamador.excelEfector') }}";
    const FOTO = "@fileUrl('lectura')/Fotos/";
    const dataPaciente = "{{ route('llamador.verPaciente') }}";
</script>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script src="{{ asset('/js/llamador/index.js')}}?v={{ time() }}"></script>
<script src="{{ asset('/js/llamador/paginacion.js')}}?v={{ time() }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
@endpush

@endsection