@extends('template')

@section('content')

<div class="text-center rounded-3 p-5 shadow-lg text-white">
    <h2> {{$noticia->Titulo}} </h2> 
    <br><br>
    <h4 style={{$estiloSubitutlo}}> {{$noticia->Subtitulo}} </h4> 
    <br><br>
    <h4> {!! $noticia->Texto !!} </h4> 
    <img class="img-fluid" src="{{ asset('storage/noticias/'.$noticia->Ruta) }}"></img>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v=?v={{ time() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
@endpush

@endsection