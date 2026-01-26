<?php

namespace App\Services\Reportes\Estudios;

use App\Helpers\Tools;
use App\Models\DatoPaciente;
use App\Models\Prestacion;
use App\Models\Telefono;
use App\Services\Reportes\Reporte;
use App\Services\Reportes\ReporteConfig;
use FPDF;

class HcPetreven extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'],$vistaPrevia=false):void
    {
        include('variables.php');
        $pdf->Image(public_path(ReporteConfig::$HCPETREVEN),25,25,163); 
        $pdf->Image(public_path(ReporteConfig::$HCPETREVEN1),25,45,163);

        if($prestacion->empresa->RF === 1){
            $pdf->SetFont('Arial','B',14);$pdf->SetXY(170,4);$pdf->Cell(0,3,'RF',0,0,'L');$pdf->SetFont('Arial','',8);
        }

        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);

        $pdf->SetFont('Arial','',7);$pdf->SetXY(10,5);$pdf->Cell(0,3,'1',0,0,'L');
        switch ($prestacion->TipoPrestacion) {
            case 'INGRESO':	$pdf->SetXY(56,58);$pdf->Cell(0,3,'X',0,0,'L');break;
            case 'PERIODICO':$pdf->SetXY(141,58);$pdf->Cell(0,3,'X',0,0,'L');break;
            case 'EGRESO':	$pdf->SetXY(165,58);$pdf->Cell(0,3,'X',0,0,'L');break;
        }

        list($d,$m,$a)=explode("-",$prestacion->Fecha);$pdf->SetXY(143,51);$pdf->Cell(0,3,$d,0,0,'L');
        $pdf->SetXY(160,51);$pdf->Cell(0,3,$m,0,0,'L');$pdf->SetXY(174,51);$pdf->Cell(0,3,$a,0,0,'L');
        $pdf->SetXY(27,70);$pdf->Cell(0,3,substr($paciente,0,70),0,0,'L');
        $pdf->SetXY(27,80);$pdf->Cell(0,3,$prestacion->paciente->Documento,0,0,'L');$pdf->SetXY(49,80);$pdf->Cell(0,3,$prestacion->paciente->Nacionalidad ?? '',0,0,'L');
        list($d,$m,$a)=explode("-",$prestacion->paciente->FechaNacimiento);$pdf->SetXY(75,83);$pdf->Cell(0,3,$d,0,0,'L');
        $pdf->SetXY(85,83);$pdf->Cell(0,3,$m,0,0,'L');$pdf->SetXY(97,83);$pdf->Cell(0,3,$a,0,0,'L');
        $pdf->SetXY(108,81);$pdf->Cell(0,3,$prestacion->paciente->Sexo ?? '',0,0,'L');$pdf->SetXY(127,81);$pdf->Cell(0,3,$prestacion->paciente->EstadoCivil ?? '',0,0,'L');
        $pdf->SetXY(27,90);$pdf->Cell(0,3,substr($prestacion->paciente->Direccion ?? '',0,25),0,0,'L');$pdf->SetXY(72,90);$pdf->Cell(0,3,$telefonoPaciente,0,0,'L');
        $pdf->SetXY(27,100);$pdf->Cell(0,3,substr($datosPaciente->Tareas ?? '',0,25),0,0,'L');$pdf->SetXY(72,100);$pdf->Cell(0,3,$datosPaciente->Sector ?? '',0,0,'L');
        $pdf->SetXY(138,100);$pdf->Cell(0,3,$datosPaciente->TipoJornada ?? '',0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path(ReporteConfig::$HCPETREVEN2),25,15,155); //E8_2_1.jpg
        $pdf->Image(public_path(ReporteConfig::$HCPETREVEN22),25,136,155); //E8_2_2.jpg
        $pdf->SetFont('Arial','',7);$pdf->SetXY(10,5);$pdf->Cell(0,3,'2',0,0,'L');
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        //pagina 3
        $pdf->AddPage();
        $pdf->Image(public_path(ReporteConfig::$HCPETREVEN),25,15,163);
        $pdf->Image(public_path(ReporteConfig::$HCPETREVEN3),25,35,163); //E8_3.jpg
        $pdf->SetFont('Arial','',7);$pdf->SetXY(10,5);$pdf->Cell(0,3,'3',0,0,'L');
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        //pagina 4
        $pdf->AddPage();
        $pdf->Image(public_path(ReporteConfig::$HCPETREVEN),25,15,163);
        $pdf->Image(public_path(ReporteConfig::$HCPETREVEN4),25,35,163);
        $pdf->SetFont('Arial','',7);$pdf->SetXY(10,5);$pdf->Cell(0,3,'4',0,0,'L');
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);

    }

    private function prestacion(int $id): mixed
    {
        return Prestacion::with(['empresa', 'paciente'])->find($id);
    }

    private function datosPaciente(int $id):mixed
    {
        return DatoPaciente::where('IdPrestacion', $id)->first();
    }

    private function telefono(int $idPaciente):mixed //IdEntidad
    {
        $query = Telefono::where('IdEntidad', $idPaciente)->first(['CodigoArea', 'NumeroTelefono']);
        return $query->CodigoArea.$query->NumeroTelefono;
    }

}