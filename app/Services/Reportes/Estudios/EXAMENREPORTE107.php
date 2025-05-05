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

class EXAMENREPORTE107 extends Reporte
{
    public function render(FPDF $pdf, $datos = ['id', 'idExamen']): void
    {
include('variables.php');
        
        $pdf->Image(public_path("/archivos/reportes/E102_1.jpg"),10,10,35); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','B',12);
        $pdf->SetY(25);$pdf->Cell(0,4,'Declaracion Jurada de Examenes Psicofisicos Anuales Periodicos ("EPAP")',0,0,'C');
        $pdf->SetFont('Arial','B',10);
        $pdf->SetY(35);$pdf->Cell(0,4,'Vigilancia de la Salud. Examenes Medicos',0,0,'C');
        $pdf->SetY(40);$pdf->Cell(0,4,'Documentacion Respaldatoria.',0,0,'C');
        $y=50;
        $pdf->SetFont('Arial','',8);$pdf->SetXY(155,$y-5);$pdf->Cell(0,4,$fecha,0,0,'L');
        //
        $pdf->SetFont('Arial','',10);
        $pdf->Line(55,$y+4,185,$y+4);$pdf->SetXY(10,$y);$pdf->Cell(0,4,'1.    Empresa Contratista:  '.substr($paraempresa,0,50),0,0,'L');$y=$y+7;
        $pdf->Line(55,$y+4,185,$y+4);$pdf->SetXY(10,$y);$pdf->Cell(0,4,'       Subcontratista:  ',0,0,'L');$y=$y+10;
        $pdf->Line(71,$y+4,185,$y+4);$pdf->SetXY(10,$y);$pdf->Cell(0,4,'2.    Apellido y Nombre del Empleado:  '.substr($apellido,0,23).' '.substr($nombre,0,23),0,0,'L');$y=$y+10;
        $pdf->Line(31,$y+4,75,$y+4);$pdf->SetXY(10,$y);$pdf->Cell(0,4,'3.    DNI Nro:  '.$doc,0,0,'L');
        $pdf->Line(98,$y+4,185,$y+4);$pdf->SetXY(80,$y);$pdf->Cell(0,4,' CUIL Nro:  '.$cuil,0,0,'L');$y=$y+10;
        $pdf->Line(50,$y+4,185,$y+4);$pdf->SetXY(10,$y);$pdf->Cell(0,4,'4.    Fecha de Ingreso:  '.$fi,0,0,'L');$y=$y+10;
        $pdf->Line(30,$y+4,80,$y+4);$pdf->SetXY(10,$y);$pdf->Cell(0,4,'5.    Sector: '.substr($sector,0,25),0,0,'L');
        $pdf->Line(124,$y+4,185,$y+4);$pdf->SetXY(80,$y);$pdf->Cell(0,4,' Cargo/Puesto de Trabajo:  '.substr($puestoestudio,0,25),0,0,'L');$y=$y+10;
        $pdf->SetXY(10,$y);$pdf->Cell(0,4,'6.    Tipo de trabajo para el cual se habilita la persona:',0,0,'L');$y=$y+5;
        $pdf->SetXY(35,$y);$pdf->Rect(33,$y+1,2,2,'F');$pdf->Cell(0,4,' Tarea liviana',0,0,'L');
        $pdf->SetXY(80,$y);$pdf->Cell(0,4,'APTITUD:',0,0,'L');
        $pdf->SetXY(120,$y);$pdf->Cell(0,4,'SI',0,0,'L');$pdf->Rect(127,$y,4,4);
        $pdf->SetXY(150,$y);$pdf->Cell(0,4,'NO',0,0,'L');$pdf->Rect(157,$y,4,4);
        $y=$y+7;
        $pdf->SetXY(35,$y);$pdf->Rect(33,$y+1,2,2,'F');$pdf->Cell(0,4,' Tarea con esfuerzo ',0,0,'L');
        $pdf->SetXY(80,$y);$pdf->Cell(0,4,'APTITUD:',0,0,'L');
        $pdf->SetXY(120,$y);$pdf->Cell(0,4,'SI',0,0,'L');$pdf->Rect(127,$y,4,4);
        $pdf->SetXY(150,$y);$pdf->Cell(0,4,'NO',0,0,'L');$pdf->Rect(157,$y,4,4);
        $y=$y+7;
        $pdf->SetXY(35,$y);$pdf->Rect(33,$y+1,2,2,'F');$pdf->Cell(0,4,' Esfuerzo y altura',0,0,'L');
        $pdf->SetXY(80,$y);$pdf->Cell(0,4,'APTITUD:',0,0,'L');
        $pdf->SetXY(120,$y);$pdf->Cell(0,4,'SI',0,0,'L');$pdf->Rect(127,$y,4,4);
        $pdf->SetXY(150,$y);$pdf->Cell(0,4,'NO',0,0,'L');$pdf->Rect(157,$y,4,4);
        $y=$y+7;
        $pdf->SetXY(35,$y);$pdf->Rect(33,$y+1,2,2,'F');$pdf->Cell(0,4,' Espacio confinado',0,0,'L');
        $pdf->SetXY(80,$y);$pdf->Cell(0,4,'APTITUD:',0,0,'L');
        $pdf->SetXY(120,$y);$pdf->Cell(0,4,'SI',0,0,'L');$pdf->Rect(127,$y,4,4);
        $pdf->SetXY(150,$y);$pdf->Cell(0,4,'NO',0,0,'L');$pdf->Rect(157,$y,4,4);
        $y=$y+7;
        $pdf->SetXY(35,$y);$pdf->Rect(33,$y+1,2,2,'F');$pdf->Cell(0,4,' Manejo de vehiculo',0,0,'L');
        $pdf->SetXY(80,$y);$pdf->Cell(0,4,'APTITUD:',0,0,'L');
        $pdf->SetXY(120,$y);$pdf->Cell(0,4,'SI',0,0,'L');$pdf->Rect(127,$y,4,4);
        $pdf->SetXY(150,$y);$pdf->Cell(0,4,'NO',0,0,'L');$pdf->Rect(157,$y,4,4);
        $y=$y+5;$pdf->SetXY(35,$y);$pdf->Cell(0,4,'(ver correlacion con los modulos)',0,0,'L');
        $y=$y+10;
        //
        $pdf->Line(77,$y+4,185,$y+4);$pdf->SetXY(10,$y);$pdf->Cell(0,4,'7.    Fecha del Examen Preocupacional:',0,0,'L');
        $y=$y+5;$pdf->SetXY(17,$y);$pdf->Cell(0,4,'De acuerdo a los Modulos de Salud para Examenes en YPF incluye determinacion toxicologica.',0,0,'L');
        $y=$y+10;
        $pdf->Line(100,$y+4,185,$y+4);$pdf->SetXY(10,$y);$pdf->Cell(0,4,'8.    Fecha del ultimo examen de salud anual (Periodico):  ',0,0,'L');$y=$y+10;
        $pdf->Line(165,$y+4,185,$y+4);$pdf->SetXY(10,$y);$pdf->Cell(0,4,'9.    Fecha de ultimos examenes realizados de acuerdo a legislacion vigente (Examen de Riesgo):',0,0,'L');$y=$y+10;
        //
        $pdf->SetXY(10,$y);$pdf->Cell(0,4,'10.  Antecedentes Personales Patologicos del titular de EPAP:',0,0,'L');$y=$y+5;
        $pdf->SetXY(35,$y);$pdf->Rect(33,$y+1,2,2,'F');$pdf->Cell(0,4,' HTA',0,0,'L');
        $pdf->SetXY(120,$y);$pdf->Cell(0,4,'SI',0,0,'L');$pdf->Rect(127,$y,4,4);$pdf->SetXY(150,$y);$pdf->Cell(0,4,'NO',0,0,'L');$pdf->Rect(157,$y,4,4);
        $y=$y+7;
        $pdf->SetXY(35,$y);$pdf->Rect(33,$y+1,2,2,'F');$pdf->Cell(0,4,' DIABETES',0,0,'L');
        $pdf->SetXY(120,$y);$pdf->Cell(0,4,'SI',0,0,'L');$pdf->Rect(127,$y,4,4);$pdf->SetXY(150,$y);$pdf->Cell(0,4,'NO',0,0,'L');$pdf->Rect(157,$y,4,4);
        $y=$y+7;
        $pdf->SetXY(35,$y);$pdf->Rect(33,$y+1,2,2,'F');$pdf->Cell(0,4,' Patologia Cardiovascular',0,0,'L');
        $pdf->SetXY(120,$y);$pdf->Cell(0,4,'SI',0,0,'L');$pdf->Rect(127,$y,4,4);$pdf->SetXY(150,$y);$pdf->Cell(0,4,'NO',0,0,'L');$pdf->Rect(157,$y,4,4);
        $y=$y+7;
        $pdf->SetXY(35,$y);$pdf->Rect(33,$y+1,2,2,'F');$pdf->Cell(0,4,' Patologia Neurologica',0,0,'L');
        $pdf->SetXY(120,$y);$pdf->Cell(0,4,'SI',0,0,'L');$pdf->Rect(127,$y,4,4);$pdf->SetXY(150,$y);$pdf->Cell(0,4,'NO',0,0,'L');$pdf->Rect(157,$y,4,4);
        $y=$y+7;
        $pdf->SetXY(35,$y);$pdf->Rect(33,$y+1,2,2,'F');$pdf->Cell(0,4,' Patologia Oncologicos',0,0,'L');
        $pdf->SetXY(120,$y);$pdf->Cell(0,4,'SI',0,0,'L');$pdf->Rect(127,$y,4,4);$pdf->SetXY(150,$y);$pdf->Cell(0,4,'NO',0,0,'L');$pdf->Rect(157,$y,4,4);
        $y=$y+7;
        $pdf->SetXY(35,$y);$pdf->Rect(33,$y+1,2,2,'F');$pdf->Cell(0,4,' Patologia Respiratoria',0,0,'L');
        $pdf->SetXY(120,$y);$pdf->Cell(0,4,'SI',0,0,'L');$pdf->Rect(127,$y,4,4);$pdf->SetXY(150,$y);$pdf->Cell(0,4,'NO',0,0,'L');$pdf->Rect(157,$y,4,4);
        //pagina 2
        $pdf->AddPage();
        $pdf->Image(public_path("/archivos/reportes/E102_1.jpg"),10,10,35); 
        Tools::generarQR('A', $prestacion->Id, $datos['idExamen'], $prestacion->paciente->Id, "qr");
        $pdf->SetFont('Arial','B',12);
        $pdf->SetY(24);$pdf->Cell(0,4,'Declaracion Jurada de Examenes Psicofisicos Anuales Periodicos ("EPAP")',0,0,'C');
        $pdf->SetFont('Arial','',10);$y=30;
        //
        $pdf->SetXY(10,$y);$pdf->Cell(0,4,'11.    Antecedentes Laborales Patologicos del titular de EPAP:',0,0,'L');$y=$y+5;
        $pdf->SetXY(22,$y);$pdf->Rect(20,$y+1,1,1,'F');$pdf->Cell(0,4,'Registro Accidentes Laboral en los ultimos 2 '.utf8_decode('años').':',0,0,'L');
        $pdf->SetXY(120,$y);$pdf->Cell(0,4,'SI',0,0,'L');$pdf->Rect(127,$y,4,4);
        $pdf->SetXY(150,$y);$pdf->Cell(0,4,'NO',0,0,'L');$pdf->Rect(157,$y,4,4);$y=$y+5;
        $pdf->SetXY(25,$y);$pdf->Cell(0,4,' En caso de respuesta afirmativa especifique:',0,0,'L');$y=$y+5;
        $pdf->SetXY(25,$y);$pdf->Cell(0,4,'- Porcentaje de Incapacidad:',0,0,'L');$y=$y+5;
        $pdf->SetXY(25,$y);$pdf->Cell(0,4,'- ART Tratante:',0,0,'L');$y=$y+5;
        $pdf->SetXY(25,$y);$pdf->Cell(0,4,'- Divergencia:',0,0,'L');$y=$y+5;
        $pdf->SetXY(22,$y);$pdf->Rect(20,$y+1,1,1,'F');$pdf->Cell(0,4,'Realizo denuncia de Enfermedad Profesional:',0,0,'L');
        $pdf->SetXY(120,$y);$pdf->Cell(0,4,'SI',0,0,'L');$pdf->Rect(127,$y,4,4);
        $pdf->SetXY(150,$y);$pdf->Cell(0,4,'NO',0,0,'L');$pdf->Rect(157,$y,4,4);$y=$y+5;
        $pdf->SetXY(25,$y);$pdf->Cell(0,4,' En caso de respuesta afirmativa especifique:',0,0,'L');$y=$y+5;
        $pdf->SetXY(25,$y);$pdf->Cell(0,4,'- Diagnostico:',0,0,'L');$y=$y+5;
        $pdf->SetXY(25,$y);$pdf->Cell(0,4,'- Fecha del Dictamen:',0,0,'L');$y=$y+5;
        $pdf->SetXY(25,$y);$pdf->Cell(0,4,'- ART Tratante:',0,0,'L');$y=$y+5;
        $pdf->SetXY(25,$y);$pdf->Cell(0,4,'- Evaluacion de Incapacidad: Especifique %:',0,0,'L');$y=$y+10;
        //
        $pdf->Line(90,$y+4,185,$y+4);$pdf->SetXY(10,$y);$pdf->Cell(0,4,'OBSERVACIONES MEDICAS ESPECIFICAS:',0,0,'L');$y=$y+5;
        $pdf->Line(10,$y+4,185,$y+4);$y=$y+10;
        //
        $pdf->SetXY(10,$y);$pdf->Cell(0,4,'12.    Cumple los protocolos de YPF segun Anexo adjunto:',0,0,'L');
        $pdf->SetXY(120,$y);$pdf->Cell(0,4,'SI',0,0,'L');$pdf->Rect(127,$y,4,4);
        $pdf->SetXY(150,$y);$pdf->Cell(0,4,'NO',0,0,'L');$pdf->Rect(157,$y,4,4);
        $y=$y+10;
        $pdf->SetXY(10,$y);$pdf->Cell(0,4,'13.    ART. Certificado de cobertura:',0,0,'L');
        $pdf->SetXY(120,$y);$pdf->Cell(0,4,'SI',0,0,'L');$pdf->Rect(127,$y,4,4);
        $pdf->SetXY(150,$y);$pdf->Cell(0,4,'NO',0,0,'L');$pdf->Rect(157,$y,4,4);
        $y=$y+10;
        $pdf->SetXY(10,$y);$pdf->Cell(0,4,'14.    Mapa de riesgo y examen de riesgo:',0,0,'L');
        $pdf->SetXY(120,$y);$pdf->Cell(0,4,'SI',0,0,'L');$pdf->Rect(127,$y,4,4);
        $pdf->SetXY(150,$y);$pdf->Cell(0,4,'NO',0,0,'L');$pdf->Rect(157,$y,4,4);
        $y=$y+10;
        $pdf->Line(60,$y+4,105,$y+4);$pdf->SetXY(10,$y);$pdf->Cell(0,4,'15.    Servicio Medico Externo:',0,0,'L');
        $pdf->Line(127,$y+4,185,$y+4);$pdf->SetXY(110,$y);$pdf->Cell(0,4,' Interno:',0,0,'L');
        $y=$y+10;
        $pdf->SetXY(10,$y);$pdf->Cell(0,4,'16.    Responsable del area de Salud de la Empresa:',0,0,'L');
        $pdf->SetXY(120,$y);$pdf->Cell(0,4,'SI',0,0,'L');$pdf->Rect(127,$y,4,4);
        $pdf->SetXY(150,$y);$pdf->Cell(0,4,'NO',0,0,'L');$pdf->Rect(157,$y,4,4);
        $y=$y+10;
        $pdf->SetXY(10,$y);$pdf->Cell(0,4,'17.    Matricula Profesional:',0,0,'L');
        $pdf->SetXY(120,$y);$pdf->Cell(0,4,'SI',0,0,'L');$pdf->Rect(127,$y,4,4);
        $pdf->SetXY(150,$y);$pdf->Cell(0,4,'NO',0,0,'L');$pdf->Rect(157,$y,4,4);
        $y=$y+10;
        $pdf->SetXY(10,$y);$pdf->Cell(0,4,'18.    Titulo habilitante:',0,0,'L');
        $pdf->SetXY(120,$y);$pdf->Cell(0,4,'SI',0,0,'L');$pdf->Rect(127,$y,4,4);
        $pdf->SetXY(150,$y);$pdf->Cell(0,4,'NO',0,0,'L');$pdf->Rect(157,$y,4,4);
        $y=$y+10;
        $pdf->SetXY(10,$y);$pdf->Cell(0,4,'19.    Titulo de Especialista en Medicina del Trabajo:',0,0,'L');
        $pdf->SetXY(120,$y);$pdf->Cell(0,4,'SI',0,0,'L');$pdf->Rect(127,$y,4,4);
        $pdf->SetXY(150,$y);$pdf->Cell(0,4,'NO',0,0,'L');$pdf->Rect(157,$y,4,4);
        $y=$y+10;
        $pdf->SetXY(10,$y);$pdf->Cell(0,4,'20.    Certificado de colegiacion:',0,0,'L');
        $pdf->SetXY(120,$y);$pdf->Cell(0,4,'SI',0,0,'L');$pdf->Rect(127,$y,4,4);
        $pdf->SetXY(150,$y);$pdf->Cell(0,4,'NO',0,0,'L');$pdf->Rect(157,$y,4,4);
        $y=$y+5;$pdf->SetFont('Arial','',8);
        $pdf->SetXY(10,$y);$pdf->Cell(0,4,'Nota: El pedido de certificado de colegiacion se ajusta a todas las provincias donde este requisito es obligatorio para el ejercicio de la profesion.',0,0,'L');
        $y=$y+10;$pdf->SetFont('Arial','',10);
        $pdf->SetXY(10,$y);$pdf->Cell(0,4,'21.    Contacto del Medico del Proveedor de Servicio:',0,0,'L');$y=$y+5;
        $pdf->Line(55,$y+4,185,$y+4);$pdf->SetXY(19,$y);$pdf->Cell(0,4,'Correo Electronico:',0,0,'L');$y=$y+5;
        $pdf->Line(68,$y+4,105,$y+4);$pdf->SetXY(19,$y);$pdf->Cell(0,4,'Telefono de contacto: Movil',0,0,'L');
        $pdf->Line(127,$y+4,185,$y+4);$pdf->SetXY(110,$y);$pdf->Cell(0,4,'       Fijo',0,0,'L');
        //
        $y=$y+20;
        $pdf->SetFont('Arial','B',10);
        $pdf->SetXY(20,$y);$pdf->Cell(70,3,'Sello y Firma del Apoderado',0,0,'C');
        $pdf->SetXY(120,$y);$pdf->Cell(70,3,'Sello y Firma del',0,0,'C');$y=$y+4;
        $pdf->SetXY(20,$y);$pdf->Cell(70,3,'de la Empresa',0,0,'C');
        $pdf->SetXY(120,$y);$pdf->Cell(70,3,'Responsable Medico',0,0,'C');
        $y=$y+10;
        $pdf->SetXY(10,$y);$pdf->Cell(0,3,'"Visado por el Medico de YPF y posterior carga en el SRC"',0,0,'C');
        $y=$y+7;$pdf->SetFont('Arial','B',8);
        $pdf->SetXY(10,$y);$pdf->Cell(0,3,'Nota: Por la presente Declaracion Jurada, la Contratista manifiesta que ha solicitado y realizado a todo su personal el EPAP anual',0,0,'L');$y=$y+4;
        $pdf->SetXY(10,$y);$pdf->Cell(0,3,'correspondiente. Asimismo se obliga por la presente a informar a YPF la ubicacion fisica de los EPAP.',0,0,'L');$y=$y+4;
        $pdf->SetXY(10,$y);$pdf->Cell(0,3,'YPF se reserva el derecho de realizar, en cualquier momento, auditorias de control sobre la informacion brindada por la Contratista',0,0,'L');$y=$y+4;
        $pdf->SetXY(10,$y);$pdf->Cell(0,3,'bajo Declaracion Jurada.',0,0,'L');$y=$y+4;
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
