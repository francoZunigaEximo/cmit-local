<?php

namespace App\Services\Reportes\Cuerpos;

use FPDF;
use App\Services\Reportes\Reporte;
use App\Models\Prestacion;
use App\Services\Reportes\DetallesReportes;
use Carbon\Carbon;

class ConstEstCompSimple extends Reporte
{
    use DetallesReportes;

    public function render(FPDF $pdf, $datos = ['id']): void
    {
        $prestacion = $this->prestacion($datos['id']);

        $tipoPrestacion = $this->TipoReportePrestacion($prestacion->TipoPrestacion, $prestacion->paciente->Tareas, $prestacion->paciente->Puesto);

        //texto informe
        $texto="Por medio de la presente dejo constancia que en el dia de la fecha el Sr/a, ".utf8_decode($prestacion->paciente->Apellido." ".$prestacion->paciente->Nombre).", ".$prestacion->paciente->TipoDocumento.': '.$prestacion->paciente->Documento." ha completado su examen ".utf8_decode($tipoPrestacion['tipoExamen'])." para la empresa ".utf8_decode($prestacion->empresa->ParaEmpresa);

        //header
        $pdf->SetFont('Arial','B',10);$pdf->SetXY(10,32);$pdf->Cell(200,4,utf8_decode($tipoPrestacion['titulo']),0,0,'C');	
        $pdf->SetFont('Arial','',10);$pdf->SetXY(190,36);$pdf->Cell(0,3,'Neuquen '.Carbon::parse($prestacion->Fecha)->format('d/m/Y'),0,0,'R');
        $pdf->SetFont('Arial','B',10);$pdf->SetXY(10,41);$pdf->Cell(0,3,'Sres.: '.utf8_decode($prestacion->empresa->ParaEmpresa),0,0,'L');
        $pdf->SetFont('Arial','',10);$pdf->SetXY(10,46);$pdf->MultiCell(190,4,utf8_decode($texto),0,'J',0,5);
    }

    private function prestacion(int $id): mixed
    {
        return Prestacion::with(['paciente', 'empresa', 'datoPaciente'])->find($id);
    }

}