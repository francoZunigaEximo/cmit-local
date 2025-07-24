<?php

namespace App\Services\Reportes\Estudios;

use FPDF;
use App\Services\Reportes\Reporte;
use App\Models\Prestacion;
use App\Services\Reportes\Titulos\MarcaRetiraFisico;
use App\Services\Reportes\Titulos\NroPrestacion;
use App\Helpers\Tools;
use App\Models\DatoPaciente;
use App\Models\Localidad;
use App\Models\Paciente;
use App\Models\Telefono;
use App\Services\Reportes\ReporteConfig;
use Carbon\Carbon;
use DateTime;

class AudiometriaCmit extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'],  $vistaPrevia = false): void
    {
        include('variables.php');
        $cabecera = new NroPrestacion();
        $retiraFisico = new MarcaRetiraFisico();

        $arrSubtitulo = [
            'INGRESO' => 'X INGRESO /  PERIODICO /  EGRESO /  OCUPACIONAL',
            'PERIODICO' => 'INGRESO / X PERIODICO /  EGRESO /  OCUPACIONAL',
            'EGRESO' =>  'INGRESO /  PERIODICO / X EGRESO /  OCUPACIONAL',
            'OCUPACIONAL' => 'INGRESO /  PERIODICO /  EGRESO / X OCUPACIONAL'
        ];

        $cabecera->render($pdf, ['id' => $prestacion->Id]);
        $retiraFisico->render($pdf, ['rf' => $prestacion->empresa->RF]);
        
        if(!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 182, 10, 15, 15);

        $pdf->SetFont('Arial','B',13);$pdf->SetXY(10,32);$pdf->Cell(200,5,'AUDIOGRAMA',0,0,'C');
        $pdf->SetFont('Arial','B',9);$pdf->SetXY(10,37);$pdf->Cell(200,4,$arrSubtitulo[$prestacion->TipoPrestacion],0,0,'C');$pdf->Ln();
        $pdf->Image(public_path(ReporteConfig::$AUDIOMETRIA),25,42,162); $pdf->SetFont('Arial','',8);
        $pdf->SetXY(156,50);$pdf->Cell(0,3,Carbon::parse($prestacion->Fecha)->format('d/m/Y'),0,0,'L');$pdf->SetXY(50,50);$pdf->Cell(0,3,substr($paciente,0,40),0,0,'L');
        $pdf->SetXY(45,54);$pdf->Cell(0,3,substr($prestacion->paciente->Direccion,0,25),0,0,'L');
        $pdf->SetXY(40,59);$pdf->Cell(0,3,substr($locpac,0,20),0,0,'L');$pdf->SetXY(105,59);$pdf->Cell(0,3,$pcia,0,0,'L');

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