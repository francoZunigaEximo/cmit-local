<div>
    <div class="text-center">
        <h2>Examen {{ $content['tipoPrestacion'] }}</h2>
    </div>
    <p class="mt-3"><strong>Sres. {{ $content['RazonSocial'] }}</strong></p>
    
    <p class="mt-3">El Sr/a {{ $content['paciente'] }}, {{ $content['TipoDocumento'] }} {{ $content['Documento'] }} derivado de nuestro servicio el día {{ $content['Fecha'] }} con el fin de efectuar el examen {{ $content['tipoPrestacion'] }} para la tarea {{ $content['tarea'] }} según los estudios detallados: </p>

    <p class="mt-3"><strong><u>Detalle de Estudios:</u></strong></p>

    @foreach ($content['examenes'] as $examen)
        - {{ $examen->examenes->Nombre }}<br>
    @endforeach

    {{-- <p>{{ $content['examenes'] }}</p> --}}

    <p class="mt-3"><strong>Ha presentado la siguiente calificación:</strong>
        <br>{{ $content['evaluacion'] }}
        <br>{{ $content['calificacion'] }}
    </p>

    <p class="mt-3"><strong>Observaciones de la calificación:</strong>
    <br>{{ $content['obsEvaluacion'] }}</p>
</div>