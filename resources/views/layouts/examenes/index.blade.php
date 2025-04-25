@extends('template')

@section('title', 'Examenes')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
        <h4 class="mb-sm-0">Examenes</h4>
        <x-helper>{!!$helper!!}</x-helper>
    </div>
    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="{{ route('examenes.index') }}">Examenes</a></li>
            <li class="breadcrumb-item active">Listado</li>
        </ol>
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
                                    <div class="col-sm-3 mb-3">
                                        <label for="examen" class="form-label font-weight-bold"><strong>Exámen:</strong></label>
                                        <select class="form-control" name="examen" id="examen"></select>
                                    </div>

                                    <div class="col-sm-3 mb-3">
                                        <label for="especialidad" class="form-label font-weight-bold"><strong>Especialidad</strong></label>
                                        <select class="form-control" name="especialidad" id="especialidad"></select>
                                    </div>

                                    <div class="col-sm-3 mb-3">
                                        <label for="atributos" class="form-label font-weight-bold"><strong>Atributos</strong></label>
                                        <select class="form-control" name="atributos" id="atributos">
                                            <option value="" selected>Elija una opción...</option>
                                            <option value="informe">Informe (el exámen lleva informe)</option>
                                            <option value="adjunto">Adjunto (el exámen lleva adjunto)</option>
                                            <option value="fisico">Físico (el adjunto no se imprime)</option>
                                            <option value="cerrado">Cerrado (el exámen se crea cerrado)</option>
                                            <option value="opciones">Opciones</option>
                                            <option value="estado">Estado</option>
                                            <option value="activo">Activo</option>
                                        </select>
                                    </div>

                                    <div class="col-sm-3 mb-3 opciones">
                                        <label for="opciones" class="form-label font-weight-bold"><strong>Opciones</strong></label>
                                        <select class="form-control" name="opciones" id="opciones">
                                            <option value="" selected>Elija una opción...</option>
                                            <option value="evalExclusivo">Evaluador exclusivo (cierra y adjunta el evaluador)</option>
                                            <option value="expAnexos">Exporta con anexos (adjunta copia en pdf anexos)</option>
                                            <option value="priImpresion">Prioridad impresión (se imprime primero)</option>
                                            <option value="formulario">Formulario (se completa formulario en intranet)</option>
                                            <option value="sinReporte">Sin reporte (no tiene un reporte asociado)</option>
                                            <option value="sinVencimiento">Sin vencimiento (no tiene un vencimiento registrado)</option>
                                        </select>
                                    </div>

                                    <div class="col-sm-3 mb-3 estado">
                                        <label for="estado" class="form-label font-weight-bold"><strong>Estado</strong></label>
                                        <select class="form-control" name="estado" id="estado">
                                            <option value="" selected>Elija una opción...</option>
                                            <option value="ausente">Ausente</option>
                                            <option value="devolucion">Devolución</option>
                                        </select>
                                    </div>

                                    <div class="col-sm-3 mb-3">
                                        <label for="codigoex" class="form-label font-weight-bold"><strong>Código Ex:</strong></label>
                                        <input type="text" class="form-control" id="codigoex" name="codigoex">
                                    </div>

                                    <div class="col-sm-3 mb-3 activo">
                                        <label for="activo" class="form-label font-weight-bold"><strong>Activo:</strong></label>
                                        <select class="form-control" name="activo" id="activo">
                                            <option value="" selected>Elija una opción...</option>
                                            <option value="Activo">Activo</option>
                                            <option value="nActivo">No Activo</option>
                                            <option value="tActivo">Todos</option>
                                        </select>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-sm-12" style="text-align: right;">
                                        <button type="button" id="reset" class="btn botonGeneral">Reiniciar</button>
                                        <button type="button" id="buscar" class="btn botonGeneral">Buscar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-sm-9">
                        <div>
                            <a href="{{ route('examenes.create') }}">
                                <button type="button" class="btn botonGeneral add-btn">
                                    <i class="ri-add-line align-bottom me-1"></i> Nuevo
                                </button>
                            </a>  
                            <button type="button" class="btn botonGeneral add-btn" id="exportar">
                                <i class="ri-file-excel-line align-bottom me-1"></i> Exportar
                            </button>
                        </div>
                    </div>
                    

                    <div class="table-responsive table-card mt-3 mb-1 mx-auto">
                        <table id="listaExamenes" class="display table table-bordered" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>Estudio</th>
                                    <th class="sort">Exámen</th>
                                    <th class="sort">Especialidad Efector</th>
                                    <th class="sort">Especialidad Informador</th>
                                    <th>Vto</th>
                                    <th>Cod.Examen</th>
                                    <th>Cod.Efector</th>
                                    <th class="sort">Tipo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all" id="resultadoExamenes">

                            </tbody>
                        </table>
                    </div>  

                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const lstProveedores = "{{ route('lstProveedores') }}";
    const searchExamen = "{{ route('searchExamen') }}";
    const SEARCH = "{{ route('searchExamenes') }}";
    const exportarExcel = "{{ route('examenes.excel') }}"
</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v=?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">
@endpush


@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script src="{{ asset('js/examenes/index.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/examenes/paginacion.js') }}?v={{ time() }}"></script>


<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
@endpush

@endsection