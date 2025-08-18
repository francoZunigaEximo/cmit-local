@extends('template')

@section('title', 'Notas Credito')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Items Anulados</h4>
        <a class="btn btn-warning" type="button" href="{{ route('notasCredito.index') }}"><i class="ri-arrow-left-line"></i> Volver</a>

</div>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-3">
                <label for="fechaDesdeNota">Fecha Desde</label>
                <input type="date" class="form-control" id="fechaDesdeNota">
            </div>
            <div class="col-3">
                <label for="fechaHastaNota">Fecha Hasta</label>
                <input type="date" class="form-control" id="fechaHastaNota">
            </div>
            <div class="col-3">
                <label for="nroPrestacion">Nro Prestacion</label>
                <input type="number" class="form-control" id="nroPrestacion">
            </div>
            <div class="col-3">
                <label for="nroFactura">Nro factura</label>
                <input type="number" class="form-control" id="nroFactura">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12 d-flex justify-content-end">
                <button class="btn btn-primary" id="buscarItemsAnulados">Buscar</button>
            </div>

        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 d-flex justify-content-end">
        <button class="btn botonGeneral add-btn m-1" onclick="reactivarMasivo()">Reactivar Seleccionados <i class="ri-arrow-up-circle-fill"></i></button>
        <button class="btn botonGeneral add-btn m-1" onclick="altaModalMasivo()">Crear Nota de Crédito <i class="ri-file-text-fill"></i></button>
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

<!-- Modal -->
<div class="modal fade" id="modalNuevaNC" tabindex="-1" aria-labelledby="modalNuevaNCLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="modalNuevaNCLabel">Crear Nueva Nota de Crédito</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-12">
                <label for="nroNotaCredito" class="form-label">Nro Nota de Crédito</label>
                <div class="row">
                    <div class="col-2">
                        <input type="text" class="form-control" id="tipo" placeholder="A" required>
                    </div>
                    <div class="col-4">
                        <input type="number" class="form-control" id="sucursal" placeholder="0000" max="9999" min="0" required>
                    </div>
                    <div class="col-6">
                        <input type="number" class="form-control" id="nroNotaCredito" placeholder="00000000" max="99999999" min="0" required>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <label for="fechaNotaCredito" class="form-label">Fecha</label>
                <input type="date" class="form-control" id="fechaNotaCredito" required>
            </div>
            <div class="col-12">
                <label for="observacionNotaCredito" class="form-label">Observacion</label>
                <textarea class="form-control" id="observacionNotaCredito" rows="3" placeholder="Ingrese una observación"></textarea>
            </div>
        </div>
    </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" onclick="altaNotaCredito()">Guardar</button>
      </div>
    </div>
  </div>
</div>

<script>
    const items_facturas = "{{ route('notasCredito.getItemsAnuladosClientes') }}";
    const idCliente = "{{ $idEmpresa }}";
    const reactivarItem = "{{ route('notasCredito.reactivarItem') }}";
    const crearNotaCreditoUrl = "{{ route('notasCredito.crear') }}";
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