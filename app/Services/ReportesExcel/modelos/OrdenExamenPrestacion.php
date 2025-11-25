<?php

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use App\Helpers\ToolsReportes;
use App\Models\ItemPrestacion;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use Illuminate\Support\Str;

class OrdenExamenPrestacion implements ReporteInterface
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
        $encabezados = [
            'A1' => 'Especialidad',
            'B1' => 'Fecha',
            'C1' => 'Prestacion',
            'D1' => 'Empresa',
            'E1' => 'Paciente',
            'F1' => 'Estado',
            'G1' => 'Examen',
            'H1' => 'Efector',
            'I1' => 'Estado Efector',
            'J1' => 'Tipo Adjunto',
            'K1' => 'Informador',
            'L1' => 'Estado Informador',
            'M1' => 'Fecha Vencimiento'
        ];

        $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J','K','L','M'];

        foreach ($encabezados as $celda => $valor) {
            $sheet->setCellValue($celda, $valor);
        }

        foreach ($columnas as $columna) {
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        $sheet->getStyle('A1:M1')->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('CCCCCCCC'); 

        // Agregar bordes gruesos a la celda
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];

        $sheet->getStyle('A1:M1')->applyFromArray($styleArray);
        $sheet->getStyle('A1:M1')->getFont()->setBold(true)->setSize(11);
    }

    public function datos($sheet, $examenes)
    {
        $fila = 2;

        $items = $this->queryPrestacion($examenes);

        foreach($items as $item){

            $estado = $item->PresCerrado === 0 && $item->PresFinalizado === 0
                        ? 'Abierto'
                        : ($item->PresCerrado === 1 && $item->PresFinalizado === 0
                            ? 'Cerrado'
                            : ($item->PresCerrado === 1 && $item->PresFinalizado === 1
                                ? 'Finalizado'
                                : ($item->PresEntregado === 1
                                    ? 'Entregado'
                                    : ($item->PresCerrado === 1 && $item->PresEnviado === 1
                                        ? 'eEnviado'
                                        : '-'))));

            $estadoEfector = in_array($item->Efector, [1,2,4]) 
                                ? "Pendiente"
                                : (in_array($item->Efector, [3,5])
                                    ? 'Cerrado'
                                    : '-');

            $arr = [0 => '', 1 => 'Abierto/Pdte', 2 => 'Abierto/Adjunto', 3 => '', 4 => 'Cerrado/Pdte', 5 => 'Cerrado/Adjunto'];

            $adjunto = $arr[$item->Efector];
            
            $estadoInformador = ($item->Informador === 1) 
                                    ? "Pendiente"
                                    : ($item->Informador === 2
                                        ? 'Borrador'
                                        : ($item->Informador === 3
                                            ? 'Cerrado'
                                            : '-'));

            $itemFecha = new DateTime($item->Fecha);
            $vencimiento = $itemFecha->add(new DateInterval('P' . intval($item->DiasVencimiento) . 'D'));
                                            

            $sheet->setCellValue('A'.$fila, $item->Especialidad);
            $sheet->setCellValue('B'.$fila, Carbon::parse($item->Fecha)->format('d/m/Y'));
            $sheet->setCellValue('C'.$fila, $item->IdPrestacion);
            $sheet->setCellValue('D'.$fila, $item->Empresa);
            $sheet->setCellValue('E'.$fila, $item->NombrePaciente ." ". $item->ApellidoPaciente);
            $sheet->setCellValue('F'.$fila, $estado);
            $sheet->setCellValue('G'.$fila, $item->Examen);
            $sheet->setCellValue('H'.$fila, $item->NombreProfesional . " " . $item->ApellidoProfesional);
            $sheet->setCellValue('I'.$fila, $estadoEfector);
            $sheet->setCellValue('J'.$fila, $adjunto);
            $sheet->setCellValue('K'.$fila, $item->NombreProfesional2 . " " . $item->ApellidoProfesional2);
            $sheet->setCellValue('L'.$fila, $estadoInformador);
            $sheet->setCellValue('M'.$fila, Carbon::parse($vencimiento)->format('d/m/Y'));
            $fila++;
        }
    }

    public function generar($examenes)
    {
        $this->columnasYEncabezados($this->sheet);
        $this->datos($this->sheet, $examenes);

        $name = 'ordenesExPrestacion' . Str::random(6) . '.xlsx';
        return $this->generarArchivo($this->spreadsheet, $name);
    }

    private function queryPrestacion(?array $ids): mixed
    {

        return ItemPrestacion::join('prestaciones', 'itemsprestaciones.IdPrestacion', '=', 'prestaciones.Id')
        ->join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
        ->join('proveedores', 'examenes.IdProveedor2', '=', 'proveedores.Id')
        ->join('clientes', 'prestaciones.IdEmpresa', '=', 'clientes.Id')
        ->join('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
        ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
        ->join('profesionales as prof1', 'itemsprestaciones.IdProfesional', '=', 'prof1.Id')
        ->join('profesionales as prof2', 'itemsprestaciones.IdProfesional2', '=', 'prof2.Id')
        ->select(
            'itemsprestaciones.Id as IdItem',
            'itemsprestaciones.Fecha as Fecha',
            'itemsprestaciones.CAdj as Efector',
            'itemsprestaciones.CInfo as Informador',
            'itemsprestaciones.IdProfesional as IdProfesional',
            'proveedores.Nombre as Especialidad',
            'proveedores.Id as IdEspecialidad',
            'prestaciones.Id as IdPrestacion',
            'prestaciones.Cerrado as PresCerrado',
            'prestaciones.Finalizado as PresFinalizado',
            'prestaciones.Entregado as PresEntregado',
            'prestaciones.eEnviado as PresEnviado',
            'clientes.RazonSocial as Empresa',
            'pacientes.Nombre as NombrePaciente',
            'pacientes.Apellido as ApellidoPaciente',
            'prof1.Nombre as NombreProfesional',
            'prof1.Apellido as ApellidoProfesional',
            'prof2.Nombre as NombreProfesional2',
            'prof2.Apellido as ApellidoProfesional2',
            'examenes.Nombre as Examen',
            'examenes.Id as IdExamen',
            'examenes.DiasVencimiento as DiasVencimiento',
            'examenes.NoImprime as NoImprime'
        )->whereNot('itemsprestaciones.Id', 0)
        ->whereIn('itemsprestaciones.Id', $ids)
        ->get();
    }
}