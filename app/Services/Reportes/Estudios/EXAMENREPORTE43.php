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

class EXAMENREPORTE43 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
        include('variables.php');
        $pdf->Image(public_path("/archivos/reportes/E43.jpg"), 25, 15, 162);
        $pdf->SetXY(174, 31);
        $pdf->Cell(0, 3, '1', 0, 0, 'L');
        switch ($tipo) {
            case 'INGRESO':
                $pdf->SetXY(56, 54);
                $pdf->Cell(0, 3, 'X', 0, 0, 'L');
                break;
            case 'PERIODICO':
                $pdf->SetXY(141, 54);
                $pdf->Cell(0, 3, 'X', 0, 0, 'L');
                break;
            case 'EGRESO':
                $pdf->SetXY(165, 54);
                $pdf->Cell(0, 3, 'X', 0, 0, 'L');
                break;
        }
        list($d, $m, $a) = explode("/", $fecha);
        $pdf->SetXY(143, 48);
        $pdf->Cell(0, 3, $d, 0, 0, 'L');
        $pdf->SetXY(160, 48);
        $pdf->Cell(0, 3, $m, 0, 0, 'L');
        $pdf->SetXY(174, 48);
        $pdf->Cell(0, 3, $a, 0, 0, 'L');
        $pdf->SetXY(27, 66);
        $pdf->Cell(0, 3, $paciente, 0, 0, 'L');
        $pdf->SetXY(27, 75);
        $pdf->Cell(0, 3, $doc, 0, 0, 'L');
        $pdf->SetXY(49, 75);
        $pdf->Cell(0, 3, $nac, 0, 0, 'L');
        
        list($d, $m, $a) = explode("-", $fechanac);
        $pdf->SetXY(75, 79);
        $pdf->Cell(0, 3, $d, 0, 0, 'L');
        $pdf->SetXY(85, 79);
        $pdf->Cell(0, 3, $m, 0, 0, 'L');
        $pdf->SetXY(97, 79);
        $pdf->Cell(0, 3, $a, 0, 0, 'L');
        $pdf->SetXY(108, 77);
        $pdf->Cell(0, 3, $sexo, 0, 0, 'L');
        $pdf->SetXY(127, 77);
        $pdf->Cell(0, 3, $ec, 0, 0, 'L');
        $pdf->SetXY(27, 86);
        $pdf->Cell(0, 3, $domipac, 0, 0, 'L');
        $pdf->SetXY(72, 86);
        $pdf->Cell(0, 3, $telpac, 0, 0, 'L');
        $pdf->SetXY(27, 95);
        $pdf->Cell(0, 3, $tareas, 0, 0, 'L');
        $pdf->SetXY(72, 95);
        $pdf->Cell(0, 3, $sector, 0, 0, 'L');
        $pdf->SetXY(138, 95);
        $pdf->Cell(0, 3, $jor, 0, 0, 'L');
        $pdf->SetY(125);
        include('paginaadicional.php');
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
