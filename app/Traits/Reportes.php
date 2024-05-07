<?php

namespace App\Traits;

use App\Models\Cliente;
use FPDF;
use App\Models\Prestacion;
use App\Models\Parametro;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;

trait Reportes
{
    private static $URLPORTADA = "/archivos/reportes/portada.jpg";
    private static $LOGO = "/archivos/reportes/LogoEmpresa.jpg";
    private static $TITULO = "CMIT | SALUD OCUPACIONAL SRL";
    private static $DIRECCION = "Juan B. Justo 825 - Neuquen Cap. - 0299 4474371 /4474686 - www.cmit.com.ar";

    private static $E43 = "/archivos/reportes/E43.jpg";

    public function eEstudioCaratula(int $id): void
    {
        $prestacion = Prestacion::find($id);
        $miEmpresa = Parametro::getMiEmpresa();
        if($prestacion)
        {
            $paciente = $prestacion->paciente->Nombre ." ". $prestacion->paciente->Apellido;

            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->Image(url('/').self::$URLPORTADA,1,0,209); 
            $y=220;
            $pdf->SetFont('Arial','B',14);
            $pdf->SetTextColor(255, 255, 255, 255);//white
            $pdf->SetXY(109,$y);$pdf->Cell(0,3,substr($paciente,0,28),0,0,'L');$y=$y+10;
            $pdf->SetXY(109,$y);$pdf->Cell(0,3,$prestacion->Fecha,0,0,'L');$y=$y+10;
            $pdf->SetXY(109,$y);$pdf->Cell(0,3,$prestacion->paciente->TipoDocumento.' '.$prestacion->paciente->Documento,0,0,'L');$y=$y+10;
            $pdf->SetXY(109,$y);$pdf->Cell(0,3,substr($prestacion->empresa->RazonSocial,0,28),0,0,'L');$y=$y+10;
            $pdf->SetXY(109,$y);$pdf->Cell(0,3,substr($prestacion->empresa->ParaEmpresa,0,28),0,0,'L');$y=$y+10;
            $pdf->SetXY(109,$y);$pdf->Cell(0,3,$id,0,0,'L');$y=$y+10;
            $pdf->SetTextColor(0, 0, 0, 0);
            $pdf->Output($miEmpresa->Path4."caratula_".$id.".pdf", "F");
        }
    }
    
    public function PDFREPG1($pdf,$idprest,$tipocar,$firmaeval, ?int $salida){
        //si lo llaman desde enviarinformes.php
        if($salida==4){
            $pdf=new FPDF('P','mm','A4');
        }
        //$tipocar 1 e-estudio,$firmaeval 1:foto+sello,2:sello
        $this->PDFREPG8($pdf,$idprest,$tipocar);
        //busco idempresa
        $idempresa = $this->REP_EmpresaPrestacion($idprest) ?? 0;
        //verifico reporte segun idempresa
        if ($idempresa==203){$this->PDFREPE43($pdf,$idprest,0,$firmaeval);}//resumen petreven
        else{
            //resumen cmit
            $idp=str_pad($idprest, 8, "0", STR_PAD_LEFT);
            $query="Select p.Fecha,p.TipoPrestacion,p.Observaciones,p.Evaluacion,p.Calificacion,p.IdEvaluador,p.Cerrado,pa.Apellido,pa.Nombre,pa.TipoDocumento,pa.Foto,pa.Documento,c.ParaEmpresa,dp.Edad,dp.Tareas,dp.Puesto From prestaciones p,pacientes pa,clientes c,datospacientes dp Where dp.IdPrestacion=p.Id and c.Id=p.IdEmpresa and pa.Id=p.IdPaciente and p.Id=$idprest";$rs=mysql_query($query,$conn);$row=mysql_fetch_array($rs);
            if(mysql_num_rows($rs)!=0){
                $fecha=FormatoFecha($row['Fecha']);$paraempresa=substr($row['ParaEmpresa'],0,50);$tipop=$row['TipoPrestacion'];
                $paciente=substr($row['Apellido']." ".$row['Nombre'],0,25);$doc=$row['TipoDocumento'].': '.$row['Documento'];
                $obs=$row['Observaciones'];	
                $tareas=$row['Tareas'];$puesto=$row['Puesto'];$calif=$row['Calificacion'];$eval=$row['Evaluacion'];$idevaluador=$row['IdEvaluador'];
                if ($row['Foto']!=''){$foto=$nivelarbol."Archivos/Fotos/".$row['Foto'];}else{$foto='';}
                $prestcerrada=$row['Cerrado'];//para ver si muestro calificacion y firma
            }mysql_free_result($rs);
            //
            GLO_PRETipoDetalle($tipop,$titulo,$tipoex,$tipof,$tareas,$puesto,0);
            //
            $texto= "El Sr/a, $paciente, $doc derivado a nuestro servicio con el fin de efectuar examen $tipoex para la tarea $tipof segun los estudios detallados, ha presentado la siguiente calificacion.";
    
            //Reporte:evaluacion medica, Campo bd: Calificacion
            $eval1='';$eval2='';$eval3='';
            switch (substr($calif,0,1)) {
            case '1': $eval1='X';break;
            case '2': $eval2='X';break;
            case '3': $eval3='X';break;
            case '4': $eval2='X';$eval3='X';break;
            }
            //Reporte: calificacion final, Campo bd: Evaluacion
            $calif1='';$calif2='';$calif3='';$calif4='';
            switch (substr($eval,0,1)) {
            case '1':	$calif1='X';break;
            case '2': $calif2='X';break;
            case '3': $calif3='X';break;
            case '4': $calif4='X';break;
            }
            //banner
            $pdf->AddPage();
            include ("IncludesRep/zBannerLogo.php");
            include ("IncludesRep/zBannerId.php");
            //header
            $pdf->SetFont('Arial','B',10);$pdf->SetXY(10,32);$pdf->Cell(200,4,$titulo,0,0,'C');
            $pdf->SetFont('Arial','',10);$pdf->SetXY(190,36);$pdf->Cell(0,3,'Neuquen '.$fecha,0,0,'R');
            $pdf->SetFont('Arial','B',10);$pdf->SetXY(10,41);$pdf->Cell(0,3,'Sres.: '.$paraempresa,0,0,'L');
            $pdf->SetFont('Arial','',10);$pdf->SetXY(10,46);$pdf->MultiCell(190,4,$texto,0,'J',0,5);
            //examenes 
            $pdf->SetFont('Arial','B',10);$pdf->SetXY(10,63);$pdf->Cell(0,3,'DETALLE DE ESTUDIOS',0,0,'L');$pdf->Ln(4);
            $pdf->SetFont('Arial','',7);
            $query="Select distinct ex.Nombre,ip.ObsExamen From itemsprestaciones ip,examenes ex Where ex.Id=ip.IdExamen and ip.Anulado=0 and ip.IdPrestacion=$idprest Order by ex.Nombre";$rs1=mysql_query($query,$conn);
            //(si son mas de 17 hace dos columnas)
            $cantexamenes=0;$x1=10;$yinicioex=$pdf->GetY();$yfinexcol1=0;$yfinexcol2=0;
            while($row1=mysql_fetch_array($rs1)){ 
                //sumo examen
                $cantexamenes++;
                //si es el nro 18 armo segunda columna
                if($cantexamenes==18){$x1=115;$yfinexcol1=$pdf->GetY();$pdf->SetY($yinicioex);}
                $pdf->SetX($x1);$pdf->Cell(0,3,' - '.substr($row1['Nombre'],0,45),0,0,'L');
                //$pdf->SetX(88);$pdf->Cell(0,3,substr($row1['ObsExamen'],0,75),0,0,'L');
                $pdf->Ln(4);
            }mysql_free_result($rs1);
            //tomo $y fin segunda columna si hay
            if($cantexamenes>17){$yfinexcol2=$pdf->GetY();}else{$yfinexcol1=$pdf->GetY();}
            //comparo cual columna es mas larga para tomar el $y
            if($yfinexcol2>$yfinexcol1){$pdf->SetY($yfinexcol2);}else{$pdf->SetY($yfinexcol1);}
            //espacio
            $pdf->Ln(4);
            //cuadro
            include("IncludesRep/zCuadroResumen.php");
        }
        //si lo llaman desde enviarinformes.php
        if($salida==4){
            $adjunto='../Archivos/EnviarOpciones/eResumen'.$idprest.'.pdf';
            unlink('../Archivos/EnviarOpciones/eResumen'.$idprest.'.pdf');
            $pdf->Output($adjunto,'F');
        }
    }
   
    //caratula
    private function PDFREPG8($pdf,$idprest, $tipocar){//$tipocar 1 e-estudio

        $query = Prestacion::join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->join('clientes', 'prestaciones.IdEmpresa', '=', 'clientes.Id')
            ->select(
                'prestaciones.Fecha as Fecha',
                'pacientes.Id as IdPaciente',
                'pacientes.Apellido as Apelido',
                'pacientes.Nombre as Nombre',
                'pacientes.TipoDocumento as TipoDocumento',
                'pacientes.Documento as Documento',
                'clientes.ParaEmpresa as ParaEmpresa',
                'clientes.RazonSocial as RazonSocial',
            )
            ->where('pacientes.Id', $idprest)
            ->first();


        foreach($query as $q) {
            $pdf->AddPage();
            //tipo caratula
            if($tipocar==0){
                //caratula interna
                $pdf->Image(url('/').self::$LOGO,10,6,20);$pdf->SetY(19);
                $pdf->SetFont('Arial','B',7);$pdf->SetX(10);$pdf->Cell(100,3,self::$TITULO,0,0,'L');$pdf->Ln();
                $pdf->SetFont('Arial','',7);$pdf->SetX(10);$pdf->Cell(0,3,self::$DIRECCION,0,0,'L');$pdf->Ln();
                $pdf->Line(10,26,200,26);	
                $y=35;$pdf->Rect(10,$y,170,44); $pdf->SetFont('Arial','',14);$y=$y+5;
                $pdf->SetXY(11,$y);$pdf->Cell(0,3,'Paciente:',0,0,'L');
                $pdf->SetXY(38,$y);$pdf->Cell(0,3,substr($q->Nombre.' '.$q->Apellido,0,40),0,0,'L');$y=$y+8;
                $pdf->SetXY(11,$y);$pdf->Cell(0,3,'Fecha:',0,0,'L');
                $pdf->SetXY(38,$y);$pdf->Cell(0,3,$q->Fecha,0,0,'L');
                $pdf->SetXY(70,$y);$pdf->Cell(0,3,$q->TipoDocumento.': '.$q->Documento,0,0,'L');$y=$y+8;
                $pdf->SetXY(11,$y);$pdf->Cell(0,3,'Cliente:',0,0,'L');
                $pdf->SetXY(38,$y);$pdf->Cell(0,3,substr($q->RazonSocial,0,40),0,0,'L');$y=$y+8;
                $pdf->SetXY(11,$y);$pdf->Cell(0,3,'Empresa:',0,0,'L');
                $pdf->SetXY(38,$y);$pdf->Cell(0,3,substr($q->ParaEmpresa,0,40),0,0,'L');$y=$y+8;
                $pdf->SetXY(11,$y);$pdf->Cell(0,3,'Prestacion:',0,0,'L');
                $pdf->SetXY(38,$y);$pdf->Cell(0,3,str_pad($idprest, 8, "0", STR_PAD_LEFT),0,0,'L');
            }else{
                //e-estudio
                $pdf->Image(url('/').self::$URLPORTADA,1,0,209);  
                $y=220;$pdf->SetFont('Arial','B',14);
                $pdf->SetTextColor(255, 255, 255, 255);//white
                $pdf->SetXY(109,$y);$pdf->Cell(0,3,substr($q->Nombre.' '.$q->Apellido,0,28),0,0,'L');$y=$y+10;
                $pdf->SetXY(109,$y);$pdf->Cell(0,3,$q->Fecha,0,0,'L');$y=$y+10;
                $pdf->SetXY(109,$y);$pdf->Cell(0,3,$q->TipoDocumento.': '.$q->Documento,0,0,'L');$y=$y+10;
                $pdf->SetXY(109,$y);$pdf->Cell(0,3,substr($q->RazonSocial,0,28),0,0,'L');$y=$y+10;
                $pdf->SetXY(109,$y);$pdf->Cell(0,3,substr($q->ParaEmpresa,0,28),0,0,'L');$y=$y+10;
                $pdf->SetXY(109,$y);$pdf->Cell(0,3,str_pad($idprest, 8, "0", STR_PAD_LEFT),0,0,'L');$y=$y+10;
                $pdf->SetTextColor(0, 0, 0, 0);
            }
        }
    
    } 

    private function REP_EmpresaPrestacion(?int $idprest){
        return Prestacion::find($idprest, ['IdEmpresa']);
    }

    /* Resumen Petreven (no lleva qr, es llamado por reportes generales) */
    private function PDFREPE43($pdf,$idprest,?int $idexamen,$firmaeval){//$firmaeval 1:foto+sello, 2:sello
        $idp = str_pad($idprest, 8, "0", STR_PAD_LEFT); $idpac=0;
        //datos

        $query = Prestacion::join('datospacientes', 'prestaciones.Id', '=', 'datospacientes.IdPrestacion')
            ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->join('clientes as empresa', 'prestaciones.IdEmpresa', '=', 'empresa.Id')
            ->join('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
            ->join('localidades', 'clientes.IdLocalidad', '=', 'localidades.Id')
            ->join('telefonos', 'pacientes.Id', '=', 'teledonos.IdEntidad')
            ->select(
                'datospacientes.Edad as Edad',
                'art.RazonSocial as rsart',
                'pacientes.Id as IdPaciente',
                'pacientes.Identificacion as Identificacion',
                'prestaciones.Fecha as Fecha',
                'prestaciones.TipoPrestacion as TipoPrestacion',
                'prestaciones.Observaciones as Observaciones',
                'prestaciones.Calificacion as Calificacion',
                'prestaciones.Evaluacion as Evaluacion',
                'prestaciones.Evaluador as Evaluador',
                'prestaciones.IdEvaluador as IdEvaluador',
                'prestaciones.Cerrado as Cerrado',
                'pacientes.FechaNacimiento as FechaNacimiento',
                'pacientes.LugarNacimiento as LugarNacimiento',
                'pacientes.Sexo as Sexo',
                'pacientes.Foto as Foto',
                'localidades.Nombre as localidad',
                'localidad.CP as CP',
                'pacientes.Nombre as Nombre',
                'pacientes.Apellido as Apellido',
                'pacientes.Documento as Documento',
                'pacientes.TipoDocumento as TipoDocumento',
                'datospacientes.Direccion as Direccion',
                'datospacientes.EstadoCivil as EstadoCivil',
                'pacientes.Hijos as Hijos',
                'pacientes.Nacionalidad as Nacionalidad',
                'clientes.RF as RF',
                'clientes.ParaEmpresa as ParaEmpresa',
                'datospacientes.Tareas as Tareas',
                'datospacientes.FechaIngreso as FechaIngreso',
                'datospacientes.AntigPuesto as AntigPuesto',
                'datospacientes.Sector as Sector',
                'datospacientes.Jornada as Jornada',
                'datospacientes.TipoJornada as TipoJornada',
                'datospacientes.Puesto as Puesto',
                'telefonos.CodigoArea as CodigoArea',
                'telefonos.NumeroTelefono as NumeroTelefono'
            )
            ->where('prestaciones.Id', $idprest)
            ->whereNot('telefonos.Id', 0)
            ->where('telefonos.TipoEntidad', 'C')
            ->first();

        foreach($query as $q) {

             //Reporte:evaluacion medica, Campo bd: Calificacion
            $eval1='';$eval2='';$eval3='';
            switch (substr($q->Calificacion,0,1)) {
            case '1':	$eval1='X';break;
            case '2':	$eval2='X';break;
            case '3':	$eval3='X';break;
            case '4':	$eval2='X';$eval3='X';break;
            }
            //Reporte: calificacion final, Campo bd: Evaluacion
            $calif1='';$calif2='';$calif3='';$calif4='';
            switch (substr($q->Evaluacion,0,1)) {
            case '1':	$calif1='X';break;
            case '2':	$calif2='X';break;
            case '3':	$calif3='X';break;
            case '4':	$calif4='X';break;

            //cuerpo
            $pdf->SetFont('Arial','',7);
            //pagina 1
            $pdf->AddPage();$pdf->Image(url('/').self::$E43,25,15,162); 
            $pdf->SetXY(174,31);$pdf->Cell(0,3,'1',0,0,'L');
            switch ($q->TipoPrestacion) {
                case 'INGRESO': $pdf->SetXY(56,54);$pdf->Cell(0,3,'X',0,0,'L');break;
                case 'PERIODICO': $pdf->SetXY(141,54);$pdf->Cell(0,3,'X',0,0,'L');break;
                case 'EGRESO': $pdf->SetXY(165,54);$pdf->Cell(0,3,'X',0,0,'L');break;
            }

            $fecha = \Carbon\Carbon::parse($q->Fecha);
            $fechaNac = \Carbon\Carbon::parse($q->FechaNacimiento);
            $telpac = "(".$q->CodigoArea." )".$q->NumeroTelefono;
            $jor = substr($q->TipoJornada.' '.$q->Jornada,0,15);

            $fecha->format('d-m-Y');$pdf->SetXY(143,48);$pdf->Cell(0,3,$fecha->day,0,0,'L');
            $pdf->SetXY(160,48);$pdf->Cell(0,3,$fecha->month,0,0,'L');$pdf->SetXY(174,48);$pdf->Cell(0,3,$fecha->year,0,0,'L');
            $pdf->SetXY(27,66);$pdf->Cell(0,3,substr($q->Apellido." ".$q->Nombre,0,60),0,0,'L');
            $pdf->SetXY(27,75);$pdf->Cell(0,3,$q->Documento,0,0,'L');$pdf->SetXY(49,75);$pdf->Cell(0,3,$q->Nacionalidad,0,0,'L');
            $fechaNac->format('d-m-Y');$pdf->SetXY(75,79);$pdf->Cell(0,3,$fechaNac->day,0,0,'L');
            $pdf->SetXY(85,79);$pdf->Cell(0,3,$fechaNac->month,0,0,'L');$pdf->SetXY(97,79);$pdf->Cell(0,3,$fechaNac->year,0,0,'L');
            $pdf->SetXY(108,77);$pdf->Cell(0,3,$q->Sexo,0,0,'L');$pdf->SetXY(127,77);$pdf->Cell(0,3,$q->EstadoCivil,0,0,'L');
            $pdf->SetXY(27,86);$pdf->Cell(0,3,substr($q->Direccion,0,25),0,0,'L');$pdf->SetXY(72,86);$pdf->Cell(0,3,$telpac,0,0,'L');
            $pdf->SetXY(27,95);$pdf->Cell(0,3,$q->Tareas,0,0,'L');$pdf->SetXY(72,95);$pdf->Cell(0,3,$q->Sector,0,0,'L');
            $pdf->SetXY(138,95);$pdf->Cell(0,3,$jor,0,0,'L');
            $pdf->SetY(125);
            //cuadro
            $this->cuadroResumen($idprest, $pdf, $q->Observacion);
            }

        }        
    }

    private function cuadroResumen(?int $id, $pdf, $obs)
    {
        $query = DB::table('prestaciones_atributos')->where('IdPadre', $id)->first(['SinEval']) ?? 0;

        $y=$pdf->GetY();$yinicial=$y;
        //aca verifico si sigo en esta pagina o addpage, segun espacio cuadro
        //el alto de la pagina es 290  y cuadro+firma es 130
        if($y>150){$pdf->AddPage();$y=10;$yinicial=10;}

        //calificacion (es el campo Calificacion en la bd)
        $pdf->Rect(10,$y,190,10);
        if($query == 0){//si la prest no lleva evaluacion, solo foto y obs
            $pdf->SetFont('Arial','B',9);$y=$y+1;
            $pdf->SetXY(11,$y);$pdf->Cell(0,3,'EVALUACION MEDICA:',0,0,'L');
            $pdf->SetFont('Arial','',8);$y=$y+5;
            $pdf->SetXY(11,$y);$pdf->Cell(0,3,'SANO',0,0,'L');$pdf->Rect(22,$y-1,4,4);
            $pdf->SetXY(45,$y);$pdf->Cell(0,3,'CON AFECCION CONOCIDA PREVIAMENTE',0,0,'L');$pdf->Rect(107,$y-1,4,4);
            $pdf->SetXY(124,$y);$pdf->Cell(0,3,'CON AFECCION DETECTADA EN ESTE EXAMEN',0,0,'L');$pdf->Rect(192,$y-1,4,4);
        }else{$y=$y+6;}

        //evaluacion (es el campo Evaluacion en la bd)
        $y=$y+5;
        $pdf->Rect(10,$y,190,40);
        if($query==0){//si la prest no lleva evaluacion, solo foto y obs
            $pdf->SetFont('Arial','B',9);$y=$y+1;
            $pdf->SetXY(11,$y);$pdf->Cell(0,3,'CALIFICACION FINAL DE APTITUD LABORAL:',0,0,'L');
            $pdf->SetFont('Arial','',8);$y=$y+5;
            $pdf->Rect(11,$y-1,4,4);$pdf->SetXY(15,$y);$pdf->Cell(0,3,'APTO',0,0,'L');
            $pdf->SetXY(31,$y);$pdf->MultiCell(125,3,'SANO SIN PRE-EXISTENCIA: Salud Ocupacional Normal',0,'L',0,3);
            $y=$y+7;
            $pdf->Rect(11,$y-1,4,4);$pdf->SetXY(15,$y);$pdf->Cell(0,3,'APTO',0,0,'L');
            $pdf->SetXY(31,$y);$pdf->MultiCell(125,3,'CON PRE-EXISTENCIA: Existen alteraciones organicas y/o funcionales permanentes, pero que por el momento no interfieren en el adecuado '.utf8_decode('desempeño').' laboral del interesado en sus tareas especificas',0,'L',0,3);
            $y=$y+11;
            $pdf->Rect(11,$y-1,4,4);$pdf->SetXY(15,$y);$pdf->Cell(0,3,'APTO',0,0,'L');
            $pdf->SetXY(31,$y);$pdf->MultiCell(125,3,'CON PRE-EXISTENCIA: Solo puede cumplir con las tareas en condiciones especiales de trabajo',0,'L',0,3);
            $y=$y+7;
            $pdf->Rect(11,$y-1,4,4);$pdf->SetXY(15,$y);$pdf->Cell(0,3,'NO APTO',0,0,'L');
            $pdf->SetXY(31,$y);$pdf->MultiCell(125,3,'Existen alteraciones organicas y/o funcionales INCOMPATIBLES con un adecuado y saludable '.utf8_decode('desempeño').' del postulante en las tareas para las que fuera propuesto',0,'L',0,3);
        }else{$y=$y+31;}

        //observaciones
        $y=$y+10;
        $pdf->Rect(10,$y,190,27);
        $pdf->SetFont('Arial','B',9);$y=$y+1;
        $pdf->SetXY(11,$y);$pdf->Cell(0,3,'CALIFICACION:',0,0,'L');
        $pdf->SetFont('Arial','',7);$y=$y+5;
        //la prestacion debe estar cerrada para mostrar observaciones evaluacion
        if($prestcerrada==1){
            $pdf->SetXY(11,$y);$pdf->MultiCell(180,3,$obs,0,'J',0,5);
        }



        //evaluacion
        $y=$yinicial;
        $pdf->SetFont('Arial','B',10);

        //la prestacion debe estar cerrada para mostrar evaluacion y calificacion
        //si la prest no lleva evaluacion, solo foto y obs
        if($prestcerrada==1 and $query==0){	
            $pdf->SetXY(22,$y+6);$pdf->Cell(0,3,$eval1,0,0,'L');//Reporte:evaluacion medica, Campo bd: Calificacion
            $pdf->SetXY(107,$y+6);$pdf->Cell(0,3,$eval2,0,0,'L');
            $pdf->SetXY(192,$y+6);$pdf->Cell(0,3,$eval3,0,0,'L');
            //calificacion
            $pdf->SetXY(11,$y+17);$pdf->Cell(0,3,$calif1,0,0,'L');//Reporte: calificacion final, Campo bd: Evaluacion
            $pdf->SetXY(11,$y+24);$pdf->Cell(0,3,$calif2,0,0,'L');
            $pdf->SetXY(11,$y+35);$pdf->Cell(0,3,$calif3,0,0,'L');
            $pdf->SetXY(11,$y+42);$pdf->Cell(0,3,$calif4,0,0,'L');	
        }

        //foto paciente
        if($foto!=''){$pdf->Image($foto,160,$y+15,38,27);}


        //la prestacion debe estar cerrada para mostrar firma y sello
        //si la prest no lleva evaluacion, solo foto y obs
        if($prestcerrada==1){	
            //sello y firma evaluador (si tiene y no es pagina Imprimir.php)
            if($idevaluador!=0 and ($firmaeval==1 or $firmaeval==2)){//$firmaeval 1:foto+sello, 2:sello
                //busco sello y firma
                REP_BuscarFirma($idevaluador,$firma,$imagenfirma,$conn);
                //muestro sello y firma
                $y=$y+112;
                if($firmaeval==1){GLO_FirmaPDF($imagenfirma,10,$y+8,$pdf);}//1:foto+sello	
                $pdf->Line(10,$y,60,$y);
                $pdf->SetFont('Arial','',8);$pdf->SetXY(10,$y+2);$pdf->WriteHTMLNonthue($firma);
            }
        }

            }

}