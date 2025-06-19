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

class EXAMENREPORTE20 extends Reporte
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
        $pdf->Image(public_path("/archivos/reportes/E3_1.jpg"),25,20,60);
        $pdf->Image(public_path("/archivos/reportes/E20.jpg"),25,40,169); 
        if(!$vistaPrevia) Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr", $pdf);
        else $pdf->Image(Tools::generarQRPrueba('A', "qr"), 190, 15, 15, 15);
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(100,21);$pdf->Cell(0,3,'Nro Examen:',0,0,'L');
        $pdf->SetXY(100,25);$pdf->Cell(0,3,'Apellido y Nombres: '.substr($paciente,0,30),0,0,'L');
        $pdf->SetXY(100,29);$pdf->Cell(0,3,'Empresa: '.substr($paraempresa,0,30),0,0,'L');
        $pdf->SetXY(100,33);if($cuil!=''){$pdf->Cell(0,3,'CUIL: '.$cuil,0,0,'L');}else{$pdf->Cell(0,3,'CUIL: '.$doc,0,0,'L');}
        $pdf->SetXY(45,45);$pdf->Cell(0,3,substr($domipac,0,30),0,0,'L');$pdf->SetXY(130,45);$pdf->Cell(0,3,$telpac,0,0,'L');
        $pdf->SetXY(45,49);$pdf->Cell(0,3,$locpac,0,0,'L');
        $pdf->SetXY(45,53);$pdf->Cell(0,3,$nac,0,0,'L');$pdf->SetXY(130,53);$pdf->Cell(0,3,$fechanac,0,0,'L');
        $pdf->SetXY(45,58);$pdf->Cell(0,3,substr($oj,0,60),0,0,'L');
        $pdf->SetXY(45,63);$pdf->Cell(0,3,substr($paraempresa,0,60),0,0,'L');
        $pdf->SetXY(45,71);$pdf->Cell(0,3,$art,0,0,'L');
        $pdf->SetXY(58,82);$pdf->Cell(0,3,$antig,0,0,'L');$pdf->SetXY(130,82);$pdf->Cell(0,3,$fi,0,0,'L');
        $pdf->SetXY(47,86);$pdf->Cell(0,3,$puesto,0,0,'L');
        $pdf->SetXY(56,91);$pdf->Cell(0,3,$antigpto,0,0,'L');
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E20_1.jpg"),25,23,167); 
        $pdf->Image(public_path("/archivos/reportes/E20_2.jpg"),25,210,167);

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