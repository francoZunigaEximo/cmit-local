<?php

namespace App\Services\Reportes\Cuerpos;

use App\Services\Reportes\Reporte;
use App\Models\ExamenCuentaIt;
use App\Models\Profesional;
use FPDF;
use Illuminate\Support\Facades\DB;

class FacturaCompraCuerpo extends Reporte
{
    public function render(FPDF $pdf, $datos = [
        'id'
    ]): void
    {
        $profesional = $this->getProfesional($datos['id']);

        // Renderizar la factura de compra utilizando los datos y el profesional
        $nombre = utf8_decode($profesional->Nombre . ' ' . $profesional->Apellido);
        $localidad = utf8_decode($profesional->Localidad);
        $direccion = utf8_decode($profesional->Direccion);
        $cuit = utf8_decode($profesional->Identificacion);
        $localidadString = utf8_decode($profesional->Localidad ? $profesional->Localidad : '');
        $localidadString .= utf8_decode($profesional->Provincia ? ' - ' . $profesional->Provincia : '');
        $CP = $profesional->CP;

        //encabezado
       // Coordenadas y tamaño del recuadro
        $x = 10;
        $y = 10;
        $w = 190;
        $h = 30;

        // Dibuja el recuadro
        $pdf->Rect($x, $y, $w, $h);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(10, 10);
        $pdf->Cell(100, 4, "Profesional:", 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(50, 10);
        $pdf->Cell(100, 4, $nombre, 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(10, 20);
        $pdf->Cell(100, 4, "Datos:", 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(50, 20);
        $pdf->Cell(200, 4, "DOMICILIO: " . $direccion, 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(50, 25);
        $pdf->Cell(200, 4, "LOCALIDAD: " . $localidadString, 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(150, 20);
        $pdf->Cell(200, 4, "CUIT: " . $cuit, 0, 0, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(150, 25);
        $pdf->Cell(200, 4, "CP: " . $CP, 0, 0, 'L');

        $pdf->Ln(20);
        $pdf->Cell($w, 4, "Examenes Efector", 0, 1, 'L'); //salto de linea
        $pdf->Ln(4);
        //
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $w = 35;

        //colocamos el encabezado de la tabla
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY($x, $y);
        $pdf->Cell($w, 4, "PRESTACION", 0, 0, 'L');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY($x+($w*1), $y);
        $pdf->Cell($w, 4, "EXAMEN", 0, 0, 'L');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY($x+($w*2), $y);
        $pdf->Cell($w, 4, "EMPRESA", 0, 0, 'L');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY($x+($w*3), $y);
        $pdf->Cell($w, 4, "PACIENTE", 0, 0, 'L');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY($x+($w*4), $y);
        $pdf->Cell($w, 4, "TIPO", 0, 0, 'L');

        $pdf->Ln(5);	
        
        $examenesEfector = $this->getExamenesEfector($datos["id"]);
        $w = 35;
        $i = 1;
       
        foreach ($examenesEfector as $examen) {
            $y = $pdf->GetY();

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetXY($x, $y);
            $pdf->MultiCell($w, 6, $examen->idPrestacion, 0,'L');
            
            $pdf->SetFont('Arial', '', 7);
            $pdf->SetXY($x+($w*1), $y);
            $pdf->MultiCell($w, 6, $examen->Examen, 0, 'L');
            $pdf->SetFont('Arial', '', 7);
            $pdf->SetXY($x+($w*2), $y);
            $pdf->MultiCell($w, 6, $examen->Empresa, 0, 'L');

            $pdf->SetFont('Arial', '', 7);
            $pdf->SetXY($x+($w*3), $y);
            $pdf->MultiCell($w, 6, $examen->Paciente, 0,'L');
            
            $pdf->SetFont('Arial', '', 7);
            $pdf->SetXY($x+($w*4), $y);
            $pdf->MultiCell($w, 6, "Efector", 0,'L');

            $pdf->Ln(4);
            $i++;
            if ($i % 25 == 0) {
                $pdf->AddPage();
            }
            //$pdf->SetFont('Arial','',7);$pdf->SetX(93);$pdf->Cell(0,3,substr($row1['ObsExamen'],0,80),0,0,'L');$pdf->Ln(4);
        }

        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell($w, 4, "Examenes Informador", 0, 1, 'L'); //salto de linea
        $pdf->Ln(4);

        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $w = 35;

        //colocamos el encabezado de la tabla
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY($x, $y);
        $pdf->Cell($w, 4, "PRESTACION", 0, 0, 'L');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY($x+($w*1), $y);
        $pdf->Cell($w, 4, "EXAMEN", 0, 0, 'L');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY($x+($w*2), $y);
        $pdf->Cell($w, 4, "EMPRESA", 0, 0, 'L');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY($x+($w*3), $y);
        $pdf->Cell($w, 4, "PACIENTE", 0, 0, 'L');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY($x+($w*4), $y);
        $pdf->Cell($w, 4, "TIPO", 0, 0, 'L');

        $pdf->Ln(5);	
        
        $examenesInformador = $this->getExamenesInformador($datos["id"]);
        $w = 35;
        $i = 1;
        foreach ($examenesInformador as $examen) {
            $y = $pdf->GetY();

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetXY($x, $y);
            $pdf->MultiCell($w, 4, $examen->idPrestacion, 0,'L');
            
            $pdf->SetFont('Arial', '', 7);
            $pdf->SetXY($x+($w*1), $y);
            $pdf->MultiCell($w, 4, $examen->Examen, 0, 'L');

            $pdf->SetFont('Arial', '', 7);
            $pdf->SetXY($x+($w*2), $y);
            $pdf->MultiCell($w, 4, $examen->Empresa, 0, 'L');

            $pdf->SetFont('Arial', '', 7);
            $pdf->SetXY($x+($w*3), $y);
            $pdf->MultiCell($w, 4, $examen->Paciente, 0,'L');

            $pdf->SetFont('Arial', '', 7);
            $pdf->SetXY($x+($w*4), $y);
            $pdf->MultiCell($w, 4, "Informador", 0,'L');

            $pdf->Ln(4);
            $i++;
            if ($i % 25 == 0) {
                $pdf->AddPage();
            }
            //$pdf->SetFont('Arial','',7);$pdf->SetX(93);$pdf->Cell(0,3,substr($row1['ObsExamen'],0,80),0,0,'L');$pdf->Ln(4);
        }
        $pdf->Ln(4);
    }

    private function getProfesional($idFactura): mixed
    {
        return DB::table('profesionales')
            ->select('profesionales.*', 'localidades.Nombre as Localidad')
            ->join('facturascompra', 'facturascompra.IdProfesional', '=', 'profesionales.Id')
            ->join('localidades', 'profesionales.IdLocalidad', '=', 'localidades.Id')
            ->where('facturascompra.Id', $idFactura)
            ->first();
    }

    public function getExamenesEfector($idFactura)
    {
        return DB::Select("CALL getExamenesFacturaCompraEfector(?)", [$idFactura]);
    }

    public function getExamenesInformador($idFactura)
    {
        return DB::Select("CALL getExamenesFacturaCompraInformador(?)", [$idFactura]);
    }
}
