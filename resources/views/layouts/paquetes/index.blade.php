@extends('template')

@section('title', 'Pacientes')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Paquetes</h4>

    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item active">Listado</li>
        </ol>
    </div>
</div>
<div class="card-header d-flex justify-content-between">
    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" data-bs-toggle="tab" href="#facturacion" role="tab" aria-selected="true">
                <i class="ri-window-line"></i>
                Facturacion
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#examenes" role="tab" aria-selected="true">
                <i class="ri-window-line"></i>
                Examenes
            </a>
        </li>

    </ul>
</div>
<div class="row">
    <div class="col-lg-12">
        
            <div class="tab-content">
                <div class="tab-pane active" id="facturacion" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            Facturacion
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="examenes" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            Examenes
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>
<script>
</script>
@push('styles')
<link href="{{ asset('css/hacks.css') }}?v={{ time() }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" />
<link rel="stylesheet" href="{{ asset('css/fixSelect2.css') }}">
@endpush

@endsection