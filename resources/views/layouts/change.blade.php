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

<form class="form-change" id="form-change" action="{{ route('changePassword') }}" method="POST" enctype="multipart/form-data">
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
                        @elseif(Session::has('fail'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{Session::get('fail')}}
                            <button type="button" class="btn btn-sm btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @else 
                    @endif

                    <label for="passactual" class="form-label">Contraseña actual</label>
                    <input type="password" class="form-control" id="passactual" name="passactual" placeholder="Escriba su contraseña actual">
                </div>

                <div class="col-12" style="padding: 1em">
                    <label for="newpass" class="form-label">Nueva Contraseña</label>
                    <input type="password" class="form-control" id="newpass" name="newpass" placeholder="Escriba su nueva contraseña">
                </div>

                <div class="col-12" style="padding: 1em">
                    <label for="newpass_confirmed" class="form-label">Repetir nueva contraseña</label>
                    <input type="password" class="form-control" id="newpass_confirmation" name="newpass_confirmation" placeholder="Vuelva a escribir su nueva contraseña">
                </div>

                <div class="col-lg-12">
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Cambiar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

</form>


@endsection