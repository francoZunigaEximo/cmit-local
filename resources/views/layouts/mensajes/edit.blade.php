@extends('template')

@section('title', 'Editar emails')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Editar emails</h4>
    <button type="button" class="btn btnSuccess volver"><i class="ri-arrow-go-back-line"></i>&nbsp;Volver</button>
</div>

<div class="row">
    <div class="col-sm-12 p-3 fondo-grisClaro mb-3">
        <span class="fw-bolder">Nro Cliente:</span> <span class="text-uppercase">{{ $mensaje->Id }}</span> |
        <span class="fw-bolder">Razón Social:</span> <span class="text-uppercase">{{ $mensaje->RazonSocial }}</span> |
        <span class="fw-bolder">CUIT:</span> <span class="text-uppercase">{{ $mensaje->Identificacion }}</span> |
        <span class="fw-bolder">ParaEMpresa:</span> <span class="text-uppercase">{{$mensaje->ParaEmpresa }} </span>
    </div>

    <div class="col-12">
        <p><span class="required">(*)</span> Las direcciones de correo electronico van separados por comas.</p>
    </div>

    <div class="col-sm-4">
        <div>
            <label class="form-label fw-bolder" for="EMailFactura">Email para Facturas: <span class="required">(*)</span></label>
        </div>
        <div>
            <textarea name="EMailFactura" id="EMailFactura" cols="50" rows="5">{{ $mensaje->EMailFactura ?? ''}}</textarea>
        </div>
    </div>

    <div class="col-sm-4">
        <div>
            <label class="form-label fw-bolder" for="EMailResultados">Email para Masivos: <span class="required">(*)</span></label>
        </div>
        <div>
            <textarea name="EMailResultados" id="EMailResultados" cols="50" rows="5">{{ $mensaje->EMailResultados ?? ''}}</textarea>
        </div>
    </div>

    <div class="col-sm-4">
        <div>
            <label class="form-label fw-bolder" for="EMailInformes">Email para Informes: <span class="required">(*)</span></label>
        </div>
        <div>
            <input type="hidden" id="Id" value="{{ $mensaje->Id ?? ''}}">
            <textarea name="EMailInformes" id="EMailInformes" cols="50" rows="5">{{ $mensaje->EMailInformes ?? ''}}</textarea>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12 text-center mt-2">
        
        <button type="button" class="btn btn-sm botonGeneral actualizar"><i class="ri-save-line"></i>&nbsp;Actualizar</button>
    </div>
</div>

<script>
    const updateEmail = "{{ route('updateEmail') }}";
    const VOLVER = "{{ route('mensajes.index') }}";
</script>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v=?v={{ time() }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/mensajeria/edit.js') }}?v={{ time() }}"></script>
@endpush

@endsection