<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title>Error 403 - Salud Ocupacional SRL</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link rel="mask-icon" href="{{ asset('images/safari-pinned-tab.svg') }}" color="#5bbad5">

    <link href="{{ asset('css/signin.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">

</head>
<body>
    <div class="row">
        <div class="col-sm-12">
            <h1 class="titulo-error">403</h1>
        </div>
        <div class="col-sm-12 center">
            <h3 class="subtitulo-error">Prohibido</h3>
        </div>
        <div class="col-sm-12 center">
            <p class="mensaje-error">No tiene acceso a esta información.</p>
        </div>
        <div class="col-sm-12 center">
            <img class="logo-error img-fluid opacity-35 " src="{{ asset('images/logo.png')}}" alt="Salud Ocupacional SRL">
        </div>
        <div class="col-sm-12 center">
            <a class="boton-error btn btn-primary" href="javascript:history.back()">Volver atrás</a>
        </div>
    </div>
    
    
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</html>