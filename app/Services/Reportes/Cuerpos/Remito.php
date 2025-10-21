<?php

namespace App\Services\Reportes\Cuerpos;

use App\Models\Prestacion;
use FPDF;
use App\Services\Reportes\Reporte;
use App\Services\Reportes\ReporteConfig;
use Illuminate\Support\Facades\DB;

class Remito extends Reporte 
{

    public function render(FPDF $pdf, $datos = ['id']):void
    {
        $prestacion = $this->prestacion($datos['id']);
        $query = $this->informacion($datos['id']);
        $totalExamenes = $this->totalExamenes($datos['id']);

        $pdf->Image(public_path(ReporteConfig::$LOGO),10,6,20);
        $pdf->SetY(19);
        $pdf->SetFont('Arial','B',7);
        $pdf->SetX(10);
        $pdf->Cell(100,3,ReporteConfig::$TITULO,0,0,'L');
        $pdf->Ln();

        $pdf->SetFont('Arial','',7);
        $pdf->SetX(10);
        $pdf->Cell(0,3, ReporteConfig::$DIRECCION,0,0,'L');
        $pdf->Ln();

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetXY(10, 7);
        $pdf->Cell(200, 15, "REMITO DE ENTREGA DE ESTUDIOS", 0, 0, 'C'); // Centrado

        $anchoPagina = $pdf->GetPageWidth();
        $pdf->Code39(155,12,$datos['id'],1,5);

        $pdf->SetLineWidth(0.2);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->Line(10, 30, $anchoPagina - 10, 30);

        $pdf->Rect(10,35,190,12);
        $pdf->SetFont('Arial','B',9);
        $pdf->SetXY(10,37);
        $pdf->Cell(0,3,"ART: " . utf8_decode($prestacion->art->RazonSocial),0,0,'L');
        $pdf->SetXY(150,37);$pdf->Cell(0,3,"REMITO: " . $prestacion->NroCEE,0,0,'L');
        $pdf->SetXY(10,42);
        $pdf->Cell(0,3,"EMPRESA: " . utf8_decode($prestacion->empresa->RazonSocial),0,0,'L');
        $pdf->SetXY(150,42);$pdf->Cell(0,3,"MAPA: " . $prestacion->IdMapa,0,0,'L');

        $pdf->SetXY(10,49);
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(0,3,utf8_decode("Por medio de la presente le entregamos los estudios que a continuación se detallan."),0,0,'L');

        $pdf->SetFillColor(240, 240, 240);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(0, 0, 0);

        $w_paciente = 57;
        $w_dni = 25;
        $w_cuil = 25;
        $w_prestacion = 25;
        $w_examen = 57;

        $pdf->SetXY(10,55); 
        $pdf->Cell($w_paciente, 6, utf8_decode("Paciente"), 1, 0, 'L', true);
        $pdf->Cell($w_dni, 6, utf8_decode("DNI"), 1, 0, 'C', true);
        $pdf->Cell($w_cuil, 6, utf8_decode("CUIL"), 1, 0, 'C', true);
        $pdf->Cell($w_prestacion, 6, utf8_decode("Prestación"), 1, 0, 'L', true);
        $pdf->Cell($w_examen, 6, utf8_decode("Exámen"), 1, 0, 'L', true);
        $pdf->Ln();

        $pdf->SetFillColor(255, 255, 255); 
        $pdf->SetTextColor(0, 0, 0);

        foreach ($query as $registro) {

            $pdf->SetX(10); // Reiniciar posición X para cada fila
            $pdf->Cell($w_paciente, 6, utf8_decode($registro->nombreCompleto), 1, 0, 'L', true);
            $pdf->Cell($w_dni, 6, $registro->Documento, 1, 0, 'C', true);
            $pdf->Cell($w_cuil, 6, $registro->Cuit, 1, 0, 'C', true);
            $pdf->Cell($w_prestacion, 6, utf8_decode($registro->IdPrestacion), 1, 0, 'L', true);
            $pdf->Cell($w_examen, 6, utf8_decode($registro->NombreExamen), 1, 0, 'L', true);
            $pdf->Ln(); // Nueva fila
        }

        $pdf->Ln(5);
        $pdf->SetX(10);
        $pdf->SetFont('Arial','',8);

        $anchoColumna = 190 / 3;

        $pdf->Cell($anchoColumna,3,utf8_decode("Cantidad: " . $totalExamenes),0,0,'L');
        $pdf->Cell($anchoColumna,3,utf8_decode("Recibí Conforme"),0,0,'C');
        $pdf->Cell($anchoColumna,3,utf8_decode("Entrega a Domicilio"),0,0,'R');


    }

    private function prestacion(int $id):mixed
    {
        return Prestacion::where('NroCEE' , $id)->first();
    }

    private function informacion(int $id):mixed
    {
         return Prestacion::join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
                        ->join('clientes as empresa', 'prestaciones.IdEmpresa', '=', 'empresa.Id')
                        ->join('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
                        ->join('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
                        ->join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
                        ->select(
                            DB::raw("CONCAT(pacientes.Apellido,' ',pacientes.Nombre) as nombreCompleto"),
                            'pacientes.Documento as Documento',
                            'pacientes.Identificacion as Cuit',
                            'prestaciones.Id as IdPrestacion',
                            'examenes.Nombre as NombreExamen'
                        )->where('prestaciones.NroCEE', $id)
                        ->get();
    }

    private function totalExamenes(int $id): int
    {
         return Prestacion::join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
                        ->join('clientes as empresa', 'prestaciones.IdEmpresa', '=', 'empresa.Id')
                        ->join('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
                        ->join('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
                        ->join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
                        ->where('prestaciones.NroCEE', $id)
                        ->count();
    }
}