@extends('template')

@section('title', 'Clientes')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Clientes</h4>

    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="{{ route('clientes.index') }}">Clientes</a></li>
            <li class="breadcrumb-item active">Listado</li>
        </ol>
    </div>
</div>

<div class="card">
    <div id="mensajeria"></div>

    <div class="card-body">
        <div class="listjs-table" id="customerList">
            <div class="row mb-2">
                <div class="col-3">
                        
                    <div>
                        @can('clientes_add')
                        <a href="{{ route('clientes.create') }}">
                            <button type="button" class="btn botonGeneral add-btn"  id="create-btn" data-bs-target="#showModal"><i class="ri-add-line align-bottom me-1"></i> Nuevo</button>
                        </a>
                        @endcan
                        @can('clientes_export')
                        <button type="button" id="excel" class="btn botonGeneral" title="Generar reporte en Excel">
                            <i class="ri-file-excel-line"></i>
                        </button>
                        @endcan
                        @can('clientes_send')
                        <a class="btn botonGeneral" title="Mensajería a clientes" href="{{ route('mensajes.index') }}">
                            <i class="ri-send-plane-line" title="Mensajería"></i>
                        </a> 
                        @endcan
                    </div>
                </div>
                <div class="col-2">
                    <div class="mb-2">
                        <select id="TipoCliente" name="TipoCliente" class="form-select">
                            <option value="" disabled selected hidden>Tipo</option>
                            <option value="E">Empresa</option>
                            <option value="A">ART</option>
                        </select>
                    </div>
                </div>
                <div class="col-2">
                    <div class="mb-2">
                        <select class="js-example-basic-multiple" name="filtro[]" multiple="multiple" id="filtro" data-placeholder="Filtro">
                            <option value="bloqueados">Bloqueados</option>
                            <option value="sinMailFact">Sin Mail Facturas</option>
                            <option value="entregaDomicilio">Entrega a Domicilio</option>
                            <option value="sinMailInfor">Sin Mail Informes</option>
                            <option value="sinEnvioMail">Sin Envio Mail</option>
                            <option value="sinMailResultados">Sin Mail Resultados</option>
                            <option value="retiraFisico">Retira Fisico</option>
                            <option value="factSinPaquetes">Fact. Sin Paquetes</option>
                            <option value="sinEval">Sin Evaluación</option>
                        </select>
                    </div>
                </div>
                <div class="col-2">
                    <div class="mb-2">
                        <select id="FPago" name="FPago" class="form-select">
                            <option value="" disabled selected hidden>F.Pago</option>
                            <option value="A">CC.</option>
                            <option value="B">Ctdo.</option>
                            <option value="C">Ctdo(CC Bloq)</option>
                        </select>
                    </div>
                </div>
                <div class="col-3">
                    <div class="" style="width: 100%;">
                        <div class="search-box ms-1">
                            <input type="text" id="buscar" name="buscar" class="form-control search" placeholder="Buscar CUIT, R. Social o ParaEmpresa">
                            <p id="search-instructions" style="font-size: 10px; color: #888;">ENTER para buscar | ESC para limpiar la busqueda</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive table-card mb-1">
                <table id="listaClientes" class="display table table-bordered" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th><input type="checkbox" id="checkAll" name="Id"></th>
                            <th class="sort">Empresa</th>
                            <th class="sort">Para empresa</th>
                            <th class="sort">CUIT</th>
                            <th class="sort">Tipo</th>
                            <th class="sort">Estado</th>
                            <th class="sort">Forma de Pago</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="list form-check-all">
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div><!-- end card -->
</div>



<script>
//Rutas
const exportExcelClientes = "{{ route('exportExcelClientes') }}";
const multipleDown = "{{ route('clientes.multipleDown') }}";
const SEARCH = "{{ route('searchClientes') }}";
const baja = "{{ route('baja') }}";

//Constantes
const TOKEN = "{{ csrf_token() }}";
const GOINDEX = "{{ route('clientes.index') }}";

</script>


@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v=?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
@endpush


@push('scripts')
<!--datatable js-->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

<script src="{{ asset('js/clientes/index.js')}}?=v{{ time() }}"></script>
<script src="{{ asset('js/clientes/utils.js')}}?=v{{ time() }}"></script>
<script src="{{ asset('js/clientes/paginacion.js')}}?=v{{ time() }}"></script>

<script src="{{ asset('js/scripts.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('js/init.select2.js') }}"></script>
@endpush

@endsection