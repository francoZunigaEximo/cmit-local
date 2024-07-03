<?php

namespace App\Traits;

use App\Models\FacturaDeVenta;
use App\Models\PrecioPorCodigo;
use App\Models\ReporteFinneg;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use illuminate\Support\Str;

trait ReporteExcel 
{
    public function finnegans(array $ids, string $tipo)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', ];

        foreach($columnas as $columna){
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        $sheet->setCellValue('A1', 'NUMERO');
        $sheet->setCellValue('B1', 'FECHA');
        $sheet->setCellValue('C1', 'CLIENTE');
        $sheet->setCellValue('D1', 'COMPROBANTE');
        $sheet->setCellValue('E1', 'CONDICIONPAGO');
        $sheet->setCellValue('F1', 'VENDEDOR');
        $sheet->setCellValue('G1', 'SUCURSAL');
        $sheet->setCellValue('H1', 'DESCRIPCION');
        $sheet->setCellValue('I1', 'PRODUCTO');
        $sheet->setCellValue('J1', 'DESCRIPCIONITEM');
        $sheet->setCellValue('K1', 'CANTIDAD');
        $sheet->setCellValue('L1', 'PRECIO');
        $sheet->setCellValue('M1', 'MONEDA_COTIZACION');
        $sheet->setCellValue('N1', 'COTIZACION');
        $sheet->setCellValue('O1', 'MONEDA');
        $sheet->setCellValue('P1', 'WORKFLOW');
        $sheet->setCellValue('Q1', 'FECHACOMPROBANTE');
        $sheet->setCellValue('R1', 'FECHABASEVENCIMIENTO');
        $sheet->setCellValue('S1', 'DESTINATARIO');
        $sheet->setCellValue('T1', 'COMPROBANTEADICIONAL');
        $sheet->setCellValue('U1', 'DIMENSION');
        $sheet->setCellValue('V1', 'DIMENSIONVALOR');
        $sheet->setCellValue('W1', 'DIMENSION2');
        $sheet->setCellValue('X1', 'DIMENSIONVALOR2');
        $sheet->setCellValue('Y1', 'DIMENSION3');
        $sheet->setCellValue('Z1', 'DIMENSIONVALOR3');

        $query = FacturaDeVenta::with(['empresa','facturaresumen','prestacion'])->whereHas('empresa', function($q) use ($tipo) {
            $q->where('TipoCliente', $tipo);
        })->whereIn('Id', $ids)->orderBy('Id')->get();


        $fila = 2;
        foreach($query as $data){
            $queryPrecio = PrecioPorCodigo::where('Cod', $data->facturaResumen->Cod)->whereNot('Id', 0)->first();
            $precio = $data->facturaResumen->Ajuste != 0 ? $queryPrecio->Precio * (1-($data->facturaResumen->Ajuste/100)) : ($queryPrecio->Precio ?? '0.00');

            $contador = ReporteFinneg::count() === 0 ? 10000 : ReporteFinneg::max('IdFinneg') + 1;

            ReporteFinneg::create([
                'IdFinneg' => $contador,
                'IdFactura' => $data->Id,
                'cuit_cliente' =>  str_replace("-", "", $data->empresa->Identificacion),
                'created_at' => now()->format('Y-m-d H:i:m'),
                'updated_at' => now()->format('Y-m-d H:i:m'),
            ])->refresh();

            $sheet->setCellValue('A'.$fila, ReporteFinneg::max('IdFinneg'));
            $sheet->setCellValue('B'.$fila, now()->format('d/m/Y'));
            $sheet->setCellValue('C'.$fila, str_replace('-','', $data->empresa->Identificacion));
            $sheet->setCellValue('D'.$fila, $data->Tipo . '-' . sprintf('%04d', $data->Sucursal) . '-' . sprintf('%08d', $data->NroFactura));
            $sheet->setCellValue('E'.$fila, '7');
            $sheet->setCellValue('F'.$fila, '');
            $sheet->setCellValue('G'.$fila, '');
            $sheet->setCellValue('H'.$fila, 'REMITO NRO: '.$data->prestacion->NroCEE.'| Mapa: '.$data->prestacion->IdMapa.' | Empresa: '.$data->empresa->RazonSocial);
            $sheet->setCellValue('I'.$fila, $data->facturaresumen->Cod);
            $sheet->setCellValue('J'.$fila, '');
            $sheet->setCellValue('K'.$fila, $data->facturaresumen->Total);
            $sheet->setCellValue('L'.$fila, '$'.number_format($precio, 2, ',', '.'));
            $sheet->setCellValue('M'.$fila, 'DOL');
            $sheet->setCellValue('N'.$fila, '16,1');
            $sheet->setCellValue('O'.$fila, 'PES');
            $sheet->setCellValue('P'.$fila, 'VTASSERV');
            $sheet->setCellValue('Q'.$fila, now()->format('d/m/Y'));
            $sheet->setCellValue('R'.$fila, now()->format('d/m/Y'));
            $sheet->setCellValue('S'.$fila, '');
            $sheet->setCellValue('T'.$fila, '');
            $sheet->setCellValue('U'.$fila, '');
            $sheet->setCellValue('V'.$fila, '');
            $sheet->setCellValue('W'.$fila, '');
            $sheet->setCellValue('X'.$fila, '');
            $sheet->setCellValue('Y'.$fila, '');
            $sheet->setCellValue('Z'.$fila, '');
            $fila++;
        }

        // Generar un nombre aleatorio para el archivo
        $name = Str::random(10).'.xlsx';

        // Guardar el archivo en la carpeta de almacenamiento
        $filePath = storage_path('app/public/'.$name);

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        chmod($filePath, 0777);

        // Devolver la ruta del archivo generado
        return response()->json(['filePath' => $filePath, 'msg' => 'Se ha generado correctamente el reporte de Finnegans', 'estado' => 'success']);   
    }

}