@extends('template')

@section('title', 'Especialidades')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Especialidades</h4>
</div>

<div class="row">

    <div class="col-12">

        <div class="card">
            <div id="msgProfesionales"></div>

            <div class="row p-3">
                <div class="col-3 p-2 mb-2">
                    <label for="especialidad" class="form-label">Especialidad</label>
                    <input class="form-control" type="text" name="especialidad" id="especialidad">
                </div>
            
                <div class="col-3 p-2 mb-2">
                    <label for="opciones" class="form-label">Opciones</label>
                    <select class="form-control" name="opciones" id="opciones">
                        <option value="" selected>Elija una opción...</option>
                        <option value="Todo">Todo</option>
                        <option value="Interno">Interno</option>
                        <option value="Externo">Externo</option>
                        <option value="Multi">Multi Adjunto</option>
                        <option value="MultiE">Multi Exámen</option>
                    </select>
                </div>
            
                <div class="col-2 p-2 mb-2 d-flex align-items-end">
                    <button type="button" id="buscar" class="btn botonGeneral"><i class="ri-zoom-in-line"></i> Buscar</button>
                </div>

                <div class="col-4 p-2 mb-2 d-flex align-items-end justify-content-end">
                    <div class="mx-1">
                        <a href="{{ route('especialidades.create') }}">
                            <button type="button" class="btn botonGeneral"><i class="ri-add-line"></i> Nuevo</button>
                        </a>
                    </div>
                    <div class="mx-1">
                        <button type="button" id="excel" class="btn botonGeneral" title="Excel">
                            <i class="ri-file-excel-line"></i> Excel
                        </button>
                    </div>
                    <div class="mx-1">
                        <button type="button" id="multiple" class="btn botonGeneral" title="Bloquear">
                            <i class="ri-forbid-2-line"></i> Inhabilitar Mult
                        </button>
                    </div>
                </div>
            </div>
        </div>

  
        <div class="table-responsive mt-3 mb-1">
            
            <table id="listaEspecialidades" class="table-bordered nowrap">
                <thead class="table-light">
                    <tr>
                        <th><input type="checkbox" id="checkAll" name="Id"></th>
                        <th class="sort">Proveedor</th>
                        <th>Ubicación</th>
                        <th>Teléfono</th>
                        <th>Direccion</th>
                        <th>Localidad</th>
                        <th>Adjunto</th>
                        <th>Exámen</th>
                        <th>Informe</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="list form-check-all">
                    
                </tbody>
            </table>
        </div>

    </div>

</div>



   

        
    


<script>
    const SEARCH = "{{ route('especialidades.index') }}";
    const especialidadExcel = "{{ route('especialidadExcel') }}";
    const multiDownEspecialidad = "{{ route('multiDownEspecialidad') }}";
    const bajaEspecialidad = "{{ route('bajaEspecialidad') }}";
</script>


@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v=?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
@endpush


@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script src="{{ asset('js/especialidades/index.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/especialidades/paginacion.js') }}?v={{ time() }}"></script>

<script src="{{ asset('js/scripts.js') }}?v={{ time() }}"></script>
@endpush

@endsection
