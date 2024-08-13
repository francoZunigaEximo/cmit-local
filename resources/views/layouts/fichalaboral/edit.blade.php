@extends('template')

@section('title', 'Editar Ficha Laboral')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Editar Ficha Laboral</h4>
        </div>  
    </div>
</div>

<div class="row">
    <div class="col-9 mx-auto box-information">
        <div class="row">
            <div class="col-6">
                <div class="input-group input-group-sm mb-2">
                    <span class="input-group-text">Paciente</span>
                    <input type="text" class="form-control" id="Id" name="Id" value="{{ $fichalaboral->paciente->Id ?? ''}}" @readonly(true)>
                    <input type="text" class="form-control" style="width: 50%" id="NombreCompleto" name="NombreCompleto" value="{{ $fichalaboral->paciente->Apellido ?? ''}} {{ $fichalaboral->paciente->Nombre ?? ''}}" @readonly(true)>
                </div>

                <div class="input-group input-group-sm mb-2 selectClientes2">
                    <span class="input-group-text">Empresa</span>
                    <select class="form-control" id="selectClientes">
                        <option value="{{ $fichalaboral->empresa->Id ?? '' }}">{{ $fichalaboral->empresa->RazonSocial ?? '' }}</option>
                    </select>
                </div>   
            </div>
        
            <div class="col-6">
                <br /><br />
                <div class="input-group input-group-sm mb-2 selectArt2">
                    <span class="input-group-text">ART</span>
                    <select class="form-control-sm" id="selectArt" >
                        <option value="{{ $fichalaboral->art->Id ?? '' }}">{{ $fichalaboral->art->RazonSocial ?? '' }}</option>
                    </select>
                </div>
            </div>
        </div>
        <hr class="mt-1 mb-1">
        <div class="row text-center">
            <div class="col-12">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="TipoPrestacion" id="ART" value="ART" {{ isset($fichalaboral) && $fichalaboral->TipoPrestacion === 'ART' ? 'checked' : '' }}>
                    <label class="form-check-label" for="ART">ART</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="TipoPrestacion" id="INGRESO" value="INGRESO" {{ isset($fichalaboral) && $fichalaboral->TipoPrestacion === 'INGRESO' ? 'checked' : '' }}>
                    <label class="form-check-label" for="ingreso">INGRESO</label>
                </div>
        
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="TipoPrestacion" id="PERIODICO" value="PERIODICO" {{ isset($fichalaboral) && $fichalaboral->TipoPrestacion === 'PERIODICO' ? 'checked' : '' }}>
                    <label class="form-check-label" for="periodico">PERIODICO</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="TipoPrestacion" id="OCUPACIONAL" value="OCUPACIONAL" {{ isset($fichalaboral) && $fichalaboral->TipoPrestacion === 'OCUPACIONAL' ? 'checked' : '' }}>
                    <label class="form-check-label" for="ocupacional">OCUPACIONAL</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="TipoPrestacion" id="EGRESO" value="EGRESO" {{ isset($fichalaboral) && $fichalaboral->TipoPrestacion === 'EGRESO' ? 'checked' : '' }}>
                    <label class="form-check-label" for="egreso">EGRESO</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="TipoPrestacion" id="TipoPrestacion" id="OTRO" value="OTRO" {{ isset($fichalaboral) && in_array($fichalaboral->TipoPrestacion, ['CARNET', 'NO ART', 'RECMED','S/C_OCUPACIONAL']) ? 'checked' : '' }}>
                    <label class="form-check-label" for="otro">OTRO</label>
                </div>
                <div class="form-check form-check-inline" id="divtipoPrestacionPresOtros" style="display: {{  isset($fichaLaboral) && in_array($fichaLaboral->TipoPrestacion, ['CARNET', 'NO ART', 'RECMED','S/C_OCUPACIONAL']) ? '' : 'none' }}">
                    <select class="form-select" id="tipoPrestacionPresOtros">
                        <option selected value="{{ isset($fichaLaboral) && in_array($fichalaboral->TipoPrestacion, ['CARNET', 'NO ART', 'RECMED','S/C_OCUPACIONAL']) ? $fichalaboral->TipoPrestacion : '' }}">{{ isset($fichaLaboral) && in_array($fichalaboral->TipoPrestacion, ['CARNET', 'NO ART', 'RECMED','S/C_OCUPACIONAL']) ? $fichalaboral->TipoPrestacion : 'Elija una opción...' }}</option>
                        @foreach ($tiposPrestacionOtros as $tipo)
                        <option value="{{ $tipo->Nombre }}">{{ $tipo->Nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <hr class="mt-1 mb-1">

        <div class="row mt-2">
            <div class="col-6 ">

                <div class="input-group input-group-sm mb-2 TareaRealizar">
                    <span class="input-group-text">Tareas a realizar</span>
                    <input type="text" class="form-control" id="TareaRealizar" name="TareaRealizar" value="{{ $fichalaboral->Tareas ?? '' }}">
                </div>

                <div class="input-group input-group-sm mb-2 UltimoPuesto">
                    <span class="input-group-text">Última empresa y puesto</span>
                    <input type="text" class="form-control" id="UltimoPuesto" name="UltimoPuesto" value="{{ $fichalaboral->TareasEmpAnterior ?? '' }}">
                </div>

                <div class="input-group input-group-sm mb-2 PuestoActual">
                    <span class="input-group-text">Puesto actual</span>
                    <input type="text" class="form-control" id="PuestoActual" name="PuestoActual" value="{{ $fichalaboral->Puesto ?? '' }}">
                </div>

                <div class="input-group input-group-sm mb-2 SectorActual">
                    <span class="input-group-text">Sector Actual</span>
                    <input type="text" class="form-control" id="SectorActual" name="SectorActual" value="{{ $fichalaboral->Sector ?? '' }}">
                </div>

                <div class="input-group input-group-sm mb-2 CCosto">
                    <span class="input-group-text">C.Costos</span>
                    <input type="text" class="form-control" id="CCostos" name="CCostos" value="{{ $fichalaboral->CCosto ?? '' }}">
                </div>

                <div class="row">
                    <div class="col-6">
                        
                        <div class="input-group input-group-sm mb-2 AntiguedadPuesto">
                            <span class="input-group-text">Antig. Puesto</span>
                            <input type="number" class="form-control" placeholder="0" id="AntiguedadPuesto" value="{{ $fichalaboral->AntigPuesto ?? '' }}">
                        </div>

                        <div class="input-group input-group-sm mb-2 AntiguedadEmpresa">
                            <span class="input-group-text">Antig. Empresa</span>
                            <input type="number" class="form-control" placeholder="0" id="AntiguedadEmpresa" readonly="">
                        </div>
                    </div>

                    <div class="col-6">

                        <div class="input-group input-group-sm mb-2 FechaIngreso">
                            <span class="input-group-text">Fecha Ingreso</span>
                            <input type="date" class="form-control" id="FechaIngreso" value="{{ (isset($fichalaboral->FechaIngreso) && $fichalaboral->FechaIngreso !== '0000-00-00') ? \Carbon\Carbon::parse($fichalaboral->FechaIngreso)->format('Y-m-d') : '' }}">
                        </div>

                        <div class="input-group input-group-sm mb-2 FechaEgreso">
                            <span class="input-group-text">Fecha Egreso</span>
                            <input type="date" class="form-control" id="FechaEgreso" value="{{ (isset($fichalaboral->FechaIngreso) && $fichalaboral->FechaEgreso !== '0000-00-00') ? \Carbon\Carbon::parse($fichalaboral->FechaEgreso)->format('Y-m-d') : '' }}">
                        </div>

                    </div>
                </div>


            </div>

            <div class="col-6">
                <div class="input-group input-group-sm mb-2">
                    <span class="input-group-text">Jornada</span>
                    <select class="form-select" id="TipoJornada">
                        <option selected value="{{ $fichalaboral->TipoJornada ?? ''}}">{{ $fichalaboral->TipoJornada ?? 'Elija una opción...'}}</option>
                        <option value="NORMAL">Normal</option>
                        <option value="PROLONGADA">Prolongada</option>
                    </select>
                    <select class="form-select" id="Horario">
                        <option selected value="{{ $fichalaboral->Jornada ?? '' }}">{{ $fichalaboral->Jornada ?? 'Elija una opción...' }}</option>
                        <option value="DIURNA">Diurna</option>
                        <option value="NOCTURNO">Nocturno</option>
                        <option value="ROTATIVO">Rotativo</option>
                        <option value="FULLTIME">Fulltime</option>
                </select>
                </div>

                <div class="mt-3">
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">Fecha Preocupacional</span>
                        <input type="date" class="form-control" id="FechaPreocupacional" value="{{ (isset($fichalaboral->FechaPreocupacional) && $fichalaboral->FechaPreocupacional !== '0000-00-00') ? \Carbon\Carbon::parse($fichalaboral->FechaPreocupacional)->format('Y-m-d') : '' }}">
                    </div>

                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">Fecha Ult. Periodico Empresa</span>
                        <input type="date" class="form-control"  id="FechaUltPeriod" value="{{ (isset($fichalaboral->FechaUltPeriod) && $fichalaboral->FechaUltPeriod !== '0000-00-00') ? \Carbon\Carbon::parse($fichalaboral->FechaUltPeriod)->format('Y-m-d') : '' }}">
                    </div>

                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text">Fecha Ex ART</span>
                        <input type="date" class="form-control" id="FechaExArt" value="{{ (isset($fichalaboral->FechaExArt) && $fichalaboral->FechaExArt !== '0000-00-00') ? \Carbon\Carbon::parse($fichalaboral->FechaExArt)->format('Y-m-d') : '' }}">
                    </div>
                </div>

                <div class="mt-3">
                    <label for="Observaciones" class="form-label">Observaciones</label>
                    <textarea class="form-control" style="height: 100px" placeholder="Observaciones de la jornada laboral" id="ObservacionesFicha">{{ $fichalaboral->Observaciones ?? '' }}</textarea>
                </div>

            </div>
        </div>

        <hr class="mt-1 mb-1">

        <div class="row">
            <div class="col-sm-6">

                <div class="input-group input-group-sm mb-2">
                    <span class="input-group-text">Forma de Pago</span>
                    <select class="form-select" id="PagoLaboral">
                        <option value="" selected>Elija una opción...</option>
                        <option value="B">Contado</option>
                        <option value="A">Cuenta Corriente</option>
                        <option value="P">Exámen a Cuenta</option>
                    </select>
                </div>
            </div>
        </div>

            <hr class="mt-1 mb-1">

            <div class="row">
                <div class="col-12 text-center mt-2">
                    <button type="button" class="btn botonGeneral volver" >Volver a la página anterior</button>
                    <button type="button" id="guardarFicha" class="btn botonGeneral">Actualizar</button>
                </div>
            </div>
            
        </div>
    </div>
</div>
<script>
    const IDFICHA = "{{ $fichalaboral->Id ?? 0}}";
    const getClientes = "{{ route('getClientes') }}";
    const lstExDisponibles = "{{ route('lstExDisponibles') }}";
    const saveFichaAlta = "{{ route('saveFichaAlta') }}";
    const TOKEN = "{{ csrf_token() }}";
</script>


@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/fichalaboral/edit.js') }}?v={{ time() }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>

<script src="{{ asset('js/pages/form-masks.init.js') }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
@endpush

@endsection
