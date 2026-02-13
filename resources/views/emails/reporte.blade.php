<!-- $asunto='Estudios '.substr($paciente,0,20).' '.$doc;
$cuerpo='<BODY><FONT FACE="arial"><P>Sres.: '.$paraempresa.'
    <P>Adjuntamos estudios.
        <P>Paciente: '.$paciente.'<BR>Fecha: '.$fechaprest.'<BR>'.$tipodoc.': '.$doc.'<BR>Cliente: '.$rsempresa.'<BR>Empresa: '.$paraempresa.'<BR>Prestacion: '.str_pad($idprest, 8, "0", STR_PAD_LEFT).'<BR><BR>Muchas gracias<BR><BR><BR><SMALL><B>Area de Examenes<BR>CMIT | SALUD OCUPACIONAL SRL<BR>Juan B. Justo 825<BR>Tel: (0299) 4474371 / 4474686<BR>Neuquen Capital</B></SMALL></FONT></BODY>';	 -->

<div>
    <p>Sres: {{ $content['ParaEmpresa'] }} </p>
    <p>Adjuntamos estudios</p>
    <p>Paciente: {{ $content['paciente'] }}
        <br />Fecha: {{ $content['Fecha'] }}
        <br />{{ $content['TipoDocumento']}}: {{ $content['Documento']}}
        <br />Cliente: {{ $content['RazonSocial']}}
        <br />Empresa: {{ $content['ParaEmpresa']}}
        <br />Prestación: {{ $content['idPrestacion']}}
    </p>
    <p>Muchas gracias.</p>
    <small>
        <b>
            Area de Examenes
            <br />CMIT | SALUD OCUPACIONAL SRL
            <br />Juan B. Justo 825
            <br />Tel: (0299) 4474371 / 4474686
            <br />Neuquen Capital
        </b>
    </small>
</div>