<?php 

namespace App\Services\Reportes\Cuerpos;

use App\Helpers\FileHelper;
use App\Models\ItemPrestacion;
use App\Models\Prestacion;
use App\Services\Reportes\Reporte;
use FPDF;

//Control de corte en Editar Prestaciones es 1

class ControlPaciente extends Reporte
{

    public function render(FPDF $pdf, $datos = ['id', 'controlCorte']): void
    {
        $prestaciones = $this->prestaciones($datos['id']);
        $itemsprestaciones = $this->itemsprestaciones($datos['id']);

        //control corte
        if ($datos['controlCorte'] === 1){
            
            $y = $pdf->GetY(); 
            $cantlineas = count($itemsprestaciones); 
            $controlcorte = $pdf->GetY();
            if (($controlcorte+60+$cantlineas*7)>273){$pdf->AddPage();$y=0;$controlcorte=0;}
        
        }else{$pdf->AddPage();$y=0;}

        $pdf->Rect(10,$y+15,90,17); $pdf->SetFont('Arial','B',8);
        $pdf->SetXY(11,$y+18);$pdf->Cell(0,3,'Paciente: '.utf8_decode($prestaciones->paciente->Apellido." ".$prestaciones->paciente->Nombre),0,0,'L');
        $pdf->SetXY(11,$y+23);$pdf->Cell(0,3,'Fecha: '.$prestaciones->Fecha.' '.$prestaciones->paciente->TipoDocumento.': '.$prestaciones->paciente->Documento,0,0,'L');
        $pdf->SetXY(11,$y+28);$pdf->Cell(0,3,'Empresa: '.utf8_decode($prestaciones->empresa->ParaEmpresa),0,0,'L');
        $pdf->SetFont('Arial','B',12);
        $pdf->SetXY(120,$y+15);$pdf->Cell(0,4,'RESUMEN PARA EL PACIENTE',0,0,'L');
        $pdf->Code39(130,$y+22,$prestaciones->Id,1,10);
        $pdf->SetXY(10,$y+45);$pdf->SetFont('Arial','',8);
        if(!empty($prestaciones->paciente->Foto) || $prestaciones->paciente->Foto !== 'foto-default.png'){$pdf->Image(@FileHelper::getFileUrl('lectura')."/Fotos/".$prestaciones->paciente->Foto,162,$y+36,38,27);}

        $prov = 0;

        foreach($itemsprestaciones as $item) { 
            if ($prov === 0 || $prov !== $item->IdProveedor) {		
                if($prov !== 0){$pdf->Ln(7);}
                $prov = $item->IdProveedor;
                $pdf->SetX(9);$pdf->Cell(0,3,utf8_decode(substr($item->NombreProveedor,0,50)),0,0,'L');$pdf->Ln(4);
            }
            $pdf->SetX(10);$pdf->Cell(3,3,'',1,0,'L');$pdf->SetX(15);$pdf->Cell(0,3,utf8_decode(substr($item->NombreExamen,0,60)),0,0,'L');$pdf->Ln(4);
            
        }
        //aclaracion
        $pdf->Ln(7);$y=$pdf->GetY();$pdf->SetFont('Arial','B',10);
        $pdf->SetXY(10,$y);$pdf->Cell(0,15,'POR CUESTIONES DE PROTOCOLO, TODOS LOS PROFESIONALES LE SOLICITARAN SU DNI',0,0,'L');
    }

    private function prestaciones(int $id): mixed
    {
        return Prestacion::with(['paciente', 'empresa'])->find($id);
    }

    private function itemsprestaciones(int $id): mixed
    {
        return ItemPrestacion::join('proveedores', 'itemsprestaciones.IdProveedor', '=', 'proveedores.Id')
            ->join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
            ->where('itemsprestaciones.Anulado', 0)
            ->where('itemsprestaciones.IdPrestacion', $id)
            ->orderBy('proveedores.Nombre')
            ->orderBy('examenes.Nombre')
            ->select('itemsprestaciones.*', 'proveedores.Nombre as NombreProveedor', 'examenes.Nombre as NombreExamen')
            ->get();
    }
}