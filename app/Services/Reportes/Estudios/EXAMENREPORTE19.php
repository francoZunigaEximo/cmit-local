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

class EXAMENREPORTE19 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {   
        $prestacion = $this->prestacion($datos['id']);
        $datosPaciente = $this->datosPaciente($prestacion->paciente->Id);
        $telefonoPaciente = $this->telefono($prestacion->paciente->Id);
        $paciente = $prestacion->paciente->Apellido.' '.$prestacion->paciente->Nombre;
        $localidad = $this->localidad($prestacion->paciente->IdLocalidad) ?? '';
        $provinciaPaciente = Provincia::where('Id', $localidad->IdPcia)->first();
        $fichaLaboral = Fichalaboral::where('IdPaciente', $prestacion->paciente->Id)->first();
        $cliente = $prestacion->empresa;
        
        $localidadCliente = Localidad::where('Id', $cliente->IdLocalidad)->first();
        $provinciaCliente = Provincia::where('Id', $localidadCliente->IdPcia)->first();
        $Art = Cliente::where('Id', $prestacion->IdART)->first();
        
        //empresa
        $paraempresa = $prestacion->empresa->ParaEmpresa;
        $ide = str_pad( $prestacion->empresa->IdEmpresa, 6, "0", STR_PAD_LEFT);
        $rsempresa =  $prestacion->empresa->RazonSocial;
        $actividad =  $prestacion->empresa->Actividad;
        $cuit =  $prestacion->empresa->cuit;
        $domie = $cliente->DireccionE;
        $loce =  $localidadCliente->Nombre;
        $cpe =  $localidadCliente->CP;
        $pciae =  $provinciaCliente->Nombre;
        $rf =  $cliente->RF;
        $art =  $Art->RazonSocial;
        $ida = str_pad( $Art->Id, 6, "0", STR_PAD_LEFT);
        //paciente

        $idpac = $prestacion->paciente->IdPaciente;
        $paciente = $paciente;
        $apellido = $prestacion->paciente->Apellido;
        $nombre = $prestacion->paciente->Nombre;
        $cuil = $prestacion->paciente->Identificacion;
        $doc = $prestacion->paciente->Documento;
        $tipodoc = $prestacion->paciente->TipoDocumento;
        $edad = $this->edad($prestacion->paciente->FechaNacimiento);
        $ec = $prestacion->paciente->EstadoCivil;
        $obsec = $prestacion->paciente->ObsEC;
        $hijos = $prestacion->paciente->Hijos;
        if ($prestacion->paciente->Sexo == 'F') {
            $sexo = "Femenino";
        } else {
            $sexo = "Masculino";
        }
        $fechanac = $prestacion->paciente->FechaNacimiento;
        $lugarnac = $prestacion->paciente->LugarNacimiento;
        $nac = $prestacion->paciente->Nacionalidad;
        
        if ($prestacion->paciente->Foto != '') {
            $foto = public_path("/archivos/fotos/" . $prestacion->paciente->Foto) ;
        } else {
            $foto = '';
        }
        //datos extras
        $locpac = $localidad->Nombre;
        $cp = $localidad->CP;
        $domipac = $prestacion->paciente->Direccion;
        $pcia = $provinciaPaciente->Nombre;
        //puesto
        $puesto = $datosPaciente->Puesto;
        $sector = $datosPaciente->Sector;
        $tareas = $datosPaciente->Tareas;
        $tareasea = $datosPaciente->TareasEmpAnterior;
        $oj = $datosPaciente->ObsJornada;
        $antigpto = $datosPaciente->AntigPuesto;
        $antig = $datosPaciente->FechaIngreso;
        if ($antig == '00-00-0000') {
            $antig = "";
            $fi = "";
        } else {
            $fi = $antig;
            $antig = $datosPaciente->FechaIngreso;
        }
        $fe = $datosPaciente->FechaEgreso;
        if ($fe == '00-00-0000') {
            $fe = "";
        }
        $jor = $datosPaciente->TipoJornada . ' ' . $datosPaciente->Jornada;
        $obsjor = $datosPaciente->ObsJornada;
        //ficha laboral
        $obsfl = $fichaLaboral->Observaciones;
        $telpac = "(".$telefonoPaciente->CodigoArea.") ".$telefonoPaciente->NumeroTelefono;
        $fecha = date('d/m/Y');
        $anio = date('Y');
        if($prestacion->empresa->RF === 1){
            $pdf->SetFont('Arial','B',14);$pdf->SetXY(170,4);$pdf->Cell(0,3,'RF',0,0,'L');$pdf->SetFont('Arial','',8);
        }
        $pdf->Image(public_path("/archivos/reportes/E6.jpg"),25,20,169); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','B',8);$pdf->SetXY(26,240);$pdf->Cell(0,3,'HISTORIA CLINICA OCUPACIONAL',0,0,'L');
        $pdf->SetXY(173,240);$pdf->Cell(0,3,'Pagina 1',0,0,'L');
        $pdf->SetXY(85,55);$pdf->Cell(0,3,'Ingreso',0,0,'L');$pdf->SetXY(110,55);$pdf->Cell(0,3,'Periodico X',0,0,'L');
        $pdf->SetXY(140,55);$pdf->Cell(0,3,'Egreso',0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(38,55);$pdf->Cell(0,3,$fecha,0,0,'L');
        $pdf->SetXY(50,75);$pdf->Cell(0,3,substr($paciente,0,80),0,0,'L');
        $pdf->SetXY(47,79);$pdf->Cell(0,3,$doc,0,0,'L');$pdf->SetXY(104,79);$pdf->Cell(0,3,substr($lugarnac,0,20),0,0,'L');
        $pdf->SetXY(50,84);$pdf->Cell(0,3,$fechanac,0,0,'L');
        if($sexo=='Masculino'){$pdf->SetXY(90,84);}else{$pdf->SetXY(101,84);}$pdf->Cell(0,3,'X',0,0,'L');
        $pdf->SetXY(130,84);$pdf->Cell(0,3,$ec,0,0,'L');
        $pdf->SetXY(42,92);$pdf->Cell(0,3,substr($domipac,0,30),0,0,'L');$pdf->SetXY(104,92);$pdf->Cell(0,3,substr($telpac,0,20),0,0,'L');
        $pdf->SetXY(150,92);$pdf->Cell(0,3,substr($locpac,0,20),0,0,'L');
        $pdf->SetXY(51,97);$pdf->Cell(0,3,substr($tareas,0,30),0,0,'L');
        $pdf->SetXY(121,101);$pdf->Cell(0,3,substr($paraempresa,0,30),0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E6_1.jpg"),25,20,166);
        $pdf->SetFont('Arial','B',8);$pdf->SetXY(26,240);$pdf->Cell(0,3,'HISTORIA CLINICA OCUPACIONAL',0,0,'L');
        $pdf->SetXY(173,240);$pdf->Cell(0,3,'Pagina 2',0,0,'L');
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        //pagina 3
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E6_2.jpg"),25,20,166);
        $pdf->SetFont('Arial','B',8);$pdf->SetXY(26,240);$pdf->Cell(0,3,'HISTORIA CLINICA OCUPACIONAL',0,0,'L');
        $pdf->SetXY(173,240);$pdf->Cell(0,3,'Pagina 3',0,0,'L');
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        //pagina 4
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E6_3.jpg"),25,20,166);
        $pdf->SetFont('Arial','B',8);$pdf->SetXY(26,240);$pdf->Cell(0,3,'HISTORIA CLINICA OCUPACIONAL',0,0,'L');
        $pdf->SetXY(173,240);$pdf->Cell(0,3,'Pagina 4',0,0,'L');
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
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