@extends('template')

@section('title', 'Lista de Prestaciones - Llamador Combinado')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0 capitalize">ordenes de examen <span class="custom-badge amarillo capitalize">combinado</span></h4>
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

                                    <div class="col-sm-2 mb-3">
                                        <label for="profesionalInf" class="form-label fw-bolder">Profesional <span class="required">(*)</span></label>
                                        <select class="form-control" name="profesional" id="profesional">

                                            @if(!is_null($combinados) && $combinados->count() === 1)
                                                <option value="{{ $combinados->first()->Id ?? 0}}">{{ $combinados->first()->NombreCompleto ?? '' }}</option>
                                            @elseif(!is_null($combinados))
                                                <option value="" selected>Elija una opción...</option>

                                                @forelse($combinados as $combinado)
                                                    <option value="{{ $combinado->Id ?? 0}}">{{ $combinado->NombreCompleto ?? '' }}</option>
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
                                            <input type="text" class="form-control" name="especialidad" id="especialidad" data-id="{{ session('IdEspecialidad')->Id ?? '' }}" value="{{ session('Profesional') === 'COMBINADO' ? session('Especialidad') : 'Sin Especialidad' }}">
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
                                    <table id="listaLlamadaCombinado" class="table table-bordered">
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


<script>
    const SEARCH = "{{ route('llamador.buscar') }}";
    const lnkPres = "{{ route('prestaciones.edit', ['prestacione' => '__item__']) }}";
    const printExportar = "{{ route('llamador.exportar') }}";
    const FOTO = "@fileUrl('lectura')/Fotos/";
    const dataPaciente = "{{ route('llamador.verPaciente') }}";
    const USERACTIVO = "{{ Auth::user()->profesional_id }}";
    const addAtencion = "{{ route('llamador.llamar-paciente') }}";
    const checkLlamado = "{{ route('llamador.check') }}";
    const asignacionProfesional = "{{ route('llamador.asignarPaciente') }}";
    const sessionProfesional = "{{ session('Profesional') }}";
    const searchEspecialidad = "{{ route('llamador.buscarEspecialidad') }}";
    const itemPrestacionEstado = "{{ route('llamador.cambioEstado') }}";
</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script src="{{ asset('/js/llamador/libreria.js')}}?v={{ time() }}"></script>

<script src="{{ asset('/js/llamador/combinado/index.js')}}?v={{ time() }}"></script>
<script src="{{ asset('/js/llamador/combinado/paginacion.js')}}?v={{ time() }}"></script>
<script src="{{ asset('/js/llamador/sockets.js')}}?v={{ time() }}"></script>
<script src="{{ asset('/js/llamador/atenderPaciente.js') }}?v={{ time() }}"></script>



<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
@endpush

@endsection