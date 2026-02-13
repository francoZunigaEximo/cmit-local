@extends('template')

@section('title', 'Noticias')

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Noticias</h4>
</div>
<div>
    <form id="form-update"action="{{ route('noticias.update', ['noticia' => 1]) }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-6 mt-3">
                <label for="Titulo" class="form-label fw-bolder uppercase">Titulo</label>
                <input type="text" class="form-control" id="Titulo" name="Titulo" placeholder="Titulo" value="{{ $noticia->Titulo ?? ''}}">
            </div> 
        </div>
        <div class="row">
            <div class="col-6 mt-3">
                <label for="Subtitulo" class="form-label fw-bolder">Subtitulo</label>
                <input type="text" class="form-control" id="Subtitulo" name="Subtitulo" placeholder="Subtitulo" value="{{ $noticia->Subtitulo ?? ''}}">
            </div>  
            <div class="col-3 mt-3 align-self-center">
                <label class="form-check-label fw-bolder" for="Urgente">Urgente</label>
                <input class="form-check-input" type="checkbox" id="Urgente" name="Urgente" {{$noticia->Urgente == 0 ? '' : 'checked'}}>
            </div>
        </div>

        <div class="row">
            <div class="col-9 mt-3">
                <img id="vistaPrevia" src="@fileUrl('lectura')/Noticias/{{ $noticia->Ruta }}?v={{ time() }}" alt="Previsualización de imagen" style="{{ $noticia->Ruta ? '' : 'display: none;' }}  max-width: 200px; max-height: 200px;">
            </div>
        </div>
        
        <div class="row">
            <div class="col-9 mt-3">
                <label for="Texto" class="form-label fw-bolder">Texto</label>
                <textarea class="form-control" id="Texto" name="Texto" placeholder="Texto" rows="15"> {{ $noticia->Texto ?? ''}} </textarea>
            </div>  
        </div>
        <div class="row">
            <div class="col-9 mt-3">
                <label for="Ruta" class="form-label">Imagen</label>
                <input type="file" class="form-control-sm custom-file-input" id="Ruta" name="Ruta" accept="image/*" style="display: none;">
                <label class="custom-file-label" for="Ruta" style="cursor: pointer;">Selecciona una imagen aquí</label>
                
            </div>
        </div>
        <div class="row">
            <div class="col-lg-1 pt-4">
                <div class="hstack gap-2 justify-content-end">
                    <button type="submit" class="btn botonGeneral">Registrar</button>
                </div>
            </div>
        </div>
    </form>
    <br><br>
    
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hacks.css')}}?v=?v={{ time() }}">
<link rel="stylesheet" href="{{ asset('css/richtext.min.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/noticias/edit.js')}}?v={{ time() }}"></script>
<script src="{{ asset('js/richText/jquery.richtext.js') }}?v={{ time() }}"></script>
@endpush

@endsection