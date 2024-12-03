<?php

namespace App\Traits;

use App\Models\Auditor;
use App\Models\Cliente;
use App\Models\ExamenCuentaIt;
use App\Models\FacturaDeVenta;
use App\Models\ItemPrestacion;
use App\Models\PrecioPorCodigo;
use App\Models\ReporteFinneg;
use App\Models\Telefono;
use Carbon\Carbon;
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
        $name = "finnegans_".Str::random(10).'.xlsx';
        return $this->generarArchivo($spreadsheet, $name); 
    }

    public function listadoPaciente($pacientes)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'M', 'N'];

        foreach($columnas as $columna){
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        $sheet->setCellValue('A1', 'Numero');
        $sheet->setCellValue('B1', 'Apellido');
        $sheet->setCellValue('C1', 'Nombre');
        $sheet->setCellValue('D1', 'CUIL/CUIT');
        $sheet->setCellValue('E1', 'Documento');
        $sheet->setCellValue('F1', 'Nacionalidad');
        $sheet->setCellValue('G1', 'Fecha de Nacimiento');
        $sheet->setCellValue('H1', 'Direccion');
        $sheet->setCellValue('I1', 'Localidad');
        $sheet->setCellValue('J1', 'Provincia');
        $sheet->setCellValue('K1', 'Email');
        $sheet->setCellValue('M1', 'Antecedentes');
        $sheet->setCellValue('N1', 'Observaciones');

        $fila = 2;
        foreach($pacientes as $paciente){
            $sheet->setCellValue('A'.$fila, $paciente->Id);
            $sheet->setCellValue('B'.$fila, $paciente->Apellido);
            $sheet->setCellValue('C'.$fila, $paciente->Nombre);
            $sheet->setCellValue('D'.$fila, $paciente->Identificacion);
            $sheet->setCellValue('E'.$fila, $paciente->Documento);
            $sheet->setCellValue('F'.$fila, $paciente->Nacionalidad);
            $sheet->setCellValue('G'.$fila, $paciente->FechaNacimiento);
            $sheet->setCellValue('H'.$fila, $paciente->Direccion);
            $sheet->setCellValue('I'.$fila, $paciente->localidad->Nombre);
            $sheet->setCellValue('J'.$fila, $paciente->Provincia);
            $sheet->setCellValue('K'.$fila, $paciente->EMail);
            $sheet->setCellValue('H'.$fila, $paciente->Antecedentes);
            $sheet->setCellValue('H'.$fila, $paciente->Observaciones);
            $fila++;
        }

        $name = 'pacientes_'.Str::random(6).'.xlsx';
        return $this->generarArchivo($spreadsheet, $name);
    }

    public function listadoCliente($clientes)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];

        foreach($columnas as $columna){
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        $sheet->setCellValue('A1', 'Numero');
        $sheet->setCellValue('B1', 'Razón Social');
        $sheet->setCellValue('C1', 'Identificación');
        $sheet->setCellValue('D1', 'Condición IVA');
        $sheet->setCellValue('E1', 'Para Empresa');
        $sheet->setCellValue('F1', 'Dirección');
        $sheet->setCellValue('G1', 'Provincia');
        $sheet->setCellValue('H1', 'Localidad');
        $sheet->setCellValue('I1', 'CódigoPostal');

        $fila = 2;
        foreach($clientes as $cliente){
            $sheet->setCellValue('A'.$fila, $cliente->Id);
            $sheet->setCellValue('B'.$fila, $cliente->RazonSocial);
            $sheet->setCellValue('C'.$fila, $cliente->Identificacion);
            $sheet->setCellValue('D'.$fila, $cliente->CondicionIva);
            $sheet->setCellValue('E'.$fila, $cliente->ParaEmpresa);
            $sheet->setCellValue('F'.$fila, $cliente->Direccion);
            $sheet->setCellValue('G'.$fila, $cliente->Provincia);
            $sheet->setCellValue('H'.$fila, $cliente->localidad->Nombre);
            $sheet->setCellValue('I'.$fila, $cliente->localidad->CP);
            $fila++;
        }

        $name = 'clientes'.Str::random(6).'.xlsx';
        return $this->generarArchivo($spreadsheet, $name);

    }

    public function listadoMapa($mapas)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'];

        foreach($columnas as $columna){
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        $sheet->setCellValue('A1', 'Id');
        $sheet->setCellValue('B1', 'Nro');
        $sheet->setCellValue('C1', 'Art');
        $sheet->setCellValue('D1', 'Empresa');
        $sheet->setCellValue('E1', 'Fecha Corte');
        $sheet->setCellValue('F1', 'Fecha Entrega');
        $sheet->setCellValue('G1', 'Inactivo');
        $sheet->setCellValue('H1', 'Nro de Remito');
        $sheet->setCellValue('I1', 'eEnviado');
        $sheet->setCellValue('J1', 'Cerrado');
        $sheet->setCellValue('K1', 'Entregado');
        $sheet->setCellValue('L1', 'Finalizado');
        $sheet->setCellValue('M1', 'Apellido y Nombre');
        $sheet->setCellValue('N1', 'Observación');

        $fila = 2;
        foreach($mapas as $mapa){
            $sheet->setCellValue('A'.$fila, $mapa->Id);
            $sheet->setCellValue('B'.$fila, $mapa->Nro);
            $sheet->setCellValue('C'.$fila, $mapa->Art);
            $sheet->setCellValue('D'.$fila, $mapa->Empresa);
            $sheet->setCellValue('E'.$fila, $mapa->Fecha);
            $sheet->setCellValue('F'.$fila, $mapa->FechaE);
            $sheet->setCellValue('G'.$fila, $mapa->Inactivo === 0 ? "No" : "Si");
            $sheet->setCellValue('H'.$fila, $mapa->NroCEE);
            $sheet->setCellValue('I'.$fila, $mapa->eEnviado);
            $sheet->setCellValue('J'.$fila, $mapa->Cerrado);
            $sheet->setCellValue('K'.$fila, $mapa->Entregado);
            $sheet->setCellValue('L'.$fila, $mapa->Finalizado);
            $sheet->setCellValue('M'.$fila, $mapa->Apellido.' '.$mapa->Nombre);
            $sheet->setCellValue('N'.$fila, $mapa->Obs);
            $fila++;
        }

        $name = 'mapas'.Str::random(6).'.xlsx';
        return $this->generarArchivo($spreadsheet, $name);

    }

    public function listadoEspecialidad($especialidades)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

        foreach($columnas as $columna){
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        $sheet->setCellValue('A1', 'Id');
        $sheet->setCellValue('B1', 'Proveedor');
        $sheet->setCellValue('C1', 'Ubicacion');
        $sheet->setCellValue('D1', 'Telefono');
        $sheet->setCellValue('E1', 'Adjunto');
        $sheet->setCellValue('F1', 'Examen');
        $sheet->setCellValue('G1', 'Informe');

        $fila = 2;
        foreach($especialidades as $especialidad){
            $sheet->setCellValue('A'.$fila, $especialidad->IdEspecialidad ?? '-');
            $sheet->setCellValue('B'.$fila, $especialidad->Nombre ?? '-');
            $sheet->setCellValue('C'.$fila, $especialidad->Ubicacion === 0 ? 'Interno':($especialidad->Ubicacion === 1 ? 'Externo' : '-'));
            $sheet->setCellValue('D'.$fila, $especialidad->Telefono ?? '-');
            $sheet->setCellValue('E'.$fila, $especialidad->Adjunto === 0 ? 'Simple' : ($especialidad->Adjunto === 1 ? 'Multiple' : '-'));
            $sheet->setCellValue('F'.$fila, $especialidad->Examen === 0 ? 'Simple' : ($especialidad->Examen === 1 ? 'Multiple' : '-'));
            $sheet->setCellValue('G'.$fila, $especialidad->Informe === 0 ? 'Simple' : ($especialidad->Informe === 1 ? 'Multiple' : '-'));
            $fila++;
        }

        $name = 'especialidades'.Str::random(6).'.xlsx';
        return $this->generarArchivo($spreadsheet, $name);
    }

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

    public function resumenPrestacion($prestacion)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $nombreCompleto = $prestacion->paciente->Apellido.' '.$prestacion->paciente->Nombre;

        $factura = ExamenCuentaIt::join('pagosacuenta', 'pagosacuenta_it.IdPago', '=', 'pagosacuenta.Id')
            ->select(
                'pagosacuenta.Tipo as Tipo',
                'pagosacuenta.Suc as Suc',
                'pagosacuenta.Nro as Nro'
            )
            ->where('pagosacuenta_it.IdPrestacion', $prestacion->Id)->first();

        $telefono = Telefono::join('pacientes', 'telefonos.IdEntidad', '=', 'pacientes.Id')->where('telefonos.IdEntidad', $prestacion->paciente->Id)->where('telefonos.TipoEntidad', 'i')->first();
        
        $examenes = ItemPrestacion::with(['examenes', 'proveedores', 'profesionales1', 'profesionales2'])->where('itemsprestaciones.IdPrestacion', $prestacion->Id)->get();

        $auditorias = Auditor::with('auditarAccion')->where('IdRegistro', $prestacion->Id)->orderBy('Id', 'Asc')->get();

        $nroFactura = $factura !== null 
            ? 'EXAMEN A CUENTA NRO '.$factura->Tipo ?? 'X'.str_pad($factura->Suc, 4, '0', STR_PAD_LEFT) ?? '0000'.'-'.str_pad($factura->Nro, 8, '0', STR_PAD_LEFT) ?? '00000000'
            : '';

        $telefonoPaciente = $telefono !== null 
            ? '('.$telefono->CodigoArea ?? '000'.')'.$telefono->NumeroTelefono ?? '0000000'
            : '(000)000000';

        $fechaVencimiento = $this->formatearFecha($prestacion->FechaVto);
        $fechaCierre =  $this->formatearFecha($prestacion->FechaCierre);
        $fechaFinalizado = $this->formatearFecha($prestacion->FechaFinalizado);
        $fechaEntrega =  $this->formatearFecha($prestacion->FechaEntrega);
        $fechaAnulado = $this->formatearFecha($prestacion->FechaAnul);
        $fechaIngreso =  $this->formatearFecha($prestacion->paciente->fichaLaboral->first()->FechaIngreso);
        $fechaEgreso = $this->formatearFecha($prestacion->paciente->fichaLaboral->first()->FechaEgreso);

        $sheet->setCellValue('A1', 'Prestación: '.$prestacion->Id)->getStyle('A19')->getFont()->setBold(true);
        $sheet->setCellValue('B1', 'Alta: '.Carbon::parse($prestacion->Fecha)->format('d/m/Y') ?? '');
        $sheet->setCellValue('B2', 'Paciente: '.$prestacion->paciente->Id.' '.$nombreCompleto);
        $sheet->setCellValue('C1', 'Vencimiento: '.$fechaVencimiento);
        $sheet->setCellValue('C2', 'Empresa: '.$prestacion->empresa->RazonSocial ?? '');
        $sheet->setCellValue('D1', 'Tipo: '.$prestacion->TipoPrestacion ?? '');
        $sheet->setCellValue('D2', 'ART: '. $prestacion->art->RazonSocial ?? '');

        $sheet->setCellValue('A4', 'Estado')->getStyle('A19')->getFont()->setBold(true);
        $sheet->setCellValue('A5', 'Cerrado: '.$fechaCierre);
        $sheet->setCellValue('A6', 'Finalizado: '.$fechaFinalizado);
        $sheet->setCellValue('B6', 'Nro Constancia')->getStyle('A19')->getFont()->setBold(true);
        $sheet->setCellValue('B7', 'Entrega: '. $prestacion->NroCEE ?? '');
        $sheet->setCellValue('A8', 'Entregado: '. $fechaEntrega);
        $sheet->setCellValue('A9', 'Facturado: '.$nroFactura);
        $sheet->setCellValue('A10', 'Anulado: '.$fechaAnulado);

        $sheet->setCellValue('A12', 'Resultados')->getStyle('A12')->getFont()->setBold(true);
        $sheet->setCellValue('A13', 'Evaluación: '.substr($prestacion->Evaluacion, 2) ?? '');
        $sheet->setCellValue('A14', 'Calificación: '.substr($prestacion->Calificacion, 2) ?? '');
        $sheet->setCellValue('A15', 'Observación: '.$prestacion->Observaciones ?? '');
        $sheet->setCellValue('A16', 'Comentarios Examenes');
        $sheet->setCellValue('A17', $prestacion->ObsExamenes);

        $sheet->setCellValue('A19', 'Datos del Paciente')->getStyle('A19')->getFont()->setBold(true);
        $sheet->setCellValue('A20', $prestacion->paciente->TipoDocumento.': '.$prestacion->paciente->Documento.' - Edad: '.Carbon::parse($prestacion->paciente->FechaNacimiento)->age.' - '.$prestacion->paciente->EstadoCivil ?? '');
        $sheet->setCellValue('A21', 'Dirección: '.$prestacion->paciente->Direccion ?? '');
        $sheet->setCellValue('A22', 'Tareas: '.$prestacion->paciente->fichaLaboral->first()->Tareas.' - Ultima Empresa: '.$prestacion->paciente->fichaLaboral->first()->TareasTareasEmpAnterior ?? ''.'C.Costos: '.$prestacion->paciente->fichaLaboral->first()->CCosto ?? '');
        $sheet->setCellValue('A23', 'Puesto: '.$prestacion->paciente->fichaLaboral->first()->Puesto ?? ''.' - Sector: '.$prestacion->paciente->fichaLaboral->first()->Sector ?? ''.' - Jornada: '.$prestacion->paciente->fichaLaboral->first()->Jornada ?? ''.' - F. Ingreso: '.$fechaIngreso.' - F. Egreso: '.$fechaEgreso);
        $sheet->setCellValue('A24', 'Tel: '.$telefonoPaciente);

        $sheet->setCellValue('A26', 'Examenes')->getStyle('A26')->getFont()->setBold(true);

        $sheet->setCellValue('A27', 'Examen');
        $sheet->setCellValue('B27', 'Proveedor');
        $sheet->setCellValue('C27', 'Observaciones');
        $sheet->setCellValue('D27', 'Asignado');
        $sheet->setCellValue('E27', 'Pagado');
        $sheet->setCellValue('F27', 'Facturado');
        $sheet->setCellValue('G27', 'Anulado');

        $fila = 28;

        $nuevaFila = $fila + count($examenes) + 5;

        foreach($examenes as $examen) {

            $nombreProfesional = $examen->profesionales1->RegHis === 1 
                ? $examen->profesionales1->Apellido ?? ''.' '.$examen->profesionales1->Nombre ?? ''
                : $examen->profesionales1->user->personal->Apellido ?? ''.' '.$examen->profesionales1->user->personal->Nombre ?? '';

            $sheet->setCellValue('A'.$fila, $examen->examenes->Nombre ?? '-');
            $sheet->setCellValue('B'.$fila, $examen->proveedores->Nombre ?? '-');
            $sheet->setCellValue('C'.$fila, $examen->examenes->Nombre ?? '-');
            $sheet->setCellValue('D'.$fila, '-');
            $sheet->setCellValue('E'.$fila, $this->formatearFecha($examen->FechaAsignado).' '.$nombreProfesional);
            $sheet->setCellValue('F'.$fila, $examen->Facturado === 1 ? 'SI' : 'NO');
            $sheet->setCellValue('G'.$fila, $examen->Anulado === 1 ? 'SI' : 'NO');
            $fila++;
        }

        $sheet->setCellValue('A'.$nuevaFila - 2, 'Auditoria de Cambios')->getStyle('A'.$nuevaFila - 2)->getFont()->setBold(true);

        $sheet->setCellValue('A'.$nuevaFila, 'Usuario');
        $sheet->setCellValue('B'.$nuevaFila, 'Acción');
        $sheet->setCellValue('C'.$nuevaFila, 'Fecha');

        $tablaFila = $nuevaFila + 1;

        foreach ($auditorias as $auditoria) {
            $sheet->setCellValue('A'.$tablaFila, $auditoria->IdUsuario ?? '-');
            $sheet->setCellValue('B'.$tablaFila, $auditoria->auditarAccion->Nombre ?? '-');
            $sheet->setCellValue('C'.$tablaFila, Carbon::parse($auditoria->Fecha)->format('d/m/Y h:i:s'));
            $tablaFila++; 
        }

        $name = 'mapas'.Str::random(6).'.xlsx';
        return $this->generarArchivo($spreadsheet, $name);
    }

    private function generarArchivo($excel, $nombre)
    {
          // Guardar el archivo en la carpeta de almacenamiento
          $filePath = storage_path(self::$RUTATEMPORAL.$nombre);
 
          $writer = new Xlsx($excel);
          $writer->save($filePath);
          chmod($filePath, 0777);
 
          return response()->json(['filePath' => $filePath, 'msg' => 'Se ha generado correctamente el reporte ', 'estado' => 'success']);
    }

    private function formatearFecha($fecha)
    {
        return $fecha === '0000-00-00' ? '' : Carbon::parse($fecha)->format('d/m/Y');
    }
    

}