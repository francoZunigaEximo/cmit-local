<?php

namespace App\Services\ReportesExcel\modelos;

use App\Services\ReportesExcel\ReporteInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use Illuminate\Support\Str;
use App\Helpers\ToolsReportes;

use App\Models\Mapa as ModeloMapa;
use Illuminate\Support\Facades\DB;

class Mapa implements ReporteInterface
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
            'A1' => 'Id',
            'B1' => 'Nro',
            'C1' => 'Art',
            'D1' => 'Empresa',
            'E1' => 'Fecha Corte',
            'F1' => 'Fecha Entrega',
            'G1' => 'Inactivo',
            'H1' => 'Nro de Remito',
            'I1' => 'eEnviado',
            'J1' => 'Cerrado',
            'K1' => 'Entregado',
            'L1' => 'Finalizado',
            'M1' => 'Apellido y Nombre',
            'N1' => 'Observación'
        ];

        $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'M', 'N'];

        foreach ($columnas as $columna) {
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        // Establecer los encabezados de las celdas
        foreach ($encabezados as $celda => $valor) {
            $sheet->setCellValue($celda, $valor);
        }
    }

    public function datos($sheet, $ids)
    {
        $mapas = $this->queryMapa($ids);

        $fila = 2;
        foreach($mapas as $mapa){
            $sheet->setCellValue('A'.$fila, $mapa->Id);
            $sheet->setCellValue('B'.$fila, $mapa->Nro);
            $sheet->setCellValue('C'.$fila, $mapa->Art ?? '');
            $sheet->setCellValue('D'.$fila, $mapa->Empresa ?? '');
            $sheet->setCellValue('E'.$fila, $mapa->Fecha);
            $sheet->setCellValue('F'.$fila, $mapa->FechaE);
            $sheet->setCellValue('G'.$fila, $mapa->Inactivo === 0 ? "No" : "Si");
            $sheet->setCellValue('H'.$fila, $mapa->NroCEE);
            $sheet->setCellValue('I'.$fila, in_array($mapa->eEnviado, [0,'',null]) ? "No" : "Si");
            $sheet->setCellValue('J'.$fila, in_array($mapa->Cerrado, [0,'',null]) ? "No" : "Si");
            $sheet->setCellValue('K'.$fila, in_array($mapa->Entregado, [0,'',null]) ? "No" : "Si");
            $sheet->setCellValue('L'.$fila, in_array($mapa->Finalizado, [0,'',null]) ? "No" : "Si");
            $sheet->setCellValue('M'.$fila, $mapa->NombreCompleto ?? '-');
            $sheet->setCellValue('N'.$fila, $mapa->Obs);
            $fila++;
        }
    }

    public function generar($mapas)
    {
        $this->columnasYEncabezados($this->sheet);
        $this->datos($this->sheet, $mapas);
        
        $name = 'mapas_' . Str::random(6) . '.xlsx';
        return $this->generarArchivo($this->spreadsheet, $name);
    }

    private function queryMapa(array $ids)
    {
        return ModeloMapa::join('clientes as empresa', 'mapas.IdEmpresa', '=', 'empresa.Id')
            ->join('clientes as art', 'mapas.IdART', '=', 'art.Id')
            ->leftJoin('prestaciones', 'mapas.Id', '=', 'prestaciones.IdMapa')
            ->leftJoin('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->select(
                'mapas.Id as Id',
                'mapas.Nro as Nro',
                'art.RazonSocial as Art',
                'empresa.RazonSocial as Empresa',
                'mapas.Fecha',
                'mapas.FechaE',
                'mapas.Inactivo as Inactivo',
                'prestaciones.NroCEE as NroCEE',
                'prestaciones.eEnviado as eEnviado',
                'prestaciones.Cerrado as Cerrado',
                'prestaciones.Entregado as Entregado',
                'prestaciones.Finalizado as Finalizado',
                DB::raw("CONCAT(pacientes.Apellido,' ',pacientes.Nombre) as NombreCompleto"),
                'mapas.Obs as Obs'
            )->whereIn('mapas.Id', $ids)
            ->get();
    }
}