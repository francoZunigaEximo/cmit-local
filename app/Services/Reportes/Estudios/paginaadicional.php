<?php
//init

use App\Helpers\Tools;

$pdf->AddPage();
$y=18;
$z='........................';
$z2=$z.$z.$z.$z.$z.$z.$z.$z.$z.$z.'.................';
//title
$pdf->SetFont('Arial','B',10);$pdf->SetXY(10,$y);$pdf->Cell(0,4,'EXAMEN CLINICO',0,0,'C');$y=$y+5;

//text
$pdf->SetFont('Arial','',7);
$pdf->SetXY(5,$y);$pdf->Cell(0,4,'Por medio de la presente autorizo que se me realicen los estudios y practicas necesarias para cumplimentar las exigencias legales',0,0,'C');$y=$y+3;
$pdf->SetXY(5,$y);$pdf->Cell(0,4,'y las que fueran necesarias para el puesto al cual aspiro y/o desempe�o.',0,0,'C');$y=$y+5;

//rect1
$pdf->Rect(15,$y,180,54);$y=$y+1;
$pdf->SetFont('Arial','B',7);$pdf->SetXY(15,$y);$pdf->Cell(0,3,'ASPECTO GENERAL:',0,0,'L');$pdf->SetFont('Arial','',7);$y=$y+6;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,'TA: '.$z.' FC: '.$z.' Estado Nutricional: '.$z.$z.$z.$z.$z.'.......... ZURDO / DIESTRO',0,0,'L');$y=$y+6;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,'Talla: '.$z.' mts. Peso: '.$z.' Kgs. Piel y Faneras: '.$z.$z.$z.$z.$z.$z.'..',0,0,'L');$y=$y+8;
$pdf->SetFont('Arial','B',7);$pdf->SetXY(15,$y);$pdf->Cell(0,3,'CABEZA Y CUELLO:',0,0,'L');$pdf->SetFont('Arial','',7);$y=$y+6;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,'Nariz: '.$z.$z.$z.$z.$z.' Dentadura: '.$z.$z.$z.$z.'..........',0,0,'L');$y=$y+6;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,'Tiroides: '.$z.$z.$z.' Car�tidas: '.$z.$z.$z.' Yugulares: '.$z.$z.'............',0,0,'L');$y=$y+6;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,'Pupilas:'.$z.$z.$z.' Reflejo Fotomotor: '.$z.$z.' Campo Visual Cl�nico: '.$z.$z.'.......',0,0,'L');$y=$y+6;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,'Agudeza visual: Ojo Der.: '.$z.' Ojo Izq.: '.$z.' Con Anteojos? '.$z.$z.' Visi�n Crom�tica: '.$z.$z,0,0,'L');$y=$y+6;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,'Ac�fenos: '.$z.$z.'..... Otoscop�a:'.$z.$z.$z.$z.$z.$z.$z,0,0,'L');$y=$y+5;

//rect2
$pdf->Rect(15,$y,180,42);$y=$y+1;
$pdf->SetFont('Arial','B',7);$pdf->SetXY(15,$y);$pdf->Cell(0,3,'APARATO CARDIOVASCULAR:',0,0,'L');$pdf->SetFont('Arial','',7);$y=$y+6;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,'Auscultaci�n: '.$z.$z.$z.' Ritmo: '.$z.$z.' Soplos: '.$z.$z.$z.'...............',0,0,'L');$y=$y+6;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,'Sistema Vascular Perif�rico: '.$z.$z.$z.$z.$z.$z.$z.$z.'.................',0,0,'L');$y=$y+6;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,'Pulsos Perif�ricos: '.$z.$z.$z.$z.$z.$z.$z.$z.$z.'........',0,0,'L');$y=$y+8;
$pdf->SetFont('Arial','B',7);$pdf->SetXY(15,$y);$pdf->Cell(0,3,'APARATO RESPIRATORIO:',0,0,'L');$pdf->SetFont('Arial','',7);$y=$y+6;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,'Frecuencia Respiratoria: '.$z.$z.$z.$z.$z.$z.$z.$z.$z,0,0,'L');$y=$y+6;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,'Auscultaci�n: '.$z.$z.$z.$z.$z.$z.$z.$z.$z.'.................',0,0,'L');$y=$y+5;

//rect3
$pdf->Rect(15,$y,180,30);$y=$y+1;
$pdf->SetFont('Arial','B',7);$pdf->SetXY(15,$y);$pdf->Cell(0,3,'APARATO DIGESTIVO:',0,0,'L');$pdf->SetFont('Arial','',7);$y=$y+6;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,'Abdomen: '.$z.$z.$z.$z.'.......... H�gado: '.$z.$z.'..... Bazo: '.$z.$z.'.....',0,0,'L');$y=$y+6;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,'Hernias: '.$z.$z.$z.$z.$z.$z.$z.$z.$z.$z,0,0,'L');$y=$y+8;
$pdf->SetFont('Arial','B',7);$pdf->SetXY(15,$y);$pdf->Cell(0,3,'APARATO GENITOURINARIO:',0,0,'L');$pdf->SetFont('Arial','',7);$y=$y+6;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,'Ri�ones: P.P. (        ) DIURESIS: '.$z.$z.$z.' Pr�stata: '.$z.$z.' Genitales: '.$z.$z,0,0,'L');$y=$y+5;

//rect4
$pdf->Rect(15,$y,180,54);$y=$y+1;
$pdf->SetFont('Arial','B',7);$pdf->SetXY(15,$y);$pdf->Cell(0,3,'SISTEMA NERVIOSO:',0,0,'L');$pdf->SetFont('Arial','',7);$y=$y+6;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,'Motricidad: '.$z.$z.$z.$z.'..................... Reflejos: '.$z.$z.$z.$z.'.......',0,0,'L');$y=$y+6;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,'Sensibilidad: '.$z.$z.$z.$z.'.................. Cr�neo: '.$z.$z.$z.$z.'.........',0,0,'L');$y=$y+6;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,'Tono Muscular: '.$z.$z.$z.$z.'..............Signo de Romberg: '.$z.$z.$z.'.............',0,0,'L');$y=$y+8;
$pdf->SetFont('Arial','B',7);$pdf->SetXY(15,$y);$pdf->Cell(0,3,'SISTEMA OSTEOMIOARTICULAR:',0,0,'L');$pdf->SetFont('Arial','',7);$y=$y+6;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,'ADAMS:',0,0,'L');$y=$y+6;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,'Columna Cervical: '.$z.$z.$z.$z.'........ Articulaciones: '.$z.$z.$z.$z,0,0,'L');$y=$y+6;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,'Columna Dorsal: '.$z.$z.$z.$z.'........... Extremidades: '.$z.$z.$z.$z,0,0,'L');$y=$y+6;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,'Columna Lumbosacra: '.$z.$z.$z.$z.'.. Lass�ge: '.$z.$z.$z.$z.'.........',0,0,'L');$y=$y+5;

//obs
$pdf->Rect(15,$y,180,25);$y=$y+1;
$pdf->SetFont('Arial','B',7);$pdf->SetXY(15,$y);$pdf->Cell(0,3,'OBSERVACIONES GENERALES:',0,0,'L');$pdf->SetFont('Arial','',7);$y=$y+5;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,$z2,0,0,'L');$y=$y+5;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,$z2,0,0,'L');$y=$y+5;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,$z2,0,0,'L');$y=$y+5;
$pdf->SetXY(15,$y);$pdf->Cell(0,3,$z2,0,0,'L');$y=$y+6;

//firmas
$pdf->SetFont('Arial','',7);$y=$y+10;
$pdf->Line(30,$y-1,80,$y-1);$pdf->SetXY(30,$y);$pdf->Cell(50,3,'FIRMA DEL POSTULANTE',0,0,'C');
$pdf->Line(130,$y-1,180,$y-1);$pdf->SetXY(130,$y);$pdf->Cell(50,3,'SELLO Y FIRMA DEL PROFESIONAL',0,0,'C');
$y=$y+6;

//conformidad
$pdf->Rect(15,$y,180,15);$y=$y+1;
$pdf->SetFont('Arial','B',7);$pdf->SetXY(15,$y);$pdf->Cell(0,3,'CONFORMIDAD DEL POSTULANTE ENTREVISTADO',0,0,'L');
$pdf->SetFont('Arial','',7);$y=$y+10;
$pdf->Line(25,$y-1,55,$y-1);$pdf->SetXY(25,$y);$pdf->Cell(30,3,'FECHA',0,0,'C');
$pdf->Line(75,$y-1,135,$y-1);$pdf->SetXY(75,$y);$pdf->Cell(60,3,'ACLARACION Y DNI',0,0,'C');
$pdf->Line(155,$y-1,185,$y-1);$pdf->SetXY(155,$y);$pdf->Cell(30,3,'FIRMA',0,0,'C');
$y=$y+6;

//header 

if(!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);

$pdf->SetFont('Arial','',7);
$pdf->SetXY(15,6);$pdf->Cell(0,3,'Paciente: '.$paciente.' '.$doc,0,0,'L');
$pdf->SetXY(15,10);$pdf->Cell(0,3,$fecha,0,0,'L');
?>