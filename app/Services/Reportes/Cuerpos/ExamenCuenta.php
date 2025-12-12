<?php

 namespace App\Services\Reportes\Cuerpos;

use App\Services\Reportes\Reporte;
use App\Models\ExamenCuentaIt;
use FPDF;
use Illuminate\Support\Facades\DB;

 class ExamenCuenta extends Reporte
 {
    public function render(FPDF $pdf, $datos = [
        'id'
    ]): void
    {
        //titulos columnas
        $pdf->Cell(41,5,'ESPECIALIDAD EFECTOR',0,0,'L');
        $pdf->Cell(65,5,'EXAMEN',0,0,'L');
        $pdf->Cell(20,5,'PRESTACION',0,0,'R');
        $pdf->Cell(60,5,'PACIENTE',0,0,'L');
        $pdf->Ln();
        $pdf->Line(10,82,205,82);
        $pdf->SetFont('Arial','',7);

        $examenes = $this->grillaExamenCuenta($datos['id']);

        foreach($examenes as $reporte) {
            $pdf->Cell(41,3,utf8_decode(substr($reporte->NombreEstudio,0,10)),0,0,'L');
            $pdf->Cell(65,3,utf8_decode(substr($reporte->NombreExamen,0,40)),0,0,'L');
            $pdf->Cell(20,3,$reporte->IdPrestacion === 0 ? '-' : $reporte->IdPrestacion,0,0,'R');
            $pdf->Cell(60,3,utf8_decode(substr($reporte->Apellido . " " . $reporte->Nombre,0,30)),0,0,'L');$pdf->Ln();
        }

        $listado = $this->detalladoExamenesCuenta($datos['id']);

        $pdf->Ln(6);$pdf->SetFont('Arial','BU',10);	
        $pdf->Cell(0,5,'TOTAL EXAMENES DEL PAGO:',0,0,'L');
        $pdf->Ln();
        $pdf->SetFont('Arial','',7);

        foreach($listado as $item) {
            $pdf->Cell(20,3,$item->Cantidad,0,0,'R');
            $pdf->Cell(0,3,utf8_decode($item->NombreExamen),0,0,'L');
            $pdf->Ln();
        }
        
        $totalExamenes = $this->totalExamenes($datos['id']);
        $totalDisponibles = $this->totalDisponibles(($datos['id']));
        $listDisponibles = $this->listadoDisponibles($datos['id']);

        $pdf->Ln(5);
        $pdf->SetFont('Arial','B',8);	
        $pdf->Cell(0,5,'Examenes: '.$totalExamenes.', Disponibles: '.$totalDisponibles,0,0,'L');
        $pdf->Ln();				

        $pdf->Ln(6);$pdf->SetFont('Arial','B',10);	
        $pdf->Cell(0,5,'LISTADO DISPONIBLES:',0,0,'L');
        $pdf->Ln();
        $pdf->SetFont('Arial','',7);
        
        foreach($listDisponibles as $item) {
            $pdf->Cell(0,3,utf8_decode($item->NombreExamen),0,0,'L');
            $pdf->Ln();
        }
    }

    private function grillaExamenCuenta(int $id): mixed
    {
        return ExamenCuentaIt::join('examenes', 'pagosacuenta_it.IdExamen', '=', 'examenes.Id')
            ->join('prestaciones', 'pagosacuenta_it.IdPrestacion', '=', 'prestaciones.Id')
            ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->join('estudios', 'examenes.IdEstudio', '=', 'estudios.Id')
            ->select(
                'prestaciones.Id as IdPrestacion',
                'estudios.Nombre as NombreEstudio',
                'examenes.Nombre as NombreExamen',
                'pacientes.Nombre as Nombre',
                'pacientes.Apellido as Apellido'  
            )
            ->where('pagosacuenta_it.IdPago', $id)
            ->orderBy('examenes.Nombre')
            ->orderBy('estudios.Nombre')
            ->get();
    }

    private function detalladoExamenesCuenta(int $id): mixed
    {
        $query = DB::table('pagosacuenta_it')
            ->join('examenes', 'pagosacuenta_it.IdExamen', '=', 'examenes.Id')
            ->select(
                DB::raw('COUNT(pagosacuenta_it.IdExamen) as Cantidad'), 
                'examenes.Nombre as NombreExamen')
            ->where('pagosacuenta_it.IdPago', $id)
            ->groupBy('examenes.Nombre');
        return $query->get();
    }

    private function detalleCantidadExamenes(int $id): mixed{
        return ExamenCuentaIt::join('examenes', 'examenes.Id', '=', 'pagosacuenta_it.IdExamen')
            ->select(
                DB::raw('COUNT(pagosacuenta_it.IdExamen) as Cantidad'), 
                'examenes.Nombre as NombreExamen')
            ->where('pagosacuenta_it.IdPago', $id)
            ->orderBy('examenes.Nombre')
            ->groupBy('examenes.Nombre')
            ->get();
    }

    private function totalExamenes(int $id): int
    {
        return ExamenCuentaIt::where('IdPago', $id)->count() ?? 0;
    }

    private function totalDisponibles(int $id): int
    {
        return ExamenCuentaIt::where('IdPago', $id)->where('IdPrestacion', 0)->count() ?? 0;
    }

    private function listadoDisponibles(int $id)
    {
        return ExamenCuentaIt::join('examenes', 'pagosacuenta_it.IdExamen', '=', 'examenes.Id')
            ->select('examenes.Nombre as NombreExamen')
            ->where('IdPago', $id)->where('IdPrestacion')->get();
    }
 }