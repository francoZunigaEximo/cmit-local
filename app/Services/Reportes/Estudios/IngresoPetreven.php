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

class IngresoPetreven extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
        $pdf->AddPage();
        $pdf->Image(public_path(ReporteConfig::$INGRESOPETREVEN),25,20,169);

        $prestacion = $this->prestacion($datos['id']);
        $datosPaciente = $this->datosPaciente($datos['id']);
        $telefonoPaciente = $this->telefono($prestacion->paciente->Id);

        if($prestacion->empresa->RF === 1){
            $pdf->SetFont('Arial','B',14);$pdf->SetXY(170,4);$pdf->Cell(0,3,'RF',0,0,'L');$pdf->SetFont('Arial','',8);
        }

        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");

        $paciente = $prestacion->paciente->Apellido.' '.$prestacion->paciente->Nombre;
        $localidad = $this->localidad($datosPaciente->IdLocalidad) ?? '';

        $pdf->SetFont('Arial','B',8);$pdf->SetXY(26,240);$pdf->Cell(0,3,'HISTORIA CLINICA OCUPACIONAL',0,0,'L');
        $pdf->SetXY(173,240);$pdf->Cell(0,3,'Pagina 1',0,0,'L');
        $pdf->SetXY(90,55);$pdf->Cell(0,3,'Ingreso X',0,0,'L');$pdf->SetXY(130,55);$pdf->Cell(0,3,'Egreso',0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(38,55);$pdf->Cell(0,3,$prestacion->Fecha,0,0,'L');
        $pdf->SetXY(50,75);$pdf->Cell(0,3,substr($paciente,0,70),0,0,'L');
        $pdf->SetXY(47,79);$pdf->Cell(0,3,$prestacion->paciente->Documento,0,0,'L');$pdf->SetXY(104,79);$pdf->Cell(0,3,substr($prestacion->paciente->LugarNacimiento ?? '',0,40),0,0,'L');
        $pdf->SetXY(50,84);$pdf->Cell(0,3,$prestacion->paciente->FechaNacimiento,0,0,'L');
        if($prestacion->paciente->Sexo =='Masculino'){$pdf->SetXY(90,84);}else{$pdf->SetXY(101,84);}$pdf->Cell(0,3,'X',0,0,'L');
        $pdf->SetXY(130,84);$pdf->Cell(0,3,$prestacion->paciente->EstadoCivil ?? '',0,0,'L');
        $pdf->SetXY(42,92);$pdf->Cell(0,3,substr($datosPaciente->Direccion ?? '',0,30),0,0,'L');$pdf->SetXY(104,92);$pdf->Cell(0,3,substr($telefonoPaciente,0,25),0,0,'L');
        $pdf->SetXY(150,92);$pdf->Cell(0,3,substr($localidad,0,25),0,0,'L');
        $pdf->SetXY(51,97);$pdf->Cell(0,3,substr($datosPaciente->Tareas ?? '',0,30),0,0,'L');
        $pdf->SetXY(121,101);$pdf->Cell(0,3,substr($prestacion->empresa->ParaEmpresa,0,30),0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path(ReporteConfig::$INGRESOPETREVEN1),25,20,166);
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','B',8);$pdf->SetXY(26,240);$pdf->Cell(0,3,'HISTORIA CLINICA OCUPACIONAL',0,0,'L');
        $pdf->SetXY(173,240);$pdf->Cell(0,3,'Pagina 2',0,0,'L');
        //pagina 3
        $pdf->AddPage();
        $pdf->Image(ReporteConfig::$INGRESOPETREVEN2,25,20,166);
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','B',8);$pdf->SetXY(26,240);$pdf->Cell(0,3,'HISTORIA CLINICA OCUPACIONAL',0,0,'L');
        $pdf->SetXY(173,240);$pdf->Cell(0,3,'Pagina 3',0,0,'L');
        //pagina 4
        $pdf->AddPage();
        $pdf->Image(ReporteConfig::$INGRESOPETREVEN3,25,20,166);
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','B',8);$pdf->SetXY(26,240);$pdf->Cell(0,3,'HISTORIA CLINICA OCUPACIONAL',0,0,'L');
        $pdf->SetXY(173,240);$pdf->Cell(0,3,'Pagina 4',0,0,'L');
        //pagina 5
        $pdf->AddPage();
        $pdf->Image(ReporteConfig::$INGRESOPETREVEN4,25,20,169);
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(38,54);$pdf->Cell(0,3,$prestacion->Fecha,0,0,'L');
        $pdf->SetXY(55,73);$pdf->Cell(0,3,substr($paciente,0,70),0,0,'L');
        $pdf->SetXY(48,80);$pdf->Cell(0,3,$prestacion->paciente->Documento,0,0,'L');
        $pdf->SetXY(52,86);$pdf->Cell(0,3,substr($datosPaciente->Tareas ?? '',0,30),0,0,'L');
        $pdf->SetXY(127,100);$pdf->Cell(0,3,substr($prestacion->empresa->ParaEmpresa,0,30),0,0,'L');
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