@extends('template')

@section('title', 'Notas Credito')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-start">
    <h4 class="mb-sm-0">Notas Credito</h4>
</div>
<div class="card-header d-flex justify-content-between">
    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" data-bs-toggle="tab" href="#clientes" role="tab" aria-selected="true">
                <i class="ri-window-line"></i>
                Clientes
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#notas" role="tab" aria-selected="true">
                <i class="ri-window-line"></i>
                Notas Credito
            </a>
        </li>

    </ul>
</div>

<div class="row">
    <div class="col-lg-12">

        <div class="tab-content">
            <div class="tab-pane active" id="clientes" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="listaClientes" class="table nowrap align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort">Cliente</th>
                                            <th class="sort">CUIT</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">

                                    </tbody>
                                </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="notas" role="tabpanel">

            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="p-12">
        <div class="d-flex justify-content-center align-items-center">
        </div>
    </div>
</div>



<script>
    const getClientes = "{{ route('notasCredito.getClientes') }}";
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

<script src="{{ asset('js/notasCredito/index.js')}}?v={{ time() }}"></script>
<script src="{{ asset('js/notasCredito/paginacion.js')}}?v={{ time() }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>

@endpush
@endsection