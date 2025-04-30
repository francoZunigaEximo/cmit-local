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

class EXAMENREPORTE11 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
        include('variables.php');

        $prestacion = $this->prestacion($datos['id']);
        $datosPaciente = $this->datosPaciente($prestacion->paciente->Id);
        $telefono = $this->telefono($prestacion->paciente->Id);
        $telefonoPaciente = "(".$telefono->CodigoArea.") ".$telefono->NumeroTelefono;


        if($prestacion->empresa->RF === 1){
            $pdf->SetFont('Arial','B',14);$pdf->SetXY(170,4);$pdf->Cell(0,3,'RF',0,0,'L');$pdf->SetFont('Arial','',8);
        }

        $paciente = $prestacion->paciente->Apellido.' '.$prestacion->paciente->Nombre;
        $localidad = $this->localidad($prestacion->paciente->IdLocalidad) ?? '';
        $fecha = $prestacion->paciente->FechaNacimiento;
        
    
        //$pdf->AddPage();
        //cuerpo
        $pdf->Image(public_path("/archivos/reportes/E11.jpg"),25,40,166); 
       
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','B',12);$pdf->SetXY(60,31);$pdf->Cell(0,4,'REGISTRO DE HISTORIA CLINICA OCUPACIONAL',0,0,'L');
        $pdf->SetFont('Arial','B',8);$pdf->SetXY(95,36);$pdf->Cell(0,3,'SECRETO MEDICO',0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(40,68);$pdf->Cell(0,3,$prestacion->empresa->ParaEmpresa,0,0,'L');
        $pdf->SetXY(51,74);$pdf->Cell(0,3,substr($paciente,0,40),0,0,'L');
        $pdf->SetXY(35,81);$pdf->Cell(0,3,$prestacion->paciente->Sexo,0,0,'L');
        $pdf->SetXY(70,81);$pdf->Cell(0,3,$prestacion->paciente->EstadoCivil,0,0,'L');
        $pdf->SetXY(100,81);$pdf->Cell(0,3,$prestacion->paciente->ObsEC,0,0,'L');
        $pdf->SetXY(45,87);$pdf->Cell(0,3,substr($localidad->Nombre,0,20),0,0,'L');
        $pdf->SetXY(43,94);$pdf->Cell(0,3,$prestacion->paciente->FechaNacimiento,0,0,'L');
        $pdf->SetXY(80,94);$pdf->Cell(0,3,substr($prestacion->paciente->Nacionalidad,0,20),0,0,'L');
        $pdf->SetXY(137,94);$pdf->Cell(0,3,$prestacion->paciente->Documento,0,0,'L');
        $pdf->SetXY(44,107);$pdf->Cell(0,3,substr($prestacion->paciente->Direccion,0,50),0,0,'L');
        $pdf->SetXY(40,113);$pdf->Cell(0,3,substr($localidad->Nombre,0,20),0,0,'L');
        $pdf->SetXY(98,113);$pdf->Cell(0,3,'lkasjdfl',0,0,'L');
        $pdf->SetXY(40,120);$pdf->Cell(0,3,$telefonoPaciente,0,0,'L');
        $pdf->SetXY(44,134);$pdf->Cell(0,3,$datosPaciente->Tareas,0,0,'L');
        $pdf->SetXY(47,159);$pdf->MultiCell(130,3,$prestacion->Observaciones,0,'J',0,5);
        //if($foto!=''){$pdf->Image($foto,155,55,52,38);}
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E5_1.jpg"),20,18,180);
        $pdf->Image(public_path("/archivos/reportes/E5_2.jpg"),20,182,180);
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','',7);
        $pdf->SetXY(20,6);$pdf->Cell(0,3,'Paciente: '.$paciente.' '.$prestacion->paciente->Documento,0,0,'L');
        $pdf->SetXY(20,10);$pdf->Cell(0,3,$fecha,0,0,'L');
        
        include('paginaadicional.php');
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