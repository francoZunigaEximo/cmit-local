<div>
    <p>Sres. {{ $content['RazonSocial'] }}</p>
    <p class="mt-3">El Sr/a {{ $content['paciente'] }}, {{ $content['TipoDocumento'] }} {{ $content['Documento'] }} derivado de nuestro servicio el día {{ $content['Fecha'] }} con el fin de efectuar el examen para la tarea según los estudios detallados.</p>

    <p class="mt-3"><strong><u>Detalle de Estudios:</u></strong></p>

    @foreach ($content['examenes'] as $examen)
    
        <p>{{ $examen->Nombre }}</p>
    
    @endforeach

    <p class="mt-3">HA SIDO CALIFICADO</p>

    <p class="mt-3">Solicitamos <strong>cancelar el saldo adeudado</strong> para avanzar con el envío del estudio a su casilla de correo.</p>

    <p class="mt-3">E el caso de haber realizado el pago, por favor, envienos el respectivo comprobante de pago a info@cmit.com.ar</p>
</div>