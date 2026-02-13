@extends('template')

@section('title', 'Pacientes')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Pacientes</h4>

    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="{{ route('pacientes.index') }}">Pacientes</a></li>
            <li class="breadcrumb-item active">Listado</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">


            <div id="mensajeria"></div>
            
            <div class="card-body">
                    <div class="row g-4 mb-3">
                        <div class="col-sm-8">
                            <div>
                                @can("pacientes_add")
                                <a href="{{ route('pacientes.create') }}">
                                    <button type="button" class="btn botonGeneral">
                                        <i class="ri-add-line align-bottom me-1"></i> Nuevo
                                    </button>
                                </a>  
                                @endcan 
                                @can("pacientes_delete")
                                <button type="button" id="btnBajaMultiple" class="btn botonGeneral" title="Baja multiple de pacientes">
                                    <i class="ri-delete-bin-2-line"></i> Baja Multiple
                                </button>
                                @endcan
                                @can("pacientes_report")
                                <button type="button" id="excel" class="btn botonGeneral" title="Generar reporte en Excel">
                                    <i class="ri-file-excel-line"></i> Reporte
                                </button>
                                <button type="button" id="excelAll" class="btn botonGeneral" title="Generar reporte en Excel">
                                    <i class="ri-file-excel-line"></i> Reporte Completo
                                </button>
                                @endcan
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="d-flex" style="width: 100%;">
                                <input type="text" id="buscar" name="buscar" class="form-control me-2" placeholder="Buscar por Nombre, Apellido o Documento">
                                <button class="btn botonGeneral btnBuscar"><i class="ri-zoom-in-line"></i>&nbsp;Buscar</button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive table-card mt-3 mb-1">
                        <table id="listaPac" class="display table table-bordered" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center"><input type="checkbox" id="checkAll" name="Id"></th>
                                    <th>Nro</th>
                                    <th class="sort">Apellido y Nombre</th>
                                    <th class="sort">Documento</th>
                                    <th class="sort">Teléfono</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all">

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

<script>

//Rutas
const exportExcel = "{{ route('excelPacientes') }}";
const exportExcelAll = "{{ route('pacientes.excelAll') }}";
const down = "{{ route('pacientes.down') }}";
const getNombre = "{{ route('getNombre') }}";

//Extras
const GOINDEX = "{{ route('pacientes.index') }}";
const ROUTE = "{{ route('pacientes.index') }}";

</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css">
@endpush

@push('scripts')
<!--datatable js-->
{{-- <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script> --}}

<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>

<script src="{{ asset('js/scripts.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/pacientes/index.js')}}?v={{ time() }}"></script>
<script src="{{ asset('js/pacientes/utils.js')}}?v={{ time() }}"></script>
<script src="{{ asset('js/pacientes/paginacion.js')}}?v={{ time() }}"></script>

@endpush

@endsection