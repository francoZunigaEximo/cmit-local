<?php

namespace App\Services\Reportes\Estudios;

use App\Helpers\Tools;
use App\Models\Cliente;
use App\Models\DatoPaciente;
use App\Models\Fichalaboral;
use App\Models\Localidad;
use App\Models\Prestacion;
use App\Models\Provincia;
use App\Models\Telefono;
use App\Services\Reportes\Reporte;
use App\Services\Reportes\ReporteConfig;
use FPDF;

use DateTime;

class EXAMENREPORTE12 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');
        
        if($prestacion->empresa->RF === 1){
            $pdf->SetFont('Arial','B',14);$pdf->SetXY(170,4);$pdf->Cell(0,3,'RF',0,0,'L');$pdf->SetFont('Arial','',8);
        }

        //pagina 1
        $pdf->Image(public_path("/archivos/reportes/E5.jpg"),25,44,170); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','B',12);$pdf->SetXY(60,32);$pdf->Cell(0,4,'REGISTRO DE HISTORIA CLINICA OCUPACIONAL',0,0,'L');
        $pdf->SetFont('Arial','B',10);$pdf->SetXY(90,36);$pdf->Cell(0,4,'EXAMEN PERIODICO',0,0,'L');
        $pdf->SetFont('Arial','B',8);$pdf->SetXY(95,40);$pdf->Cell(0,3,'SECRETO MEDICO',0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(40,73);$pdf->Cell(0,3,$ide.' '.substr($paraempresa,0,40),0,0,'L');
        $pdf->SetXY(51,80);$pdf->Cell(0,3,substr($paciente,0,40),0,0,'L');
        $pdf->SetXY(35,87);$pdf->Cell(0,3,$sexo,0,0,'L');$pdf->SetXY(70,87);$pdf->Cell(0,3,$ec,0,0,'L');
        $pdf->SetXY(100,87);$pdf->Cell(0,3,$obsec,0,0,'L');
        $pdf->SetXY(45,93);$pdf->Cell(0,3,substr($lugarnac,0,20),0,0,'L');
        $pdf->SetXY(43,100);$pdf->Cell(0,3,$fechanac,0,0,'L');$pdf->SetXY(80,100);$pdf->Cell(0,3,substr($nac,0,20),0,0,'L');
        $pdf->SetXY(137,100);$pdf->Cell(0,3,$doc,0,0,'L');
        $pdf->SetXY(44,113);$pdf->Cell(0,3,substr($domipac,0,50),0,0,'L');
        $pdf->SetXY(40,119);$pdf->Cell(0,3,substr($locpac,0,20),0,0,'L');
        $pdf->SetXY(98,119);$pdf->Cell(0,3,$pcia,0,0,'L');
        $pdf->SetXY(40,126);$pdf->Cell(0,3,$telpac,0,0,'L');
        $pdf->SetXY(44,139);$pdf->Cell(0,3,$puesto,0,0,'L');$pdf->SetXY(100,139);$pdf->Cell(0,3,$sector,0,0,'L');
        $pdf->SetXY(169,139);$pdf->Cell(0,3,$fi,0,0,'L');
        $pdf->SetXY(56,146);$pdf->Cell(0,3,$antigpto,0,0,'L');$pdf->SetXY(117,146);$pdf->Cell(0,3,$antig,0,0,'L');
        $pdf->SetXY(43,153);$pdf->Cell(0,3,substr($jor,0,15),0,0,'L');
        $pdf->SetXY(47,159);$pdf->Cell(0,3,$obsjor,0,0,'L');
        $pdf->SetXY(47,165);$pdf->MultiCell(130,3,$obsfl,0,'J',0,5);
        //if($foto!=''){$pdf->Image($foto,155,60,52,38);}
        //pagina 2
        $pdf->SetFont('Arial','',7);
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E5_1.jpg"),20,18,180);
        $pdf->Image(public_path("/archivos/reportes/E5_2.jpg"),20,182,180);
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',7);
        $pdf->SetXY(20,6);$pdf->Cell(0,3,'Paciente: '.$paciente.' '.$doc,0,0,'L');$pdf->SetXY(20,10);$pdf->Cell(0,3,$fecha,0,0,'L');
        include('paginaadicional.php');
    }

    private function edad($fechaNacimiento){
        
        $fecha_nacimiento = new DateTime($fechaNacimiento);
        // Fecha actual
        $hoy = new DateTime('now');

        // Calcular la diferencia
        $edad = $hoy->diff($fecha_nacimiento)->y;
        return $edad;
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
        return Telefono::where('IdEntidad', $idPaciente)->first(['CodigoArea', 'NumeroTelefono']);
    }

    private function localidad(int $id):mixed
    {
        return Localidad::find($id);
    }

}