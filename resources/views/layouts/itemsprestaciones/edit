@extends('template')

@section('title', 'Exámen Prestación')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Exámen Prestación <span class="badge text-bg-info">N°{{ $itemsprestacione->Id }}</span> QR: </h4>
    <input type="hidden" value="{{ $itemsprestacione->Id }}" id="Id">
    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="{{ route('prestaciones.index') }}">Examenes</a></li>
            <li class="breadcrumb-item active">Editar</li>
        </ol>
    </div>
</div>

<div class="col-xl-12">

    <div class="row">
        <div class="col-2">
            <label for="Fecha" class="form-label"> Fecha Estudio</label>
            <div class="mb-3">
                <input type="date" class="form-control" id="Fecha" name="Fecha" value="{{ $itemsprestacione->Fecha ?? ''}}">
            </div>
        </div>

        <div class="col-10">
            <label for="Examen" class="form-label">Exámen</label>
            <div class="mb-3">
                <input type="text" class="form-control" id="Examen" name="Examen" value="{{ $itemsprestacione->examenes->Nombre ?? ''}}" @readonly(true)>
            </div>
        </div>

        <div class="col-6">
            <label for="provEfector" class="form-label">Proveedor Efector</label>
            <input type="text" class="form-control" name="provEfector" id="provEfector" value="{{ $itemsprestacione->examenes->proveedor1->Nombre ?? ''}}" @readonly(true)>
            <input type="hidden" id="IdEfector" value="{{ $itemsprestacione->examenes->proveedor1->Id ?? '' }}">
        </div>

        <div class="col-6">
            <label for="provInformador" class="form-label">Proveedor Informador</label>
            <input type="text" class="form-control" name="provInformador" id="provInformador" value="{{ $itemsprestacione->examenes->proveedor2->Nombre ?? '' }}" @readonly(true)>
            <input type="hidden" id="IdInformador" value="{{ $itemsprestacione->examenes->proveedor2->Id ?? '' }}">
        </div>

        <div class="col-12 mt-4 mb-4">
            <label for="ObsExamen" class="form-label">Observaciones</label>
            <textarea class="form-control" name="ObsExamen" id="ObsExamen">{{ $itemsprestacione->ObsExamen ?? '' }}</textarea>
        </div>

        <hr>

        <div class="col-3">
            <label for="Efector" class="form-label">Efector</label>
            <select name="efectores" id="efectores" class="form-control">
                <option value="{{ $itemsprestacione->profesionales1->Id ?? '' }}" selected>{{ $itemsprestacione->profesionales1->Apellido ?? '' }} {{ $itemsprestacione->profesionales1->Nombre ?? '' }}</option>
            </select>
        </div>

        <div class="col-2">
            <label for="FechaAsignado">Fecha de asignación</label>
            <div class="mb-3">
                <input type="date" class="form-control" id="FechaAsignado" name="FechaAsignado" value="{{ $itemsprestacione->FechaAsignado ?? ''}}">
            </div>
        </div>

        <div class="col-2">
            <label for="FechaPagado" class="form-label">Fecha de pago</label>
            <div class="mb-3">
                <input type="date" class="form-control" id="FechaPagado" name="FechaPagado" value="{{ $itemsprestacione->FechaPagado ?? ''}}" @readonly(true)>
            </div>
        </div>

        <div class="col-2">
            <label for="Estado" class="form-label">Estado <span class="badge text-bg-{{ (in_array($itemsprestacione->CAdj, [0, 1, 2]) ? 'success' : (in_array($itemsprestacione->CAdj, [3, 4, 5]) ? 'danger' : '')) ?? ''}}">{{ (in_array($itemsprestacione->CAdj, [0, 1, 2]) ? 'Abierto' : (in_array($itemsprestacione->CAdj, [3, 4, 5]) ? 'Cerrado' : '')) ?? ''}}</span></label>
            <div class="mb-3">
                <button type="button" id="abrir" class="btn btn-primary btn-sm abrir">Abrir</button>
                <button type="button" id="cerrar" class="btn btn-secondary btn-sm cerrar">Cerrar</button>
                <button type="button" id="asignar" class="btn btn-warning btn-sm asignar">Asignar</button>
                <button type="button" id="liberar" class="btn btn-danger btn-sm liberar">Liberar</button>
                <input type="hidden" value="{{ $itemsprestacione->CAdj }}" id="CAdj">
            </div>
        </div>

        <div class="col-3">
            <label for="Adjuntos" class="form-label">Adjuntos <span class="badge text-bg-success">{{ (in_array($itemsprestacione->CAdj, [2,5]) ? 'Adjunto' : (in_array($itemsprestacione->CAdj, [1,4]) ? 'Pendiente' : (in_array($itemsprestacione->CAdj, [0,3]) ? 'NA' : ' - ')))  ?? ''}}</span></label>
            <div class="mb-3">
                <button id="adjuntos" type="button" data-adjuntoefector="{{ $itemsprestacione->CAdj ?? ''}}" class="btn btn-primary btn-sm">
                  {{  (in_array($itemsprestacione->CAdj, [1,4]) ? 'Pendiente' : (in_array($itemsprestacione->CAdj, [2,5]) ? 'Adjunto' : (in_array($itemsprestacione->CAdj, [0,3]) ? 'NA' : ' - '))) ?? ''}}
                </button>
            </div>
        </div>
            
        <div class="col-3">
            <label for="Informador class="form-label">Informador</label>
            <select name="informadores" id="informadores" class="form-control">
                <option value="{{ $itemsprestacione->profesionales2->Id ?? '' }}" selected>{{ $itemsprestacione->profesionales2->Apellido ?? '' }} {{ $itemsprestacione->profesionales2->Nombre ?? '' }}</option>
            </select>
        </div>

        <div class="col-2">
            <label for="EstadoInf" class="form-label">Estado <span class="badge text-bg-{{ ($itemsprestacione->CInfo === 1 ? 'success' : ($itemsprestacione->CInfo === 2 ? 'warning' : ($itemsprestacione->CInfo === 3 ? 'danger' : 'secondary'))) ?? ''}}">{{ ($itemsprestacione->CInfo === 1 ? 'Pediente' : ($itemsprestacione->CInfo === 2 ? 'Borrador' : ($itemsprestacione->CInfo === 3 ? 'Cerrado' : ' - sin datos -'))) ?? ''}}</span></label>
            <div class="mb-3">
                <button type="button" data-estadoinformador="{{ $itemsprestacione->CInfo ?? '' }}"class="btn btn-primary btn-sm">{{ (in_array($itemsprestacione->CInfo, [1, 2]) ? 'Cerrado' : ($itemsprestacione->CInfo === 3 ? "Completa" : ' - sin datos - ')) ?? '' }}</button>
            </div>
        </div>

        <div class="col-3">
            <label for="FechaPagado2" class="form-label">Pagado</label>
            <input type="text" id="FechaPagado2" name="FechaPagado2" class="form-control" value="{{ $itemsprestacione->FechaPagado2}}" @readonly(true)>
        </div>

        <div class="col-12 mt-4 mb-4">
            <label for="Obs" class="form-label">Observaciones</label>
            <textarea class="form-control" name="Obs" id="Obs">{!! $itemsprestacione->itemsInfo->Obs ?? '' !!}</textarea>
        </div>

        <div class="col-lg-12">
            <div class="hstack gap-2 justify-content-end">

                <button type="button" class="btn btn-soft-secondary" id="btnVolver">Volver</button>
                <a class="btn btn-success" id="actualizarExamen">Guardar</a>
            </div>
        </div>

        <div class="col-2">
            <label for="FechaFacturaVta" class="form-label">Fecha facturación</label>
            <div class="mb-3">
                <input type="date" class="form-control" id="FechaFacturaVta" name="FechaFacturaVta" value="{{ $itemsprestacione->facturadeventa->Fecha ?? ''}}" @readonly(true)>
            </div>
        </div>

        <div class="col-2">
            <label for="FechaNC">Nota crédito</label>
            <div class="mb-3">
                <input type="text" class="form-control" id="FechaNC" name="FechaNC" value="{{ $itemsprestacione->notaCreditoIt->notaCredito->Tipo ?? ''}}{{ $itemsprestacione->notaCreditoIt->notaCredito->Sucursal ?? ''}}{{ $itemsprestacione->notaCreditoIt->notaCredito->Nro ?? ''}}" @readonly(true)>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive table-card mt-3 mb-1">

    <table class="display table table-bordered mb-4" style="width:100%"  id="listadoEfector">
        <thead class="table-light">
            <tr>
                <th class="sort" title="Exámen">Exámen</th>
                <th>Descripción</th>
                <th>Adjunto</th>
                <th>Multi</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="listaefectores" class="list form-check-all">

        </tbody>
    </table>

    <table class="display table table-bordered mt-4" style="width:100%"  id="listadoInformador">
        <thead class="table-light">
            <th class="sort" title="Exámen">Exámen</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </thead>
        <tbody id="listainformadores" class="list form-check-all">

        </tbody>
    </table>

</div>
<script>
    const TOKEN = '{{ csrf_token() }}';

    const updateItem = "{{ route('updateItem') }}";
    const updateEfector = "{{ route('updateEfector') }}";
    const listGeneral = "{{ route('listGeneral') }}";
    const updateAdjunto = "{{ route('updateAdjunto') }}";
    const paginacionGeneral = "{{ route('paginacionGeneral') }}";
    const updateExamen = "{{ route('updateExamen') }}";

</script>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/itemsprestaciones/edit.js') }}?v={{ time() }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('js/fancyTable.js') }}"></script>

@endpush

@endsection