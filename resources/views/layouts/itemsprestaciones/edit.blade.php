@extends('template')

@section('title', 'Exámen Prestación')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Exámen prestación <span class="custom-badge original">N°{{ $itemsprestacione->IdPrestacion }}</span> | Paciente <span class="custom-badge original">{{ $paciente->Nombre ?? ''}} {{ $paciente->Apellido ?? '' }}</span></h4>
    <input type="hidden" value="{{ $itemsprestacione->Id }}" id="Id">
    <div class="page-title-right d-inline">
        <p><strong>QR:</strong> {{ $qrTexto ?? ''}}</p>
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
                    <button type="button" id="asignar" class="btn botonGeneral btn-sm asignar">Asignar</button>
                    <button type="button" id="liberar" class="btn botonGeneral btn-sm liberar">Liberar</button>
                    <button type="button" id="abrir" class="btn botonGeneral btn-sm abrir">Abrir</button>
                    <button type="button" id="cerrar" class="btn botonGeneral btn-sm cerrar">Cerrar</button>
                    <input type="hidden" value="{{ $itemsprestacione->CAdj }}" id="CAdj">
                </div>

            </div>
           <!-- (in_array($itemsprestacione->CAdj, [1,4]) ? 'Pendiente' : (in_array($itemsprestacione->CAdj, [2,5]) ? 'Adjunto' : (in_array($itemsprestacione->CAdj, [0,3]) ? 'No lleva Adjuntos' : ' - '))) ?? ''-->
            <div class="row">
                <div class="col-md-2">
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">Adjunto</span>
                        <input type="text" class="form-control" id="Estado" name="Estado" value="{{ ($itemsprestacione->examenes->Adjunto === 0 ? 'No lleva Adjuntos' : ($itemsprestacione->examenes->Adjunto === 1 && $adjuntoEfector === 0 ? 'Pendiente' : ($itemsprestacione->examenes->Adjunto === 1 && $adjuntoEfector === 1 ? 'Adjuntado' : '-'))) }}" @readonly(true)>
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
                        <span class="input-group-text">Informador</span>
                        <select name="informadores" id="informadores" class="form-control">
                            <option value="{{ $itemsprestacione->profesionales2->Id ?? '' }}" selected>{{ $itemsprestacione->profesionales2->Apellido ?? '' }} {{ $itemsprestacione->profesionales2->Nombre ?? '' }}</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">Estado</span>
                        <input type="text" class="form-control" style="color: {{ ($itemsprestacione->CInfo === 0 || $itemsprestacione->CInfo === 1 || $itemsprestacione->CInfo === 2 ? 'red' : ($itemsprestacione->CInfo === 3 ? 'green' : '')) ?? ''}}" id="EstadoI" name="EstadoI" value="{{ ($itemsprestacione->CInfo === 1 || $itemsprestacione->CInfo === 0 ? 'Pediente' : ($itemsprestacione->CInfo === 2 ? 'Borrador' : ($itemsprestacione->CInfo === 3 ? 'Cerrado' : ''))) ?? ''}}" @readonly(true)>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">Fecha Pago</span>
                        <input type="date" id="FechaPagado2" name="FechaPagado2" class="form-control" value="{{ $itemsprestacione->FechaPagado2}}" @readonly(true)>
                    </div>
                </div>

                <div class="col-md-2">
                    <button type="button" id="asignarI" class="btn botonGeneral btn-sm asignarI">Asignar</button>
                    <button type="button" id="cerrarI" class="btn botonGeneral btn-sm cerrarI">Cerrar</button>
                    <input type="hidden" value="{{ $itemsprestacione->CInfo }}" id="CInfo">
                </div>

            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12 mx-auto box-information">
            <div class="input-group input-group-sm mb-2">
                <span class="input-group-text">Observaciones Informador</span>
                <textarea class="form-control" style="height: 80px" name="Obs" id="Obs" disabled>{!! isset($itemsprestacione->itemsInfo) ? strip_tags($itemsprestacione->itemsInfo->Obs) : '' !!}</textarea>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12 mx-auto box-information">

            <div class="row">
                <div class="col-md-4">
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">Fecha facturación</span>
                        <input type="date" class="form-control" id="FechaFacturaVta" name="FechaFacturaVta" value="{{ $itemsprestacione->facturadeventa->Fecha ?? ''}}" @readonly(true)>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">Nota crédito</span>
                        <input type="text" class="form-control" id="FechaNC" name="FechaNC" value="{{ $itemsprestacione->notaCreditoIt->notaCredito->Tipo ?? ''}}{{ $itemsprestacione->notaCreditoIt->notaCredito->Sucursal ?? ''}}{{ $itemsprestacione->notaCreditoIt->notaCredito->Nro ?? ''}}" @readonly(true)>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12 box-information text-center">
            <button type="button" class="btn botonGeneral" id="btnVolver">Volver</button>
            <a class="btn botonGeneral" id="actualizarExamen">Guardar</a>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12 box-information text-center">

            <div class="table-responsive table-card mt-3 mb-1">

                <table class="display table table-bordered mb-4" style="width:100%"  id="listadoEfector">
                    <thead class="table-light">
                        <tr>
                            <th class="sort" title="Exámen">Exámen</th>
                            <th>Descripción</th>
                            <th>Adjunto</th>
                            <th>Multi</th>
                            <th>Acciones <button type="button" class="btn botonGeneral adjuntarEfector" data-bs-toggle="modal" data-bs-target="#modalEfector">Adjuntar archivo</button></th>
                        </tr>
                    </thead>
                    <input type="file" name="archivo" id="archivoEfector" style="display: none;" />
                    <tbody id="listaefectores" class="list form-check-all">
            
                    </tbody>
                </table>
            
                <table class="display table table-bordered mt-4" style="width:100%"  id="listadoInformador">
                    <thead class="table-light">
                        <th class="sort" title="Exámen">Exámen</th>
                        <th>Descripción</th>
                        <th>Acciones <button type="button" class="btn botonGeneral adjuntarInformador" data-bs-toggle="modal" data-bs-target="#modalInformador">Adjuntar archivo</button></th>
                    </thead>
                    <tbody id="listainformadores" class="list form-check-all">
            
                    </tbody>
                </table>
            
            </div>

        </div>
    </div>

</div>

<div id="modalEfector" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Adjuntar archivo Efector</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <form id="form-efector">
                    
                    <input type="file" class="form-control fileA" name="fileEfector"/>
                
                    <div class="mt-3">
                        <label for="Descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" name="DescripcionE" id="DescripcionE" rows="5"></textarea>
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

<div id="modalInformador" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Adjuntar archivo Informador</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <form id="form-informador">
                    
                    <input type="file" class="form-control fileA" name="fileInformador"/>
                
                    <div class="mt-3">
                        <label for="Descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" name="DescripcionI" id="DescripcionI" rows="5"></textarea>
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

    const updateItem = "{{ route('updateItem') }}";
    const updateAsignado = "{{ route('updateAsignado') }}";
    const listGeneral = "{{ route('listGeneral') }}";
    const updateAdjunto = "{{ route('updateAdjunto') }}";
    const paginacionGeneral = "{{ route('paginacionGeneral') }}";
    const updateExamen = "{{ route('updateExamen') }}";
    const volver = "{{ route('prestaciones.edit', ['prestacione' => $itemsprestacione->IdPrestacion]) }}";
    const fileUpload = "{{ route('uploadAdjunto') }}";
    const descargaE = "{{ asset('storage/ArchivosEfectores') }}";
    const descargaI = "{{ asset('storage/ArchivosInformadores') }}";
    const deleteIdAdjunto = "{{ route('deleteIdAdjunto') }}";



</script>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">
<link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">

<!-- include FilePond image preview styles (if needed) -->
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ asset('js/itemsprestaciones/edit.js') }}?v={{ time() }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('js/fancyTable.js') }}"></script>

@endpush

@endsection