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

class EXAMENREPORTE123 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');
        
        $pdf->Image(public_path("/archivos/reportes/E123_1.jpg"),8,20,195); 
        $pdf->Image(public_path("/archivos/reportes/E123_2.jpg"),8,150,195); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E123_3.jpg"),8,20,195); 
        $pdf->Image(public_path("/archivos/reportes/E123_4.jpg"),8,139,195); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        //pagina 3
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E123_5.jpg"),8,25,195); 
        $pdf->Image(public_path("/archivos/reportes/E123_6.jpg"),8,150,195); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        //pagina 4
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E123_7.jpg"),8,30,195); 
        $pdf->Image(public_path("/archivos/reportes/E123_8.jpg"),8,163,195); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        //pagina 5
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E123_9.jpg"),8,25,195); 
        $pdf->Image(public_path("/archivos/reportes/E123_10.jpg"),8,165,195); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        //pagina 6
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E123_11.jpg"),8,22,195); 
        $pdf->Image(public_path("/archivos/reportes/E123_12.jpg"),8,169,195); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        //pagina 7
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E123_13.jpg"),8,25,195); 
        $pdf->Image(public_path("/archivos/reportes/E123_14.jpg"),8,158,195); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        //pagina 8
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E123_15.jpg"),8,25,195); 
        $pdf->Image(public_path("/archivos/reportes/E123_16.jpg"),8,163,195); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
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
