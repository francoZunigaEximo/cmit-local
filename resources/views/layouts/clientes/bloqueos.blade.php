@extends('template')

@section('title', 'Listado de Bloqueos y Eliminados')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-start">
    <h4 class="mb-sm-0">Listado de Bloqueos y Eliminados</h4>
</div>

<div class="row card">
    <div class="col-12 card-header">
    
    </div>
    <div class="col-12 card-body">

        <div class="row mx-auto">
            <div class="table-responsive table-card mt-3 mb-1 mx-auto col-sm-8">
                <table id="lstBloqueoCliente" class="display table table-bordered">
                    <thead class="table-light">
                            <tr>
                                <th>Razon Social</th>
                                <th>CUIT</th>
                                <th>Para Empresa</th>
                                <th>Nombre Fantasia</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    <tbody class="list form-check-all" id="lstBloqueo">

                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
<script>
    const SEARCHBLOQUEO = "{{ route('clientes.listadoBloqueados') }}";
    const restaurarEliminado = "{{ route('clientes.restaurarEliminado') }}";
</script>


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>


    <script src="{{ asset('js/clientes/paginacionEliminados.js')}}?v={{ time() }}"></script>
     <script src="{{ asset('js/clientes/bloqueados.js')}}?v={{ time() }}"></script>

@endpush

@endsection