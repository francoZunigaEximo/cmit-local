<?php

namespace App\Services\Reportes\Cuerpos;

use App\Services\Reportes\Reporte;
use App\Models\Prestacion;
use App\Models\ItemsFacturaVenta;
use App\Models\FacturaResumen;
use FPDF;
use Carbon\Carbon;

class Factura extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id']):void
    {
        //titulos columnas
			$pdf->Cell(14,5,'FECHA',0,0,'L');$pdf->Cell(20,5,'PRESTACION',0,0,'R');$pdf->Cell(31,5,'PACIENTE',0,0,'L');
			$pdf->Cell(17,5,'C.COSTOS',0,0,'L');$pdf->Cell(34,5,'NRO.CE',0,0,'L');$pdf->Cell(70,5,'DETALLE',0,0,'L');$pdf->Ln();
			$pdf->Line(10,82,205,82);

            $grilla = $this->getGrilla($datos);

            $sumaprest=0;
            
            foreach ($grilla as $fila) {
                $sumaprest=$sumaprest+1;

                $pdf->Cell(10,3,Carbon::parse($fila->Fecha)->format('d/m/Y'),0,0,'L');
                $pdf->Cell(20,3,str_pad($fila->Id, 8, "0", STR_PAD_LEFT),0,0,'R');
				$pdf->Cell(35,3,substr($fila->paciente->Apellido." ".$fila->paciente->Nombre,0,20),0,0,'L');
				$pdf->Cell(17,3,substr($fila->CCosto,0,10),0,0,'L');
                $pdf->Cell(14,3,str_pad($fila->NroCEE, 8, "0", STR_PAD_LEFT),0,0,'L');
                $pdf->Cell(20,3,$fila->TSN,0,0,'L');

                $detalles = $this->getDetalles($datos, $fila->Id);

                foreach ($detalles as $detalle) {
                    $pdf->MultiCell(70,3,$detalle->Detalle,0,'L',0,5);
                    $pdf->Ln();
                }
            }

            $sumaresumen = 0;

            $pdf->Ln(6);
            $pdf->SetFont('Arial','BU',10);
            $pdf->Cell(0,5,'TOTAL EXAMENES:',0,0,'L');
            $pdf->Ln();
			$pdf->SetFont('Arial','',7);

            $resumenes = $this->getResumenFactura($datos);

            foreach ($resumenes as $resumen) {
                $pdf->Cell(20,3,$resumen->Total,0,0,'R');
                $pdf->Cell(0,3,$resumen->Detalle,0,0,'L');
                $pdf->Ln();
				
                $sumaresumen = $sumaresumen + $resumen->Total;
            }
            $pdf->Ln(5);
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(0,5,'Prestaciones: '.$sumaprest.', Examenes: '.$sumaresumen,0,0,'L');
            $pdf->Ln();	
    }

    private function getGrilla(array $datos): mixed
    {
        return Prestacion::with(['paciente', 'itemFacturaVenta'])
            ->whereHas('itemFacturaVenta', function ($query) use ($datos) {
                $query->where('IdFactura', $datos['id']);
            })->get();
    }

    private function getDetalles(array $datos, int $filaId): mixed
    {
        return ItemsFacturaVenta::where('IdFactura', $datos['id'])->where('IdPrestacion', $filaId)->get();
    }

    private function getResumenFactura(array $datos): mixed
    {
        return FacturaResumen::where('IdFactura', $datos['id'])->get();
    }
}