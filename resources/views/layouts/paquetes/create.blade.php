@extends('template')

@section('title', 'Registrar un paciente')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Nuevo Paquete de Examenes</h4>

</div>

<div class="container-fluid">
    <div class="col-12 p-3 border border-1 border-color" style="border-color: #666666;">
        <div class="row">
            <div class="col-2 p-1">
                <div>
                    <label for="" class="form-label">Codigo:</label>
                    <input type="text" class="form-control" id="codigo">
                </div>
            </div>
            <div class="col-4 p-1">
                <div>
                    <label for="" class="form-label">Nombre:</label>
                    <input type="text" class="form-control" id="nombre">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 p-1">
                <div>
                    <label for="" class="form-label">Codigo:</label>
                    <textarea class="form-control" id="descripcion"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>


@push('styles')
<link href="{{ asset('css/hacks.css') }}?v=1.1" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/pacientes/validaciones.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/pacientes/create.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/pacientes/utils.js') }}?v={{ time() }}"></script>

<script src="{{ asset('js/webcam.min.js') }}?V={{ time() }}"></script>
<script src="{{ asset('js/webcam-picture.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/scripts.js') }}?v={{ time() }}"></script>
<script src="{{ asset('libs/cleave.js/cleave.min.js') }}"></script>
<script src="{{ asset('js/pages/form-masks.init.js') }}"></script>
@endpush

@endsection