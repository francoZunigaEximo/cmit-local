@extends('template')

@section('title', 'Craer grupo clientes')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Nuevo Grupo Clientes</h4>
    <a href="{{ route('paquetes.index') }}" class="btn botonGeneral">Volver</a>
</div>
<div class="container-fluid">
     <div id="mensajeria"></div>
    @csrf
    <div class="col-12 p-3 border border-1 border-color" style="border-color: #666666;">
        <div class="row">
            <div class="col-2 p-1">
                <div>
                    <label for="" class="form-label">Codigo  <span class="required" aria-required="true">(*)</span>:</label>
                    <input type="text" class="form-control" id="idGrupo" value="{{$grupo->Id}}" disabled>
                </div>
            </div>
            <div class="col-10 p-1">
                <div>
                    <label for="" class="form-label">Nombre  <span class="required" aria-required="true">(*)</span>:</label>
                    <input type="text" class="form-control" id="nombregrupo" value="{{$grupo->Nombre}}">
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 p-3 border border-1 border-color mt-1" style="border-color: #666666;">
        <div class="row">
            <div class="col-10 p-1">
                <div>
                    <label for="empresaSelect2" class="form-label">Empresa:</label>
                    <select name="empresaSelect2" class="form-control" id="empresaSelect2">
                    </select>
                </div>
            </div>
            <div class="col-2 p-1 d-flex align-items-end justify-content-center">
                <div>
                    <button type="button" class="btn add-btn agregarCliente" data-bs-toggle="offcanvas">
                        <i class="ri-add-circle-line align-bottom me-1" style="font-size: 2em;"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>
    <table id="listaEmpresas" class="table nowrap align-middle">
        <thead class="table-light">
            <tr>
                <th class="sort">Numero</th>
                <th class="sort">Razon Social</th>
                <th class="sort">Para Empresa</th>
                <th class="sort">CUIT</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody class="list form-check-all">

        </tbody>
    </table>
    <div class="col-12 box-information mt-2 text-center">
        <button type="submit" id="btnRegistrar" class="btn botonGeneral">Editar</button>
    </div>
</div>

<script>
    const TOKEN = "{{ csrf_token() }}";
    const getClientes = "{{ route('getClientes') }}";
    const getCliente = "{{ route('grupos.getCliente')}}";
    const getEmpresasGrupoCliente = "{{ route('grupos.getEmpresasGrupoCliente') }}";
    const postEditGrupoCliente = "{{route('grupos.postEditGrupoCliente')}}";
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

<script src="{{ asset('js/grupos/edit.js') }}?v={{ time() }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
@endpush
@endsection