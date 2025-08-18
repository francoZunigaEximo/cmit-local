@extends('template')

@section('title', 'Lista de Prestaciones - Llamador Informador')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0 capitalize">ordenes de examen <span class="custom-badge rojo capitalize">informador</span></h4>
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
                                        <label for="profesionalInf" class="form-label fw-bolder">Profesional <span class="required">(*)</span></label>
                                        <select class="form-control" name="profesional" id="profesional">

                                            @if(!is_null($informadores) && $informadores->count() === 1)
                                                <option value="{{ $informadores->first()->Id ?? 0}}">{{ $informadores->first()->NombreCompleto ?? '' }}</option>
                                            @elseif(!is_null($informadores))
                                                <option value="" selected>Elija una opción...</option>

                                                @forelse($informadores as $informador)
                                                    <option value="{{ $informador->Id ?? 0}}">{{ $efector->NombreCompleto ?? '' }}</option>
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
                                        @php
                                            $rolesPermitidos = ['Administrador', 'Admin SR', 'Recepcion SR'];
                                            $rolesUsuario = Auth::user()->role->pluck('nombre')->toArray();
                                            $tieneRol = !empty(array_intersect($rolesPermitidos, $rolesUsuario));
                                        @endphp

                                        @if($tieneRol)
                                            <select name="especialidadSelect" id="especialidadSelect" class="form-control"></select>
                                        @else
                                            <input type="text" class="form-control" name="especialidad" id="especialidad" data-id="{{ session('IdEspecialidad')->Id ?? '' }}" value="{{ session('Profesional') === 'INFORMADOR' ? session('Especialidad') : 'Sin Especialidad' }}">
                                        @endif
                                    </div>

                                    <div class="col-sm-2 mb-3">
                                        <label for="fechaDesde" class="form-label fw-bolder">Fecha Desde <span class="required">(*)</span></label>
                                        <input type="date" class="form-control" name="fechaDesde" id="fechaDesde">
                                    </div>

                                    <div class="col-sm-2 mb-3">
                                        <label for="fechaHasta" class="form-label fw-bolder">Fecha Hasta <span class="required">(*)</span></label>
                                        <input type="date" class="form-control" name="fechaHasta" id="fechaHasta">
                                    </div>

                                    <div class="col-sm-2 mb-3">
                                        <label for="prestacion" class="form-label fw-bolder">Prestación</label>
                                        <input type="text" class="form-control" name="prestacion" id="prestacion">
                                    </div>

                                    <div class="col-sm-2 mb-3">
                                        <label for="estadoInf" class="form-label fw-bolder">Estado</label>
                                        <select name="estado" id="estado" class="form-control">
                                            <option value=""selected>Elija una opción...</option>
                                            <option value="abierto">Abiertos</option>
                                            <option value="cerrado">Cerrados</option>
                                            <option value="vacio">Vacíos</option>
                                            <option value="todos">Todos</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 text-end">
                                        <div class="d-flex align-items-center justify-content-end">
                                            <button class="btn btn-sm botonGeneral" id="buscar">
                                                <i class="ri-zoom-in-line"></i>Buscar
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn btn-sm botonGeneral exportar"><i class="ri-file-excel-line"></i>&nbsp;Exportar</button>
                                        <button class="btn btn-sm botonGeneral detalles"><i class="ri-file-excel-line"></i>&nbsp;Detalles</button>
                                    </div>
                                </div>

                                <div class="table mt-3 mb-1 mx-auto">
                                    <table id="listaLlamadaInformador" class="table table-bordered">
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

<div id="atenderInformador" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidde="true" style="display: none">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel"> Atender Paciente - Informador</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>

            <div class="modal-body">
                <hr size="1">
                <div class="row p-2 fondo-grisClaro">
                    <div class="col-6 text-start">
                        <button class="btn btn-sm botonGeneral liberarPrestacion">Liberar</button>
                    </div>
                    <div class="col-6 text-end">
                        <button class="btn btn-sm botonGeneral">Llamar todo</button>
                    </div>
                </div>
                <hr size="1">

                <div class="card card-h-100">
                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-9 d-flex justify-content-center align-content-center">

                                <div class="row d-flex align-content-center">

                                    <div class="col-md-4 p-2">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Profesional</span>
                                            <input type="text" class="form-control" id="profesional_var" name="profesional_var" readonly="true">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4 p-2">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Prestación</span>
                                            <input type="text" class="form-control" id="prestacion_var" name="prestacion_var" readonly="true">
                                        </div>
                                    </div>
        
                                    <div class="col-md-4 p-2">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Tipo Exámen</span>
                                            <input type="text" class="form-control" id="tipo_var" name="tipo_var" readonly="true">
                                        </div>
                                    </div>

                                    <div class="col-md-4 p-2">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">ART</span>
                                            <input type="text" class="form-control" id="art_var" name="art_var" readonly="true">
                                        </div>
                                    </div>

                                    <div class="col-md-4 p-2">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Empresa</span>
                                            <input type="text" class="form-control" id="empresa_var" name="empresa_var" readonly="true">
                                        </div>
                                    </div>

                                    <div class="col-md-4 p-2">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Para Empresa</span>
                                            <input type="text" class="form-control" id="paraEmpresa_var" name="paraEmpresa_var" readonly="true">
                                        </div>
                                    </div>

                                    <div class="col-md-4 p-2 mt-1">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Paciente</span>
                                            <input type="text" class="form-control" id="paciente_var" name="paciente_var" readonly="true">
                                        </div>
                                    </div>

                                    <div class="col-md-4 p-2 mt-1">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Edad</span>
                                            <input type="text" class="form-control" id="edad_var" name="edad_var" readonly="true">
                                        </div>
                                    </div>

                                    <div class="col-md-4 p-2 mt-1">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Fecha Prestación</span>
                                            <input type="text" class="form-control" id="fecha_var" name="fecha_var" readonly="true">
                                        </div>
                                    </div>

                                </div>

                            </div>

                            
                            <div class="col-md-3">
                                <img class="round mx-auto d-block img-fluid" id="foto_var" src="" alt="Foto del paciente" width="150px">
                                <span class="d-flex justify-content-center mt-1">
                                    <a id="descargaFoto" class="descargaFoto" href="" download>
                                        <button class="descargarImagen btn btn-sm botonGeneral">Descargar</button>
                                    </a>
                                </span>
                                
                            </div>
                            
                        </div>

                        <hr size="1">
                   
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button class="btn btn-sm botonGeneral">Resultados</button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12" id="tablasExamenes">

                            </div>
                        </div>

                        <hr size="1">

                        <div class="row justify-content-center">
                             <div class="table mx-auto text-center col-md-7 col-sm-9 col-12">
                                <table id="listadoAdjuntosInformadores" class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Examen</th>
                                            <th>Descripción</th>
                                            <th>Adjunto</th>
                                            <th>Tipo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all" id="adjuntosInformadores">
                        
                                    </tbody>
                                </table>
                            </div>

                        </div>

                    </div>
                </div>

            </div>
            <div class="modal-footer text-center">
                <button type="button" class="btn btn-sm botonGeneral" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-sm botonGeneral terminarAtencion">Terminar</button>
            </div>
        </div>
    </div>
</div>

<script>
    const SEARCH = "{{ route('llamador.buscarInformador') }}";
    const lnkPres = "{{ route('prestaciones.edit', ['prestacione' => '__item__']) }}";
    const printExportar = "{{ route('llamador.exportar') }}";
    const FOTO = "@fileUrl('lectura')/Fotos/";
    const dataPaciente = "{{ route('llamador.verPaciente') }}";
    const USERACTIVO = "{{ Auth::user()->profesional_id }}";
    const addAtencion = "{{ route('llamador.llamar-paciente') }}";
    const checkLlamado = "{{ route('llamador.check') }}";
    const ROLESUSER = @json(Auth::user()->role);
    const asignacionProfesional = "{{ route('llamador.asignarPaciente') }}";
    const sessionProfesional = "{{ session('Profesional') }}";
    const searchEspecialidad = "{{ route('llamador.buscarEspecialidad') }}";
</script>
</script>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
@endpush

@push('scripts')

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script src="{{ asset('/js/llamador/informador/index.js')}}?v={{ time() }}"></script>
<script src="{{ asset('/js/llamador/informador/paginacion.js')}}?v={{ time() }}"></script>
<script src="{{ asset('/js/llamador/sockets.js')}}?v={{ time() }}"></script>
<script src="{{ asset('/js/llamador/atenderPaciente.js') }}?v={{ time() }}"></script>


<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>

@endpush

@endsection
