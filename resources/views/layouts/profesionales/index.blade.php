@extends('template')

@section('title', 'Profesionales')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Profesionales</h4>

    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="{{ route('profesionales.index') }}">Profesionales</a></li>
            <li class="breadcrumb-item active">Listado</li>
        </ol>
    </div>
</div>


<div class="card">
    <div id="msgProfesionales"></div>

    <div class="row p-3">
        <div class="col-2 p-2 mb-2">
            <label for="especialidad" class="form-label">Especialidad</label>
            <select class="form-control" name="especialidad" id="especialidad"></select>
        </div>

        <div class="col-2 p-2 mb-2">
            <!-- Esto debería ser un select múltiple. -->
            <label for="opciones" class="form-label">Opciones</label>
            <select class="js-example-basic-multiple" name="opciones[]" multiple="multiple" id="opciones" data-placeholder="Opciones ...">
                <option value="pago0">Pago por exámen</option>
                <option value="pago1">Pago por hora</option>
                <option value="inactivo1">Inactivos</option>
                <option value="inactivo0">Activos</option>
                <option value="inactivo2">Baja</option>
            </select>
        </div>
        <div class="col-2 p-2 mb-2">
            <!-- Esto debería ser un select múltiple. -->
            <label for="tipo" class="form-label">Tipo</label>
            <select class="js-example-basic-multiple" name="tipo[]" multiple="multiple" id="tipo" data-placeholder="Elija ...">
                <option value="t2">Informador</option>
                <option value="t1">Efector</option>
                <option value="t4">Combinado</option>
                <option value="t3">Evaluador</option>

            </select>
        </div>
        <div class="col-2 p-2 mb-2">
                <label for="tipo" class="form-label">Buscar</label>
                <div class="search-box ms-2">
                    <input type="text" name="buscar" id="buscar" class="form-control search" placeholder="DNI o Apellido y Nombre...">
                    <i class="ri-search-line search-icon"></i>
                </div>
            
        </div>
        <div class="col-2 p-2 mb-2 d-flex align-items-end justify-content-left pb-3">
            <button type="button" class="btn btn-sm botonGeneral" id="buscarProfesional"><i class="ri-zoom-in-line"></i>&nbsp;Buscar</button>
        </div>
    </div>
    <div class="card-body">
        <div class="listjs-table" id="listaProfesionales">
            <div class="row">
                <div class="col-4">

                    <div>
                        <a href="{{ route('profesionales.create') }}">
                            <button type="button" class="btn botonGeneral"><i class="ri-add-line align-bottom me-1"></i> Nuevo</button>
                        </a>

                        <button class="btn botonGeneral multipleBProf"><i class="ri-forbid-2-line"></i></button>
                        <button class="btn botonGeneral multipleDProf"><i class="ri-delete-bin-2-line"></i></button>
                    </div>
                </div>
                <div class="col-8">
                    
                </div>
            </div>

            <div class="table-responsive table-card mt-3 mb-1">
                <table id="listaProf" class="display table table-bordered" style="width:100%">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th><input type="checkbox" id="checkAll" name="Id"></th>
                            <th class="sort">Apellido y Nombre</th>
                            <th class="sort">DNI</th>
                            <th class="sort">Especialidad</th>
                            <th>Rol</th>
                            <th>Login</th>
                            <th>Pago</th>
                            <th>Estado</th>
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



<script>
    const TOKEN = "{{ csrf_token() }}";
    const SEARCH = "{{ route('searchProfesionales') }}";
    const getProveedores = "{{ route('getProveedores') }}";
    const estadoProfesional = "{{ route('estadoProfesional') }}";

</script>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">
@endpush

@push('scripts')
<!--datatable js-->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

<script src="{{ asset('js/profesionales/index.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/profesionales/paginacion.js') }}?v={{ time() }}"></script>

<script src="{{ asset('js/scripts.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
@endpush
@endsection
