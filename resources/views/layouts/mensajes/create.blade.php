@extends('template')

@section('title', 'Crear nuevo mensaje')

@section('content')
<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Crear Nuevo Mensaje</h4>
    <a class="btn btn-sm botonGeneral" href="{{ route('mensajes.modelos') }}"><i class="ri-arrow-go-back-line"></i>Volver</a>
</div>

<div class="col-sm-12 mt-2 mb-2">
    <p class="small"><span class="required">(*)</span> Los datos son obligatorios</p>
</div>

<form id="form-create">
<div class="row mt-2 fondo-grisClaro p-2">
    <div class="col-sm-6">
        <label for="Nombre" class="form-label fw-bolder">Nombre del modelo: <span class="required">(*)</span></label>
        <input class="form-control" type="text" name="Nombre" id="Nombre" placeholder="Escriba un nombre">
    </div>
    <div class="col-sm-6">
        <label for="Asunto" class="form-label fw-bolder">Asunto del email: <span class="required">(*)</span></label>
        <input class="form-control" type="text" name="Asunto" id="Asunto" placeholder="El asunto del email">
    </div>

    <div class="col-sm-12 mt-2">
        <label for="Cuerpo" class="form-label fw-bolder">Cuerpo del email:</label>
        <textarea name="Cuerpo" id="Cuerpo" rows="10" class="form-control Cuerpo"></textarea>
    </div>

    <hr class="mt-2">

    <div class="col-sm-12 text-center">
        
        <button type="button" class="btn btn-sm botonGeneral crear"><i class="ri-save-3-line"></i>Crear modelo</button>
    </div>
</div>
</form>


<script>
    const crearModelo = "{{ route('mensajes.modelos.save') }}";
    const listadoModelo = "{{ route('mensajes.modelos') }}";
</script>
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v=?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/richtext.min.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/mensajeria/validaciones.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/mensajeria/modeloCreate.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/richText/jquery.richtext.js') }}?v={{ time() }}"></script>
@endpush

@endsection