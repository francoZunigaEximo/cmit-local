@extends('template')

@section('title', 'Detalle Grupos')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Detalle Grupo Clientes</h4>
</div>

<div class="container-fluid">
    <div id="mensajeria"></div>
    @csrf
    <div class="row">
        <div class="col-lg-12">
            <form id="form-index">
                <div class="col-12 p-3 border border-1 border-color" style="border-color: #666666;">
                    <div class="row">
                        <div class="col-3 p-1">
                            <label for="grupoSelect2" class="form-label">Grupo</label>
                            <select name="grupoSelect2" class="form-control" id="grupoSelect2">
                            </select>
                        </div>
                        <div class="col-3 p-1">
                            <label for="empresaSelect2" class="form-label">Cliente</label>
                            <select name="empresaSelect2" class="form-control" id="empresaSelect2">
                            </select>
                        </div>
                        <div class="col-3 p-1">
                            <label for="nrocliente" class="form-label">Nro Cliente</label>
                            <input type="text" class="form-control" id="nrocliente">
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
                    <button type="button" class="btn botonGeneral btnExcel">
                        <i class="ri-file-excel-line align-bottom me-1"></i> Exportar
                    </button>
                </div>
            </div>
            <table id="listaGrupoClientes" class="table nowrap align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="sort">Grupo</th>
                        <th class="sort">NroCliente</th>
                        <th class="sort">Razon Social</th>
                        <th class="sort">Para Empresa</th>
                        <th class="sort">CUIT</th>
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
    const search = "{{ route('grupos.detalleSearch') }}";
    const exportarExcel = "{{route('grupos.exportDetalleExcel')}}";
    const getClientes = "{{ route('getClientes') }}";
    const getGrupos = "{{route('getGrupos')}}";
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

<script src="{{ asset('js/grupos/detalle.js') }}?v={{ time() }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
@endpush
@endsection