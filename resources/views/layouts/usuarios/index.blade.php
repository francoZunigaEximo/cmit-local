@extends('template')

@section('title', 'Usuarios')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Usuarios</h4>
</div>


<div class="p-4">
    <div class="card">

        <!-- Start Filter -->
        <div class="d-flex justify-content-center flex-wrap">
            <div class="p-2">
                <label for="nombre" class="form-labe font-weight-bold"><strong>Nombre</strong></label>
                <!-- Select2 de la base -->
                <select name="" id="" hidden></select>
                <input class="form-control" type="text" name="nombre" id="nombre" placeholder="colocar select2">

            </div>
            <div class="p-2">
                <label for="usuario" class="form-label font-weight-bold"><strong> Usuario </strong></label>
                <input type="text" class="form-control" id="usuario" name="usuario" >
            </div>
            <div class="p-2">
                <label for="rol" class="form-label font-weight-bold"><strong>Rol </strong></label>
                <input type="text" class="form-control" id="rol" name="rol">
            </div>

            <div class="d-flex align-items-end p-3">
                <!-- Busqueda y reinicio -->
                <button type="button" id="buscar" class="btn botonGeneral mt-4"><i class="ri-zoom-in-line"></i>&nbsp;Buscar</button>
                <button type="button" id="reset" class="btn botonGeneral mt-4 ms-1"><i class=" ri-close-circle-line"></i>&nbsp;Reiniciar</button>
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
                <table id="listaOrdenesEfectores" class="display table table-bordered ">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center pe-3"><input type="checkbox" id="checkAll" name="id"></th>
                            <th class="">User</th>
                            <th class="">Nombre</th>
                            <th class="">Rol Activo</th>
                            <th class="">Estado</th>
                            <th class="">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="list form-check-all">
                        <!-- Listar Usuarios -->
                        <tr>
                            <td class="text-center"><input type="checkbox" id=""></td>
                            <td></td>
                            <td></td>
                            <td >
                                <span class="custom-badge nuevoAzul m-1">Rol ejemplo</span>
                                <span class="custom-badge nuevoAzul m-1">Rol ejemplo</span>
                            </td>
                            <td><p class="text-success">Activo</p></td>
                            <td >
                                <div>
                                    <a title="Editar" href="http://localhost/cmit/public/examenesCuenta/9506/edit"><button type="button" class="btn btn-sm iconGeneralNegro"><i class="ri-edit-line"></i></button></a>
                                    <button data-id="" title="Dar de baja" type="button" class="btn btn-sm iconGeneralNegro"><i class="ri-delete-bin-2-line"></i></button>
                                    <button class="btn btn-sm iconGeneralNegro"><i class=" ri-lock-2-line"></i></button>
                                </div>
                            </td>
                            
                        </tr>
                        <tr>
                            <td class="text-center"><input type="checkbox" id=""></td>
                            <td></td>
                            <td></td>
                            <td >
                                <span class="custom-badge nuevoAzul m-1">Rol ejemplo</span>
                                <span class="custom-badge nuevoAzul m-1">Rol ejemplo</span>
                                <span class="custom-badge nuevoAzul m-1">Rol ejemplo</span>
            
                            </td>
                            <td><p class="text-danger">Inactivo</p></td>
                            <td>
                                <div>
                                    <a title="Editar" href="http://localhost/cmit/public/examenesCuenta/9506/edit"><button type="button" class="btn btn-sm iconGeneralNegro"><i class="ri-edit-line"></i></button></a>
                                    <button data-id="" title="Dar de baja" type="button" class="btn btn-sm iconGeneralNegro"><i class="ri-delete-bin-2-line"></i></button>
                                    <button class="btn btn-sm iconGeneralNegro"><i class=" ri-lock-unlock-line"></i></button>
                                </div>
                            </td>
                            
                        </tr>
                        
                    </tbody>
                </table>

                <!-- data table (revisar)-->
                <div class="row">
                    <div class="col-sm-12 col-md-5">
                        <div class="dataTables_info" id="listadoExamenesCuentas_info" role="status" aria-live="polite">
                            Mostrando
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-7">
                        <div class="dataTables_paginate paging_simple_numbers" id="listadoExamenesCuentas_paginate">
                            <ul class="pagination">
                                <li class="paginate_button page-item previous disabled" id="listadoExamenesCuentas_previous">
                                    <a aria-controls="listadoUsuarios" aria-disabled="true" aria-label="Anterior" role="link" data-dt-idx="previous" tabindex="-1" class="page-link">
                                        Anterior
                                    </a>
                                </li>
                                <li class="paginate_button page-item active">
                                    <a href="#" aria-controls="listadoUsuarios" role="link" aria-current="page" data-dt-idx="0" tabindex="0" class="page-link">
                                        1
                                    </a>
                                </li>
                                <li class="paginate_button page-item next disabled" id="listadoExamenesCuentas_next">
                                    <a aria-controls="listadoExamenesCuentas" aria-disabled="true" aria-label="Siguiente" role="link" data-dt-idx="next" tabindex="-1" class="page-link">
                                        Siguiente
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Fin Data table (revisar) -->
            </div>
        </div>
        <!-- End Table -->

        
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
@endpush

@push('scripts')
<!--datatable js-->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script src="{{ asset('js/usuarios/index.js')}}?v={{ time() }}"></script>
<script src="{{ asset('js/usuarios/paginacion.js')}}?v={{ time() }}"></script>

@endpush

@endsection