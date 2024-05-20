@extends('template')

@section('title', 'Crear Usuario')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Alta Usuario</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">Usuario</li>
                    <li class="breadcrumb-item active">Nuevo</li>       
                </ol> 
            </div>

        </div>
    </div>
</div>

<div class="card-header card-header-tabs d-flex justify-content-between">
    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#datosUsuarios" role="tab" aria-selected="true">
                <i class="ri-window-line"></i>
                Datos de usuario
            </a>
        </li>      
    </ul>
</div>

<div class="p-4 tab-content">

    <div class="row">
        <div class="col-sm-12">
            <p>Los nuevos usuarios tienen como contraseña estandar <span class="verde fw-semibold">"cmit1234"</span>. En su primera sesión se le solicitará el cambio.</p>
            <p>Los campos con asteriscos <span class="required">(*)</span> son obligatorios.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 card row tab-pane active" id="datosUsuarios" role="tabpanel">
            <form id="form-create">
                <div class="col-12 d-flex ">
                    <div class="p-2 col-md-6">
                        <label for="usuario" class="form-label font-weight-bold"><strong>Usuario <span class="required">(*)</span></strong></label>
                        <input id="usuario" name="usuario" class="form-control" type="text">
                    </div>
                    <div class="p-2 col-md-6">
                        <label for="email" class="form-label font-weight-bold"><strong>Email  <span class="required">(*)</span></strong></label>
                        <input id="email" name="email" class="form-control" type="email">
                    </div>
                </div>
                
                <div class="p-3 col-md-12 d-flex justify-content-center ">
                    <button class="btn botonGeneral m-2" id="volver" type="button">Volver</button>
                    <button class="btn botonGeneral m-2" id="crear" type="button">Crear</button>
                </div>
            </div> 
        </form>
    </div>
    
</div>

<script>
    const checkUsuario = "{{ route('checkUsuario') }}";
    const checkCorreo = "{{ route('checkCorreo') }}";
    const TOKEN = '{{ csrf_token() }}';
    const register = "{{ route('register') }}";
</script>


@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">
@endpush

@push('scripts')
<!--datatable js-->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/auth/create.js')}}?v={{ time() }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>

@endpush

@endsection


