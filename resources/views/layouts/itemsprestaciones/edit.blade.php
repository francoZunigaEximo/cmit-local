@extends('template')

@section('title', 'Exámen Prestación')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Exámen prestación <span class="custom-badge original">N°{{ $itemsprestacione->IdPrestacion }}</span> | Paciente <span class="custom-badge original">{{ $data['paciente']->paciente->Nombre ?? ''}} {{ $data['paciente']->Apellido ?? '' }}</span> {!! ($itemsprestacione->Anulado === 1) ? '<span class="custom-badge rojo">Bloqueado</span>' : '' !!}</h4>
    <input type="hidden" value="{{ $itemsprestacione->Id }}" id="Id">
    <div class="page-title-right d-inline">
        <p><strong>QR:</strong> {{ $data['qrTexto'] ?? ''}}</p>
    </div>
</div>

<div class="container-fluid">

    <div class="row mb-3">
        <div class="col-12 mx-auto box-information">
            <div class="messageExamen"></div>

            <div class="row">
                <input type="hidden" id="identificacion" value="{{ $itemsprestacione->Id }}">
                <input type="hidden" id="prestacion" value="{{ $itemsprestacione->IdPrestacion}}">

                <div class="col-6">
                    <div class="input-group input-group-sm mb-2 size50porcent">
                        <span class="input-group-text">Fecha Estudio</span>
                        <input type="date" name="Fecha" id="Fecha" class="form-control" value="{{ $itemsprestacione->prestaciones->Fecha ?? ''}}">
                    </div>

                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">Exámen</span>
                        <input type="text" name="Examen" id="Examen" class="form-control" value="{{ $itemsprestacione->examenes->Nombre ?? ''}}" @readonly(true)>
                    </div>
                </div>
                <div class="col-6"></div>

            </div>

            <div class="row">
                <div class="col-6">
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">Especialidad Efector</span>
                        <input type="text" class="form-control" name="provEfector" id="provEfector" value="{{ $itemsprestacione->examenes->proveedor1->Nombre ?? ''}}" @readonly(true)>
                        <input type="hidden" id="IdEfector" value="{{ $itemsprestacione->examenes->proveedor1->Id ?? '' }}">
                    </div>
                </div>
                <div class="col-6">
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">Especialidad Informador</span>
                        <input type="text" class="form-control" name="provInformador" id="provInformador" value="{{ $itemsprestacione->examenes->proveedor2->Nombre ?? '' }}" @readonly(true)>
                        <input type="hidden" id="IdInformador" value="{{ $itemsprestacione->examenes->proveedor2->Id ?? '' }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">Observaciones</span>
                        <textarea class="form-control" style="height: 80px" id="ObsExamen" name="ObsExamen">{{ strip_tags($itemsprestacione->ObsExamen) ?? '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12 mx-auto box-information">

            <div class="row">
                <div class="col-md-4">
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">Efector</span>
                        <select name="efectores" id="efectores" class="form-control">
                            <option value="{{ $itemsprestacione->profesionales1->Id ?? '' }}" selected>
                                {{ $itemsprestacione->profesionales1->Apellido ?? '' }} {{ $itemsprestacione->profesionales1->Nombre ?? '' }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">Fecha asig.</span>
                        <input type="date" class="form-control" id="FechaAsignado" name="FechaAsignado" value="{{ $itemsprestacione->FechaAsignado ?? ''}}" @readonly(true)>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">Estado</span>
                        <input type="text" class="form-control" style="color: {{ (in_array($itemsprestacione->CAdj, [0, 1, 2]) ? 'red' : (in_array($itemsprestacione->CAdj, [3, 4, 5]) ? 'green' : '')) ?? ''}}" id="Estado" name="Estado" value="{{ (in_array($itemsprestacione->CAdj, [0, 1, 2]) ? 'Abierto' : (in_array($itemsprestacione->CAdj, [3, 4, 5]) ? 'Cerrado' : '')) ?? ''}}" @readonly(true)>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">Fecha Pago</span>
                        <input type="date" class="form-control" id="FechaPagado" name="FechaPagado" value="{{ $itemsprestacione->FechaPagado ?? ''}}" @readonly(true)>
                    </div>
                </div>

                <div class="col-md-2">
                    {!! ($itemsprestacione->Anulado === 1) ? '' : '<button type="button" id="asignar" class="btn botonGeneral btn-sm asignar">Asignar</button>' !!}
                    <button type="button" id="liberar" class="btn botonGeneral btn-sm liberar">Liberar</button>
                    {!! ($itemsprestacione->Anulado === 1) ? '' : '<button type="button" id="abrir" class="btn botonGeneral btn-sm abrir">Abrir</button>' !!}
                    {!! ($itemsprestacione->Anulado === 1) ? '' : '<button type="button" id="cerrar" class="btn botonGeneral btn-sm cerrar">Cerrar</button>' !!}
                   <input type="hidden" value="{{ $itemsprestacione->CAdj }}" id="CAdj">
                </div>

            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">Adjunto</span>
                        <input type="text" style="{{ ($itemsprestacione->examenes->Adjunto === 1 && !empty($adjuntoEfector) && $adjuntoEfector === 0 ? 'color: red' : ($itemsprestacione->examenes->Adjunto === 1 && !empty($adjuntoEfector) && $adjuntoEfector === 1 ? 'color: green' : '')) }}" class="form-control" id="Estado" name="Estado" value="{{ ($itemsprestacione->examenes->Adjunto === 0 ? 'No lleva Adjuntos' : ($itemsprestacione->examenes->Adjunto === 1 && !empty($adjuntoEfector) && $adjuntoEfector === 0 ? 'Pendiente' : ($itemsprestacione->examenes->Adjunto === 1 && !empty($adjuntoEfector) && $adjuntoEfector === 1 ? 'Adjuntado' : '-'))) }}" @readonly(true)>
                        <button type="button" class="btn botonGeneral adjuntarEfector" data-bs-toggle="modal" data-bs-target="#modalEfector">Adjuntar archivo</button>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12 mx-auto box-information">

            <div class="row">
                <div class="col-md-4">
                    <div class="input-group input-group-sm mb-2">
                        @if($itemsprestacione->CInfo !== 0)
                        <span class="input-group-text">Informador</span>
                            <select name="informadores" id="informadores" class="form-control">
                                <option value="{{ $itemsprestacione->profesionales2->Id ?? '' }}" selected>{{ $itemsprestacione->profesionales2->Apellido ?? '' }} {{ $itemsprestacione->profesionales2->Nombre ?? '' }}</option>
                            </select>
                        @endif
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="input-group input-group-sm mb-2">     

                        @if($itemsprestacione->CInfo !== 0)
                            <span class="input-group-text">Estado</span>
                            <input type="text" class="form-control" style="color: {{ ($itemsprestacione->CInfo === 0 || $itemsprestacione->CInfo === 1 || $itemsprestacione->CInfo === 2 ? 'red' : ($itemsprestacione->CInfo === 3 ? 'green' : '')) ?? ''}}" id="EstadoI" name="EstadoI" value="{{ (in_array($itemsprestacione->CInfo, [0,1]) ? 'Pediente' : ($itemsprestacione->CInfo === 2 ? 'Borrador' : ($itemsprestacione->CInfo === 3 ? 'Cerrado' : ''))) ?? ''}}" @readonly(true)>

                        @endif
                    </div>
                </div>

                <div class="col-md-2">
                    @if($itemsprestacione->CInfo !== 0)
                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text">Fecha Pago</span>
                            <input type="date" id="FechaPagado2" name="FechaPagado2" class="form-control" value="{{ $itemsprestacione->FechaPagado2}}" @readonly(true)>
                        </div>
                    @endif
                </div>

                <div class="col-md-2">

                    @if($itemsprestacione->CInfo !== 0)
                        {!! ($itemsprestacione->Anulado === 1) ? '' : '<button type="button" id="asignarI" class="btn botonGeneral btn-sm asignarI">Asignar</button>' !!}
                        <button type="button" id="liberarI" class="btn botonGeneral btn-sm liberarI">Liberar</button>
                        {!! ($itemsprestacione->Anulado === 1) ? '' : '<button type="button" id="cerrarI" class="btn botonGeneral btn-sm cerrarI">Cerrar</button>' !!}
                        <input type="hidden" value="{{ $itemsprestacione->CInfo }}" id="CInfo">
                    @endif
                </div>

            </div>
            @if($itemsprestacione->CInfo !== 0)
            <div class="row">
                <div class="col-md-3">
                    <div class="input-group input-group-sm mb-2">

                        <span class="input-group-text">Adjunto</span>
                        <input type="text" style="{{ ($itemsprestacione->profesionales2->InfAdj === 1 && $data['adjuntoInformador'] === 0 ? 'color: red' : ($itemsprestacione->profesionales2->InfAdj === 1 && $data['adjuntoInformador'] === 1 ? 'color: green' : '')) }}" class="form-control" id="EstadoInf" name="EstadoInf" value="{{ ($itemsprestacione->profesionales2->InfAdj === 0 ? 'No lleva Adjuntos' : ($itemsprestacione->profesionales2->InfAdj === 1 && $data['adjuntoInformador'] === 0 ? 'Pendiente' : ($itemsprestacione->profesionales2->InfAdj === 1 && $data['adjuntoInformador'] === 1 ? 'Adjuntado' : '-'))) }}" @readonly(true)>
                        <button type="button" class="btn botonGeneral adjuntarInformador"  data-bs-toggle="modal" data-bs-target="#modalInformador">Adjuntar archivo</button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @if($itemsprestacione->CInfo !== 0)
    <div class="row mb-3">
        <div class="col-12 mx-auto box-information">
            <div class="input-group input-group-sm mb-2">
                <span class="input-group-text">Observaciones Informador</span>
                <textarea class="form-control" style="height: 80px" name="Obs" id="Obs" disabled>{!! isset($itemsprestacione->itemsInfo) ? strip_tags($itemsprestacione->itemsInfo->Obs) : '' !!}</textarea>
            </div>
        </div>
    </div>
    @endif

    <div class="row mb-3">
        <div class="col-12 mx-auto box-information">

            <div class="row">
                <div class="col-md-4">
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">Fecha Factura</span>
                        <input type="date" class="form-control" id="FechaFacturaVta" name="FechaFacturaVta" value="{{ $itemsprestacione->facturadeventa->Fecha ?? ''}}" @readonly(true)>
                    </div>
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">Nro Factura</span>
                        <input type="text" class="form-control" id="NroFacturaVta" name=" NroFacturaVta" value="{{ $itemsprestacione->facturadeventa->Tipo ?? ''}}{{ $itemsprestacione->facturadeventa->Sucursal ?? ''}}{{ $itemsprestacione->facturadeventa->NroFactura ?? ''}}" @readonly(true)>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">Fecha NC</span>
                        <input type="date" class="form-control" id="FechaNC" name="FechaNC" value="{{ $itemsprestacione->notaCreditoIt->notaCredito->Fecha ?? ''}}" @readonly(true)>
                    </div>
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">Número NC</span>
                        <input type="text" class="form-control" id="NumeroNC" name="NumeroNC" value="{{ $itemsprestacione->notaCreditoIt->notaCredito->Tipo ?? ''}}{{ $itemsprestacione->notaCreditoIt->notaCredito->Sucursal ?? ''}}{{ $itemsprestacione->notaCreditoIt->notaCredito->Nro ?? ''}}" @readonly(true)>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12 box-information text-center">
            <button type="button" class="btn botonGeneral" id="actualizarExamen">Guardar</button>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12 box-information text-center">

            <div class="table-responsive table-card mt-3 mb-1">

                <table class="display table table-bordered mb-4" style="width:100%"  id="listadoEfector">
                    <thead class="table-light">
                        <tr>
                            <th class="sort" title="Adjunto Efector">Adjunto Efector</th>
                            <th>Descripción</th>
                            <th>Adjuntar</th>
                            <th>Multi</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="listaefectores" class="list form-check-all">
            
                    </tbody>
                </table>

                @if($itemsprestacione->CInfo !== 0)
                    <table class="display table table-bordered mt-4" style="width:100%"  id="listadoInformador">
                        <thead class="table-light">
                            <th class="sort" title="Adjunto Informador">Adjunto Informador</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </thead>
                        <tbody id="listainformadores" class="list form-check-all">
                
                        </tbody>
                    </table>
                @endif
            </div>

        </div>
    </div>

</div>

<div id="modalEfector" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Adjuntar archivo Efector</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <form id="form-efector">
                   
                    @if($itemsprestacione->examenes->proveedor1->Multi == 1)
                    <div class="alert alert-info alert-border-left alert-dismissible fade show mb-2" role="alert">
                        Exámen con multi adjunto habilitado. Elija a que exámen quiere asociar el reporte.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <div class="list-group">
                         @foreach($multiEfector as $examen)
                        <label class="list-group-item">
                            <input class="form-check-input me-1" type="checkbox" id="Id_multiAdj_{{ $examen->Id }}" value="{{ $examen->Id}}" {{ $examen->archivos_count > 0 ? 'disabled' : 'checked' }}> 
                            {!! $examen->archivos_count > 0 ? $examen->examenes->Nombre . ' <i title="Con archivo adjunto" class="ri-attachment-line verde"></i>' : $examen->examenes->Nombre  !!}
                        </label>
                        @endforeach
                    </div>
                    @endif
                    
                    <input type="file" class="form-control fileA" name="fileEfector"/>
                
                    <div class="mt-3">
                        <label for="Descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" name="DescripcionE" id="DescripcionE" rows="5"></textarea>
                        <input type="hidden" id="multi" value="{{ $itemsprestacione->examenes->proveedor1->Multi == 1 ? 'success' : 'fail'}}">
                    </div>
                </form> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn botonGeneral" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn botonGeneral btnAdjEfector">Guardar adjunto</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="replaceAdjunto" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel3" aria-hidden="true" style="display: none">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Reemplazar archivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <form id="form-replace">
                    <input type="file" class="form-control" name="fileReplace"/>
                    <input type="hidden" name="replaceId" id="replaceId" value="">
                    <input type="hidden" name="replaceTipo" id="replaceTipo" value="">
                </form> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn botonGeneral" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn botonGeneral btnReplaceAdj">Guardar adjunto</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="modalInformador" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel2" aria-hidden="true" style="display: none">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Adjuntar archivo Informador</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <form id="form-informador">
                    @if($itemsprestacione->examenes->proveedor2->MultiE == 1 && $itemsprestacione->profesionales2->InfAdj == 1)
                    <div class="alert alert-info alert-border-left alert-dismissible fade show mb-2" role="alert">
                        Exámen con multi adjunto habilitado. Elija el reporte que quiere asociar.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                   
                    <div class="list-group">
                         @foreach($multiInformador as $informe)
                        <label class="list-group-item">
                            <input class="form-check-input me-1" type="checkbox" id="Id_multiAdjInf_{{ $informe->Id }}" value="{{ $informe->Id}}" {{ $informe->archivos_count > 0 ? 'disabled' : 'checked' }}> 
                            {!! 
                                $informe->archivos_count > 0 
                                ? ($informe->examenes->Nombre ?? '') 
                                    . ' (' . ($informe->examenes->proveedor2->Nombre ?? '') . ') <i title="Con archivo adjunto" class="ri-attachment-line verde"></i>' 
                                : ($informe->examenes->Nombre ?? '') 
                                    . ' (' . ($informe->examenes->proveedor2->Nombre ?? '') . ')'
                            !!}
                            
                        </label>
                        @endforeach
                    </div>
                    @endif
                    <input type="file" class="form-control fileA" name="fileInformador"/>
                
                    <div class="mt-3">
                        <label for="Descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" name="DescripcionI" id="DescripcionI" rows="5"></textarea>
                        <input type="hidden" id="multiE" value="{{ $itemsprestacione->examenes->proveedor2->MultiE == 1 && $itemsprestacione->profesionales2->InfAdj == 1 ? 'success' : 'fail'}}">
                    </div>
                </form> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn botonGeneral" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn botonGeneral btnAdjInformador">Guardar adjunto</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<script>
    const TOKEN = '{{ csrf_token() }}';

    const IDITEMPRES = "{{ $itemsprestacione->Id }}";
    const updateItem = "{{ route('updateItem') }}";
    const updateAsignado = "{{ route('updateAsignado') }}";
    const listGeneral = "{{ route('listGeneral') }}";
    const updateAdjunto = "{{ route('updateAdjunto') }}";
    const paginacionGeneral = "{{ route('paginacionGeneral') }}";
    const updateItemExamen = "{{ route('updateItemExamen') }}";
    const volver = "{{ route('prestaciones.edit', ['prestacione' => $itemsprestacione->IdPrestacion]) }}";
    const fileUpload = "{{ route('uploadAdjunto') }}";
    const descargaE = "@fileUrl('lectura')/AdjuntosEfector";
    const descargaI = "@fileUrl('lectura')/AdjuntosInformador";
    const deleteIdAdjunto = "{{ route('deleteIdAdjunto') }}";
    const replaceIdAdjunto = "{{ route('replaceIdAdjunto') }}";
    const getBloqueoItemPrestacion = "{{ route('getBloqueoItemPrestacion') }}";
    const checkAdj = "{{ route('itemsprestaciones.checkAdjuntos') }}";



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