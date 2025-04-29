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

class EXAMENREPORTE24 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {   
        include('variables.php');

        if($prestacion->empresa->RF === 1){
            $pdf->SetFont('Arial','B',14);$pdf->SetXY(170,4);$pdf->Cell(0,3,'RF',0,0,'L');$pdf->SetFont('Arial','',8);
        }
        $pdf->SetFont('Arial','B',13);$pdf->SetXY(10,32);$pdf->Cell(200,5,'ELECTROCARDIOGRAMA EN REPOSO',0,0,'C');
        $pdf->Image(public_path("/archivos/reportes/E24.jpg"),25,40,161);
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',8);
        $pdf->Line(25,38,186,38);
        $pdf->SetXY(50,45);$pdf->Cell(0,3,$paciente,0,0,'L');
        $pdf->SetXY(35,50);$pdf->Cell(0,3,$doc,0,0,'L');$pdf->SetXY(88,50);$pdf->Cell(0,3,$fechanac,0,0,'L');$pdf->SetXY(131,50);$pdf->Cell(0,3,$edad,0,0,'L');
        $pdf->SetXY(35,54);$pdf->Cell(0,3,$sexo,0,0,'L');
        $pdf->SetXY(35,59);$pdf->Cell(0,3,$puesto,0,0,'L');$pdf->SetXY(125,59);$pdf->Cell(0,3,$antig,0,0,'L');
        //pie
        $pdf->Line(25,270,65,270);$pdf->Line(85,270,125,270);$pdf->Line(145,270,185,270);
        $pdf->SetXY(25,272);$pdf->Cell(40,3,'SELLO Y FIRMA DEL PROFESIONAL',0,0,'C');
        $pdf->SetXY(85,272);$pdf->Cell(40,3,'FIRMA POSTULANTE',0,0,'C');
        $pdf->SetXY(145,272);$pdf->Cell(40,3,'ACLARACION Y D.N.I.',0,0,'C');
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