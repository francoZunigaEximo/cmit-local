@extends('template')

@section('title', 'Administracion de Llamador')

@section('content')
<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0 capitalize">Llamador <span class="custom-badge amarillo capitalize">administración</span></h4>
    <div class="page-title-right d-inline"></div>
</div>

<div class="card-header d-flex justify-content-between">
    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" data-bs-toggle="tab" href="#auditoria" role="tab" aria-selected="true">
                <i class="fas fa-home"></i>
                Auditoria Admins
            </a>
        </li>
    </ul>
</div>

<div class="card-body p-4">
    <div class="tab-content">
        <div id="messageClientes"></div>
        <div class="tab-pane active" id="auditoria" role="tabpanel">

            <div class="col-lg-12">
                <div class="card">

                    <div class="card-header d-flex justify-content-center align-items-center">
                        <div class="col-sm-8 mt-5 mt-lg-0">
                            <div class="row g-3 mb-0 justify-content-center">

                                <div class="col-sm-3">
                                    <label for="usuario">Usuario: </label>
                                    <select name="usua" id="usuario" class="form-control"></select>
                                </div>

                                <div class="col-sm-3">
                                    <label for="rol">Prestacion Nro: </label>
                                    <input class="form-control" name="prestacion" id="prestacion">
                                </div>

                                <div class="col-auto d-flex justify-content-end align-items-end">
                                    <button type="button" class="btn btn-sm botonGeneral buscar"><i class="ri-search-line"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="card-body">
                        <div class="listjs-table">

                            <div class="table mt-3 mb-1">
                                <table class="table align-middle table-nowrap" id="listadoLlamado">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Usuario</th>
                                            <th>Prestación</th>
                                            <th>Acción</th>
                                            <th>Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all" id="listaLlamadosAdmin">
                                        
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div><!-- end card -->
                </div>
                <!-- end col -->
            </div>

        </div>
    </div>
</div>

<script>
    const SEARCHUSUARIO = "{{ route('llamador.buscarRegistros') }}";
    const loadAdmins = "{{ route('llamador.loadAdministradores') }}";
</script>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css">
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>

    <script src="{{ asset('/js/llamador/paginacionAdmin.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('/js/llamador/index.admin.js') }}?v={{ time() }}"></script>
@endpush

@endsection