@extends('template')

@section('title', 'Exámen')

@section('content')

<div class="row mb-4">
    <div class="col-12 text-end">
        <button class="btn btn-warning multiVolver"><i class="ri-arrow-left-line"></i>&nbsp;Volver</button>
    </div>
</div>

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Examen&nbsp;<span class="custom-badge original">{{ $examene->Id }}</span> {!! ($examene->Inactivo === 3) ? '<span class="custom-badge rojo">Deshabilitado</span>' : '' !!}</h4>

    <div class="page-title-right">
        <button type="button" id="clonar" class="btn botonGeneral"><i class="ri-file-copy-2-line"></i> Clonar</button>
        <button type="button" {!! $examene->Inactivo === 3 ? 'disabled' : 'id="eliminar"' !!} class="btn botonGeneral"><i class="ri-delete-bin-line"></i> Eliminar</button>
    </div>
</div>

<div class="container-fluid">
    <form id="form-update">
        <div class="row">
            <div class="col-10 mx-auto">

                <div class="col-12 box-information mb-2">
                    <div class="row">
                        <div class="col-6">
                            <div class="input-group input-group-sm size80porcent mx-auto">
                                <span class="input-group-text">Exámen&nbsp;<span class="required">(*)</span></span>
                                <input type="text" class="form-control" id="Examen" name="Examen" value="{{ $examene->Nombre ?? '' }}">
                            </div>
                        </div>

                        <div class="col-6 ">
                            <div class="input-group input-group-sm size80porcent mx-auto">
                                <span class="input-group-text">Estudio&nbsp;<span class="required">(*)</span></span>
                                <select class="form-control" id="Estudio" name="Estudio">
                                    <option value="{{ $examene->estudios->Id ?? ''}}" selected>{{ ($examene->estudios->Nombre === '' ? 'Elija una opción...' : $examene->estudios->Nombre ?? 'Elija una opción...' )}}</option>
                                    @foreach($estudios as $estudio)
                                        <option value="{{ $estudio->Id }}">{{ $estudio->Nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-6 mt-3">
                            <div class="input-group input-group-sm size80porcent mx-auto">
                                <span class="input-group-text">Descripción</span></span>
                                <input type="text" class="form-control" name="Descripcion" id="Descripcion" value="{{ $examene->Descripcion ?? ''}}">
                            </div>
                        </div>

                        <div class="col-6 mt-3">
                            <div class="input-group input-group-sm size80porcent mx-auto">
                                <span class="input-group-text">Reporte</span></span>
                                <select class="form-control" id="Reporte" name="Reporte">
                                    <option value="{{ $examene->reportes->Id ?? ''}}" selected>{{ ($examene->reportes->Nombre === '' ? 'Elija una opción...' : $examene->reportes->Nombre  ?? 'Elija una opción...') }}</option>
                                    @foreach($reportes as $reporte)
                                        <option value="{{ $reporte->Id }}">{{ $reporte->Nombre }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-sm botonGeneral" title="Vista previa"><i class="ri-search-line vistaPrevia"></i></button>
                            </div>
                        </div>

                        <div class="col-6 mt-3">
                            <div class="input-group input-group-sm size80porcent mx-auto">
                                <span class="input-group-text">Código de exámen</span></span>
                                <input type="text" class="form-control" id="CodigoEx" name="CodigoEx" value="{{ $examene->Cod ?? ''}}">
                            </div>
                        </div>

                        <div class="col-6 mt-3">
                            <div class="input-group input-group-sm size80porcent mx-auto">
                                <span class="input-group-text">Formulario</span></span>
                                <input type="text" class="form-control" id="Formulario" name="Formulario">
                            </div>
                        </div>

                        <div class="col-6 mt-3">
                            <div class="input-group input-group-sm size80porcent mx-auto">
                                <span class="input-group-text">Código de efector</span></span>
                                <input type="text" class="form-control" id="CodigoE" name="CodigoE" value="{{ $examene->Cod2 ?? ''}}">
                            </div>
                        </div>

                        <div class="col-6 mt-3">
                            <div class="input-group input-group-sm size80porcent mx-auto">
                                <span class="input-group-text">Día de vencimiento</span></span>
                                <input type="number" class="form-control" id="DiasVencimiento" name="DiasVencimiento" value="{{ $examene->DiasVencimiento ?? ''}}">
                            </div>
                        </div>

                        <div class="col-6 mt-3 text-center">
                            <div class="form-check form-check-inline mx-auto">
                                <input class="form-check-input" type="checkbox" id="Inactivo" name="Inactivo" {{ ($examene->Inactivo === 1 ? 'checked' : '') }}>
                                <label class="form-check-label" for="Inactivo">Inactivo</label>
                            </div>
                        </div>

                        <div class="col-6 mt-3 text-center">
                            <div class="form-check form-check-inline mx-auto">
                                <input class="form-check-input" type="checkbox" id="priImpresion" name="priImpresion" {{ ($examene->PI === 1 ? 'checked' : '') }}>
                                <label class="form-check-label" for="priImpresion">Prioridad de impresión</label>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-12 box-information mb-2">
                    <div class="row">
                        <div class="col-6">
                            <div class="input-group input-group-sm size80porcent mx-auto">
                                <span class="input-group-text">Especialidad Efector&nbsp;<span class="required">(*)</span></span>
                                <select id="ProvEfector" class="form-control" name="ProvEfector">
                                    <option value="{{ $examene->proveedor1->Id ?? '' }}" selected>{{ ($examene->proveedor1->Nombre === '' ? 'Elija una opción...' : $examene->proveedor1->Nombre ?? 'Elija una opción...' )}}</option>
                                    @foreach($proveedores as $proveedor)
                                        <option value="{{ $proveedor->Id ?? '' }}">{{ $proveedor->Nombre ?? '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="input-group input-group-sm size80porcent mx-auto">
                                <span class="input-group-text">Especialidad Informador&nbsp;<span class="required">(*)</span></span>
                                <select id="ProvInformador" class="form-control" name="ProvInformador">
                                    <option value="{{ $examene->proveedor2->Id ?? '' }}" selected>{{ ($examene->proveedor2->Nombre === '' ? 'Elija una opción...' : $examene->proveedor2->Nombre ?? 'Elija una opción...' )}}</option>
                                    @foreach($proveedores as $proveedor)
                                        <option value="{{ $proveedor->Id ?? '' }}">{{ $proveedor->Nombre ?? '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="row text-center mt-4">
                            <div class="col-12">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="Informe" name="Informe" {{ ($examene->Informe === 1 ? 'checked' : '') }}>
                                    <label class="form-check-label" for="Informe">Informe</label>
                                </div>

                                <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="Cerrado" name="Cerrado" {{ ($examene->Cerrado === 1 ? 'checked' : '') }} {{ (Auth::user()->Rol === 'Admin' && Auth::user()->profesional->T1 === 1 ? '' : 'disabled title="Debe ser Administrador y Efector"') }}>
                                    <label class="form-check-label" for="Informe">Cerrado</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="Fisico" name="Fisico" {{ ($examene->NoImprime === 1 ? 'checked' : '') }}>
                                    <label class="form-check-label" for="Fisico">Físico</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="Adjunto" name="Adjunto" {{ ($examene->Adjunto === 1 ? 'checked' : '') }}>
                                    <label class="form-check-label" for="Adjunto">Adjunto</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="Ausente" name="Ausente" {{ ($examene->Ausente === 1 ? 'checked' : '') }}>
                                    <label class="form-check-label" for="Ausente">Ausente</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="Devolucion" name="Devolucion" {{ ($examene->Devol === 1 ? 'checked' : '') }}>
                                    <label class="form-check-label" for="Devolucion">Devolución</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="EvalExclusivo" name="EvalExclusivo" {{ ($examene->Evaluador === 1 ? 'checked' : '') }}>
                                    <label class="form-check-label" for="EvalExclusivo">Evaluador exclusivo</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="ExpAnexo" name="ExpAnexo" {{ ($examene->EvalCopia === 1 ? 'checked' : '') }}>
                                    <label class="form-check-label" for="ExpAnexo">Exporta con anexo</label>
                                </div>
                    
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 box-information mt-2 text-center mt-3">
                    <button type="button" {!! $examene->Inactivo === 3 ? 'disabled' : 'id="guardar"' !!} class="btn botonGeneral">Guardar</button>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
    const TOKEN = "{{ @csrf_token() }}";
    const ID = "{{ $examene->Id }}";
    const GOCREATE = "{{ route('examenes.create') }}";
    const GOINDEX = "{{ route('examenes.index') }}";
    const updateExamen = "{{ route('updateExamen') }}";
    const deleteExamen = "{{ route('deleteExamen') }}";
</script>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
@endpush

@push('scripts')
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/examenes/edit.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/examenes/validaciones.js')}}?v={{ time() }}"></script>
@endpush

@endsection