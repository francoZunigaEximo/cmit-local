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

class EXAMENREPORTE16 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen'], $vistaPrevia = false): void
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
        $pdf->Image(public_path("/archivos/reportes/E16.jpg"),25,25,168); 
        $pdf->Image(public_path("/archivos/reportes/E16_1.jpg"),25,119,168);
        if(!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);        
        $pdf->Rect(145,30,40,15); $pdf->SetFont('Arial','B',8);
        $pdf->SetXY(148,33);$pdf->Cell(0,3,'CUIT: '.$cuit,0,0,'L');
        $pdf->SetXY(148,37);if($cuil!=''){$pdf->Cell(0,3,'CUIL: '.$cuil,0,0,'L');}else{$pdf->Cell(0,3,'CUIL: '.$doc,0,0,'L');}
        $pdf->SetXY(148,41);$pdf->Cell(0,3,utf8_decode('AÑO: ').$anio,0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(45,80);$pdf->Cell(0,3,$paraempresa,0,0,'L');$pdf->SetXY(159,80);$pdf->Cell(0,3,$cuit,0,0,'L');
        $pdf->SetXY(43,85);$pdf->Cell(0,3,$domie,0,0,'L');$pdf->SetXY(117,85);$pdf->Cell(0,3,$loce,0,0,'L');
        $pdf->SetXY(168,85);$pdf->Cell(0,3,$cp,0,0,'L');
        $pdf->SetXY(48,98);$pdf->Cell(0,3,$paciente,0,0,'L');$pdf->SetXY(160,98);
        if($cuil!=''){$pdf->Cell(0,3,$cuil,0,0,'L');}else{$pdf->Cell(0,3,$doc,0,0,'L');}
        $pdf->SetXY(51,102);$pdf->Cell(0,3,$fechanac,0,0,'L');$pdf->SetXY(111,102);$pdf->Cell(0,3,$puesto,0,0,'L');
        $pdf->SetXY(55,107);$pdf->Cell(0,3,$antigpto,0,0,'L');$pdf->SetXY(140,107);$pdf->Cell(0,3,$fi,0,0,'L');
        $pdf->SetXY(40,111);$pdf->Cell(0,3,$domipac,0,0,'L');$pdf->SetXY(143,111);$pdf->Cell(0,3,$telpac,0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E16_2.jpg"),25,30,165); 
        $pdf->SetY(20);$pdf->SetFont('Arial','B',13);$pdf->Cell(0,5,'EXAMEN FISICO',0,0,'C');
        if(!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
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