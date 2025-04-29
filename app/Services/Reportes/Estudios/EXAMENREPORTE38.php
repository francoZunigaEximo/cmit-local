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

class EXAMENREPORTE38 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
        include('variables.php');
        $pdf->Image(public_path("/archivos/reportes/E38.jpg"),25,20,141);
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(105,60);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(43,67);$pdf->Cell(0,3,$paraempresa,0,0,'L');
        $pdf->SetXY(54,91);$pdf->Cell(0,3,$paciente,0,0,'L');
        $pdf->SetXY(50,96);$pdf->Cell(0,3,$doc,0,0,'L');
        if($sexo=='Masculino'){$pdf->SetXY(52,102);}else{$pdf->SetXY(61,102);}$pdf->Cell(0,3,'X',0,0,'L');
        $pdf->SetXY(55,106);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(50,111);$pdf->Cell(0,3,$puesto,0,0,'L');
        $pdf->SetXY(60,116);$pdf->Cell(0,3,$antig,0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E38_1.jpg"),25,30,141);
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
