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

class EXAMENREPORTE118 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
        include('variables.php');
        
        $pdf->Image(public_path("/archivos/reportes/E118_1.jpg"),20,25,180); 
        $pdf->Image(public_path("/archivos/reportes/E118_2.jpg"),20,199,180); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(154,31);$pdf->Cell(0,3,$cuit,0,0,'L');
        $pdf->SetXY(154,35);$pdf->Cell(0,3,$cuil,0,0,'L');
        $pdf->SetXY(154,39);$pdf->Cell(0,3,$a,0,0,'L');
        $pdf->SetXY(162,59);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(49,65);$pdf->Cell(0,3,substr($paraempresa,0,50),0,0,'L');$pdf->SetXY(156,65);$pdf->Cell(0,3,substr($cuit,0,30),0,0,'L');
        $pdf->SetXY(44,69);$pdf->Cell(0,3,substr($domie,0,35),0,0,'L');$pdf->SetXY(121,69);$pdf->Cell(0,3,substr($loce,0,12),0,0,'L');
        $pdf->SetXY(157,69);$pdf->Cell(0,3,substr($cpe,0,25),0,0,'L');
        $pdf->SetXY(57,78);$pdf->Cell(0,3,substr($paciente,0,45),0,0,'L');
        $pdf->SetXY(155,78);if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(60,82);$pdf->Cell(0,3,substr($fechanac,0,30),0,0,'L');$pdf->SetXY(128,82);$pdf->Cell(0,3,substr($tareas,0,30),0,0,'L');
        $pdf->SetXY(65,86);$pdf->Cell(0,3,$antigpto,0,0,'L');$pdf->SetXY(150,86);$pdf->Cell(0,3,$fi,0,0,'L');
        $pdf->SetXY(45,90);$pdf->Cell(0,3,substr($telpac,0,25),0,0,'L');$pdf->SetXY(113,90);$pdf->Cell(0,3,substr($domipac,0,35),0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E118_3.jpg"),20,20,186); 
        $pdf->Image(public_path("/archivos/reportes/E118_4.jpg"),20,169,186); 
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
