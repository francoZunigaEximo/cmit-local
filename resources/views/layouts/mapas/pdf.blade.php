<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html"; charset="UTF-8">

<style>
    @page {
    size: landscape;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    th, td {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    thead {
        background-color: #f2f2f2; /* Color de fondo para el encabezado */
    }
</style>

</head>
<body>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th scope="col">Nro</th>
                <th scope="col">Art</th>
                <th scope="col">Empresa</th>
                <th scope="col">Inactivo</th>
                <th scope="col">Nro de Remito</th>
                <th scope="col">Apellido y Nombre</th>
                <th scope="col">eEnviado</th>
                <th scope="col">Cerrado</th>
                <th scope="col">Entregado</th>
                <th scope="col">Observación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($result as $data)
            <tr>
                <td>{{ $data->Nro}}</td>
                <td>{{ $data->Art }}</td>
                <td>{{ $data->Empresa }}</td>
                <td>{{ $data->Inactivo == 0 ? 'No': 'Sí' }}</td>
                <td>{{ $data->NroCEE }}</td>
                <td>{{ $data->Apellido }} {{ $data->Nombre }}</td>
                <td>{{ $data->eEnviado == 0 ? 'No': 'Sí' }}</td>
                <td>{{ $data->Cerrado == 0 ? 'No': 'Sí' }}</td>
                <td>{{ $data->Entregado == 0 ? 'No': 'Sí' }}</td>
                <td>{{ $data->Obs }}</td>
            </tr>
            @endforeach
            
        </tbody>
    </table> 
</body>
</html>