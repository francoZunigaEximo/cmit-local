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

class EXAMENREPORTE86 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
        include('variables.php');
 
        $pdf->Image(public_path("/archivos/reportes/E86_1.jpg"),12,15,190); 
        $pdf->Image(public_path("/archivos/reportes/E86_2.jpg"),12,153,190); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(27,31);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(35,47);$pdf->Cell(0,3,substr($paraempresa,0,50),0,0,'L');
        $pdf->SetXY(35,85);$pdf->Cell(0,3,$apellido,0,0,'L');$pdf->SetXY(115,85);$pdf->Cell(0,3,$nombre,0,0,'L');
        $pdf->SetXY(35,90);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(35,95);$pdf->Cell(0,3,substr($domipac,0,40),0,0,'L');
        $pdf->SetXY(78,100);$pdf->Cell(0,3,substr($locpac,0,19),0,0,'L');$pdf->SetXY(120,100);$pdf->Cell(0,3,$cp,0,0,'L');
        $pdf->SetXY(35,105);$pdf->Cell(0,3,$telpac,0,0,'L');$pdf->SetXY(142,105);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(42,110);$pdf->Cell(0,3,substr($puesto,0,20),0,0,'L');$pdf->SetXY(90,110);$pdf->Cell(0,3,substr($sector,0,20),0,0,'L');
        $pdf->SetXY(165,110);$pdf->Cell(0,3,$fi,0,0,'L');
        $pdf->SetXY(50,115);$pdf->Cell(0,3,$antigpto,0,0,'L');
        $pdf->SetXY(34,144);$pdf->Cell(0,3,'CMIT de Irigoyen Miguel Antonio',0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E86_3.jpg"),12,15,190); 
        $pdf->Image(public_path("/archivos/reportes/E86_4.jpg"),12,146,190); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        //pagina 3
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E86_5.jpg"),12,15,190); 
        $pdf->Image(public_path("/archivos/reportes/E86_6.jpg"),12,148,190); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        //pagina 4
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E86_7.jpg"),12,19,190); 
        $pdf->Image(public_path("/archivos/reportes/E86_8.jpg"),12,160,190); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
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
