@extends('template')

@section('title', 'Registrar exámen')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
        <h4 class="mb-sm-0">Registrar exámen</h4>
        <x-helper>{!!$helper!!}</x-helper>
    </div>
    <div class="page-title-right">
    </div>
    <button type="button" id="volver" class="btn btnSuccess"><i class="ri-arrow-left-line"></i>&nbsp;Volver</button>
</div>

<div class="container-fluid">
    <form id="form-create" action="{{ route('examenes.store') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        <div class="row">
            <div class="col-10 mx-auto">

                <div class="" id="messageExamenes"></div>

                <div class="col-12 box-information mb-2">
                    <div class="row">
                        <div class="col-6">
                            <div class="input-group input-group-sm size80porcent mx-auto">
                                <span class="input-group-text">Exámen&nbsp;<span class="required">(*)</span></span>
                                <input type="text" class="form-control" id="Examen" name="Examen">
                            </div>
                        </div>

                        <div class="col-6 ">
                            <div class="input-group input-group-sm size80porcent mx-auto">
                                <span class="input-group-text">Estudio&nbsp;<span class="required">(*)</span></span>
                                <select class="form-control" id="Estudio" name="Estudio">
                                    <option value="" selected>Elija una opción...</option>
                                    @foreach($estudios as $estudio)
                                    <option value="{{ $estudio->Id }}">{{ $estudio->Nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-6 mt-3">
                            <div class="input-group input-group-sm size80porcent mx-auto">
                                <span class="input-group-text">Descripción</span></span>
                                <input type="text" class="form-control" id="Descripcion" name="Descripcion">
                            </div>
                        </div>

                        <div class="col-6 mt-3">
                            <div class="input-group input-group-sm size80porcent mx-auto">
                                <span class="input-group-text">Reporte</span></span>
                                <div style="width: 65% !important;">
                                <select id="Reporte" name="Reporte">
                                        
                                </select>
                                </div>
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-sm botonGeneral" id="vistaPrevia" title="Vista previa">
                                    <i class="ri-search-line vistaPrevia"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-6 mt-3">
                            <div class="input-group input-group-sm size80porcent mx-auto">
                                <span class="input-group-text">Código de exámen</span></span>
                                <input type="text" class="form-control" id="CodigoEx" name="CodigoEx">
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
                                <input type="text" class="form-control" id="CodigoE" name="CodigoE">
                            </div>
                        </div>

                        <div class="col-6 mt-3">
                            <div class="input-group input-group-sm size80porcent mx-auto">
                                <span class="input-group-text">Día de vencimiento</span></span>
                                <input type="number" class="form-control" id="DiasVencimiento" name="DiasVencimiento">
                            </div>
                        </div>

                        <div class="col-6 mt-3 text-center">
                            <div class="form-check form-check-inline mx-auto">
                                <input class="form-check-input" type="checkbox" id="Inactivo" name="Inactivo">
                                <label class="form-check-label" for="Inactivo">Inactivo</label>
                            </div>
                        </div>

                        <div class="col-6 mt-3 text-center">
                            <div class="form-check form-check-inline mx-auto">
                                <input class="form-check-input" type="checkbox" id="priImpresion" name="priImpresion">
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
                                <select id="ProvEfector" name="ProvEfector" class="form-control">
                                    <option value="" selected>Elija una opción...</option>
                                    @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->Id ?? '' }}">{{ $proveedor->Nombre ?? '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="input-group input-group-sm size80porcent mx-auto">
                                <span class="input-group-text">Especialidad Informador&nbsp;<span class="required">(*)</span></span>
                                <select id="ProvInformador" name="ProvInformador" class="form-control">
                                    <option value="" selected>Elija una opción...</option>
                                    @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->Id ?? '' }}">{{ $proveedor->Nombre ?? '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-6">
                                <span class="input-group input-group-sm size80porcent mx-auto">
                                    <span class="input-group-text">Alias PDF&nbsp;</span>
                                    <input type="text" class="form-control" id="aliasexam" name="aliasexam">
                                </span>
                            </div>
                            <div class="col-6">

                            </div>
                        </div>


                        <div class="row text-center mt-4">
                            <div class="col-12">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="Informe" name="Informe">
                                    <label class="form-check-label" for="Informe">Informe</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="Cerrado" name="Cerrado" {{ (Auth::user()->Rol === 'Admin' && Auth::user()->profesional->T1 === 1 ? '' : 'disabled title="Debe ser Administrador y Efector"') }}>
                                    <label class="form-check-label" for="Informe">Cerrado</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="Fisico" name="Fisico">
                                    <label class="form-check-label" for="Fisico">Físico</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="Adjunto" name="Adjunto">
                                    <label class="form-check-label" for="Adjunto">Adjunto</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="Ausente" name="Ausente">
                                    <label class="form-check-label" for="Ausente">Ausente</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="Devolucion" name="Devolucion">
                                    <label class="form-check-label" for="Devolucion">Devolución</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="EvalExclusivo" name="EvalExclusivo">
                                    <label class="form-check-label" for="EvalExclusivo">Evaluador exclusivo</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="ExpAnexo" name="ExpAnexo">
                                    <label class="form-check-label" for="ExpAnexo">Exporta con anexo</label>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 box-information mt-2 text-center mt-3">
                    
                    <button type="submit" id="crear" class="btn btnDanger">Registrar</button>
                </div>

            </div>
        </div>
    </form>
</div>
<script>
    const vistaPrevia = "{{ route('examenes.getVistaPrevia')}}"
    const archivos = "{{ url('archivos/reportes') }}"
    const TOKEN = "{{ @csrf_token() }}";
    const getReportes = "{{ Route('examenes.getReportes') }}";
    const getReporte = "{{ Route('prestaciones.pdfPrueba')}}";
    const reporte = '';

</script>
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/fancyTable.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>

<script src="{{ asset('js/examenes/create.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/examenes/validaciones.js')}}?v={{ time() }}"></script>
<script src="{{ asset('js/examenes/vistaprevia.js')}}?v={{ time()}}"></script>

@endpush

@endsection