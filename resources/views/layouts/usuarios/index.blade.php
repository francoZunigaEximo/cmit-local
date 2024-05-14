@extends('template')

@section('title', 'Usuarios')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Usuarios</h4>
</div>


<div class="row">
    <div class="p-12">
        <div class="d-flex justify-content-center align-items-center">
            <div class="col-sm-8 mt-5 mt-lg-0">
                <div class="row g-3 mb-0 justify-content-center">

                    <div class="col-sm-3">
                        <label for="nombre">Nombre y apellido: </label>
                        <select name="nombre" id="nombre" class="form-control"></select>
                    </div>

                    <div class="col-sm-3">
                        <label for="usuario">Usuario: </label>
                        <select name="usua" id="usua" class="form-control"></select>
                    </div>

                    <div class="col-sm-3">
                        <label for="rol">Rol: </label>
                        <select name="rol" id="rol" class="form-control"></select>
                    </div>

                    <div class="col-auto d-flex justify-content-end align-items-end">
                        <button type="button" class="btn btn-sm botonGeneral buscarUsuario"><i class="ri-search-line"></i> Buscar</button>&nbsp;
                        <button type="button" class="btn btn-sm botonGeneral reiniciarUsuario"><i class="ri-arrow-go-forward-fill"></i> Reiniciar</button>
                    </div>
                </div>
            </div>
        </div>
            <!-- End Filter -->
    
            <div class="p-3 d-flex justify-content-end">
                <a href="./pages-starter - AltaUser.html">
                    <button type="button" class="btn botonGeneral"><i class="ri-add-line"></i> Nuevo</button>
                </a>
            </div>
    
    
            <!-- Start Table -->
            <div class="card-body">
                <div class="table-card table-responsive mt-3 mb-1 mx-auto">
                    <table id="listaUsuarios" class="display table table-bordered ">
                        <thead class="table-light">
                            <tr>
                                <th class="sort">User</th>
                                <th class="sort">Nombre</th>
                                <th class="sort">Rol Activo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="list form-check-all">
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- End Table -->
    
            
        </div>
    </div>
    
</div>
<script>
    const searchNombreUsuario = "{{route('searchNombreUsuario') }}";
    const searchUsuario = "{{route('searchUsuario') }}";
    const searchRol = "{{ route('searchRol') }}";
    const SEARCHUSUARIO = "{{ route('buscarUsuario') }}";
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

<script src="{{ asset('js/auth/index.js')}}?v={{ time() }}"></script>
<script src="{{ asset('js/auth/paginacion.js')}}?v={{ time() }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/es.js"></script>
<script src="{{ asset('js/pages/select2.init.js') }}"></script>

@endpush

@endsection