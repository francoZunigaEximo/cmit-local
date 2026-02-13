<?php

namespace App\Traits;

use App\Models\Auditor;
use App\Models\Cliente;
use App\Models\ExamenCuentaIt;
use App\Models\FacturaDeVenta;
use App\Models\ItemPrestacion;
use App\Models\Mapa;
use App\Models\PrecioPorCodigo;
use App\Models\Prestacion;
use App\Models\PrestacionObsFase;
use App\Models\ReporteFinneg;
use App\Models\Telefono;
use Carbon\Carbon;
use FontLib\TrueType\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use illuminate\Support\Str;

trait ReporteExcel 
{
    private static $RUTATEMPORAL = "app/public/";

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
            
            $cliente = Cliente::find($data->IdEmpresa, ['Descuento']);

            $queryPrecio = PrecioPorCodigo::where('Cod', $data->facturaResumen->Cod)->whereNot('Id', 0)->first();
            $precio = $cliente->Descuento != 0 ? $queryPrecio->Precio * (1-($cliente->Descuento/100)) : ($queryPrecio->Precio ?? '0.00');

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
            $sheet->setCellValue('H'.$fila, 'REMITO NRO: '.$data->prestacion->NroCEE.' | Mapa: '.$data->prestacion->IdMapa.' | Empresa: '.$data->empresa->RazonSocial);
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
        $name = "finnegans_".Str::random(10).'.xlsx';
        return $this->generarArchivo($spreadsheet, $name); 
    }

    // public function listadoPaciente($pacientes)
    // {
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

    //     $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'M', 'N'];

    //     foreach($columnas as $columna){
    //         $sheet->getColumnDimension($columna)->setAutoSize(true);
    //     }

    //     $sheet->setCellValue('A1', 'Numero');
    //     $sheet->setCellValue('B1', 'Apellido');
    //     $sheet->setCellValue('C1', 'Nombre');
    //     $sheet->setCellValue('D1', 'CUIL/CUIT');
    //     $sheet->setCellValue('E1', 'Documento');
    //     $sheet->setCellValue('F1', 'Nacionalidad');
    //     $sheet->setCellValue('G1', 'Fecha de Nacimiento');
    //     $sheet->setCellValue('H1', 'Direccion');
    //     $sheet->setCellValue('I1', 'Localidad');
    //     $sheet->setCellValue('J1', 'Provincia');
    //     $sheet->setCellValue('K1', 'Email');
    //     $sheet->setCellValue('M1', 'Antecedentes');
    //     $sheet->setCellValue('N1', 'Observaciones');

    //     $fila = 2;
    //     foreach($pacientes as $paciente){
    //         $sheet->setCellValue('A'.$fila, $paciente->Id);
    //         $sheet->setCellValue('B'.$fila, $paciente->Apellido);
    //         $sheet->setCellValue('C'.$fila, $paciente->Nombre);
    //         $sheet->setCellValue('D'.$fila, $paciente->Identificacion);
    //         $sheet->setCellValue('E'.$fila, $paciente->Documento);
    //         $sheet->setCellValue('F'.$fila, $paciente->Nacionalidad);
    //         $sheet->setCellValue('G'.$fila, $paciente->FechaNacimiento);
    //         $sheet->setCellValue('H'.$fila, $paciente->Direccion);
    //         $sheet->setCellValue('I'.$fila, $paciente->localidad->Nombre);
    //         $sheet->setCellValue('J'.$fila, $paciente->Provincia);
    //         $sheet->setCellValue('K'.$fila, $paciente->EMail);
    //         $sheet->setCellValue('H'.$fila, $paciente->Antecedentes);
    //         $sheet->setCellValue('H'.$fila, $paciente->Observaciones);
    //         $fila++;
    //     }

    //     $name = 'pacientes_'.Str::random(6).'.xlsx';
    //     return $this->generarArchivo($spreadsheet, $name);
    // }

    // public function listadoCliente($clientes)
    // {
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

    //     $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];

    //     foreach($columnas as $columna){
    //         $sheet->getColumnDimension($columna)->setAutoSize(true);
    //     }

    //     $sheet->setCellValue('A1', 'Numero');
    //     $sheet->setCellValue('B1', 'Razón Social');
    //     $sheet->setCellValue('C1', 'Identificación');
    //     $sheet->setCellValue('D1', 'Condición IVA');
    //     $sheet->setCellValue('E1', 'Para Empresa');
    //     $sheet->setCellValue('F1', 'Dirección');
    //     $sheet->setCellValue('G1', 'Provincia');
    //     $sheet->setCellValue('H1', 'Localidad');
    //     $sheet->setCellValue('I1', 'CódigoPostal');

    //     $fila = 2;
    //     foreach($clientes as $cliente){
    //         $sheet->setCellValue('A'.$fila, $cliente->Id);
    //         $sheet->setCellValue('B'.$fila, $cliente->RazonSocial);
    //         $sheet->setCellValue('C'.$fila, $cliente->Identificacion);
    //         $sheet->setCellValue('D'.$fila, $cliente->CondicionIva);
    //         $sheet->setCellValue('E'.$fila, $cliente->ParaEmpresa);
    //         $sheet->setCellValue('F'.$fila, $cliente->Direccion);
    //         $sheet->setCellValue('G'.$fila, $cliente->Provincia);
    //         $sheet->setCellValue('H'.$fila, $cliente->localidad->Nombre);
    //         $sheet->setCellValue('I'.$fila, $cliente->localidad->CP);
    //         $fila++;
    //     }

    //     $name = 'clientes'.Str::random(6).'.xlsx';
    //     return $this->generarArchivo($spreadsheet, $name);

    // }

    // public function listadoMapa($ids)
    // {
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

    //     $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'];

    //     foreach($columnas as $columna){
    //         $sheet->getColumnDimension($columna)->setAutoSize(true);
    //     }

    //     $sheet->setCellValue('A1', 'Id');
    //     $sheet->setCellValue('B1', 'Nro');
    //     $sheet->setCellValue('C1', 'Art');
    //     $sheet->setCellValue('D1', 'Empresa');
    //     $sheet->setCellValue('E1', 'Fecha Corte');
    //     $sheet->setCellValue('F1', 'Fecha Entrega');
    //     $sheet->setCellValue('G1', 'Inactivo');
    //     $sheet->setCellValue('H1', 'Nro de Remito');
    //     $sheet->setCellValue('I1', 'eEnviado');
    //     $sheet->setCellValue('J1', 'Cerrado');
    //     $sheet->setCellValue('K1', 'Entregado');
    //     $sheet->setCellValue('L1', 'Finalizado');
    //     $sheet->setCellValue('M1', 'Apellido y Nombre');
    //     $sheet->setCellValue('N1', 'Observación');

    //     $mapas = $this->queryMapa($ids);

    //     $fila = 2;
    //     foreach($mapas as $mapa){
    //         $sheet->setCellValue('A'.$fila, $mapa->Id);
    //         $sheet->setCellValue('B'.$fila, $mapa->Nro);
    //         $sheet->setCellValue('C'.$fila, $mapa->Art ?? '');
    //         $sheet->setCellValue('D'.$fila, $mapa->Empresa ?? '');
    //         $sheet->setCellValue('E'.$fila, $mapa->Fecha);
    //         $sheet->setCellValue('F'.$fila, $mapa->FechaE);
    //         $sheet->setCellValue('G'.$fila, $mapa->Inactivo === 0 ? "No" : "Si");
    //         $sheet->setCellValue('H'.$fila, $mapa->NroCEE);
    //         $sheet->setCellValue('I'.$fila, in_array($mapa->eEnviado, [0,'',null]) ? "No" : "Si");
    //         $sheet->setCellValue('J'.$fila, in_array($mapa->Cerrado, [0,'',null]) ? "No" : "Si");
    //         $sheet->setCellValue('K'.$fila, in_array($mapa->Entregado, [0,'',null]) ? "No" : "Si");
    //         $sheet->setCellValue('L'.$fila, in_array($mapa->Finalizado, [0,'',null]) ? "No" : "Si");
    //         $sheet->setCellValue('M'.$fila, $mapa->NombreCompleto ?? '-');
    //         $sheet->setCellValue('N'.$fila, $mapa->Obs);
    //         $fila++;
    //     }

    //     $name = 'mapas'.Str::random(6).'.xlsx';
    //     return $this->generarArchivo($spreadsheet, $name);

    // }

    // public function listadoEspecialidad($especialidades)
    // {
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

    //     $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

    //     foreach($columnas as $columna){
    //         $sheet->getColumnDimension($columna)->setAutoSize(true);
    //     }

    //     $sheet->setCellValue('A1', 'Id');
    //     $sheet->setCellValue('B1', 'Proveedor');
    //     $sheet->setCellValue('C1', 'Ubicacion');
    //     $sheet->setCellValue('D1', 'Telefono');
    //     $sheet->setCellValue('E1', 'Adjunto');
    //     $sheet->setCellValue('F1', 'Examen');
    //     $sheet->setCellValue('G1', 'Informe');

    //     $fila = 2;
    //     foreach($especialidades as $especialidad){
    //         $sheet->setCellValue('A'.$fila, $especialidad->IdEspecialidad ?? '-');
    //         $sheet->setCellValue('B'.$fila, $especialidad->Nombre ?? '-');
    //         $sheet->setCellValue('C'.$fila, $especialidad->Ubicacion === 0 ? 'Interno':($especialidad->Ubicacion === 1 ? 'Externo' : '-'));
    //         $sheet->setCellValue('D'.$fila, $especialidad->Telefono ?? '-');
    //         $sheet->setCellValue('E'.$fila, $especialidad->Adjunto === 0 ? 'Simple' : ($especialidad->Adjunto === 1 ? 'Multiple' : '-'));
    //         $sheet->setCellValue('F'.$fila, $especialidad->Examen === 0 ? 'Simple' : ($especialidad->Examen === 1 ? 'Multiple' : '-'));
    //         $sheet->setCellValue('G'.$fila, $especialidad->Informe === 0 ? 'Simple' : ($especialidad->Informe === 1 ? 'Multiple' : '-'));
    //         $fila++;
    //     }

    //     $name = 'especialidades'.Str::random(6).'.xlsx';
    //     return $this->generarArchivo($spreadsheet, $name);
    // }

    public function listadoExamen($examenes)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S'];

        foreach($columnas as $columna){
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        $sheet->setCellValue('A1', 'Estudio');
        $sheet->setCellValue('B1', 'Examen');
        $sheet->setCellValue('C1', 'Alias PDF');
        $sheet->setCellValue('D1', 'Descripción');
        $sheet->setCellValue('E1', 'Código del Exámen');
        $sheet->setCellValue('F1', 'Código Efector');
        $sheet->setCellValue('G1', 'Día de Vencimiento');
        $sheet->setCellValue('H1', 'Especialidad Efector');
        $sheet->setCellValue('I1', 'Especialidad Informador');
        $sheet->setCellValue('J1', 'Inactivo');
        $sheet->setCellValue('K1', 'Prioridad de Impresión');
        $sheet->setCellValue('L1', 'Informe');
        $sheet->setCellValue('M1', 'Cerrado');
        $sheet->setCellValue('N1', 'Físico');
        $sheet->setCellValue('O1', 'Adjunto');
        $sheet->setCellValue('P1', 'Ausente');
        $sheet->setCellValue('Q1', 'Devolución');
        $sheet->setCellValue('R1', 'Evaluador Exclusivo');
        $sheet->setCellValue('S1', 'Exportar Anexo');

        $fila = 2;
        foreach($examenes as $examen){
            $sheet->setCellValue('A'.$fila, $examen->estudios->Nombre ?? '-');
            $sheet->setCellValue('B'.$fila, $examen->Nombre ?? '-');
            $sheet->setCellValue('C'.$fila, $examen->aliasexamen ?? '-');
            $sheet->setCellValue('D'.$fila, $examen->Descripcion ?? '-');
            $sheet->setCellValue('E'.$fila, $examen->Cod ?? '-');
            $sheet->setCellValue('F'.$fila, $examen->Cod2 ?? '-');
            $sheet->setCellValue('G'.$fila, $examen->DiasVencimiento ?? '-');
            $sheet->setCellValue('H'.$fila, $examen->proveedor1->Nombre ?? '-');
            $sheet->setCellValue('I'.$fila, $examen->proveedor2->Nombre ?? '-');
            $sheet->setCellValue('J'.$fila, $examen->Inactivo ?? '-');
            $sheet->setCellValue('K'.$fila, $examen->PI ?? '-');
            $sheet->setCellValue('L'.$fila, $examen->Informe ?? '-');
            $sheet->setCellValue('M'.$fila, $examen->Cerrado ?? '-');
            $sheet->setCellValue('N'.$fila, $examen->NoImprime ?? '-');
            $sheet->setCellValue('O'.$fila, $examen->Adjunto ?? '-');
            $sheet->setCellValue('P'.$fila, $examen->Ausente ?? '-');
            $sheet->setCellValue('Q'.$fila, $examen->Devol ?? '-');
            $sheet->setCellValue('R'.$fila, $examen->Evaluador ?? '-');
            $sheet->setCellValue('S'.$fila, $examen->EvalCopia ?? '-');
            $fila++;
        }

        $name = 'mapas'.Str::random(6).'.xlsx';
        return $this->generarArchivo($spreadsheet, $name);
    }

    // public function resumenPrestacion($prestacion)
    // {
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

    //     $styleTitulos = [
    //         'fill' => [
    //             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
    //             'startColor' => ['rgb' => 'EEEEEE'], // Color de fondo (amarillo en este caso)
    //         ],
    //         'borders' => [
    //             'allBorders' => [
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, // Bordes finos
    //                 'color' => ['rgb' => '000000'], // Color del borde (negro en este caso)
    //             ],
    //         ],
    //         'alignment' => [
    //             'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Alineación centrada
    //             'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, // Alineación centrada vertical
    //         ]
    //     ];

    //     $styleBordes = [
    //         'borders' => [
    //             'allBorders' => [
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, // Bordes finos
    //                 'color' => ['rgb' => '000000'], // Color del borde (negro en este caso)
    //             ],
    //         ],
    //         'alignment' => [
    //             'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Alineación centrada
    //             'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, // Alineación centrada vertical
    //         ]
    //     ];

    //     $nombreCompleto = $prestacion->paciente->Apellido.' '.$prestacion->paciente->Nombre;

    //     $factura = ExamenCuentaIt::join('pagosacuenta', 'pagosacuenta_it.IdPago', '=', 'pagosacuenta.Id')
    //         ->select(
    //             'pagosacuenta.Tipo as Tipo',
    //             'pagosacuenta.Suc as Suc',
    //             'pagosacuenta.Nro as Nro'
    //         )
    //         ->where('pagosacuenta_it.IdPrestacion', $prestacion->Id)->first();

    //     $telefono = Telefono::join('pacientes', 'telefonos.IdEntidad', '=', 'pacientes.Id')->where('telefonos.IdEntidad', $prestacion->paciente->Id)->where('telefonos.TipoEntidad', 'i')->first();
        
    //     $examenes = ItemPrestacion::with(['examenes', 'examenes.proveedor1', 'profesionales1', 'profesionales2'])->where('itemsprestaciones.IdPrestacion', $prestacion->Id)->get();

    //     $auditorias = Auditor::with('auditarAccion')->where('IdRegistro', $prestacion->Id)->orderBy('Id', 'Asc')->get();

    //     $nroFactura = $factura !== null 
    //         ? 'EXAMEN A CUENTA NRO '.$factura->Tipo ?? 'X'.str_pad($factura->Suc, 4, '0', STR_PAD_LEFT) ?? '0000'.'-'.str_pad($factura->Nro, 8, '0', STR_PAD_LEFT) ?? '00000000'
    //         : '';

    //     $telefonoPaciente = $telefono !== null 
    //         ? '('.$telefono->CodigoArea ?? '000'.')'.$telefono->NumeroTelefono ?? '0000000'
    //         : '(000)000000';

    //     $comentariosPrivados = PrestacionObsFase::join('prestaciones', 'prestaciones_obsfases.IdEntidad', '=', 'prestaciones.Id')
    //             ->join('mapas', 'prestaciones.IdMapa', '=', 'mapas.Id')
    //             ->join('users', 'prestaciones_obsfases.IdUsuario', '=', 'users.name')
    //             ->select('prestaciones_obsfases.*', 'prestaciones_obsfases.Rol as nombre_perfil')
    //             ->where('prestaciones.Id', $prestacion->Id)
    //             ->orderBy('prestaciones_obsfases.Id', 'DESC')
    //             ->get();

    //     $fechaVencimiento = $this->formatearFecha($prestacion->FechaVto);
    //     $fechaCierre =  $this->formatearFecha($prestacion->FechaCierre);
    //     $fechaFinalizado = $this->formatearFecha($prestacion->FechaFinalizado);
    //     $fechaEntrega =  $this->formatearFecha($prestacion->FechaEntrega);
    //     $fechaAnulado = $this->formatearFecha($prestacion->FechaAnul);
    //     $fechaIngreso =  $this->formatearFecha($prestacion->paciente->fichaLaboral->first()->FechaIngreso);
    //     $fechaEgreso = $this->formatearFecha($prestacion->paciente->fichaLaboral->first()->FechaEgreso);

    //     $sheet->setCellValue('A1', 'Prestación: '.$prestacion->Id)->getStyle('A1')->getFont()->setBold(true);
    //     $sheet->setCellValue('A2', 'Alta: '.Carbon::parse($prestacion->Fecha)->format('d/m/Y') ?? '');
    //     $sheet->setCellValue('B2', 'Paciente: '.$prestacion->paciente->Id.' '.$nombreCompleto);
    //     $sheet->setCellValue('A3', 'Vencimiento: '.$fechaVencimiento);
    //     $sheet->setCellValue('B3', 'Empresa: '.$prestacion->empresa->RazonSocial ?? '');
    //     $sheet->setCellValue('A4', 'Tipo: '.$prestacion->TipoPrestacion ?? '');
    //     $sheet->setCellValue('B4', 'ART: '. $prestacion->art->RazonSocial ?? '');

    //     $sheet->setCellValue('A6', 'Estado')->getStyle('A6')->getFont()->setBold(true);
    //     $sheet->setCellValue('A7', 'Cerrado: '.$fechaCierre);
    //     $sheet->setCellValue('A8', 'Finalizado: '.$fechaFinalizado);
    //     $sheet->setCellValue('B7', 'Nro Constancia'. $prestacion->NroCEE ?? '')->getStyle('B7')->getFont()->setBold(true);
    //     $sheet->setCellValue('A9', 'Entregado: '. $fechaEntrega);
    //     $sheet->setCellValue('A10', 'Facturado: '.$nroFactura);
    //     $sheet->setCellValue('A11', 'Anulado: '.$fechaAnulado);

    //     $sheet->setCellValue('A13', 'Resultados')->getStyle('A13')->getFont()->setBold(true);
    //     $sheet->setCellValue('A14', 'Evaluación: '.substr($prestacion->Evaluacion, 2) ?? '');
    //     $sheet->setCellValue('A15', 'Calificación: '.substr($prestacion->Calificacion, 2) ?? '');
    //     $sheet->setCellValue('A16', 'Observación: '.$prestacion->Observaciones ?? '');
    //     $sheet->setCellValue('A18', 'Comentarios Examenes');
    //     $sheet->setCellValue('A19', 'Comentarios: '.$prestacion->ObsExamenes);

    //     $sheet->setCellValue('A21', 'Informe de Placas')->getStyle('A21')->getFont()->setBold(true);
    //     $sheet->setCellValue('A22', 'Informante: ');
    //     $sheet->setCellValue('A23', 'Informe: ');

    //     $sheet->setCellValue('A25', 'Datos del Paciente')->getStyle('A25')->getFont()->setBold(true);
    //     $sheet->setCellValue('A26', $prestacion->paciente->TipoDocumento.': '.$prestacion->paciente->Documento.' - Edad: '.Carbon::parse($prestacion->paciente->FechaNacimiento)->age.' - '.$prestacion->paciente->EstadoCivil ?? '');
    //     $sheet->setCellValue('A27', 'Dirección: '.$prestacion->paciente->Direccion ?? '');
    //     $sheet->setCellValue('A28', 'Tareas: '.$prestacion->paciente->fichaLaboral->first()->Tareas.' - Ultima Empresa: '.$prestacion->paciente->fichaLaboral->first()->TareasTareasEmpAnterior ?? ''.'C.Costos: '.$prestacion->paciente->fichaLaboral->first()->CCosto ?? '');
    //     $sheet->setCellValue('A29', 'Puesto: '.$prestacion->paciente->fichaLaboral->first()->Puesto ?? ''.' - Sector: '.$prestacion->paciente->fichaLaboral->first()->Sector ?? ''.' - Jornada: '.$prestacion->paciente->fichaLaboral->first()->Jornada ?? ''.' - F. Ingreso: '.$fechaIngreso.' - F. Egreso: '.$fechaEgreso);
    //     $sheet->setCellValue('A30', 'Tel: '.$telefonoPaciente);

    //     $sheet->setCellValue('A32', 'Examenes')->getStyle('A32')->getFont()->setBold(true);

    //     $sheet->setCellValue('A33', 'Examen')->getStyle('A33')->applyFromArray($styleTitulos)->getFont()->setBold(true);
    //     $sheet->setCellValue('B33', 'Especialidad')->getStyle('B33')->applyFromArray($styleTitulos)->getFont()->setBold(true);
    //     $sheet->setCellValue('C33', 'Efector')->getStyle('C33')->applyFromArray($styleTitulos)->getFont()->setBold(true);
    //     $sheet->setCellValue('D33', 'Fecha y Hora Asignado')->getStyle('D33')->applyFromArray($styleTitulos)->getFont()->setBold(true);
    //     $sheet->setCellValue('E33', 'Informador')->getStyle('E33')->applyFromArray($styleTitulos)->getFont()->setBold(true);
    //     $sheet->setCellValue('F33', 'Fecha y Hora Asignado')->getStyle('F33')->applyFromArray($styleTitulos)->getFont()->setBold(true);
    //     $sheet->setCellValue('G33', 'Evaluador')->getStyle('G33')->applyFromArray($styleTitulos)->getFont()->setBold(true);
    //     $sheet->setCellValue('H33', 'Observaciones')->getStyle('H33')->applyFromArray($styleTitulos)->getFont()->setBold(true);
    //     $sheet->setCellValue('I33', 'Pagado')->getStyle('I33')->applyFromArray($styleTitulos)->getFont()->setBold(true);
    //     $sheet->setCellValue('J33', 'Facturado')->getStyle('J33')->applyFromArray($styleTitulos)->getFont()->setBold(true);
    //     $sheet->setCellValue('K33', 'Anulado')->getStyle('K33')->applyFromArray($styleTitulos)->getFont()->setBold(true);

    //     $fila = 34;

    //     $nuevaFila = $fila + count($examenes) + 3;

    //     foreach($examenes as $examen) {

    //         $nombreProfesionalEfector = $examen->profesionales1->RegHis === 1 
    //             ? $examen->profesionales1->Apellido ?? ''.' '.$examen->profesionales1->Nombre ?? ''
    //             : $examen->profesionales1->user->personal->Apellido ?? ''.' '.$examen->profesionales1->user->personal->Nombre ?? '';

    //         $nombreProfesionalInformador = $examen->profesionales2->RegHis === 1 
    //             ? $examen->profesionales2->Apellido ?? ''.' '.$examen->profesionales2->Nombre ?? ''
    //             : $examen->profesionales2->user->personal->Apellido ?? ''.' '.$examen->profesionales2->user->personal->Nombre ?? '';

    //         $nombreEvaluador = $examen->prestaciones->profesional->RegHis === 1
    //             ? $examen->prestaciones->profesional->Apellido ?? ''.' '.$examen->prestaciones->profesional->Nombre ?? ''
    //             : $examen->prestaciones->profesional->user->personal->Apellido ?? ''.' '.$examen->prestaciones->profesional->user->personal->Nombre ?? '';

    //         $sheet->setCellValue('A'.$fila, $examen->examenes->Nombre ?? '-')->getStyle('A'.$fila)->applyFromArray($styleBordes);
    //         $sheet->setCellValue('B'.$fila, $examen->examenes->proveedor1->Nombre ?? '-')->getStyle('B'.$fila)->applyFromArray($styleBordes);
    //         $sheet->setCellValue('C'.$fila, $nombreProfesionalEfector ?? '-')->getStyle('C'.$fila)->applyFromArray($styleBordes);
    //         $sheet->setCellValue('D'.$fila, $this->formatearFecha($examen->FechaAsignado).' '.$examen->HoraAsignado)->getStyle('D'.$fila)->applyFromArray($styleBordes);
    //         $sheet->setCellValue('E'.$fila, $nombreProfesionalInformador ?? '-')->getStyle('E'.$fila)->applyFromArray($styleBordes);
    //         $sheet->setCellValue('F'.$fila, $this->formatearFecha($examen->FechaAsignadoI).' '.$examen->HoraAsignadoI ?? '')->getStyle('F'.$fila)->applyFromArray($styleBordes);
    //         $sheet->setCellValue('G'.$fila, $nombreEvaluador ?? '-')->getStyle('G'.$fila)->applyFromArray($styleBordes);
    //         $sheet->setCellValue('H'.$fila, $examen->ObsExamen ?? '')->getStyle('H'.$fila)->applyFromArray($styleBordes);
    //         $sheet->setCellValue('I'.$fila, $this->formatearFecha($examen->FechaPagado))->getStyle('I'.$fila)->applyFromArray($styleBordes);
    //         $sheet->setCellValue('J'.$fila, $examen->Facturado === 1 ? 'SI' : 'NO')->getStyle('J'.$fila)->applyFromArray($styleBordes);
    //         $sheet->setCellValue('K'.$fila, $examen->Anulado === 1 ? 'SI' : 'NO')->getStyle('K'.$fila)->applyFromArray($styleBordes);
    //         $fila++;
    //     }

    //     $sheet->setCellValue('A'.$nuevaFila - 1, 'Auditoria de Cambios')->getStyle('A'.$nuevaFila - 1)->getFont()->setBold(true);

    //     $sheet->setCellValue('A'.$nuevaFila, 'Usuario')->getStyle('A'.$nuevaFila)->applyFromArray($styleTitulos)->getFont()->setBold(true);
    //     $sheet->setCellValue('B'.$nuevaFila, 'Acción')->getStyle('B'.$nuevaFila)->applyFromArray($styleTitulos)->getFont()->setBold(true);
    //     $sheet->setCellValue('C'.$nuevaFila, 'Fecha')->getStyle('C'.$nuevaFila)->applyFromArray($styleTitulos)->getFont()->setBold(true);

    //     $tablaFila = $nuevaFila + 1;

    //     foreach ($auditorias as $auditoria) {
    //         $sheet->setCellValue('A'.$tablaFila, $auditoria->IdUsuario ?? '-')->getStyle('A'.$tablaFila)->applyFromArray($styleBordes);
    //         $sheet->setCellValue('B'.$tablaFila, $auditoria->auditarAccion->Nombre ?? '-')->getStyle('B'.$tablaFila)->applyFromArray($styleBordes);
    //         $sheet->setCellValue('C'.$tablaFila, Carbon::parse($auditoria->Fecha)->format('d/m/Y h:i:s'))->getStyle('C'.$tablaFila)->applyFromArray($styleBordes);
    //         $tablaFila++; 
    //     }

    //     $comentFila = $tablaFila + count($auditorias) + 3;

    //     $sheet->setCellValue('A'.$comentFila - 1, 'Observaciones Privadas')->getStyle('A'.$comentFila - 1)->getFont()->setBold(true);

    //     $sheet->setCellValue('A'.$comentFila, 'Fecha')->getStyle('A'.$comentFila)->applyFromArray($styleTitulos)->getFont()->setBold(true);
    //     $sheet->setCellValue('B'.$comentFila, 'Usuario')->getStyle('B'.$comentFila)->applyFromArray($styleTitulos)->getFont()->setBold(true);
    //     $sheet->setCellValue('C'.$comentFila, 'Comentario')->getStyle('C'.$comentFila)->applyFromArray($styleTitulos)->getFont()->setBold(true);

    //     $tablaFila2 = $comentFila + 1;

    //     foreach ($comentariosPrivados as $comentario) {
    //         $sheet->setCellValue('A'.$tablaFila2, Carbon::parse($comentario->Fecha)->format('d/m/Y H:i:s') ?? '-')->getStyle('A'.$tablaFila2)->applyFromArray($styleBordes);
    //         $sheet->setCellValue('B'.$tablaFila2, $comentario->IdUsuario ?? '-')->getStyle('B'.$tablaFila2)->applyFromArray($styleBordes);
    //         $sheet->setCellValue('C'.$tablaFila2, $comentario->Comentario ?? '-')->getStyle('C'.$tablaFila2)->applyFromArray($styleBordes);
    //         $tablaFila2++;
    //     }


    //     $name = 'resumen'.Str::random(6).'.xlsx';
    //     return $this->generarArchivo($spreadsheet, $name);
    // }

    // public function SimplePrestacion($prestaciones)
    // {
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

    //     $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB'];

    //     foreach($columnas as $columna){
    //         $sheet->getColumnDimension($columna)->setAutoSize(true);
    //     }

    //     $sheet->setCellValue('A1', 'Fecha');
    //     $sheet->setCellValue('B1', 'Prestacion');
    //     $sheet->setCellValue('C1', 'Tipo');
    //     $sheet->setCellValue('D1', 'Paciente');
    //     $sheet->setCellValue('E1', 'DNI');
    //     $sheet->setCellValue('F1', 'Cliente');
    //     $sheet->setCellValue('G1', 'Empresa');
    //     $sheet->setCellValue('H1', 'ART');
    //     $sheet->setCellValue('I1', 'Cerrado');
    //     $sheet->setCellValue('J1', 'Finalizado');
    //     $sheet->setCellValue('K1', 'Entregado');
    //     $sheet->setCellValue('L1', 'eEnviado');
    //     $sheet->setCellValue('M1', 'Facturado');
    //     $sheet->setCellValue('N1', 'Factura');
    //     $sheet->setCellValue('O1', 'Forma de Pago');
    //     $sheet->setCellValue('P1', 'Vencimiento');
    //     $sheet->setCellValue('Q1', 'Evaluación');
    //     $sheet->setCellValue('R1', 'Calificación');
    //     $sheet->setCellValue('S1', 'Obs Resultado');
    //     $sheet->setCellValue('T1', 'Anulada');
    //     $sheet->setCellValue('U1', 'Obs Anulada');
    //     $sheet->setCellValue('V1', 'Nro CE');
    //     $sheet->setCellValue('W1', 'C.Costos');
    //     $sheet->setCellValue('X1', 'INC');
    //     $sheet->setCellValue('Y1', 'AUS');
    //     $sheet->setCellValue('Z1', 'FOR');
    //     $sheet->setCellValue('AA1', 'DEV');
    //     $sheet->setCellValue('AB1', 'Obs Estados');

    //     $fila = 2;
    //     foreach($prestaciones as $prestacion){
    //         $sheet->setCellValue('A'.$fila, $this->formatearFecha($prestacion->FechaAlta));
    //         $sheet->setCellValue('B'.$fila, $prestacion->Id ?? '');
    //         $sheet->setCellValue('C'.$fila, $prestacion->TipoPrestacion ?? '');
    //         $sheet->setCellValue('D'.$fila, $prestacion->paciente->Apellido." ".$prestacion->paciente->Nombre);
    //         $sheet->setCellValue('E'.$fila, $prestacion->paciente->Documento ?? '');
    //         $sheet->setCellValue('F'.$fila, $prestacion->empresa->RazonSocial ?? '');
    //         $sheet->setCellValue('G'.$fila, $prestacion->empresa->ParaEmpresa ?? '');
    //         $sheet->setCellValue('H'.$fila, $prestacion->art->RazonSocial ?? '');
    //         $sheet->setCellValue('I'.$fila, $prestacion->Cerrado === 1 ? 'SI' : 'NO');
    //         $sheet->setCellValue('J'.$fila, $prestacion->Finalizado === 1 ? 'SI' : 'NO');
    //         $sheet->setCellValue('K'.$fila, $prestacion->Entregado === 1 ? 'SI' : 'NO');
    //         $sheet->setCellValue('L'.$fila, $prestacion->eEnviado === 1 ? 'SI' : 'NO');
    //         $sheet->setCellValue('M'.$fila, $this->formatearFecha($prestacion->Facturado));
    //         $sheet->setCellValue('N'.$fila, $prestacion->NumeroFacturaVta ?? '0000');
    //         $sheet->setCellValue('O'.$fila, $this->formaPagoPrestacion($prestacion->Pago));
    //         $sheet->setCellValue('P'.$fila, $this->formatearFecha($prestacion->FechaVto));
    //         $sheet->setCellValue('Q'.$fila, substr($prestacion->Evaluacion, 2));
    //         $sheet->setCellValue('R'.$fila, substr($prestacion->Calificacion, 2));
    //         $sheet->setCellValue('S'.$fila, $prestacion->Observaciones ?? '');
    //         $sheet->setCellValue('T'.$fila, $prestacion->Anulado === 1 ? 'SI' : 'NO');
    //         $sheet->setCellValue('U'.$fila, $prestacion->ObsAnulado ?? '');
    //         $sheet->setCellValue('V'.$fila, $prestacion->NroCEE ?? '');
    //         $sheet->setCellValue('W'.$fila, $prestacion->paciente->fichaLaboral->CCosto ?? '');
    //         $sheet->setCellValue('X'.$fila, $prestacion->Incompleto === 1 ? 'SI' : 'NO');
    //         $sheet->setCellValue('Y'.$fila, $prestacion->Ausente === 1 ? 'SI' : 'NO');
    //         $sheet->setCellValue('Z'.$fila, $prestacion->Forma === 1 ? 'SI' : 'NO');
    //         $sheet->setCellValue('AA'.$fila, $prestacion->Devol === 1 ? 'SI' : 'NO');
    //         $sheet->setCellValue('AB'.$fila, $prestacion->prestacionComentario->Obs ?? '');
    //         $fila++;
            
    //     }

    //     $name = 'resultados_'.Str::random(6).'.xlsx';
    //     return $this->generarArchivo($spreadsheet, $name);
    // }

    // public function detalladaPrestacion($prestaciones)
    // {
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

    //     $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB'];

    //     foreach($columnas as $columna){
    //         $sheet->getColumnDimension($columna)->setAutoSize(true);
    //     }

    //     $sheet->setCellValue('A1', 'Fecha');
    //     $sheet->setCellValue('B1', 'Prestacion');
    //     $sheet->setCellValue('C1', 'Tipo');
    //     $sheet->setCellValue('D1', 'Paciente');
    //     $sheet->setCellValue('E1', 'DNI');
    //     $sheet->setCellValue('F1', 'Cliente');
    //     $sheet->setCellValue('G1', 'Empresa');
    //     $sheet->setCellValue('H1', 'ART');
    //     $sheet->setCellValue('I1', 'C.Costos');
    //     $sheet->setCellValue('J1', 'Nro de CE');
    //     $sheet->setCellValue('K1', 'Pres Anulada');
    //     $sheet->setCellValue('L1', 'Obs Anulada');
    //     $sheet->setCellValue('M1', 'Examen');
    //     $sheet->setCellValue('N1', 'Examen Anulado');
    //     $sheet->setCellValue('O1', 'INC');
    //     $sheet->setCellValue('P1', 'AUS');
    //     $sheet->setCellValue('Q1', 'FOR');
    //     $sheet->setCellValue('R1', 'DEV');
    //     $sheet->setCellValue('S1', 'Obs Estados');

    //     $fila = 2;
    //     foreach($prestaciones as $prestacion){
    //         $sheet->setCellValue('A'.$fila, $this->formatearFecha($prestacion->FechaAlta));
    //         $sheet->setCellValue('B'.$fila, $prestacion->Id ?? '');
    //         $sheet->setCellValue('C'.$fila, $prestacion->TipoPrestacion ?? '');
    //         $sheet->setCellValue('D'.$fila, $prestacion->Apellido." ".$prestacion->Nombre);
    //         $sheet->setCellValue('E'.$fila, $prestacion->DNI ?? '');
    //         $sheet->setCellValue('F'.$fila, $prestacion->EmpresaRazonSocial ?? '');
    //         $sheet->setCellValue('G'.$fila, $prestacion->EmpresaParaEmp ?? '');
    //         $sheet->setCellValue('H'.$fila, $prestacion->ArtRazonSocial ?? '');
    //         $sheet->setCellValue('I'.$fila, $prestacion->CCosto ?? '');
    //         $sheet->setCellValue('J'.$fila, $prestacion->NroCEE ?? '');
    //         $sheet->setCellValue('K'.$fila, $prestacion->Anulado === 1 ? 'SI' : 'NO');
    //         $sheet->setCellValue('L'.$fila, $prestacion->ObsAnulado ?? '');
    //         $sheet->setCellValue('M'.$fila, $prestacion->Examen ?? '');
    //         $sheet->setCellValue('N'.$fila, $prestacion->ObsExamen ?? '');
    //         $sheet->setCellValue('O'.$fila, $prestacion->Incompleto ?? '');
    //         $sheet->setCellValue('P'.$fila, $prestacion->Ausente ?? '');
    //         $sheet->setCellValue('Q'.$fila, $prestacion->Forma ?? '');
    //         $sheet->setCellValue('R'.$fila, $prestacion->Devol ?? '');
    //         $sheet->setCellValue('S'.$fila, $prestacion->ObsEstado ?? '');
    //         $fila++;
    //     }

    //     $name = 'resultados_'.Str::random(6).'.xlsx';
    //     return $this->generarArchivo($spreadsheet, $name);
    // }

    // public function remitoMapas(int $idMapa, int $nroRemito)
    // {
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

    //     $columnas = ['A', 'B', 'C', 'D', 'E'];

    //     foreach($columnas as $columna){
    //         $sheet->getColumnDimension($columna)->setAutoSize(true);
    //     }

    //     $mapa = Mapa::with(['prestacion', 'artMapa', 'empresaMapa'])->find($idMapa);

    //     $sheet->setCellValue('A1', 'REMITO DE ENTREGA DE ESTUDIOS');
    //     $sheet->setCellValue('A3', 'ART: '.$mapa->artMapa->RazonSocial ?? '');
    //     $sheet->setCellValue('A4', 'REMITO: '.$nroRemito ?? 0);
    //     $sheet->setCellValue('A5', 'EMPRESA: '.$mapa->empresaMapa->RazonSocial ?? '');
    //     $sheet->setCellValue('A6', 'MAPA: '.$mapa->Id ?? 0);

      
    //     $examenes = Prestacion::where('NroCEE', $nroRemito)->pluck('Id');
    //     $items = ItemPrestacion::with(['prestaciones', 'examenes', 'prestaciones.paciente'])->whereIn('IdPrestacion', $examenes)->get();

    //     $sheet->setCellValue('A8', 'Paciente');
    //     $sheet->setCellValue('B8', 'DNI');
    //     $sheet->setCellValue('C8', 'CUIL');
    //     $sheet->setCellValue('D8', 'Prestación');
    //     $sheet->setCellValue('E8', 'Examenes');

    //     $fila = 9;
    //     foreach($items as $item){

    //         $nombreCompleto = ($item->prestaciones->paciente->Apellido ?? '').' '.($item->prestaciones->paciente->Nombre ?? '');

    //         $sheet->setCellValue('A'.$fila, $nombreCompleto);
    //         $sheet->setCellValue('B'.$fila, $item->prestaciones->paciente->Documento ?? '');
    //         $sheet->setCellValue('B'.$fila, $item->prestaciones->paciente->Identificacion ?? '');
    //         $sheet->setCellValue('B'.$fila, $item->prestaciones->Id ?? '');
    //         $sheet->setCellValue('B'.$fila, $item->examenes->Nombre ?? '');
    //         $fila++;
    //     }

    //     $name = 'resultados_'.Str::random(6).'.xlsx';
    //     return $this->generarArchivo($spreadsheet, $name);

    // }

    private function generarArchivo($excel, $nombre)
    {
          // Guardar el archivo en la carpeta de almacenamiento
          $filePath = storage_path(self::$RUTATEMPORAL.$nombre);
 
          $writer = new Xlsx($excel);
          $writer->save($filePath);
          chmod($filePath, 0777);
 
          return response()->json(['filePath' => $filePath, 'msg' => 'Se ha generado correctamente el reporte ', 'estado' => 'success']);
    }

    // private function formatearFecha($fecha)
    // {
    //     return $fecha === '0000-00-00' ? '' : Carbon::parse($fecha)->format('d/m/Y');
    // }
    
    // private function formaPagoPrestacion(string $pago): string
    // {
    //     switch ($pago) {
    //         case "B":
    //             return 'Ctdo.';
    //         case "C":
    //             return  'CCorriente';
    //         case "P":
    //             return 'ExCuenta';
    //         default:
    //             return 'CCorriente';
    //     }
    // }

    // private function queryMapa(array $ids)
    // {
    //     return Mapa::join('clientes as empresa', 'mapas.IdEmpresa', '=', 'empresa.Id')
    //         ->join('clientes as art', 'mapas.IdART', '=', 'art.Id')
    //         ->leftJoin('prestaciones', 'mapas.Id', '=', 'prestaciones.IdMapa')
    //         ->leftJoin('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
    //         ->select(
    //             'mapas.Id as Id',
    //             'mapas.Nro as Nro',
    //             'art.RazonSocial as Art',
    //             'empresa.RazonSocial as Empresa',
    //             'mapas.Fecha',
    //             'mapas.FechaE',
    //             'mapas.Inactivo as Inactivo',
    //             'prestaciones.NroCEE as NroCEE',
    //             'prestaciones.eEnviado as eEnviado',
    //             'prestaciones.Cerrado as Cerrado',
    //             'prestaciones.Entregado as Entregado',
    //             'prestaciones.Finalizado as Finalizado',
    //             DB::raw("CONCAT(pacientes.Apellido,' ',pacientes.Nombre) as NombreCompleto"),
    //             'mapas.Obs as Obs'
    //         )->whereIn('mapas.Id', $ids)
    //         ->get();
    // }

}