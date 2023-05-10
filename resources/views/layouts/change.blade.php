@extends('template')

@section('title', 'Cambiar contraseña');

@section('content')

<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4 class="mb-sm-0">Cambiar contraseña</h4>

    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="#">Opciones</a></li>
            <li class="breadcrumb-item active">Cambiar contraseña</li>
        </ol>
    </div>
</div>

<form class="form-change" id="form-change" action="{{ route('changePassword') }}" method="POST" enctype="multipart/form-data" novalidate>
    @csrf

    <div class="row">
        <div class="col-xxl-6" style="margin-top:1em; ">
            <div class="card">
                <div class="col-12" style="padding: 1em">

                    @if(Session::has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{Session::get('success')}}
                        <button type="button" class="btn btn-sm btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <label for="passactual" class="form-label">Contraseña actual</label>
                    <input type="password" class="form-control pe-5 password-input @error('passactual') is-invalid @enderror" id="passactual" name="passactual" placeholder="Escriba su contraseña actual" value="{{ old('passactual') }}">
                    @error('passactual')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-12" style="padding: 1em">
                    <label for="newpass" class="form-label">Nueva Contraseña</label>
                    <input type="password" class="form-control @error('newpass') is-invalid @enderror" id="newpass" name="newpass" placeholder="Escriba su nueva contraseña">
                    @error('newpass')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>


                <div class="col-12" style="padding: 1em">
                    <label for="newpass_confirmed" class="form-label">Repetir nueva contraseña</label>
                    <input type="password" class="form-control @error('newpass_confirmation') is-invalid @enderror" id="newpass_confirmation" name="newpass_confirmation" placeholder="Vuelva a escribir su nueva contraseña" >
                    @error('newpass_confirmation')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-lg-12">
                    <div class="text-end" style="padding: 1em">
                        <button type="submit" class="btn btn-primary">Cambiar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

</form>


@endsection