<?php  
//Vertical: se usa en toda la intranet, no solo en la prestacion
//configura
$imagen= "/archivos/fotos/LogoEmpresa.jpg";
$nombref="CMIT | SALUD OCUPACIONAL SRL";
$dir="Juan B. Justo 825 - Neuquen Cap. - 0299 4474371 /4474686 - www.cmit.com.ar";
//imprime
$pdf->Image(public_path($imagen),10,6,20);$pdf->SetY(19);
$pdf->SetFont('Arial','B',7);$pdf->SetX(10);$pdf->Cell(100,3,$nombref,0,0,'L');$pdf->Ln();
$pdf->SetFont('Arial','',7);$pdf->SetX(10);$pdf->Cell(0,3,$dir,0,0,'L');$pdf->Ln();
$pdf->Line(10,26,200,26);	
?>