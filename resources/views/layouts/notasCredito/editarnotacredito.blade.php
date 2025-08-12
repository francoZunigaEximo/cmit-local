@extends('template')

@section('title', 'Editar Nota Credito')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Editar Nota Credito</h4>
    <a class="btn btn-warning" type="button" href="{{ route('notasCredito.index') }}"><i class="ri-arrow-left-line"></i> Volver</a>
</div>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-6">
                <label for="">Nro Nota Credito</label>
                <div class="row">
                    <div class="col-2">
                        <input type="text" class="form-control" id="tipo" value="{{ $notaCredito->Tipo }}" placeholder="A" required>
                    </div>
                    <div class="col-4">
                        <input type="number" class="form-control" id="sucursal" value="{{ $notaCredito->Sucursal }}" placeholder="0000" max="9999" min="0" required>
                    </div>
                    <div class="col-6">
                        <input type="number" class="form-control" id="nroNotaCredito" value="{{ $notaCredito->Nro }}" placeholder="00000000" max="99999999" min="0" required>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <label for="fecha">Fecha</label>
                <input type="date" class="form-control" id="fecha" name="fecha" value="{{ $notaCredito->Fecha }}" required>
            </div>
            <div class="col-12">
                <label for="descripcion">Descripcion</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ $notaCredito->Obs }}</textarea>
            </div>
            <div class="col-6">
                <label for="cliente">Cliente</label>
                <input type="text" class="form-control" id="cliente" value="{{ $cliente->RazonSocial }}" disabled>
            </div>
            <div class="col-6">
                <label for="cuit">CUIT</label>
                <input type="text" class="form-control" id="cuit" value="{{ $cliente->Identificacion }}" disabled>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                <button class="btn btn-primary" type="button" id="eliminarMasivo" onclick="eliminarMasivo()">Eliminar Items</button>
            </div>
        </div>
    </div>
    
</div>

<div class="card">
    <div class="card-body">
        <table id="itemsNotaCredito" class="display table table-bordered" style="width:100%">
            <thead  class="table-light">
                <tr>
                    <th class="text-center"><input type="checkbox" id="check-todos"></th>
                    <th class="text-center">Examen</th>
                    <th class="text-center">Fecha</th>
                    <th class="text-center">Cliente</th>
                    <th class="text-center">Factura</th>
                    <th class="text-center">Prestacion</th>
                    <th class="text-center">Accion</th>
                </tr>
            </thead>
            <tbody class="list form-check-all" id="lstMensaje">
                <!-- Los items se cargarán aquí mediante JavaScript -->
            </tbody>
        </table>
    </div>
</div>   

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                
                <button class="btn btn-primary" type="button" id="guardarCambios">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
    let idNota = '{{ $notaCredito->Id }}';
    let getItemsNotaCredito = "{{ route('notasCredito.getItemsNotaCredito') }}";
    let editarNotasCreditoPost = "{{ route('notasCredito.editarNotasCreditoPost') }}";
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


<script src="{{ asset('js/notasCredito/editarnotacredito.js')}}?v={{ time() }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>

@endpush
@endsection