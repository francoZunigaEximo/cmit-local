<?php

namespace App\Http\Controllers;

use App\Models\ArchivoEfector;
use App\Models\ArchivoInformador;
use App\Models\Auditor;
use App\Models\ItemPrestacion;
use App\Models\ItemPrestacionInfo;
use Illuminate\Http\Request;
use App\Traits\ObserverItemsPrestaciones;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ItemPrestacionesController extends Controller
{
    use ObserverItemsPrestaciones;

    const RUTA = '/var/IMPORTARPDF/SALIDA/';

    public function index()
    {
        //
    }

    public function edit(ItemPrestacion $itemsprestacione): mixed
    {
        $paciente = $this->getPaciente($itemsprestacione->IdPrestacion);
        $qrTexto = $this->generarQR('A', $itemsprestacione->IdPrestacion, $itemsprestacione->IdExamen, $paciente->Id, 'texto');
        $adjuntoEfector = $this->adjuntoEfector($itemsprestacione->Id);

        return view('layouts.itemsprestaciones.edit', compact(['itemsprestacione', 'qrTexto', 'paciente', 'adjuntoEfector']));
    }

    public function updateItem(Request $request): void
    {
        $examenes = $request->Id;

        $lstAbrir = [3 => 0, 4 => 1, 5 => 2];
        $lstCerrar = [0 => 3, 2 => 5, 1 => 4];

        if (!is_array($examenes)) {
            $examenes = [$examenes];
        }

        foreach ($examenes as $examen) {
            $item = ItemPrestacion::find($examen);

            if ($item) 
            {   
                if ($request->Para === 'abrir')
                {
                    $item->CAdj = empty($request->CAdj) ? $lstAbrir[$item->CAdj] : $request->CAdj;
                
                } elseif ($request->Para === 'cerrar' ) {

                    $item->CAdj = empty($request->CAdj) ? $lstCerrar[$item->CAdj] : $request->CAdj;

                } elseif ($request->Para === 'cerrarI'){

                    $item->CInfo = $request->CInfo;
                
                }
                $item->save();
            }
        }
    }

    public function updateAsignado(Request $request)
    {

        $item = ItemPrestacion::find($request->Id);

        if($item)
        {
            if($request->Para === 'asignar'){
                $item->IdProfesional = $request->IdProfesional;

            }elseif($request->Para === 'asignarI'){
                $item->IdProfesional2 = $request->IdProfesional;
            }
            
            $item->FechaAsignado = ($request->fecha === '0' ? '' : now()->format('Y-m-d'));
            $item->save();
        }
    }

    public function updateAdjunto(Request $request):void 
    {

        $adjunto = ItemPrestacion::find($request->Id);

        if ($adjunto)
        {   
            $adjunto->CAdj = $request->CAdj ?? '';
            $adjunto->save();
        }
    }

    public function paginacionGeneral(Request $request)
    {
        $Id = $request->Id;

        if($request->tipo === 'efector')
        {

            $query = ItemPrestacion::join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
            ->leftJoin('archivosefector', 'itemsprestaciones.Id', '=', 'archivosefector.IdEntidad')
            ->join('proveedores', 'itemsprestaciones.IdProveedor', '=', 'proveedores.Id')
            ->select(
                'archivosefector.Id as IdE',
                'archivosefector.Ruta as Nombre',
                'archivosefector.Descripcion as DescripcionE',
                'archivosefector.Ruta as RutaE',
                'examenes.NoImprime as Adjunto',
                'proveedores.MultiE as MultiE',
            )
            ->where('archivosefector.IdEntidad', $Id)
            ->get();

        }else if($request->tipo === 'informador'){

            $query = ItemPrestacion::join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
                ->leftJoin('archivosinformador', 'itemsprestaciones.Id', '=', 'archivosinformador.IdEntidad')
                ->join('proveedores', 'itemsprestaciones.IdProveedor', '=', 'proveedores.Id')
                ->select(
                    'archivosinformador.Id as IdI',
                    'archivosinformador.Ruta as Nombre',
                    'archivosinformador.Descripcion as DescripcionI',
                    'archivosinformador.Ruta as RutaI',
                    'examenes.Adjunto as Adjunto',
                )
                ->where('archivosinformador.IdEntidad', $Id)
                ->get();

        }

        return response()->json(['resultado' => $query]);
    }

    public function updateExamen(Request $request):void
    {
        $query = ItemPrestacion::find($request->Id);

        if($query){
            $query->Fecha = $request->Fecha ?? '';
            $query->ObsExamen = $request->ObsExamen ?? '';
            $query->IdProfesional2 = $request->Profesionales2 ?? '';
            
            $check = ItemPrestacionInfo::where('IdIP' , $request->Id)->first();
            if($check)
            {
                $this->updateItemPrestacionInfo($request->Id, $request->Obs);
            }else{

                $this->createItemPrestacionInfo($request->Id, $request->Obs);
            }

            $query->save();
        }
    }

    public function uploadAdjunto(Request $request)
    {

        $arr = [
            'efector' => [ArchivoEfector::max('Id') + 1, 'AEF', 'public/ArchivosEfectores'],
            'informador' => [ArchivoInformador::max('Id') + 1, 'AINF', 'public/ArchivosInformadores']
        ];

        if($request->hasFile('archivo')) {
            $fileName = $arr[$request->who][1].$arr[$request->who][0]. '_P'. $request->IdPrestacion .'.' . $request->archivo->extension();
            $request->archivo->storeAs($arr[$request->who][2], $fileName);
        }

        if($request->who === 'efector'){

            ArchivoEfector::create([
                'Id' => $arr[$request->who][0],
                'IdEntidad' => $request->IdEntidad,
                'Descripcion' => $request->Descripcion ?? '',
                'Ruta' => $fileName,
                'IdPrestacion' => $request->IdPrestacion,
                'Tipo' => 0
            ]);

            $this->updateEstado($request->who, $request->IdEntidad, $arr[$request->who][0], null);

        }elseif($request->who === 'informador'){

            ArchivoInformador::create([
                'Id' => $arr[$request->who][0],
                'IdEntidad' => $request->IdEntidad,
                'Descripcion' => $request->Descripcion ?? '',
                'Ruta' => $fileName,
                'IdPrestacion' => $request->IdPrestacion
            ]);

            $this->updateEstado($request->who, $request->IdEntidad, null, $arr[$request->who][0]);
        }
        
    }

    public function archivosAutomatico(Request $request)
    {
        $examenes = $request->Ids;

        if (!is_array($examenes)) {
            $examenes = [$examenes];
        }

        foreach ($examenes as $examen) {
            
            $item = ItemPrestacion::with('prestaciones')->where('Id', $examen)->first(['IdPrestacion', 'IdExamen']);
            
            if($item)
            {
                $archivo = $this->generarCodigo($item->IdPrestacion, $item->IdExamen, $item->prestaciones->IdPaciente);
                $ruta = self::RUTA.$archivo;
                $buscar = glob($ruta);

                if (count($buscar) === 1) {

                    copy($ruta, self::RUTA."AdjuntadasAuto/".$archivo);

                    $nuevoId = ArchivoEfector::max('Id') + 1;
                    $nuevoNombre = 'AEF'.$nuevoId.'_P'.$item->IdPrestacion.'.pdf';
                    $nuevaRuta = storage_path('app/public/ArchivosEfectores/'.$nuevoNombre);

                    ArchivoEfector::create([
                        'Id' => $nuevoId,
                        'IdEntidad' => $examen,
                        'Descripcion' => 'Se adjunto por automático',
                        'Ruta' => $nuevoNombre,
                        'IdPrestacion' => $item->IdPrestacion,
                        'Tipo' => '0'
                    ]);

                    $actualizarItem = ItemPrestacion::find($examen);
                    
                    if ($actualizarItem) {
                        $actualizarItem->CAdj = 5;
                        $actualizarItem->save();
                    }

                    copy($ruta, $nuevaRuta);
                    chmod($nuevaRuta, 0664);
                    Auditor::setAuditoria($item->IdPrestacion, 1, 36, Auth::user()->name);

                    $resultado = ["message" => "Se ha adjuntado automáticamente un archivo al exámen {$examen}", "estado" => "success"];
                    
                } elseif(count($buscar) > 1) {
                    
                    $resultado = ["message" => "En el examen {$examen} de la prestación, tiene un archivo duplicado. No se ha realizado la acción", "estado" => "fail"];
                
                }elseif(count($buscar) === 0) {

                    $resultado = ["message" => "No hay archivo con coincidencias para el exámen {$examen}", "estado" => "fail"];
                }

                $resultados[] = $resultado;

            }
        }

        return response()->json($resultados);
    }

    public function deleteIdAdjunto(Request $request)
    {

        if($request->Tipo === 'efector')
        {
            $adjunto = ArchivoEfector::find($request->Id);

            if ($adjunto) {
                $adjunto->delete();
                $this->updateEstado($request->Tipo, $request->ItemP, $request->Id, null);
            }
        
        }elseif($request->Tipo === 'informador')
        {

            $adj = ArchivoInformador::find($request->Id);

            if ($adj) {
                $adj->delete();
                $this->updateEstado($request->Tipo, $request->ItemP, null, $request->Id);
            }
        }       
    }

    public function replaceIdAdjunto(Request $request): void
    {

        $arr = [
            'efector' => [ArchivoEfector::find($request->Id), 'public/ArchivosEfectores'],
            'informador' => [ArchivoInformador::find($request->Id), 'public/ArchivosInformadores']
        ];
        
        if ($request->hasFile('archivo')) 
        {
            $query = $arr[$request->who][0];
            
            $fotoExistente = $arr[$request->who][1] ."/". $query->Ruta;
            if (Storage::exists($fotoExistente)) {
                Storage::delete($fotoExistente);
            }
            
            $filename = pathinfo($query->Ruta, PATHINFO_FILENAME). '.' . $request->archivo->extension();
            $request->archivo->storeAs($arr[$request->who][1],  $filename);

            $query->update(['Ruta' => $filename]);

        }

    }

    public function deleteEx(Request $request): void
    {
        $examenes = $request->Id;

        if (!is_array($examenes)) {
            $examenes = [$examenes];
        }

        foreach ($examenes as $examen) {

            $item = ItemPrestacion::find($examen);

            if ($item) {
                $item->delete();
            }
        }
    }
        
    public function getExamenes(Request $request): mixed
    {
        $resultados = Cache::remember('itemsprestaciones', 5, function () use ($request) {

            $query = ItemPrestacion::join('profesionales as efector', 'itemsprestaciones.IdProfesional', '=','efector.Id')
                ->join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
                ->join('prestaciones', 'itemsprestaciones.IdPrestacion', '=', 'prestaciones.Id')
                ->join('profesionales as informador', 'itemsprestaciones.IdProfesional2', '=', 'informador.Id')
                ->leftJoin('archivosefector', 'itemsprestaciones.Id', '=', 'archivosefector.IdEntidad')
                ->select(
                    'examenes.Nombre as Nombre',
                    'examenes.Id as IdExamen',
                    'examenes.Adjunto as ExaAdj',
                    'examenes.NoImprime as ExaNI',
                    'efector.Nombre as NombreE',
                    'efector.Apellido as ApellidoE',
                    'informador.Nombre as NombreI',
                    'informador.Apellido as ApellidoI',
                    'itemsprestaciones.Ausente as Ausente',
                    'itemsprestaciones.Forma as Forma',
                    'itemsprestaciones.Incompleto as Incompleto',
                    'itemsprestaciones.SinEsc as SinEsc',
                    'itemsprestaciones.Devol as Devol',
                    'itemsprestaciones.CAdj as CAdj',
                    'itemsprestaciones.CInfo as CInfo',
                    'itemsprestaciones.Id as IdItem',
                    'itemsprestaciones.Anulado as Anulado',
                    DB::raw('(SELECT COUNT(*) FROM archivosefector WHERE IdEntidad = itemsprestaciones.Id) as archivos')
                );                

            if ($request->tipo === 'listado' && is_array($request->IdExamen)) {

                    $query->whereIn('examenes.Id', $request->IdExamen)
                            ->where('itemsprestaciones.IdPrestacion', $request->Id);
            } 

            return $query->orderBy('efector.IdProveedor', 'ASC')
                         ->orderBy('ApellidoE', 'ASC')
                         ->orderBy('itemsprestaciones.Fecha', 'ASC')
                ->get();
        });
 
        return response()->json(['examenes' => $resultados]);
    }

    public function save(Request $request): void
    {

        $examenes = $request->idExamen;

        if (!is_array($examenes)) {
            $examenes = [$examenes];
        }

        foreach ($examenes as $examen) {
            
            $itemPrestacion = ItemPrestacion::where('IdPrestacion', $request->idPrestacion)->where('IdExamen', $examen)->first();

            if(!$itemPrestacion){

                ItemPrestacion::create([
                    'Id' => ItemPrestacion::max('Id') + 1,
                    'IdPrestacion' => $request->idPrestacion,
                    'IdExamen' => $examen,
                ]);
            }   
        }
    }

    public function check(Request $request): mixed
    {

        $examenes = ItemPrestacion::where('IdPrestacion', $request->Id)->get() ?? '';

        $idExamenes = [];

        foreach ($examenes as $examen) {
            $idExamenes[] = $examen->IdExamen;
        }

        return response()->json(['respuesta' => ! $examenes->isEmpty(), 'examenes' => $idExamenes]);
    }

    public function itemExamen(Request $request): void
    {
        $item = ItemPrestacion::find($request->Id);
        
        if($item){
          
            switch ($request->opcion) {
                case 'Incompleto':
                    $estado = $item->Incompleto;
                    $cambio = ($estado == 0 || $estado == null ? $item->Incompleto = 1 : $item->Incompleto = 0);
                    $item->Incompleto = $cambio;
                    break;
                
                case 'Ausente':
                    $estado = $item->Ausente;
                    $cambio = ($estado == 0 || $estado == null ? $item->Ausente = 1 : $item->Ausente = 0);
                    $item->Ausente = $cambio;
                    break;
               
                case 'Forma':
                    $estado = $item->Forma;
                    $cambio = ($estado == 0 || $estado == null ? $item->Forma = 1 : $item->Forma = 0);
                    $item->Forma = $cambio;
                    break;

                case 'SinEsc':
                    $estado = $item->SinEsc;
                    $cambio = ($estado == 0 || $estado == null ? $item->SinEsc = 1 : $item->SinEsc = 0);
                    $item->SinEsc = $cambio;
                    break;
                
                case 'Devol':
                    $estado = $item->Devol;
                    $cambio = ($estado == 0 || $estado == null ? $item->Devol = 1 : $item->Devol = 0);
                    $item->Devol = $cambio;
                    break;
            }

            $item->save();
        }
    }

    public function bloquearEx(Request $request)
    {
        $examenes = $request->Id;

        if (!is_array($examenes)) {
            $examenes = [$examenes];
        }

        foreach ($examenes as $examen) {

            $item = ItemPrestacion::find($examen);

            $item && $item->update(['Anulado' => 1]);
        }
    }

    public function asignarProfesional(Request $request): mixed
    {

        $examenes = $request->Ids;

        if (!is_array($examenes)) {
            $examenes = [$examenes];
        }

        foreach ($examenes as $examen) {
            
            $itemPrestacion = ItemPrestacion::where('Id', $examen)->first();

            $listado = [];

            if($itemPrestacion){

                $idProfesional = $request->IdProfesional ?? 0;
                $tipo = $request->tipo == 'asigEfector' ? 'IdProfesional' : 'IdProfesional2';
                $itemPrestacion->update([$tipo => $idProfesional]);
                array_push($listado, $examen);
            }
        }

        return response()->json(['message' => 'Se ha realizo la operación de manera correcta. Se actualizará la grilla']);

    }

    public function getBloqueo(Request $request)
    {
        $item = ItemPrestacion::where('Id', $request->Id)->first(['Anulado']);
        
        if($item->Anulado === 1)
        {
            return response()->json(['prestacion' => true]);
        }
    }

    private function generarCodigo(int $idprest, int $idex, int $idpac)
    {
        return 'A'.str_pad($idprest, 9, "0", STR_PAD_LEFT).str_pad($idex, 5, "0", STR_PAD_LEFT).str_pad($idpac, 7, "0", STR_PAD_LEFT).".pdf";
    }

}