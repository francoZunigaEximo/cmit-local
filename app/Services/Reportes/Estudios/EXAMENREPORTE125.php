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

class EXAMENREPORTE125 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'], $vistaPrevia = false): void
    {
include('variables.php');
        
        $pdf->Image(public_path("/archivos/reportes/E125_1.jpg"),10,20,200); 
        $pdf->Image(public_path("/archivos/reportes/E125_2.jpg"),10,165,200); 
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        //datos
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(54,45);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(54,51);$pdf->Cell(0,3,substr($paciente,0,60),0,0,'L');	
        $pdf->SetXY(35,57);$pdf->Cell(0,3,$doc,0,0,'L');
        $pdf->SetXY(39,63);$pdf->Cell(0,3,substr($domipac,0,60),0,0,'L');
        $pdf->SetXY(172,63);$pdf->Cell(0,3,substr($locpac,0,10),0,0,'L');
        $pdf->SetXY(40,75);$pdf->Cell(0,3,substr($telpac,0,60),0,0,'L');
        if($sexo=="Masculino"){$pdf->SetXY(57,87);}else{$pdf->SetXY(74,87);}$pdf->Cell(0,3,'X',0,0,'L');
        $pdf->SetXY(60,93);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(140,93);$pdf->Cell(0,3,$edad,0,0,'L');
        $pdf->SetXY(43,99);$pdf->Cell(0,3,substr($ec,0,30),0,0,'L');
        $pdf->SetXY(140,99);$pdf->Cell(0,3,$hijos,0,0,'L');
        $pdf->SetXY(57,105);$pdf->Cell(0,3,substr($paraempresa,0,60),0,0,'L');
        $pdf->SetXY(53,111);$pdf->Cell(0,3,substr($puestoestudio,0,60),0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E125_3.jpg"),6,25,200); 
        $pdf->Image(public_path("/archivos/reportes/E125_4.jpg"),6,166,200); 
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        //pagina 3
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E125_5.jpg"),6,25,200); 
        $pdf->Image(public_path("/archivos/reportes/E125_6.jpg"),6,164,200); 
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        //pagina 4
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E125_7.jpg"),6,25,200); 
        $pdf->Image(public_path("/archivos/reportes/E125_8.jpg"),6,173,200); 
        if (!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
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
