@extends('template')

@section('title', 'Auditoria')

@section('content')
<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Auditoria de envios</h4>
    <a class="btn btnSuccess" href="{{ route('mensajes.index') }}"><i class="ri-arrow-go-back-line"></i>Volver</a>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-3">
                <label for="fechaDesde" class="form-label fw-bolder">Fecha Desde:</label>
                <input class="form-control" type="date" id="fechaDesde" name="fechaDesde">
            </div>
            <div class="col-sm-3">
                <label for="fechaHasta" class="form-label fw-bolder">Fecha Hasta:</label>
                <input class="form-control" type="date" id="fechaHasta" name="fechaHasta">
            </div>
            <div class="col-sm-6 d-flex align-items-end justify-content-end">
                <button class="btn btn-sm botonGeneral buscar"><i class="ri-search-line"></i>Buscar</button>&nbsp;
                <a class="btn btn-sm botonGeneral" href="{{ route('mensajes.auditoria') }}"><i class="ri-refresh-line"></i>Reiniciar</a>&nbsp;
                
            </div>
        </div>
    </div>
</div>

<div class="table-responsive table-card mt-3 mb-1 mx-auto">
    <table id="listaAuditorias" class="display table table-bordered" style="width:100%">
        <thead class="table-light">
            <tr>  
                <th>Fecha</th>
                <th class="sort">Asunto</th>
                <th>Destinatarios</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody class="list form-check-all" id="lstAuditorias">

        </tbody>
    </table>
</div>

<div id="modalDestinatarios" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel3" aria-hidden="true" style="display: none">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Lista de Destinatarios</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="verDestinatarios">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn botonGeneral" data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="modalHistorial" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel3" aria-hidden="true" style="display: none">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Mensaje enviado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="verMensaje"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn botonGeneral" data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    const SEARCH = "{{ route('mensajes.auditoria') }}";
    const verAuditoria = "{{ route('verAuditoria') }}";
</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script src="{{ asset('js/mensajeria/auditoria.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/mensajeria/paginacionAuditoria.js') }}?v={{ time() }}"></script>
@endpush
@endsection