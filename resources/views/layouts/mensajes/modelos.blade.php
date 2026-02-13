@extends('template')

@section('title', 'Modelos')

@section('content')
<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Modelos de mensajes</h4>
    <a class="btn btnSuccess" href="{{ route('mensajes.index') }}"><i class="ri-arrow-go-back-line"></i>Volver</a>
</div>

<div class="row">
    <div class="col-sm-12 text-end">
        
        <a class="btn btn-sm botonGeneral" href="{{ route('mensajes.modelos.create')}}"><i class="ri-add-line"></i>Agregar</a>
    </div>
</div>

<div class="table-responsive table-card mt-3 mb-1 mx-auto col-sm-6">
    <table id="listadoModeloMsj" class="display table table-bordered">
        <thead class="table-light">
            <tr>
                <th class="sort">Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody class="list form-check-all" id="lstModeloMsj">

        </tbody>
    </table>
</div>  

<script>
    const SEARCHMODELO = "{{ route('mensajes.modelos')}}";
    const INDEX = "{{ route('mensajes.index') }}";
    const eliminarModelo = "{{ route('mensajes.modelos.delete') }}";
    const linkModelo = "{{ route('mensajes.modelos.edit', ['Id' => '__modelo__']) }}";
</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v=?v={{ time() }}">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script src="{{ asset('js/mensajeria/paginacionModelos.js') }}?v={{ time() }}"></script>
<script src="{{ asset('js/mensajeria/modelos.js') }}?v={{ time() }}"></script>
@endpush
@endsection