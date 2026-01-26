<?php

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Illuminate\Support\Str;
use App\Helpers\ToolsReportes;
use App\Models\Auditor;
use App\Models\ExamenCuentaIt;
use App\Models\ItemPrestacion;
use App\Models\PrestacionObsFase;
use App\Models\Telefono;
use Carbon\Carbon;

class ResumenTotal implements ReporteInterface
{
    protected $spreadsheet;
    protected $sheet;

    use ToolsReportes;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
        $this->sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

    }

    public function columnasYEncabezados($sheet)
    {
        $columnas = ['A', 'B', 'C', 'D', 'E','F','G','H','I','J','K'];

        foreach($columnas as $columna){
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }
    }

    public function datos($sheet, $prestacion)
    {
        $styleTitulos = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EEEEEE'], // Color de fondo (amarillo en este caso)
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, // Bordes finos
                    'color' => ['rgb' => '000000'], // Color del borde (negro en este caso)
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Alineación centrada
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, // Alineación centrada vertical
            ]
        ];

        $styleBordes = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, // Bordes finos
                    'color' => ['rgb' => '000000'], // Color del borde (negro en este caso)
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Alineación centrada
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, // Alineación centrada vertical
            ]
        ];

        $nombreCompleto = $prestacion->paciente->Apellido.' '.$prestacion->paciente->Nombre;

        $factura = ExamenCuentaIt::join('pagosacuenta', 'pagosacuenta_it.IdPago', '=', 'pagosacuenta.Id')
            ->select(
                'pagosacuenta.Tipo as Tipo',
                'pagosacuenta.Suc as Suc',
                'pagosacuenta.Nro as Nro'
            )
            ->where('pagosacuenta_it.IdPrestacion', $prestacion->Id)->first();

        $telefono = Telefono::join('pacientes', 'telefonos.IdEntidad', '=', 'pacientes.Id')->where('telefonos.IdEntidad', $prestacion->paciente->Id)->where('telefonos.TipoEntidad', 'i')->first();
        
        $examenes = ItemPrestacion::with(['examenes', 'examenes.proveedor1', 'profesionales1', 'profesionales2'])->where('itemsprestaciones.IdPrestacion', $prestacion->Id)->get();

        $auditorias = Auditor::with('auditarAccion')->where('IdRegistro', $prestacion->Id)->orderBy('Id', 'Asc')->get();

        $nroFactura = $factura !== null 
            ? 'EXAMEN A CUENTA NRO '.$factura->Tipo ?? 'X'.str_pad($factura->Suc, 4, '0', STR_PAD_LEFT) ?? '0000'.'-'.str_pad($factura->Nro, 8, '0', STR_PAD_LEFT) ?? '00000000'
            : '';

        $telefonoPaciente = $telefono !== null 
            ? '('.$telefono->CodigoArea ?? '000'.')'.$telefono->NumeroTelefono ?? '0000000'
            : '(000)000000';

        $comentariosPrivados = $this->comentariosPrivados($prestacion->Id);

        $fechaVencimiento = $this->formatearFecha($prestacion->FechaVto);
        $fechaCierre =  $this->formatearFecha($prestacion->FechaCierre);
        $fechaFinalizado = $this->formatearFecha($prestacion->FechaFinalizado);
        $fechaEntrega =  $this->formatearFecha($prestacion->FechaEntrega);
        $fechaAnulado = $this->formatearFecha($prestacion->FechaAnul);
        $fechaIngreso =  $this->formatearFecha($prestacion->paciente->fichaLaboral->first()->FechaIngreso);
        $fechaEgreso = $this->formatearFecha($prestacion->paciente->fichaLaboral->first()->FechaEgreso);

        $sheet->setCellValue('A1', 'Prestación: '.$prestacion->Id)->getStyle('A1')->getFont()->setBold(true);
        $sheet->setCellValue('A2', 'Alta: '.Carbon::parse($prestacion->Fecha)->format('d/m/Y') ?? '');
        $sheet->setCellValue('B2', 'Paciente: '.$prestacion->paciente->Id.' '.$nombreCompleto);
        $sheet->setCellValue('A3', 'Vencimiento: '.$fechaVencimiento);
        $sheet->setCellValue('B3', 'Empresa: '.$prestacion->empresa->RazonSocial ?? '');
        $sheet->setCellValue('A4', 'Tipo: '.$prestacion->TipoPrestacion ?? '');
        $sheet->setCellValue('B4', 'ART: '. $prestacion->art->RazonSocial ?? '');

        $sheet->setCellValue('A6', 'Estado')->getStyle('A6')->getFont()->setBold(true);
        $sheet->setCellValue('A7', 'Cerrado: '.$fechaCierre);
        $sheet->setCellValue('A8', 'Finalizado: '.$fechaFinalizado);
        $sheet->setCellValue('B7', 'Nro Constancia'. $prestacion->NroCEE ?? '')->getStyle('B7')->getFont()->setBold(true);
        $sheet->setCellValue('A9', 'Entregado: '. $fechaEntrega);
        $sheet->setCellValue('A10', 'Facturado: '.$nroFactura);
        $sheet->setCellValue('A11', 'Anulado: '.$fechaAnulado);

        $sheet->setCellValue('A13', 'Resultados')->getStyle('A13')->getFont()->setBold(true);
        $sheet->setCellValue('A14', 'Evaluación: '.substr($prestacion->Evaluacion, 2) ?? '');
        $sheet->setCellValue('A15', 'Calificación: '.substr($prestacion->Calificacion, 2) ?? '');
        $sheet->setCellValue('A16', 'Observación: '.$prestacion->Observaciones ?? '');
        $sheet->setCellValue('A18', 'Comentarios Examenes');
        $sheet->setCellValue('A19', 'Comentarios: '.$prestacion->ObsExamenes);

        $sheet->setCellValue('A21', 'Informe de Placas')->getStyle('A21')->getFont()->setBold(true);
        $sheet->setCellValue('A22', 'Informante: ');
        $sheet->setCellValue('A23', 'Informe: ');

        $sheet->setCellValue('A25', 'Datos del Paciente')->getStyle('A25')->getFont()->setBold(true);
        $sheet->setCellValue('A26', $prestacion->paciente->TipoDocumento.': '.$prestacion->paciente->Documento.' - Edad: '.Carbon::parse($prestacion->paciente->FechaNacimiento)->age.' - '.$prestacion->paciente->EstadoCivil ?? '');
        $sheet->setCellValue('A27', 'Dirección: '.$prestacion->paciente->Direccion ?? '');
        $sheet->setCellValue('A28', 'Tareas: '.$prestacion->paciente->fichaLaboral->first()->Tareas.' - Ultima Empresa: '.$prestacion->paciente->fichaLaboral->first()->TareasTareasEmpAnterior ?? ''.'C.Costos: '.$prestacion->paciente->fichaLaboral->first()->CCosto ?? '');
        $sheet->setCellValue('A29', 'Puesto: '.$prestacion->paciente->fichaLaboral->first()->Puesto ?? ''.' - Sector: '.$prestacion->paciente->fichaLaboral->first()->Sector ?? ''.' - Jornada: '.$prestacion->paciente->fichaLaboral->first()->Jornada ?? ''.' - F. Ingreso: '.$fechaIngreso.' - F. Egreso: '.$fechaEgreso);
        $sheet->setCellValue('A30', 'Tel: '.$telefonoPaciente);

        $sheet->setCellValue('A32', 'Examenes')->getStyle('A32')->getFont()->setBold(true);

        $sheet->setCellValue('A33', 'Examen')->getStyle('A33')->applyFromArray($styleTitulos)->getFont()->setBold(true);
        $sheet->setCellValue('B33', 'Especialidad')->getStyle('B33')->applyFromArray($styleTitulos)->getFont()->setBold(true);
        $sheet->setCellValue('C33', 'Efector')->getStyle('C33')->applyFromArray($styleTitulos)->getFont()->setBold(true);
        $sheet->setCellValue('D33', 'Fecha y Hora Asignado')->getStyle('D33')->applyFromArray($styleTitulos)->getFont()->setBold(true);
        $sheet->setCellValue('E33', 'Informador')->getStyle('E33')->applyFromArray($styleTitulos)->getFont()->setBold(true);
        $sheet->setCellValue('F33', 'Fecha y Hora Asignado')->getStyle('F33')->applyFromArray($styleTitulos)->getFont()->setBold(true);
        $sheet->setCellValue('G33', 'Evaluador')->getStyle('G33')->applyFromArray($styleTitulos)->getFont()->setBold(true);
        $sheet->setCellValue('H33', 'Observaciones')->getStyle('H33')->applyFromArray($styleTitulos)->getFont()->setBold(true);
        $sheet->setCellValue('I33', 'Pagado')->getStyle('I33')->applyFromArray($styleTitulos)->getFont()->setBold(true);
        $sheet->setCellValue('J33', 'Facturado')->getStyle('J33')->applyFromArray($styleTitulos)->getFont()->setBold(true);
        $sheet->setCellValue('K33', 'Anulado')->getStyle('K33')->applyFromArray($styleTitulos)->getFont()->setBold(true);

        $fila = 34;

        $nuevaFila = $fila + count($examenes) + 3;

        foreach($examenes as $examen) {

            $nombreProfesionalEfector = $examen->profesionales1->RegHis === 1 
                ? $examen->profesionales1->Apellido ?? ''.' '.$examen->profesionales1->Nombre ?? ''
                : $examen->profesionales1->user->personal->Apellido ?? ''.' '.$examen->profesionales1->user->personal->Nombre ?? '';

            $nombreProfesionalInformador = $examen->profesionales2->RegHis === 1 
                ? $examen->profesionales2->Apellido ?? ''.' '.$examen->profesionales2->Nombre ?? ''
                : $examen->profesionales2->user->personal->Apellido ?? ''.' '.$examen->profesionales2->user->personal->Nombre ?? '';

            $nombreEvaluador = $examen->prestaciones->profesional->RegHis === 1
                ? $examen->prestaciones->profesional->Apellido ?? ''.' '.$examen->prestaciones->profesional->Nombre ?? ''
                : $examen->prestaciones->profesional->user->personal->Apellido ?? ''.' '.$examen->prestaciones->profesional->user->personal->Nombre ?? '';

            $sheet->setCellValue('A'.$fila, $examen->examenes->Nombre ?? '-')->getStyle('A'.$fila)->applyFromArray($styleBordes);
            $sheet->setCellValue('B'.$fila, $examen->examenes->proveedor1->Nombre ?? '-')->getStyle('B'.$fila)->applyFromArray($styleBordes);
            $sheet->setCellValue('C'.$fila, $nombreProfesionalEfector ?? '-')->getStyle('C'.$fila)->applyFromArray($styleBordes);
            $sheet->setCellValue('D'.$fila, $this->formatearFecha($examen->FechaAsignado).' '.$examen->HoraAsignado)->getStyle('D'.$fila)->applyFromArray($styleBordes);
            $sheet->setCellValue('E'.$fila, $nombreProfesionalInformador ?? '-')->getStyle('E'.$fila)->applyFromArray($styleBordes);
            $sheet->setCellValue('F'.$fila, $this->formatearFecha($examen->FechaAsignadoI).' '.$examen->HoraAsignadoI ?? '')->getStyle('F'.$fila)->applyFromArray($styleBordes);
            $sheet->setCellValue('G'.$fila, $nombreEvaluador ?? '-')->getStyle('G'.$fila)->applyFromArray($styleBordes);
            $sheet->setCellValue('H'.$fila, $examen->ObsExamen ?? '')->getStyle('H'.$fila)->applyFromArray($styleBordes);
            $sheet->setCellValue('I'.$fila, $this->formatearFecha($examen->FechaPagado))->getStyle('I'.$fila)->applyFromArray($styleBordes);
            $sheet->setCellValue('J'.$fila, $examen->Facturado === 1 ? 'SI' : 'NO')->getStyle('J'.$fila)->applyFromArray($styleBordes);
            $sheet->setCellValue('K'.$fila, $examen->Anulado === 1 ? 'SI' : 'NO')->getStyle('K'.$fila)->applyFromArray($styleBordes);
            $fila++;
        }

        $sheet->setCellValue('A'.$nuevaFila - 1, 'Auditoria de Cambios')->getStyle('A'.$nuevaFila - 1)->getFont()->setBold(true);

        $sheet->setCellValue('A'.$nuevaFila, 'Usuario')->getStyle('A'.$nuevaFila)->applyFromArray($styleTitulos)->getFont()->setBold(true);
        $sheet->setCellValue('B'.$nuevaFila, 'Acción')->getStyle('B'.$nuevaFila)->applyFromArray($styleTitulos)->getFont()->setBold(true);
        $sheet->setCellValue('C'.$nuevaFila, 'Fecha')->getStyle('C'.$nuevaFila)->applyFromArray($styleTitulos)->getFont()->setBold(true);

        $tablaFila = $nuevaFila + 1;

        foreach ($auditorias as $auditoria) {
            $sheet->setCellValue('A'.$tablaFila, $auditoria->IdUsuario ?? '-')->getStyle('A'.$tablaFila)->applyFromArray($styleBordes);
            $sheet->setCellValue('B'.$tablaFila, $auditoria->auditarAccion->Nombre ?? '-')->getStyle('B'.$tablaFila)->applyFromArray($styleBordes);
            $sheet->setCellValue('C'.$tablaFila, Carbon::parse($auditoria->Fecha)->format('d/m/Y h:i:s'))->getStyle('C'.$tablaFila)->applyFromArray($styleBordes);
            $tablaFila++; 
        }

        $comentFila = $tablaFila + count($auditorias) + 3;

        $sheet->setCellValue('A'.$comentFila - 1, 'Observaciones Privadas')->getStyle('A'.$comentFila - 1)->getFont()->setBold(true);

        $sheet->setCellValue('A'.$comentFila, 'Fecha')->getStyle('A'.$comentFila)->applyFromArray($styleTitulos)->getFont()->setBold(true);
        $sheet->setCellValue('B'.$comentFila, 'Usuario')->getStyle('B'.$comentFila)->applyFromArray($styleTitulos)->getFont()->setBold(true);
        $sheet->setCellValue('C'.$comentFila, 'Comentario')->getStyle('C'.$comentFila)->applyFromArray($styleTitulos)->getFont()->setBold(true);

        $tablaFila2 = $comentFila + 1;

        foreach ($comentariosPrivados as $comentario) {
            $sheet->setCellValue('A'.$tablaFila2, Carbon::parse($comentario->Fecha)->format('d/m/Y H:i:s') ?? '-')->getStyle('A'.$tablaFila2)->applyFromArray($styleBordes);
            $sheet->setCellValue('B'.$tablaFila2, $comentario->IdUsuario ?? '-')->getStyle('B'.$tablaFila2)->applyFromArray($styleBordes);
            $sheet->setCellValue('C'.$tablaFila2, $comentario->Comentario ?? '-')->getStyle('C'.$tablaFila2)->applyFromArray($styleBordes);
            $tablaFila2++;
        }

    }

    public function generar($datos)
    {
        $this->columnasYEncabezados($this->sheet);
        $this->datos($this->sheet, $datos);

        $name = 'resumenTotal_' . Str::random(6) . '.xlsx';
        return $this->generarArchivo($this->spreadsheet, $name);
    }

    private function formatearFecha($fecha)
    {
        return $fecha === '0000-00-00' ? '' : Carbon::parse($fecha)->format('d/m/Y');
    }

    private function comentariosPrivados(int $id)
    {
        return PrestacionObsFase::join('prestaciones', 'prestaciones_obsfases.IdEntidad', '=', 'prestaciones.Id')
                ->join('mapas', 'prestaciones.IdMapa', '=', 'mapas.Id')
                ->join('users', 'prestaciones_obsfases.IdUsuario', '=', 'users.name')
                ->select('prestaciones_obsfases.*', 'prestaciones_obsfases.Rol as nombre_perfil')
                ->where('prestaciones.Id', $id)
                ->orderBy('prestaciones_obsfases.Id', 'DESC')
                ->get();
    }
}