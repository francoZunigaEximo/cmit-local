<?php

namespace App\Traits;

use App\Models\ExamenCuenta;
use App\Models\FacturaDeVenta;
use App\Models\FacturaResumen;
use App\Models\ItemsFacturaVenta;
use App\Models\ExamenCuentaIt;
use App\Models\Prestacion;
use App\Models\Parametro;

use FPDF;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
trait Reportes
{
    //Datos deCabeceras
    private static $URLPORTADA = "/archivos/reportes/portada.jpg";
    private static $LOGO = "/archivos/reportes/LogoEmpresa.jpg";
    private static $TITULO = "CMIT | SALUD OCUPACIONAL SRL";
    private static $DIRECCION = "Juan B. Justo 825 - Neuquen Cap. - 0299 4474371 /4474686 - www.cmit.com.ar";

    //Rutas de los archivos de reportes
    private static $RUTAFACTURAS = "/app/public/facturas/";
    private static $RUTAREXACUENTA = "/app/public/examenescuenta/";

    public function eEstudioCaratula(int $id): void
    {
        $prestacion = Prestacion::find($id);
        $miEmpresa = Parametro::getMiEmpresa();
        if($prestacion)
        {
            $paciente = $prestacion->paciente->Nombre ." ". $prestacion->paciente->Apellido;

            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->Image(url('/').self::$URLPORTADA,1,0,209); 
            $y=220;
            $pdf->SetFont('Arial','B',14);
            $pdf->SetTextColor(255, 255, 255, 255);//white
            $pdf->SetXY(109,$y);$pdf->Cell(0,3,substr($paciente,0,28),0,0,'L');$y=$y+10;
            $pdf->SetXY(109,$y);$pdf->Cell(0,3,$prestacion->Fecha,0,0,'L');$y=$y+10;
            $pdf->SetXY(109,$y);$pdf->Cell(0,3,$prestacion->paciente->TipoDocumento.' '.$prestacion->paciente->Documento,0,0,'L');$y=$y+10;
            $pdf->SetXY(109,$y);$pdf->Cell(0,3,substr($prestacion->empresa->RazonSocial,0,28),0,0,'L');$y=$y+10;
            $pdf->SetXY(109,$y);$pdf->Cell(0,3,substr($prestacion->empresa->ParaEmpresa,0,28),0,0,'L');$y=$y+10;
            $pdf->SetXY(109,$y);$pdf->Cell(0,3,$id,0,0,'L');$y=$y+10;
            $pdf->SetTextColor(0, 0, 0, 0);
            $pdf->Output($miEmpresa->Path4."caratula_".$id.".pdf", "F");
        }
    }

    public function generarDetalleFactura(int $id, ?string $tipo)
    {
        $filePath = storage_path(SELF::$RUTAFACTURAS.'F'.$id.'_'.now()->format('d-m-Y').'.pdf');
        $factura = FacturaDeVenta::with('empresa.localidad')->find($id);
        
        if ($factura) {

            $nroFactura = $factura->Tipo . '-' . sprintf('%04d', $factura->Sucursal) . '-' . sprintf('%08d', $factura->NroFactura);

            $pdf = new FPDF('P', 'mm', 'A4');
            $pdf->AddPage();
            $this->caratula($pdf, $factura->Fecha, $nroFactura, 'DETALLE DE FACTURA');
            $this->cuadroDatosCaratula($pdf, $factura);

            //titulos columnas
			$pdf->Cell(14,5,'FECHA',0,0,'L');$pdf->Cell(20,5,'PRESTACION',0,0,'R');$pdf->Cell(31,5,'PACIENTE',0,0,'L');
			$pdf->Cell(17,5,'C.COSTOS',0,0,'L');$pdf->Cell(34,5,'NRO.CE',0,0,'L');$pdf->Cell(70,5,'DETALLE',0,0,'L');$pdf->Ln();
			$pdf->Line(10,82,205,82);

            $grilla = Prestacion::with(['paciente', 'itemFacturaVenta'])
                ->whereHas('itemFacturaVenta', function ($query) use ($id) {
                    $query->where('IdFactura', $id);
                })->get();
            $sumaprest=0;
            
            foreach ($grilla as $fila) {
                $sumaprest=$sumaprest+1;

                $pdf->Cell(10,3,Carbon::parse($fila->Fecha)->format('d/m/Y'),0,0,'L');
                $pdf->Cell(20,3,str_pad($fila->Id, 8, "0", STR_PAD_LEFT),0,0,'R');
				$pdf->Cell(35,3,substr($fila->paciente->Apellido." ".$fila->paciente->Nombre,0,20),0,0,'L');
				$pdf->Cell(17,3,substr($fila->CCosto,0,10),0,0,'L');
                $pdf->Cell(14,3,str_pad($fila->NroCEE, 8, "0", STR_PAD_LEFT),0,0,'L');
                $pdf->Cell(20,3,$fila->TSN,0,0,'L');

                $detalles = ItemsFacturaVenta::where('IdFactura', $id)->where('IdPrestacion', $fila->Id)->get();

                foreach ($detalles as $detalle) {
                    $pdf->MultiCell(70,3,$detalle->Detalle,0,'L',0,5);
                    $pdf->Ln();
                }
            }

            $sumaresumen = 0;

            $pdf->Ln(6);
            $pdf->SetFont('Arial','BU',10);	
            $pdf->Cell(0,5,'TOTAL EXAMENES:',0,0,'L');
            $pdf->Ln();
			$pdf->SetFont('Arial','',7);

            $resumenes = FacturaResumen::where('IdFactura', $id)->get();

            foreach ($resumenes as $resumen) {
                $pdf->Cell(20,3,$resumen->Total,0,0,'R');
                $pdf->Cell(0,3,$resumen->Detalle,0,0,'L');
                $pdf->Ln();
				
                $sumaresumen = $sumaresumen + $resumen->Total;
            }
            $pdf->Ln(5);
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(0,5,'Prestaciones: '.$sumaprest.', Examenes: '.$sumaresumen,0,0,'L');
            $pdf->Ln();	

            $pdf->Output($filePath, "F");

            if($tipo === 'imprimir'){
                return response()->json([
                        'filePath' => $filePath, 
                        'name' => 'F'.$id.'_'.now()->format('d-m-Y').'.pdf', 
                        'msg' => 'Factura F'.$id.'_'.now()->format('d-m-Y').' generada correctamente', 
                        'icon' => 'success'
                    ]);
            }
        }
    }

    public function generarDetalleExaCuenta(int $id, ?string $tipo)
    {
        $filePath = storage_path(SELF::$RUTAREXACUENTA.'F'.$id.'_'.now()->format('d-m-Y').'.pdf');
        $examenCuenta = ExamenCuenta::with('empresa.localidad')->find($id);

        if ($examenCuenta) {

            $nroExamen = $examenCuenta->Tipo . '-' . sprintf('%04d', $examenCuenta->Suc) . '-' . sprintf('%08d', $examenCuenta->Nro);

            $pdf = new FPDF('P', 'mm', 'A4');
            $pdf->AddPage();
            $this->caratula($pdf, $examenCuenta->Fecha, $nroExamen, 'DETALLE DE EXAMEN A CUENTA');
            $this->cuadroDatosCaratula($pdf, $examenCuenta);

            //titulos columnas
            $pdf->Cell(31,5,'ESTUDIO',0,0,'L');
            $pdf->Cell(75,5,'EXAMEN',0,0,'L');
            $pdf->Cell(20,5,'PRESTACION',0,0,'R');
            $pdf->Cell(60,5,'PACIENTE',0,0,'L');
            $pdf->Ln();
            $pdf->Line(10,82,205,82);
            $pdf->SetFont('Arial','',7);

            $examenes = $this->grillaExamenCuenta($id);

            foreach($examenes as $reporte) {
                $pdf->Cell(31,3,substr($reporte->NombreEstudio,0,10),0,0,'L');
                $pdf->Cell(75,3,substr($reporte->NombreExamen,0,40),0,0,'L');
                $pdf->Cell(20,3,$reporte->IdPrestacion === 0 ? '-' : $reporte->IdPrestacion,0,0,'R');
                $pdf->Cell(60,3,substr($reporte->Apellido . " " . $reporte->Nombre,0,30),0,0,'L');$pdf->Ln();
            }

            $listado = $this->detalladoExamenesCuenta($reporte->Id);

            $pdf->Ln(6);$pdf->SetFont('Arial','BU',10);	
            $pdf->Cell(0,5,'TOTAL EXAMENES DEL PAGO:',0,0,'L');
            $pdf->Ln();
            $pdf->SetFont('Arial','',7);

            foreach($listado as $item) {
                $pdf->Cell(20,3,$item->Cantidad,0,0,'R');
                $pdf->Cell(0,3,$item->NombreExamen,0,0,'L');
                $pdf->Ln();
            }
            
            $totalExamenes = ExamenCuentaIt::where('IdPago', $examenCuenta->Id)->count() ?? 0;
            $totalDisponibles = ExamenCuentaIt::where('IdPago', $examenCuenta->Id)->where('IdPrestacion', 0)->count() ?? 0;

            $pdf->Ln(5);
            $pdf->SetFont('Arial','B',8);	
            $pdf->Cell(0,5,'Examenes: '.$totalExamenes.', Disponibles: '.$totalDisponibles,0,0,'L');
            $pdf->Ln();				
            $pdf->SetY(0);

            $pdf->Output($filePath, "F");

            if($tipo === 'imprimir'){
                return response()->json([
                        'filePath' => $filePath, 
                        'name' => 'X'.$id.'_'.now()->format('d-m-Y').'.pdf', 
                        'msg' => 'Examen a Cuenta X'.$id.'_'.now()->format('d-m-Y').' generada correctamente', 
                        'icon' => 'success'
                    ]);
            }
        }
    }

    private function caratula($pdf, $fecha, $factura, $detalle)
    {
        $pdf->Image(url('/').self::$LOGO,10,6,20);
        $pdf->SetY(19);
        $pdf->SetFont('Arial','B',7);
        $pdf->SetX(10);
        $pdf->Cell(100,3,self::$TITULO,0,0,'L');
        $pdf->Ln();
        $pdf->SetFont('Arial','',7);
        $pdf->SetX(10);
        $pdf->Cell(0,3, self::$DIRECCION,0,0,'L');
        $pdf->Ln();
        $pdf->Line(10,26,200,26);
        $pdf->SetFont('Arial','B',14);
        $pdf->SetXY(10,9);
        $pdf->Cell(200,15, $detalle,0,0,'C');
        $pdf->SetFont('Arial','',9);
        $pdf->SetXY(10,28);
        $pdf->Cell(190,5,'FECHA: '.Carbon::parse($fecha)->format('d/m/Y'),0,0,'R');
        $pdf->SetXY(10,33);$pdf->Cell(190,5,'NRO: '.$factura,0,0,'R');
        $pdf->Ln(6);

        return $pdf;
    }

    private function cuadroDatosCaratula($pdf, $cliente)
    {
        //rectangulo
        $pdf->Rect(10,40,195,30);
        $pdf->SetY(43);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(18,5,'CLIENTE: ',0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(0,5,$cliente->empresa->RazonSocial,0,0,'L');
        $pdf->Ln();
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(18,5,'EMPRESA: ',0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(0,5,$cliente->empresa->ParaEmpresa,0,0,'L');
        $pdf->Ln();	
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(18,5,'DATOS: ',0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(100,5,"DOM: ".substr($cliente->empresa->Direccion,0,40),0,0,'L');
        $pdf->Cell(80,5,"CUIT: ".substr($cliente->empresa->Identificacion,0,45),0,0,'L');
        $pdf->Ln();	
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(18,5,'',0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(100,5,"LOC: ".substr($cliente->empresa->localidad->Nombre,0,40),0,0,'L');
        $pdf->Cell(80,5,"CP: ".substr($cliente->empresa->localidad->CP,0,45),0,0,'L');
        $pdf->Ln();		
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(18,5,'',0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(0,5,"TEL: ".substr($cliente->empresa->Telefono,0,40),0,0,'L');
        $pdf->Ln(15);

        return $pdf;
    }

    private function grillaExamenCuenta(int $id)
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

    private function detalladoExamenesCuenta(int $id)
    {
        return ExamenCuentaIt::join('examenes', 'examenes.Id', '=', 'pagosacuenta_it.IdExamen')
            ->select(
                DB::raw('COUNT(pagosacuenta_it.IdExamen) as Cantidad'), 
                'examenes.Nombre as NombreExamen')
            ->where('pagosacuenta_it.IdPago', $id)
            ->orderBy('examenes.Nombre')
            ->get();
    }
    
 

}