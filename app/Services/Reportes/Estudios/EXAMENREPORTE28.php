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

class EXAMENREPORTE28 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');

        if ($prestacion->empresa->RF === 1) {
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->SetXY(170, 4);
            $pdf->Cell(0, 3, 'RF', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 8);
        }
        $pdf->SetFont('Arial','B',13);$pdf->SetXY(10,32);$pdf->Cell(200,5,'EXAMEN OSTEOARTICULAR',0,0,'C');
        $pdf->Image(public_path("/archivos/reportes/E28.jpg"),25,40,166);
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(40,45);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(51,49);$pdf->Cell(0,3,$paciente,0,0,'L');$pdf->SetXY(145,49);$pdf->Cell(0,3,$cuil,0,0,'L');
        $pdf->SetXY(40,54);$pdf->Cell(0,3,$paraempresa,0,0,'L');
        $pdf->Line(40,260,85,260);$pdf->Line(125,260,170,260);
        $pdf->SetXY(40,262);$pdf->Cell(45,3,'Firma y aclaracion del Trabajador',0,0,'C');
        $pdf->SetXY(125,262);$pdf->Cell(45,3,'Firma y sello del Medico',0,0,'C');
        $pdf->Line(25,270,191,270);
        $pdf->SetXY(25,272);$pdf->Cell(166,3,'Florida 537  -  Oficina 520  -  2 Piso  -  Ciudad Autonoma de Buenos Aires  (C1005AAK)  Argentina',0,0,'C');
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
