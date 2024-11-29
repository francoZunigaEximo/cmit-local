<?php

namespace App\Http\Controllers;

use App\Models\ExamenCuenta;
use App\Models\ExamenCuentaIt;
use App\Models\Relpaqest;
use App\Models\Relpaqfact;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Traits\ObserverExamenesCuenta;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Traits\CheckPermission;

use App\Services\Reportes\ReporteService;
use App\Services\Reportes\Titulos\Basico;
use App\Services\Reportes\Titulos\Empresa;
use App\Services\Reportes\Cuerpos\ExamenCuenta as ExCuenta;

class ExamenesCuentaController extends Controller
{
    use ObserverExamenesCuenta, CheckPermission;

    protected $reporteService;

    public function __construct(ReporteService $reporteService)
    {
        $this->reporteService = $reporteService;
    }

    public function index(Request $request)
    {
        if(!$this->hasPermission("examenCta_show")) {
            abort(403);
        }

        if ($request->ajax())
        {
            $query = $this->queryBasico();

            $result = $query->groupBy('pagosacuenta.Id', 'pagosacuenta.Tipo', 'pagosacuenta.Suc', 'pagosacuenta.Nro', 'pagosacuenta.Pagado')->limit(7)->orderBy('pagosacuenta.Id', 'DESC');

            return Datatables::of($result)->make(true);
        }

        return view('layouts.examenesCuenta.index');
    }

    public function search(Request $request)
    {
        if(!$this->hasPermission("examenCta_show")) {
            abort(403);
        }

        if ($request->ajax())
        {
            $query = $this->queryBasico();
            
            $FactDesde = explode('-', $request->rangoDesde);
            $FactHasta = empty($request->rangoHasta) ? $FactDesde : explode('-', $request->rangoHasta);

            $query->when(!empty($request->rangoDesde) || !empty($request->rangoHasta), function ($query) use ($FactDesde, $FactHasta) {

                $query->whereBetween('pagosacuenta.Tipo', [$FactDesde[0], $FactHasta[0]])
                    ->whereBetween('pagosacuenta.Suc', [intval($FactDesde[1], 10), intval($FactHasta[1], 10)])
                    ->whereBetween('pagosacuenta.Nro', [intval($FactDesde[2], 10), intval($FactHasta[2], 10)])
                    ->orderBy('pagosacuenta.Tipo', 'ASC')
                    ->orderBy('pagosacuenta.Suc', 'ASC')
                    ->orderBy('pagosacuenta.Nro', 'ASC');
            });

            $query->when(!empty($request->fechaDesde) && !empty($request->fechaHasta), function ($query) use ($request) {
                $query->whereBetween('pagosacuenta.Fecha', [$request->fechaDesde, $request->fechaHasta]);
            });

            $query->when(!empty($request->empresa), function ($query) use ($request) {
                $query->where('clientes.Id', $request->empresa);
            });

            $query->when(!empty($request->examen), function ($query) use ($request) {
                $query->where('examenes.Id', $request->examen);
            });

            $query->when(!empty($request->paciente), function ($query) use ($request) {
                $query->where('pacientes.Id', $request->paciente);
            });

            $query->when(empty($request->estado), function ($query) {
                $query->where('pagosacuenta.Pagado', 0);
            });

            $query->when(!empty($request->estado) && $request->estado === 'pago', function ($query) {
                $query->where('pagosacuenta.Pagado', 1)
                      ->whereNot('pagosacuenta.Pagado', 0);
            });

            $query->when(!empty($request->estado) && $request->estado === 'todos', function ($query) {
                $query->whereIn('pagosacuenta.Pagado', [0,1]);
            });

            $result = $query->groupBy('pagosacuenta.Id', 'pagosacuenta.Tipo', 'pagosacuenta.Suc', 'pagosacuenta.Nro', 'pagosacuenta.Pagado');

            return Datatables::of($result)->make(true);
        }

        return view('layouts.examenesCuenta.index');
    }

    public function saldo(Request $request)
    {
        if(!$this->hasPermission("examenCta_show")) {
            abort(403);
        }

        if ($request->ajax())
        {
            $query = ExamenCuenta::join('pagosacuenta_it', 'pagosacuenta.Id', '=', 'pagosacuenta_it.IdPago')
            ->join('clientes', 'pagosacuenta.IdEmpresa', '=', 'clientes.Id')
            ->join('examenes', 'pagosacuenta_it.IdExamen', '=', 'examenes.Id')
            ->join('prestaciones', 'pagosacuenta_it.IdPrestacion', '=', 'prestaciones.Id')
            ->select(
                'clientes.RazonSocial as Empresa',
                'examenes.Nombre as Examen',
                'pagosacuenta.IdEmpresa as IdEmpresa'
            );

            $query->selectRaw('COUNT(CASE WHEN pagosacuenta_it.IdPrestacion = 0 THEN 1 END) AS contadorSaldos');

            $query->when(!empty($request->examen) && empty($request->empresa), function ($query) use ($request) {
                $query->where('examenes.Id', $request->examen)
                        ->groupBy(['examenes.Id', 'clientes.Id']);
            });

            $query->when(!empty($request->empresa) && empty($request->examen), function ($query) use ($request) {
                $query->where('clientes.Id', $request->empresa)
                        ->groupBy('examenes.Nombre');
            });

            $query->when(empty($request->empresa) && empty($request->examen), function ($query) {
                $query->groupBy(['clientes.RazonSocial', 'clientes.ParaEmpresa', 'clientes.Identificacion', 'examenes.Nombre']);
            });

            $query->when(!empty($request->empresa) && !empty($request->examen), function ($query) use ($request){
                $query->where('examenes.Id', $request->examen)
                    ->where('clientes.Id', $request->empresa)
                    ->groupBy(['clientes.Id', 'clientes.RazonSocial', 'clientes.ParaEmpresa', 'clientes.Identificacion', 'examenes.Nombre']);
            });

            $result = $query->havingRaw('contadorSaldos > 0')
                ->whereNot('pagosacuenta_it.Obs', 'provisorio')
                ->orderBy('clientes.RazonSocial')
                ->orderBy('examenes.Nombre');

            return Datatables::of($result)->make(true);
        }

        return view('layouts.examenesCuenta.index');
    }

    public function cambiarPago(Request $request)
    {
        if(!$this->hasPermission("examenCta_show")) {
            abort(403);
        }

        $estados = $request->Id;
        $resultado = [];

        if (!is_array($estados)) {
            $estados = [$estados];
        }

        foreach($estados as $estado) {

            $item = ExamenCuenta::find($estado);

            if ($item) {

                $item->Pagado = $item->Pagado === 0 ? 1 : 0;
                $item->FechaP = $item->FechaP === '0000-00-00' ? now()->format('Y-m-d') : '0000-00-00';
                $item->save();
                $resultado = ['message' => 'Se ha realizado la actualización correctamente', 'estado' => 'success'];
                
            }
        }

        return response()->json($resultado);
    }

    public function create()
    {
        if(!$this->hasPermission("examenCta_add")) {
            abort(403);
        }

        return view('layouts.examenesCuenta.create');
    }

    public function edit(ExamenCuenta $examenesCuentum)
    {
        if(!$this->hasPermission("examenCta_edit")) {
            abort(403);
        }

        return view('layouts.examenesCuenta.edit', compact(['examenesCuentum']));
    }

    public function save(Request $request)
    {
        if(!$this->hasPermission("examenCta_add")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $nuevoId = ExamenCuenta::max('Id') + 1;

        ExamenCuenta::create([
            'Id' => $nuevoId,
            'IdEmpresa' => $request->IdEmpresa,
            'Fecha' => $request->Fecha ?? now()->format('Y-m-d'),
            'Tipo' => $request->Tipo,
            'Suc' => $request->Suc,
            'Nro' => $request->Nro,
            'FechaP' => $request->FechaP ?? '0000-00-00',
            'Pagado' => $request->FechaP !== null ? 1 : 0,
            'Obs' => $request->Obs ?? ''
        ]);

        $nuevoId && $this->examenProvisorio($nuevoId);

        return response()->json(['id' => $nuevoId]);
    }

    public function update(Request $request)
    {
        if(!$this->hasPermission("examenCta_edit")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $examen = ExamenCuenta::find($request->Id);

        if($examen)
        {
            $examen->IdEmpresa = $request->IdEmpresa;
            $examen->Fecha = $request->Fecha;
            $examen->Tipo = $request->Tipo;
            $examen->Suc = $request->Suc;
            $examen->Nro = $request->Nro;
            $examen->FechaP = $request->FechaP ?? '0000-00-00';
            $examen->Pagado = $request->FechaP !== null ? 1 : 0;
            $examen->Obs = $request->Obs;
            $examen->save();

        }
    }

    public function detalles(Request $request)
    {
        if(!$this->hasPermission("examenCta_show")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $detalle = ExamenCuenta::join('pagosacuenta_it', 'pagosacuenta.Id', '=', 'pagosacuenta_it.IdPago')
            ->join('examenes', 'pagosacuenta_it.IdExamen', '=', 'examenes.Id')
            ->join('prestaciones', 'pagosacuenta_it.IdPrestacion', '=', 'prestaciones.Id')
            ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->select(
                'prestaciones.Id as IdPrestacion',
                'examenes.Nombre as NombreExamen',
                'pacientes.Nombre as NombrePaciente',
                'pacientes.Apellido as ApellidoPaciente'
            )
            ->where('pagosacuenta.Id', $request->Id)
            ->orderBy('pacientes.Apellido', 'ASC')
            ->get();

        return response()->json(['result' => $detalle]);   
    }

    public function delete(Request $request)
    {   
        if(!$this->hasPermission("examenCta_delete")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $examen = ExamenCuenta::find($request->Id);
        $resultado = [];

        if ($examen) 
        {
            $examen->delete();
            $resultado = ['message' => 'Se ha eliminado el examen a cuenta de manera correcta', 'estado' => 'success'];

        } else {

            $resultado = ['message' => 'Ha ocurrido un error en el ID de eliminación. Verifique por favor', 'estado' => 'fail'];
        }

        return response()->json($resultado);
    }

    public function deleteItem(Request $request)
    {
        if(!$this->hasPermission("examenCta_delete")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $examenes = $request->Id;

        if (!is_array($examenes)) {
            $examenes = [$examenes];
        }

        foreach($examenes as $examen) {

            $item = ExamenCuentaIt::find($examen);
            $item && $item->delete();
        }
    }

    public function liberarItem(Request $request)
    {
        if(!$this->hasPermission("examenCta_edit")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $examenes = $request->Id;

        if (!is_array($examenes)) {
            $examenes = [$examenes];
        }

        foreach($examenes as $examen) {

            $item = ExamenCuentaIt::find($examen);
            if($item) 
            {
                $item->Precarga = 0;
                $item->save();
            }
        }
    }

    public function precarga(Request $request)
    {
        if(!$this->hasPermission("examenCta_edit")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $examenes = $request->Id;

        if (!is_array($examenes)) {
            $examenes = [$examenes];
        }

        foreach($examenes as $examen) {

            $item = ExamenCuentaIt::find($examen);
            if($item) 
            {
                $item->Precarga = $request->Precarga;
                $item->save();
            }
        }
    }

    public function listado(Request $request)
    {
        if(!$this->hasPermission("examenCta_show")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $query = ExamenCuentaIt::join('pagosacuenta', 'pagosacuenta_it.IdPago', '=', 'pagosacuenta.Id')
            ->join('examenes', 'pagosacuenta_it.IdExamen', '=', 'examenes.Id')
            ->join('prestaciones', 'pagosacuenta_it.IdPrestacion', '=', 'prestaciones.Id')
            ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->select(
                'pagosacuenta_it.Precarga as Precarga',
                'examenes.Nombre as Examen',
                'prestaciones.Id as Prestacion',
                'pacientes.Nombre as NombrePaciente',
                'pacientes.Apellido as ApellidoPaciente',
                'pagosacuenta_it.Id as IdEx'
            )
            ->where('pagosacuenta_it.IdPago', $request->Id)
            ->whereNot('pagosacuenta_it.Obs', 'provisorio')
            ->orderBy('pagosacuenta_it.Precarga', 'Desc')
            ->get();

        return response()->json($query);


    }

    public function saveEx(Request $request)
    {
        if(!$this->hasPermission("examenCta_add")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        switch($request->Tipo) {

            case 'examen':

                $precarga = $request->precarga ?? 0;
                $preAgregar = false;
                for ($i=0; $i < $request->cantidad; $i++) { 
                    ExamenCuentaIt::create([
                        'Id' => ExamenCuentaIt::max('Id') + 1,
                        'IdPago' => $request->Id,
                        'IdExamen' => $request->examen,
                        'IdPrestacion' => 0,
                        'Obs' => '',
                        'Obs2' => '',
                        'Precarga' => !$preAgregar ? $precarga : 0
                    ]);

                    if(!$preAgregar) {
                        $preAgregar = true;
                    }
                }
                break;

            case 'paquete':
                
                $precarga = $request->precarga ?? 0;
                $preAgregar = false;

                for ($i=0; $i < $request->cantidad; $i++) {
                    
                    $examenes = Relpaqest::where('IdPaquete', $request->examen)->get();

                    foreach($examenes as $e) {

                        ExamenCuentaIt::create([
                            'Id' => ExamenCuentaIt::max('Id') + 1,
                            'IdPago' => $request->Id,
                            'IdExamen' => $e->IdExamen,
                            'IdPrestacion' => 0,
                            'Obs' => '',
                            'Obs2' => '',
                            'Precarga' => !$preAgregar ? $precarga : 0
                        ]);  
                    }

                    if(!$preAgregar) {
                        $preAgregar = true;
                    }
                }
                break;
            
            case 'facturacion':
               
                $precarga = $request->precarga ?? 0;
                $preAgregar = false;

                for ($i=0; $i < $request->cantidad; $i++) {
                    
                    $examenes = Relpaqfact::where('IdPaquete', $request->examen)->get();

                    foreach($examenes as $e) {

                        ExamenCuentaIt::create([
                            'Id' => ExamenCuentaIt::max('Id') + 1,
                            'IdPago' => $request->Id,
                            'IdExamen' => $e->IdExamen,
                            'IdPrestacion' => 0,
                            'Obs' => '',
                            'Obs2' => '',
                            'Precarga' => !$preAgregar ? $precarga : 0,
                        ]);  
                    }

                    if(!$preAgregar) {
                        $preAgregar = true;
                    }
                }
                break;
            }       
    }

    public function lstClientes(Request $request)
    {
        if(!$this->hasPermission("examenCta_show")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $clientes = ExamenCuenta::join('pagosacuenta_it', 'pagosacuenta.Id', '=', 'pagosacuenta_it.IdPago')
            ->join('examenes', 'pagosacuenta_it.IdExamen', '=', 'examenes.Id')
            ->join('prestaciones', 'pagosacuenta_it.IdPrestacion', '=', 'prestaciones.Id')
            ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->select(
                'pagosacuenta.Id as Id',
                'pagosacuenta.Fecha as Fecha',
                'pagosacuenta.Tipo as Tipo',
                'pagosacuenta.Suc as Suc',
                'pagosacuenta.Nro as Nro',
                'pagosacuenta.Obs as Obs',
                DB::raw('COUNT(DISTINCT CASE WHEN pagosacuenta_it.IdPrestacion <> 0 THEN pagosacuenta_it.IdPrestacion END) as Cantidad')
            )
            ->where('pagosacuenta.IdEmpresa', $request->Id)
            ->orderBy('pagosacuenta.Id', 'Desc')
            ->orderBy('pagosacuenta.Fecha', 'Desc')
            ->groupBy('pagosacuenta.Id')
            ->get();

        return response()->json($clientes);
    }

    public function listadoDni(Request $request)
    {
        if(!$this->hasPermission("examenCta_show")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $clientes = ExamenCuentaIt::join('pagosacuenta', 'pagosacuenta_it.IdPago', '=', 'pagosacuenta.Id')
            ->join('prestaciones', 'pagosacuenta_it.IdPrestacion', '=', 'prestaciones.Id')
            ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->select(
                'pacientes.Documento as Documento',
                'pagosacuenta_it.IdPrestacion as IdPrestacion',
                'pagosacuenta_it.IdPago as IdPago'
            )
            ->where('pagosacuenta_it.IdPago', $request->Id)
            ->whereNot('pagosacuenta_it.Obs', 'provisorio')
            ->orderBy('pagosacuenta_it.Id', 'Desc')
            ->groupBy('pagosacuenta_it.IdPrestacion')
            ->get();

        return response()->json($clientes);
    }

   
    public function listadoEx(Request $request)
    {
        if(!$this->hasPermission("examenCta_show")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $clientes = ExamenCuentaIt::join('examenes', 'pagosacuenta_it.IdExamen', '=', 'examenes.Id')
            ->join('pagosacuenta', 'pagosacuenta_it.IdPago', '=', 'pagosacuenta.Id')
            ->select(
                'examenes.Nombre as NombreExamen',
                'pagosacuenta.Tipo as Tipo',
                'pagosacuenta.Suc as Suc',
                'pagosacuenta.Nro as Nro',
                'pagosacuenta.Pagado as Pagado',
                DB::raw('(SELECT COUNT(*) FROM pagosacuenta_it WHERE IdPago = pagosacuenta.Id AND IdExamen = examenes.Id AND pagosacuenta_it.IdPrestacion = '.$request->Id.') as Cantidad')
            )
            ->where('pagosacuenta_it.IdPrestacion', $request->Id ?? '')
            ->where('pagosacuenta_it.IdPago', $request->IdPago)
            ->whereNot('pagosacuenta_it.IdExamen', 0)
            //->whereNot('pagosacuenta_it.IdPrestacion', 0)
            ->groupBy('pagosacuenta_it.IdExamen')
            ->orderBy('pagosacuenta_it.IdPrestacion', 'ASC')
            ->get();
        
        return response()->json($clientes);
        
    }

    public function excel(Request $request)
    {
        if(!$this->hasPermission("examenCta_report")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $examen = $this->tituloReporte($request->Id);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $sheet->mergeCells('A1:D1');
        $sheet->mergeCells('B4:D4');
        
        $columnas = ['A', 'B', 'C', 'D'];

        foreach($columnas as $columna){
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        $sheet->setCellValue('A1', 'DETALLE DE EXAMENES A CUENTA');
        $sheet->getStyle('A1')->getFont()->setSize(14);
        $sheet->getRowDimension('1')->setRowHeight(40);

        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->getStyle('A1:A6')->getFill()
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
        $sheet->getStyle('A1')->applyFromArray($styleArray);

        $sheet->getStyle('A1:A6')->getFont()->setBold(true);
        $sheet->getStyle('A2:A6')->getFont()->setSize(11);

        for ($i = 2; $i <= 6; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(30); 
        }

        $factura = $examen->Tipo . '-' . sprintf('%04d', $examen->Suc) . '-' . sprintf('%08d', $examen->Nro);

        $sheet->setCellValue('A2', 'Fecha: ');
        $sheet->setCellValue('B2', Carbon::parse($examen->Fecha)->format('d/m/Y'));
        $sheet->setCellValue('A3', 'Factura: ');
        $sheet->setCellValue('B3', $factura);
        $sheet->setCellValue('A4', 'Cliente: ');
        $sheet->setCellValue('B4', sprintf('%05d', $examen->IdEmpresa) . ' - Empresa: ' . $examen->Empresa . ' - ' . $examen->Cuit);
        $sheet->setCellValue('A5', 'Total Ex: ');
        $sheet->setCellValue('B5', $this->totalExamenes($examen->Id));
        $sheet->setCellValue('A6', 'Ex Disponibles: ');
        $sheet->setCellValue('B6', $this->totalDisponibles($examen->Id));

        $examenes = $this->examenesReporte($examen->Id);

        $sheet->setCellValue('A8', 'Prestación');
        $sheet->setCellValue('B8', 'Estudio');
        $sheet->setCellValue('C8', 'Examen');
        $sheet->setCellValue('D8', 'Paciente');

        $sheet->getStyle('A8:D8')->getFont()->setBold(true)->setSize(11);
        $sheet->getRowDimension('8')->setRowHeight(30);

        $sheet->getStyle('A8:D8')->getFill()
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
        $sheet->getStyle('A8:D8')->applyFromArray($styleArray);

        $sheet->getStyle('A8:D8')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $fila = 9;
        foreach($examenes as $reporte){
            $sheet->setCellValue('A'.$fila, $reporte->IdPrestacion === 0 ? '-' : $reporte->IdPrestacion);
            $sheet->setCellValue('B'.$fila, $reporte->NombreEstudio);
            $sheet->setCellValue('C'.$fila, $reporte->NombreExamen);
            $sheet->setCellValue('D'.$fila, $reporte->Apellido . " " . $reporte->Nombre);
            $fila++;
        }

        $nuevaFila = $fila+1;
        
        $sheet->setCellValue('A'.$nuevaFila, 'TOTAL EXAMENES DEL PAGO: ');
        $sheet->mergeCells('A'.$nuevaFila.':B'.$nuevaFila);
        $sheet->getStyle('A'.$nuevaFila.':B'.$nuevaFila)->getFont()->setBold(true)->setSize(11);
        $sheet->getRowDimension('8')->setRowHeight(30);
        $sheet->getStyle('A'.$nuevaFila.':B'.$nuevaFila)->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('CCCCCCCC'); 

        $listado = $this->totalReporte($examen->Id);
        
        $ultimasFilas = $nuevaFila + 1;
        foreach($listado as $items){
            $sheet->setCellValue('A'.$ultimasFilas, $items->Cantidad);
            $sheet->setCellValue('B'.$ultimasFilas, $items->NombreExamen);
            $ultimasFilas++;
        }

        // Generar un nombre aleatorio para el archivo
        $name = Str::random(10).'.xlsx';

        // Guardar el archivo en la carpeta de almacenamiento
        $filePath = storage_path('app/public/'.$name);

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        chmod($filePath, 0777);

        // Devolver la ruta del archivo generado
        return response()->json(['filePath' => $filePath, 'Factura' => $factura]);   
    }

    public function pdf(Request $request) 
    {
        $cliente = ExamenCuenta::with(['empresa', 'examen', 'empresa.localidad'])->find($request->Id);

        $paramsTitulo = [
            'detalles' => 'DETALLE DE EXAMENES A CUENTA',
            'id' => $request->Id
        ];
        $paramsSubtitulo = ['cliente' => $cliente];
        $paramsCuerpo = ['id' =>$request->Id];

        return $this->reporteService->generarReporte(
            Basico::class,
            Empresa::class,
            ExCuenta::class,
            null,
            "imprimir",
            storage_path('app/public/archivo.pdf'),
            $request->Id,
            $paramsTitulo, 
            $paramsSubtitulo,
            $paramsCuerpo,
            [],
            null
        );
    }

    public function reporteGeneral(Request $request)
    {
        if(!$this->hasPermission("examenCta_report")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $examenes = $this->querySalDet($request->Id);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        $columnas = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

        foreach ($columnas as $columna) {
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        $condicion = $request->Tipo === 'detalles' ? 'G' : 'D';

        $sheet->getStyle('A1:'.$condicion.'1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('CCCCCCCC'); 
        
        $fila = 2;

        switch ($request->Tipo) {
            case 'detalles':
                $sheet->setCellValue('A1', 'Numero ');
                $sheet->setCellValue('B1', 'Pago ');
                $sheet->setCellValue('C1', 'Fecha ');
                $sheet->setCellValue('D1', 'Cliente ');
                $sheet->setCellValue('E1', 'Empresa ');
                $sheet->setCellValue('F1', 'Cant ');
                $sheet->setCellValue('G1', 'Examen ');

                foreach($examenes as $examen){
                    $factura = $examen->Tipo . sprintf('%04d', $examen->Suc) . sprintf('%08d', $examen->Nro);

                    $sheet->setCellValue('A'.$fila, $examen->Id);
                    $sheet->setCellValue('B'.$fila, $factura);
                    $sheet->setCellValue('C'.$fila, $examen->Fecha);
                    $sheet->setCellValue('D'.$fila, $examen->Empresa);
                    $sheet->setCellValue('E'.$fila, $examen->ParaEmpresa);
                    $sheet->setCellValue('F'.$fila, $examen->Cantidad);
                    $sheet->setCellValue('G'.$fila, $examen->NombreExamen);
                    $fila++;
                }
                
                break;
            
            case 'saldo':
                $sheet->setCellValue('A1', 'Cliente ');
                $sheet->setCellValue('B1', 'ParaEmpresa ');
                $sheet->setCellValue('C1', 'Cantidad ');
                $sheet->setCellValue('D1', 'Examen ');

                foreach($examenes as $examen){
                    $sheet->setCellValue('A'.$fila, $examen->Empresa);
                    $sheet->setCellValue('B'.$fila, $examen->ParaEmpresa);
                    $sheet->setCellValue('C'.$fila, $examen->Cantidad);
                    $sheet->setCellValue('D'.$fila, $examen->NombreExamen);
                    $fila++;
                }

                break;
        }

        // Generar un nombre aleatorio para el archivo
        $name = Str::random(10).'.xlsx';

        // Guardar el archivo en la carpeta de almacenamiento
        $filePath = storage_path('app/public/'.$name);

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        chmod($filePath, 0777);

        // Devolver la ruta del archivo generado
        return response()->json(['filePath' => $filePath]);      

    }

    public function disponibilidad(Request $request)
    {
        if(!$this->hasPermission("examenCta_show")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $examen = ExamenCuentaIt::join('pagosacuenta', 'pagosacuenta_it.IdPago', '=', 'pagosacuenta.Id')
            ->join('clientes', 'pagosacuenta.IdEmpresa', '=', 'clientes.Id')
            ->join('examenes', 'pagosacuenta_it.IdExamen', '=', 'examenes.Id')
            ->select(
                'pagosacuenta.Id as Id',
                'pagosacuenta_it.Obs as Precarga',
                'examenes.Nombre as NombreExamen', 
                //DB::raw('COUNT(pagosacuenta_it.IdExamen) as Cantidad')
            )
            ->where('pagosacuenta.IdEmpresa', $request->Id)
            ->where('pagosacuenta_it.IdPrestacion', 0)
            ->whereNot('pagosacuenta_it.Obs', 'provisorio')
            ->orderBy('examenes.Nombre', 'Asc')
            ->orderBy('pagosacuenta_it.Obs', 'Desc')
            ->get();

        return response()->json($examen);  
    }

    public function listadoUltimas(Request $request)
    {
        if(!$this->hasPermission("examenCta_show")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $prestacion = ExamenCuentaIt::join('pagosacuenta', 'pagosacuenta_it.IdPago', '=', 'pagosacuenta.Id')
                ->join('clientes', 'pagosacuenta.IdEmpresa', '=', 'clientes.Id')
                ->join('prestaciones', 'pagosacuenta_it.IdPrestacion', '=', 'prestaciones.Id')
                ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
                ->select(
                    'pagosacuenta_it.IdPrestacion as NroPrestacion',
                    'prestaciones.TipoPrestacion as TipoPrestacion',
                    'pacientes.Apellido as Apellido',
                    'pacientes.Nombre as Nombre',
                )
                ->where('pagosacuenta.IdEmpresa', $request->Id)
                ->whereNot('pagosacuenta_it.IdPrestacion', 0)
                ->orderBy('prestaciones.Id', 'Desc')
                ->distinct()
                ->limit(5)
                ->get();

        return response()->json($prestacion);
    }

    public function saldoNoDatatable(Request $request)
    {
        if(!$this->hasPermission("examenCta_show")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $query = ExamenCuenta::join('pagosacuenta_it', 'pagosacuenta.Id', '=', 'pagosacuenta_it.IdPago')
        ->join('clientes', 'pagosacuenta.IdEmpresa', '=', 'clientes.Id')
        ->join('examenes', 'pagosacuenta_it.IdExamen', '=', 'examenes.Id')
        ->join('prestaciones', 'pagosacuenta_it.IdPrestacion', '=', 'prestaciones.Id')
        ->select(
            'clientes.RazonSocial as Empresa',
            'examenes.Nombre as Examen',
            'pagosacuenta.IdEmpresa as IdEmpresa'
        )
        ->selectRaw('COUNT(CASE WHEN pagosacuenta_it.IdPrestacion = 0 THEN 1 END) AS contadorSaldos')
        ->where('clientes.Id', $request->Id)
        ->groupBy(['clientes.Id', 'clientes.RazonSocial','clientes.ParaEmpresa','clientes.Identificacion','examenes.Nombre'])
        ->havingRaw('contadorSaldos > 0')
        ->whereNot('pagosacuenta_it.Obs', 'provisorio')
        ->orderBy('clientes.RazonSocial')
        ->orderBy('examenes.Nombre')
        ->get();

        return response()->json($query);
    }

    public function disponibles(Request $request)
    {
        if(!$this->hasPermission("examenCta_show")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $query = ExamenCuenta::join('pagosacuenta_it', 'pagosacuenta.Id', '=', 'pagosacuenta_it.IdPago')
            ->where('pagosacuenta.IdEmpresa', $request->Id)
            ->where('pagosacuenta_it.IdPrestacion', 0)
            ->whereNot('pagosacuenta_it.Obs', 'provisorio')
            ->count();

        return response()->json($query);
    }

     //Listado dentro de Pacientes en Alta de Prestación - Muestra las facturas en la grilla
     public function lstExClientes(Request $request)
     {
        if(!$this->hasPermission("prestaciones_show")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

         $clientes = ExamenCuentaIt::join('examenes', 'pagosacuenta_it.IdExamen', '=', 'examenes.Id')
             ->join('pagosacuenta', function($join) use ($request) {
                $join->on('pagosacuenta_it.IdPago', '=', 'pagosacuenta.Id');
                    $join->where('pagosacuenta.IdEmpresa', $request->Id);
             })
             ->select(
                 'examenes.Nombre as NombreExamen',
                 'pagosacuenta.Tipo as Tipo',
                 'pagosacuenta.Suc as Suc',
                 'pagosacuenta.Nro as Nro',
                 'pagosacuenta.Obs as Obs',
                 'pagosacuenta.Id as Id'
             )
             ->where('pagosacuenta_it.IdPrestacion', 0)
             ->whereNot('pagosacuenta_it.Obs', 'provisorio')
             ->groupBy(['pagosacuenta.Tipo', 'pagosacuenta.Suc','pagosacuenta.Nro'])
             ->get();
 
         return response()->json($clientes);
     }

    //Listado dentro de Pacientes en Alta de Prestación con todos los DNI precargados
    public function listadoPrecarga(Request $request)
    {
        if(!$this->hasPermission("prestaciones_show")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $clientes = ExamenCuentaIt::join('pagosacuenta', 'pagosacuenta_it.IdPago', '=', 'pagosacuenta.Id')
            ->select(
                'pagosacuenta_it.Precarga as Documento',
                'pagosacuenta_it.IdPrestacion as IdPrestacion',
                'pagosacuenta_it.IdPago as IdPago'
            )
            ->where('pagosacuenta_it.IdPago', $request->Id);

            $clientes->when(!empty($request->IdExamen), function ($query) use ($request) {
                $query->where('pagosacuenta_it.IdExamen', $request->IdExamen)
                        ->whereNot('pagosacuenta_it.IdExamen', '<>', $request->IdExamen);
            });
            $clientes = $clientes->where('pagosacuenta_it.IdPrestacion', 0)
            ->groupBy('pagosacuenta_it.Precarga')  
            ->orderBy('pagosacuenta_it.Precarga', 'Desc')
            ->get();

        return response()->json($clientes);
    }

    //Listado dentro de Pacientes en Alta de Prestación
    public function listadoExCta(Request $request)
    {
        if(!$this->hasPermission("prestaciones_show")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $clientes = ExamenCuentaIt::join('examenes', 'pagosacuenta_it.IdExamen', '=', 'examenes.Id')
            ->join('pagosacuenta', 'pagosacuenta_it.IdPago', '=', 'pagosacuenta.Id')
            ->select(
                'examenes.Nombre as NombreExamen',
                'examenes.Id as IdTest',
                'pagosacuenta_it.Id as IdEx',
                'pagosacuenta.Pagado as Pagado',
                DB::raw('(SELECT COUNT(*) FROM pagosacuenta_it WHERE IdPago = pagosacuenta.Id AND IdExamen = examenes.Id AND IdPrestacion = 0) as Cantidad')
            )
            ->where(function ($query) use ($request) {
                $query->where('pagosacuenta_it.Precarga', $request->Id)
                        ->orWhereNull('pagosacuenta_it.Precarga');
            })
            ->where('pagosacuenta_it.IdPrestacion', 0)
            ->where('pagosacuenta.Id', $request->IdPago)
            ->whereNot('pagosacuenta_it.IdExamen', 0);

            $clientes = $clientes->groupBy('examenes.Id')->orderBy('pagosacuenta_it.IdPrestacion', 'ASC')
            ->get();
        
        return response()->json($clientes);
        
    }
    
    private function tituloReporte(?int $id): mixed
    {
        return ExamenCuenta::join('clientes', 'pagosacuenta.IdEmpresa', '=', 'clientes.Id')
            ->join('localidades', 'clientes.IdLocalidad', '=', 'localidades.Id')
            ->select(
                'clientes.RazonSocial as Empresa',
                'clientes.ParaEmpresa as ParaEmpresa',
                'clientes.Direccion as Direccion',
                'clientes.Identificacion as Cuit',
                'clientes.Id as IdEmpresa',
                'clientes.Telefono as Telefono',
                'pagosacuenta.Fecha as Fecha',
                'pagosacuenta.Tipo as Tipo',
                'pagosacuenta.Suc as Suc',
                'pagosacuenta.Nro as Nro',
                'pagosacuenta.Id as Id',
                'localidades.Nombre as NombreLocalidad',
                'localidades.CP as CodigoPostal',
            )
            ->where('pagosacuenta.Id', $id)
            ->first();
    }

    private function examenesReporte(?int $id): mixed
    {
        return ExamenCuentaIt::join('examenes', 'pagosacuenta_it.IdExamen', '=', 'examenes.Id')
            ->join('prestaciones', 'pagosacuenta_it.IdPrestacion', '=', 'prestaciones.Id')
            ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->join('estudios', 'examenes.IdEstudio', '=', 'estudios.Id')
            ->select(
                'prestaciones.Id as IdPrestacion',
                'estudios.Nombre as NombreEstudio',
                'examenes.Nombre as NombreExamen',
                'pacientes.Nombre as Nombre',
                'pacientes.Apellido as Apellido'  
            )
            ->where('pagosacuenta_it.IdPago', $id)
            ->orderBy('examenes.Nombre')
            ->orderBy('estudios.Nombre')
            ->get();
    }

    private function totalExamenes(?int $id): int
    {
        return ExamenCuentaIt::where('IdPago', $id)->count();
    }

    private function totalReporte(?int $id): mixed
    {
        return ExamenCuentaIt::join('examenes', 'examenes.Id', '=', 'pagosacuenta_it.IdExamen')
        ->select(
            DB::raw('COUNT(pagosacuenta_it.IdExamen) as Cantidad'), 
            'examenes.Nombre as NombreExamen')
        ->where('pagosacuenta_it.IdPago', $id)
        ->orderBy('examenes.Nombre')
        ->get();
    }

    private function totalDisponibles(?int $id): int
    {
        return ExamenCuentaIt::where('IdPago', $id)->where('IdPrestacion', 0)->count();
    }

    private function querySalDet(?int $id): mixed
    {
        return ExamenCuenta::join('pagosacuenta_it', function($join) {
            $join->on('pagosacuenta.Id', '=', 'pagosacuenta_it.IdPago')
                    ->where('pagosacuenta_it.IdPrestacion', 0);
            })
            ->join('clientes', 'pagosacuenta.IdEmpresa', '=', 'clientes.Id')
            ->join('examenes', 'pagosacuenta_it.IdExamen', '=', 'examenes.Id')
            ->select(
                'pagosacuenta.Id as Id',
                'pagosacuenta.Tipo as Tipo',
                'pagosacuenta.Suc as Suc',
                'pagosacuenta.Nro as Nro',
                'pagosacuenta.Fecha as Fecha',
                'clientes.RazonSocial as Empresa',
                'clientes.ParaEmpresa as ParaEmpresa',
                'examenes.Nombre as NombreExamen',
                DB::raw('COUNT(pagosacuenta_it.IdExamen) as Cantidad')
            )
            ->where('pagosacuenta.IdEmpresa', $id)
            ->groupBy('examenes.Nombre')
            ->orderBy('clientes.RazonSocial', 'DESC')
            ->orderBy('clientes.ParaEmpresa', 'DESC')
            ->orderBy('pagosacuenta.Tipo', 'DESC')
            ->orderBy('pagosacuenta.Suc', 'DESC')
            ->orderBy('pagosacuenta.Nro', 'DESC')
            ->orderBy('examenes.Nombre', 'DESC')
            ->get();
    }

    private function queryBasico(): mixed
    {
        return ExamenCuenta::join('clientes', 'pagosacuenta.IdEmpresa', '=', 'clientes.Id')
        ->join('pagosacuenta_it', 'pagosacuenta.Id', '=', 'pagosacuenta_it.IdPago')
        ->join('prestaciones', 'pagosacuenta_it.IdPrestacion', '=', 'prestaciones.Id')
        ->join('examenes', 'pagosacuenta_it.IdExamen', '=', 'examenes.Id')
        ->join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
        ->select(
            'pagosacuenta.Id as IdEx',
            'clientes.RazonSocial as Empresa',
            'clientes.Identificacion as Cuit',
            'clientes.ParaEmpresa as ParaEmpresa',
            'pagosacuenta.FechaP as FechaPagado',
            'pagosacuenta.Pagado as Pagado',
            'pagosacuenta.Fecha as Fecha',
            'pagosacuenta.Tipo as Tipo',
            'pagosacuenta.Suc as Sucursal',
            'pagosacuenta.Nro as Numero',
            'pacientes.Nombre as NomPaciente',
            'pacientes.Apellido as ApePaciente',
            'examenes.Nombre as Examen'
        );
    }
}
