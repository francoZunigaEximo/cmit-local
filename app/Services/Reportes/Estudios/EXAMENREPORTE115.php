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

class EXAMENREPORTE115 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
        include('variables.php');
        
        $pdf->Image(public_path("/archivos/reportes/E115_1.jpg"),20,10,180); 
        $pdf->Image(public_path("/archivos/reportes/E115_2.jpg"),20,126,180); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(151,37);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(132,17);$pdf->Cell(0,3,$cuit,0,0,'L');
        $pdf->SetXY(132,20);$pdf->Cell(0,3,$cuil,0,0,'L');
        $pdf->SetXY(132,24);$pdf->Cell(0,3,$a,0,0,'L');
        //
        $pdf->SetXY(45,45);$pdf->Cell(0,3,substr($paraempresa,0,65),0,0,'L');
        $pdf->SetXY(53,55);$pdf->Cell(0,3,substr($paciente,0,65),0,0,'L');
        $pdf->SetXY(47,61);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        if($sexo=="Femenino"){$pdf->SetXY(139,62);}else{$pdf->SetXY(130,61);}$pdf->Cell(0,3,'X',0,0,'L');
        $pdf->SetXY(53,66);$pdf->Cell(0,3,substr($puesto,0,30),0,0,'L');
        $pdf->SetXY(158,66);$pdf->Cell(0,3,substr($antig,0,30),0,0,'L');
        $pdf->SetXY(57,72);$pdf->Cell(0,3,substr($fechanac,0,30),0,0,'L');
        $pdf->SetXY(132,72);$pdf->Cell(0,3,substr($telpac,0,30),0,0,'L');
        $pdf->SetXY(61,78);$pdf->Cell(0,3,substr($domipac,0,26),0,0,'L');
        $pdf->SetXY(123,78);$pdf->Cell(0,3,substr($locpac,0,30),0,0,'L');
    }

    private function edad($fechaNacimiento)
    {

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

    private function datosPaciente(int $id): mixed
    {
        return DatoPaciente::where('IdPrestacion', $id)->first();
    }

    private function telefono(int $idPaciente): mixed //IdEntidad
    {
        return Telefono::where('IdEntidad', $idPaciente)->first(['CodigoArea', 'NumeroTelefono']);
    }

    private function localidad(int $id): mixed
    {
        return Localidad::find($id);
    }
}
