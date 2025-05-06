<?php

namespace App\Services\Reportes\Estudios;

use App\Helpers\Tools;
use App\Models\DatoPaciente;
use App\Models\Localidad;
use App\Models\Prestacion;
use App\Models\Telefono;
use App\Services\Reportes\Reporte;
use App\Services\Reportes\ReporteConfig;
use FPDF;

use DateTime;

class RepsolIngreso extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
        $prestacion = $this->prestacion($datos['id']);
        $datosPaciente = $this->datosPaciente($prestacion->paciente->Id);
        $telefonoPaciente = $this->telefono($prestacion->paciente->Id);

        if($prestacion->empresa->RF === 1){
            $pdf->SetFont('Arial','B',14);$pdf->SetXY(170,4);$pdf->Cell(0,3,'RF',0,0,'L');$pdf->SetFont('Arial','',8);
        }
        dd($prestacion);
        die();
        $paciente = $prestacion->paciente->Apellido.' '.$prestacion->paciente->Nombre;
        $localidad = $this->localidad($prestacion->paciente->IdLocalidad) ?? '';
        $fecha = $prestacion->paciente->FechaNacimiento;

        $fecha_nacimiento = new DateTime($prestacion->paciente->FechaNacimiento);
        // Fecha actual
        $hoy = new DateTime('now');
        // Calcular la diferencia
        $edad = $hoy->diff($fecha_nacimiento)->y;

        //pagina 1
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E10.jpg"),25,20,154); 
        $pdf->SetFont('Arial','B',12);$pdf->SetXY(98,28);$pdf->Cell(0,4,'Examen Medico Preocupacional',0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(120,43);$pdf->Cell(0,3,substr("",0,35),0,0,'L');
        $pdf->SetXY(64,54);$pdf->Cell(0,3,substr($paciente,0,50),0,0,'L');
        $pdf->SetXY(74,59);$pdf->Cell(0,3,$localidad->Nombre.' '.$fecha,0,0,'L');$pdf->SetXY(159,59);$pdf->Cell(0,3,$edad,0,0,'L');
        $pdf->SetXY(51,63);$pdf->Cell(0,3,$prestacion->paciente->Documento,0,0,'L');
        //$pdf->SetXY(58,67);$pdf->Cell(0,3,$tareas,0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E10_1.jpg"),25,20,149);
        $pdf->SetFont('Arial','B',8);$pdf->SetXY(168,230);$pdf->Cell(0,3,'2',0,0,'L');
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        //pagina 3
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E10_2.jpg"),25,20,150);
        $pdf->SetFont('Arial','B',12);$pdf->SetXY(95,28);$pdf->Cell(0,4,'Examen Medico Preocupacional',0,0,'L');
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
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
        $query = Telefono::where('IdEntidad', $idPaciente)->first(['CodigoArea', 'NumeroTelefono']);
        return $query->CodigoArea.$query->NumeroTelefono;
    }

    private function localidad(int $id):mixed
    {
        return Localidad::find($id);
    }

}