@extends('template')

@section('title', 'Lista de Prestaciones - Llamador Evaluador')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0 capitalize">ordenes de examen <span class="custom-badge amarillo capitalize">evaluador</span></h4>
    <div class="page-title-right d-inline"></div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v={{ time() }}">
@endpush

@push('scripts')


@endpush

@endsection