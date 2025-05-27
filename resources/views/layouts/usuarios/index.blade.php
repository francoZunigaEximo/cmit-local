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
                        <button type="button" id="reiniciar" class="btn btn-sm botonGeneral"><i class="ri-refresh-line"></i> Reiniciar</button>
                    </div>
                </div>
            </div>
        </div>
            <!-- End Filter -->
            <div class="row mt-4">
                <div class="col-sm-9">
                    <span class="small"><a class="btn btn-sm botonGeneral small p-1"><i class="ri-edit-line p-1"></i></a>&nbsp;Editar correo electronico, datos personales y roles.</span> &nbsp; 
                    <span class="small"><a class="btn btn-sm botonGeneral small p-1"><i class="ri-delete-bin-2-line p-1"></a></i>&nbsp;Dar de baja/eliminar el usuario.</span> &nbsp; 
                    <span class="small"><a class="btn btn-sm botonGeneral small p-1"><i class="ri-lock-2-line p-1"></i></a>&nbsp;Desactivar o activar usuario.</span> &nbsp;
                    <span class="small"><a class="btn btn-sm botonGeneral small p-1"><i class="ri-key-2-line p-1"></i></a>&nbsp;Reset de password a 'cmit1234'.</span>
                </div>
                <div class="col-sm-3 d-flex justify-content-end">
                    <a href="{{ route('usuarios.create') }}" class="btn botonGeneral"><i class="ri-add-line"></i> Nuevo</a>
                </div>
            </div>
            
    
    
            <!-- Start Table -->
            <div class="card-body">
                <div class="table-card table-responsive mt-3 mb-1 mx-auto">
                    <table id="listaUsuarios" class="display table table-bordered ">
                        <thead class="table-light">
                            <tr>
                                <th class="sort">Usuario</th>
                                <th class="sort">Apellido y Nombre</th>
                                <th class="sort">Roles</th>
                                <th>Estado</th>
                                <th>Status</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="list form-check-all" id="lstUsuarios">
                            
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
    const INDEX = "{{ route('usuarios.index') }}";
    const bajaUsuario = "{{ route('usuarios.delete')}}";
    const bloquearUsuario = "{{ route('bloquearUsuario') }}";
    const cambiarPassUsuario = "{{ route('cambiarPassUsuario') }}";
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