@extends('template')

@section('title', 'Examenes a cuenta')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Examenes a cuenta</h4>

    <div class="page-title-right"></div>
</div>

<div class="row mt-2 mb-2">
    <div class="small col-sm-12 mb-2"><span class="required">(*)</span> El campo es obligatorio. | <span class="required">(**)</span> La cantidad debe ser mayor a cero y obligatoria | <span class="required">(***)</span> Solo puede elegir una opción por aplicación</div>
</div>

<div class="row justify-content-md-center">
    <form id="form-index">
        <div class="col p-2 border border-1 border-color" style="border-color: #666666;">
            
            <div class="row justify-content-center">
                <div class="col-sm-2 mb-3">
                    <label for="Id" class="form-label fw-bolder">Número: </label>
                    <input type="text" id="Id" class="form-control" value="{{ sprintf('%06d', $examenesCuentum->Id)  ?? 0 }}" disabled>
                </div>

                <div class="col-sm-2 mb-3">
                    <label for="empresa" class="form-label fw-bolder">Empresa: <span class="required small">(*)</span></label>
                    <select class="form-control" name="empresa" id="empresa">
                        <option value="{{ $examenesCuentum->IdEmpresa ?? '' }}">{{ $examenesCuentum->empresa->RazonSocial ?? '' }}</option>
                    </select>
                </div>

                <div class="col-sm-2 mb-3">
                    <label for="Fecha" class="form-label fw-bolder">Fecha: <span class="required small">(*)</span></label>
                    <input type="date" id="Fecha" name="Fecha" class="form-control" value="{{ $examenesCuentum->Fecha ?? '' }}">
                </div>

                <div class="col-sm-2 mb-3">
                    <label for="Factura" class="form-label fw-bolder">Nro. Factura: <span class="required small">(*)</span></label>
                    <input type="text" id="Factura" name="Factura" class="form-control" value="{{$examenesCuentum->Tipo ?? ''}}-{{ sprintf('%04d', $examenesCuentum->Suc) ?? ''}}-{{ sprintf('%08d', $examenesCuentum->Nro) ?? '' }}">
                </div>

                <div class="col-sm-2 mb-3">
                    <label for="FechaPago" class="form-label fw-bolder">Fecha Pago: </label>
                    <input type="date" id="FechaPago" name="FechaPago" value="{{ $examenesCuentum->FechaP ?? ''}}" class="form-control">
                </div>

                <div class="col-sm-2 mb-3">
                    <label for="Obs" class="form-label fw-bolder">Observación: </label>
                    <textarea class="form-control" name="Obs" id="Obs" cols="30" rows="3">{{ $examenesCuentum->Obs ?? ''}}</textarea>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-sm-4 mb-3 text-center">
                    <button type="button" class="btn btn-sm botonGeneral actualizarPagoCuenta">Actualizar</button>
                    <button type="button" class="btn btn-sm botonGeneral volverPrincipal">Volver</button>
                </div>
            </div>
        </div>
    </form>  
</div> 

<div class="row">
    <div class="col-12 p-4 mt-3 border border-1 border-color" style="border-color: #666666;">
        <div class="row">

            <div class="col-sm-2 mb-3 text-center">
                <label for="dni" class="form-label fw-bolder">DNI Paciente:</label>
                <input type="number" max="8" name="dni" id="dni" class="form-control">
            </div>

            <div class="col-sm-2 mb-3 text-center">
                <label for="cantidad" class="form-label fw-bolder">Cantidad: <span class="required small">(**)</span></label>
                <input type="number" max="5" id="cantidad" name="cantidad" class="form-control" value="1">
            </div>

            <div class="col-sm-2 mb-3 text-center">
                <label for="examen" class="form-label fw-bolder">Examen: <span class="required small">(***)</span></label>
                <select name="examen" id="examen" class="form-control"></select>
            </div>

            <div class="col-sm-2 mb-3 text-center">
                <label for="paquete" class="form-label fw-bolder">Paquete: <span class="required small">(***)</span></label>
                <select name="paquete" id="paquete" class="form-control"></select>    
            </div>

            <div class="col-sm-2 mb-3 text-center">
                <label for="facturacion" class="form-label fw-bolder">Paq de Facturación: <span class="required small">(***)</span></label>
                <select name="facturacion" id="facturacion" class="form-control"></select> 
            </div>

            <div class="col-sm-2 mb-3 justify-content-center d-flex align-items-center">
                <button type="button" class="btn btn-sm botonGeneral aplicar"><i class="ri-file-add-line"></i>&nbsp;Aplicar</button>
            </div>

        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-sm-7 text-end">
        <button type="button" class="btn btn-sm botonGeneral editarMasivo" data-bs-toggle="modal" data-bs-target="#editarDNI"><i class="ri-edit-line"></i> Editar</button>
        <button type="button" class="btn btn-sm botonGeneral liberarItemMasivo"><i class="ri-logout-circle-line"></i> Liberar</button>
        <button type="button" class="btn btn-sm botonGeneral deleteItemMasivo"><i class="ri-delete-bin-2-line"></i> Eliminar</button>
    </div>
    <div class="col-sm-5 text-center">
        <button type="button" data-id="{{ $examenesCuentum->Id }}" class="btn btn-sm botonGeneral exportar"><i class="ri-file-excel-line"></i>Exportar</button>
        <button type="button" data-id="{{ $examenesCuentum->Id }}" class="btn btn-sm botonGeneral imprimir"><i class=" ri-file-pdf-line"></i>Imprimir</button>
    </div>
</div>

<div class="row auto-mx mb-3">
    <div class="table-responsive table-card mt-3 mb-1 mx-auto col-sm-7">
        <table id="listadoSaldos" class="display table table-bordered">
            <thead class="table-light">
                <tr>
                    <th class="text-center"><input type="checkbox" id="checkAll" name="Id"></th>
                    <th class="sort">Precarga</th>
                    <th class="sort">Examen</th>
                    <th class="sort">Prestación</th>
                    <th class="sort">Paciente</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="list form-check-all" id="lstSaldos">

            </tbody>
        </table>

    </div>
</div>

<div id="editarDNI" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Editar precarga de DNI</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body text-center p-5">
                <label for="dniNuevo" class="form-label">Escribir el nuevo DNI</label>
                <input type="number" max="8" class="form-control" name="dniNuevo" id="dniNuevo">
                <input type="hidden" id="cargarId" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn botonGeneral" data-bs-dismiss="modal">Cancelar edición</button>
                <button type="button" class="btn botonGeneral saveCambiosEdit">Modificar</button>
            </div>
        </div>
    </div>
</div>

<script>
    const getPacientes = "{{ route('getPacientes') }}";
    const searchExamen = "{{ route('searchExamen') }}";
    const saveExamenCuenta = "{{ route('saveExamenCuenta') }}";
    const INDEX = "{{ route('examenesCuenta.index') }}";
    const getPaquetes = "{{ route('getPaquetes') }}";
    const getPaqueteFact = "{{ route('getPaqueteFact') }}";
    const ID = "{{ $examenesCuentum->Id }}";
    const listadoExCta = "{{ route('listadoExCta') }}";
    const updateExamenCuenta = "{{ route('updateExamenCuenta') }}";
    const deleteItemExCta = "{{ route('deleteItemExCta') }}";
    const liberarItemExCta = "{{ route('liberarItemExCta') }}";
    const savePrecarga = "{{ route('savePrecarga') }}";
    const savePaquete = "{{ route('savePaquete') }}";
    const exportExcel = "{{ route('exportExcel') }}";
    const exportPDF = "{{ route('exportPDF') }}";

</script>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/inputmask/dist/jquery.inputmask.min.js"></script>
<script src="{{ asset('js/examenescuenta/edit.js')}}?=v{{ time() }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
<script src="{{ asset('js/fancyTable.js') }}"></script>
@endpush

@endsection