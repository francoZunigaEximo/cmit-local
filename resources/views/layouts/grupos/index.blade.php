@extends('template')

@section('title', 'Pacientes')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Grupo Clientes</h4>
</div>

<div class="container-fluid">
    <div id="mensajeria"></div>
    @csrf
    <div class="row">
        <div class="col-lg-12">
            <form id="form-index">
                <div class="col-12 p-3 border border-1 border-color" style="border-color: #666666;">
                    <div class="row">
                        <div class="col-9 p-1">
                            <label for="nombregrupo" class="form-label">Grupo</label>
                            <input type="text" class="form-control" id="nombregrupo">
                        </div>
                        <div class="col-3 p-1 d-flex align-items-center justify-content-center">
                            <button type="button" class="btn botonGeneral buscarGrupos"><i class="ri-zoom-in-line"></i>&nbsp;Buscar</button>
                        </div>
                    </div>
                </div>
            </form>
            <br />
            <div class="col-12">
                <div>
                    <a href="{{ route('grupos.create') }}" class="btn botonGeneral">
                        <i class="ri-add-line align-bottom me-1"></i> Nuevo
                    </a>
                    <button type="button" class="btn botonGeneral btnExcel">
                        <i class="ri-file-excel-line align-bottom me-1"></i> Exportar
                    </button>
                    <a href="{{ route('grupos.detalle') }}" class="btn botonGeneral">
                        Detalles
                    </a>
                </div>
            </div>
            <table id="listaGrupoClientes" class="table nowrap align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="sort">Numero</th>
                        <th class="sort">Nombre</th>
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
    const TOKEN = "{{ csrf_token() }}";
    const search = "{{ route('grupos.search')}}";
    const exportarExcel = "{{route('grupos.exportExcel')}}";
    const deleteGrupoCliente = "{{ route('grupos.deleteGrupoCliente')}}";
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

<script src="{{ asset('js/grupos/index.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/grupos/paginacion.js') }}?v={{ time() }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
@endpush
@endsection