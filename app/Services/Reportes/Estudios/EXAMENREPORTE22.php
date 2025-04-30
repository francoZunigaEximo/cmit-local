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

class EXAMENREPORTE22 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {   
        include('variables.php');

        if($prestacion->empresa->RF === 1){
            $pdf->SetFont('Arial','B',14);$pdf->SetXY(170,4);$pdf->Cell(0,3,'RF',0,0,'L');$pdf->SetFont('Arial','',8);
        }
        $pdf->Image(public_path("/archivos/reportes/E10.jpg"),25,20,154); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','B',12);$pdf->SetXY(98,28);$pdf->Cell(0,4,'Examen Medico Periodico',0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(120,43);$pdf->Cell(0,3,substr($paraempresa,0,35),0,0,'L');
        $pdf->SetXY(64,54);$pdf->Cell(0,3,substr($paciente,0,50),0,0,'L');
        $pdf->SetXY(74,59);$pdf->Cell(0,3,substr($lugarnac,0,20).' '.$fechanac,0,0,'L');$pdf->SetXY(159,59);$pdf->Cell(0,3,$edad,0,0,'L');
        $pdf->SetXY(51,63);$pdf->Cell(0,3,$doc,0,0,'L');
        $pdf->SetXY(58,67);$pdf->Cell(0,3,$tareas,0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E10_1.jpg"),25,20,149);
        $pdf->SetFont('Arial','B',8);$pdf->SetXY(168,230);$pdf->Cell(0,3,'2',0,0,'L');
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        //pagina 3
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E21_2.jpg"),25,20,151);
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
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