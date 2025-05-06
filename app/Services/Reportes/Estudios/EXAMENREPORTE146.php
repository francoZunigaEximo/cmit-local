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

class EXAMENREPORTE146 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');
        
        include ("banerlogo.php");
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");    
        $pdf->SetMargins(22,20,22); //left/top/right
        $pdf->Image(public_path("/archivos/reportes/schlumberger.jpg"),10,10,40);$pdf->SetY(20);$pdf->SetFont('Arial','',12);$pdf->Line(10,28,200,28);
        //titulo
        $pdf->SetFont('Arial','B',12);$pdf->SetXY(10,47);$pdf->Cell(200,4,"Certificado de Examen Medico Informado",0,0,'C');	
        //lugar y fecha
        $pdf->SetFont('Arial','',12);
        $pdf->SetXY(15,65);$pdf->Cell(0,3,'Neuquen, ...........................................',0,0,'L');
        //cuerpo
        $texto="Por la presente, certifico que el Sr./Sra.: ".substr($paciente,0,40)." DNI numero ".$doc.", empleado de Schlumberger Argentina con funcion de ".substr($puestoestudio,0,40).", fue informado por mi personalmente acerca de los resultados del Examen Medico Periodico | Ingreso (tachar lo que no corresponda) realizado el ".$fecha." y entendio dicho informe y las indicaciones brindadas.";
        $pdf->SetXY(15,85);$pdf->SetFont('Arial','',11);$pdf->MultiCell(180,8,$texto,0,'J',0,5);$pdf->Ln(20);
        //
        $pdf->SetX(15);$pdf->Cell(0,8,"El resultado del examen es ( marque con X donde corresponda):",0,0,'L');$pdf->Ln(10);
        //
        $pdf->SetX(15);$pdf->Cell(0,8,"Apto para su Trabajo(....)",0,0,'L');$pdf->Ln();
        $pdf->SetX(15);$pdf->Cell(0,8,"Apto con Restricciones (....)",0,0,'L');$pdf->Ln();
        $pdf->SetX(15);$pdf->Cell(0,8,"Aclaro tipo de Restriccion (....)",0,0,'L');$pdf->Ln();
        $pdf->SetX(15);$pdf->Cell(0,8,"No Apto (....)",0,0,'L');$pdf->Ln(20);
        //
        $pdf->SetX(15);$pdf->Cell(0,8,"Paciente requiere nuevo control SI - NO",0,0,'L');
        //firma
        $pdf->Line(20,250,90,250);$pdf->Line(120,250,190,250);
        $pdf->SetXY(20,252);$pdf->Cell(70,3,'Firma y sello del medico',0,0,'C');
        $pdf->SetXY(120,252);$pdf->Cell(70,3,'Firma y aclaracion del empleado',0,0,'C');
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
