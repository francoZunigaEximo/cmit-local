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
                                <a href="{{ route('pacientes.create') }}">
                                    <button type="button" class="btn botonGeneral">
                                        <i class="ri-add-line align-bottom me-1"></i> Nuevo
                                    </button>
                                </a>   
                                <button type="button" id="btnBajaMultiple" class="btn iconGeneral" title="Baja multiple de pacientes">
                                    <i class="ri-delete-bin-2-line"></i>
                                </button>
                                <button type="button" id="excel" class="btn iconGeneral" title="Generar reporte en Excel">
                                    <i class="ri-file-excel-line"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="" style="width: 100%;">
                                <div class="search-box ms-2">
                                    <input type="text" id="buscar" name="buscar" class="form-control search" placeholder="Buscar por Nombre, Apellido o Documento">
                                    <p id="search-instructions" style="font-size: 10px; color: #888;">ENTER para buscar | ESC para limpiar la busqueda</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive table-card mt-3 mb-1">
                        <table id="listaPac" class="display table table-bordered" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th><input type="checkbox" id="checkAll" name="Id"></th>
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
const multipleDown = "{{ route('pacientes.multipleDown') }}";
const exportExcel = "{{ route('excelPacientes') }}";
const bajaPaciente = "{{ route('baja', ['pacientes' => '']) }}";
const down = "{{ route('down') }}";
const getNombre = "{{ route('getNombre') }}";

//Extras
const TOKEN = "{{ csrf_token() }}";
const GOINDEX = "{{ route('pacientes.index') }}";
const ROUTE = "{{ route('pacientes.index') }}";
const SEARCH = "{{ route('search') }}";

</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
@endpush

@push('scripts')
<!--datatable js-->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script src="{{ asset('js/scripts.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/pacientes/index.js')}}?v={{ time() }}"></script>
<script src="{{ asset('js/pacientes/utils.js')}}?v={{ time() }}"></script>
<script src="{{ asset('js/pacientes/paginacion.js')}}?v={{ time() }}"></script>

@endpush

@endsection