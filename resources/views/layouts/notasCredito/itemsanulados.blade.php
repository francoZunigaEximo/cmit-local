@extends('template')

@section('title', 'Notas Credito')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-start">
    <h4 class="mb-sm-0">Items Anulados</h4>
</div>
<div class="row">
    <div class="col-12 d-flex justify-content-end">
        <button class="btn botonGeneral add-btn m-1" onclick="reactivarSeleccionados()">Reactivar Seleccionados <i class="ri-arrow-up-circle-fill"></i></button>
        <button class="btn botonGeneral add-btn m-1" onclick="crearNotaCredito()">Crear Nota de Crédito <i class="ri-file-text-fill"></i></button>
    </div>
</div>
<div class="row">
    <div class="table-responsive table-card mt-3 mb-1 mx-auto">
        <table id="tablaItemsAnulados" class="display table table-bordered" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th class="text-center"><input type="checkbox" id="check-todos"></th>
                    <th class="text-center">Fecha Anulado</th>
                    <th class="text-center">Prestación</th>
                    <th class="text-center">Nro Factura</th>
                    <th class="text-center">Examen</th>
                    <th class="text-center">Paciente</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="list form-check-all" id="lstMensaje">

            </tbody>
        </table>
    </div>
</div>
<script>
    const items_facturas = "{{ route('notasCredito.getItemsAnuladosClientes') }}";
    const idCliente = "{{ $idEmpresa }}";
</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">
<link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.3.1/css/rowGroup.dataTables.min.css">
@endpush

@push('scripts')
<!--datatable js-->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/rowgroup/1.3.1/js/dataTables.rowGroup.min.js"></script>


<script src="{{ asset('js/notasCredito/itemsanulados.js')}}?v={{ time() }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>

@endpush
@endsection