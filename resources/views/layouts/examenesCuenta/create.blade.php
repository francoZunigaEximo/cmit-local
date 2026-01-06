@extends('template')

@section('title', 'Examenes a cuenta')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Examenes a cuenta</h4>

    <div class="page-title-right"></div>
    <button type="button" class="btn btn-warning volverPagoCuenta"><i class="ri-arrow-left-line"></i>&nbsp;Volver</button>
</div>

<div class="row">
    <div class="small col-sm-12 mb-2"><span class="required">(*)</span> El campo es obligatorio.</div>
</div>

<div class="row justify-content-md-center">
    <form id="form-index">
        <div class="col p-2 border border-1 border-color" style="border-color: #666666;">
            
            <div class="row justify-content-center">
                <div class="col-sm-2 mb-3">
                    <label for="IdCreate" class="form-label fw-bolder">Número: </label>
                    <input type="text" id="IdCreate" class="form-control" value="0" disabled>
                </div>

                <div class="col-sm-2 mb-3">
                    <label for="empresaCreate" class="form-label fw-bolder">Empresa: <span class="required">(*)</span></label>
                    <select class="form-control" name="empresaCreate" id="empresaCreate"></select>
                </div>

                <div class="col-sm-2 mb-3">
                    <label for="FechaCreate" class="form-label fw-bolder">Fecha: <span class="required">(*)</span></label>
                    <input type="date" id="FechaCreate" name="FechaCreate" class="form-control">
                </div>

                <div class="col-sm-2 mb-3">
                    <label for="FacturaCreate" class="form-label fw-bolder">Nro. Factura: <span class="required">(*)</span></label>
                    <input type="text" id="FacturaCreate" name="FacturaCreate" class="form-control">
                </div>

                <div class="col-sm-2 mb-3">
                    <label for="FechaPago" class="form-label fw-bolder">Fecha Pago: </label>
                    <input type="date" id="FechaPago" name="FechaPago" class="form-control">
                </div>

                <div class="col-sm-2 mb-3">
                    <label for="FechaPago" class="form-label fw-bolder">Observación: </label>
                    <textarea class="form-control" name="ObsPago" id="ObsPago" cols="30" rows="3"></textarea>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-sm-4 mb-3 text-center">
                    <button type="button" class="btn btn-sm botonGeneral crearPagoCuenta">Registrar</button>
                </div>
            </div>
        </div>
    </form>  
</div> 

<script>
    const getClientes = "{{ route('getClientes') }}";
    const saveExamenCuenta = "{{ route('saveExamenCuenta') }}";
</script>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/inputmask/dist/jquery.inputmask.min.js"></script>
<script src="{{ asset('js/examenescuenta/index.js')}}?=v{{ time() }}"></script>
<script src="{{ asset('js/examenescuenta/create.js')}}?=v{{ time() }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>
@endpush

@endsection