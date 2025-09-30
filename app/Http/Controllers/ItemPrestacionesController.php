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
use App\Models\Examen;
use App\Models\ExamenCuentaIt;
use App\Models\ExamenPrecioProveedor;
use App\Traits\CheckPermission;
use App\Helpers\FileHelper;
use App\Helpers\Tools;
use App\Models\NotaCredito;
use App\Models\NotaCreditoIt;
use App\Models\Profesional;
use App\Models\Proveedor;
use App\Models\User;
use App\Services\Facturas\CheckFacturas;
use App\Services\ItemsPrestaciones\Helper;
use App\Services\ItemsPrestaciones\Crud;
use Illuminate\Support\Facades\Date;

class ItemPrestacionesController extends Controller
{
    use ObserverItemsPrestaciones, CheckPermission;

    private $ruta = '/var/IMPORTARPDF/SALIDA/';
    private $rutainf = '/var/IMPORTARPDF/SALIDAINFORMADOR/';
    private $rutainternaefectores = 'AdjuntosEfector';
    private $rutainternainfo = 'AdjuntosInformador';

    private $checkFacturas;
    private $itemsHelper;
    private $crud;

    public function __construct(
        CheckFacturas $checkFacturas,
        Helper $itemsHelper,
        Crud $crud
    ) {
        $this->checkFacturas = $checkFacturas;
        $this->itemsHelper = $itemsHelper;
        $this->crud = $crud;
    }

    public function edit(ItemPrestacion $itemsprestacione)
    {
        $data = $this->obtenerDatosItemPrestacion($itemsprestacione->Id);

        if (!$data) {
            return abort(404, 'No se encuentra la información solicitada');
        }
        return view('layouts.itemsprestaciones.edit', compact(['data']));
    }

    public function editModal(Request $request)
    {
        $data = $this->obtenerDatosItemPrestacion($request->Id);

        if (!$data) {
            return response()->json(['msg' => 'No se encuentra la información solicitada'], 400);
        }

        return response()->json($data, 200);
    }


    public function updateItem(Request $request)
    {
        $examenes = (array) $request->Id;

        $lstAbrir = [3 => 0, 4 => 1, 5 => 2];
        $lstCerrar = [0 => 3, 2 => 5, 1 => 4];

        $items = ItemPrestacion::with(['examenes.proveedor1','examenes.proveedor2','profesionales2'])->whereIn('Id', $examenes)->get();

        $resultados = [];

        foreach ($items as $item) {
  
            if ($request->Para === 'abrir') {  
                $item->CAdj = $lstAbrir[$item->CAdj] ?? $request->CAdj;

            } elseif ($request->Para === 'cerrar' ) {
                $item->CAdj = $lstCerrar[$item->CAdj] ?? $request->CAdj;
                $item->examenes->Informe === 0 && $item->profesionales2->InfAdj === 0 ? $item->CInfo = 0 : null;

            } elseif ($request->Para === 'cerrarI'){
                $item->CInfo = $request->CInfo;
            }

            $item->save();

            $resultados[] = [
                'data' => $item, 
                'adjuntoEfector' => $this->adjunto($item->Id, "Efector"), 
                'adjuntoInformador' => $this->adjunto($item->Id, "Informador")
            ];
        }

        return response()->json($resultados);

    }

    public function updateEstadoItem(Request $request)
    {
        $examenes = (array) $request->Ids;
        $lstAbrir = [3 => 0, 4 => 1, 5 => 2];

        $items = ItemPrestacion::with(['examenes', 'prestaciones', 'proveedores'])->whereIn('Id', $examenes)->get();

        $resultados = [];

        foreach($items as $item) {

            if ($item && $item->prestaciones->Cerrado === 0) {
                if (in_array($item->CAdj, [0, 1, 2])) {
                    $resultado = ['message' => 'El exámen no se encuentra cerrado', 'estado' => 'fail'];

                }else{
                    $item->update(['CAdj' => $lstAbrir[$item->CAdj]]);
                    $resultado = ['message' => 'Se ha realizado el cambio de estado al examen '.$item->proveedores->Nombre.' correctamente', 'estado' => 'success'];
                }
            } else {

                $resultado = ['message' => 'EL exámen ' . $item->examenes->Nombre . ' no se puede abrir porque la prestación se encuentra Cerrada', 'estado' => 'fail'];
            }

            $resultados[] = $resultado;
        }

        return response()->json($resultados);
    }

    public function liberarExamen(Request $request)
    {
        $examenes = (array) $request->Ids;

        $items = ItemPrestacion::with('prestaciones')->whereIn('Id', $examenes)->get();

        $resultados = [];

        foreach($items as $item) {

            if($item->prestaciones->Cerrado === 0) {  
               $resultados[] = ['message' => 'EL exámen no se puede liberar porque la prestación se encuentra cerrada', 'estado' => 'fail'];
                continue;
            }

            if($item->IdProfesional === 0) {
                $resultados[] = ['message' => 'No puede liberar porque el exámen no tiene efector asignado', 'estado' => 'fail'];
                continue;
            }

            if(in_array($item->CAdj, [3,4,5])) {
               $resultados[] = ['message' => 'No puede liberar porque el exámen se encuentra cerrado. Debe abrirlo antes', 'estado' => 'fail'];
                continue;
            }

            $item->update([
                'IdProfesional' => 0,
                'FechaAsignado' => '0000-00-00',
            ]);
            $resultados[] = ['message' => 'Se ha liberado al efector del examen '.$item->proveedores->Nombre.' correctamente', 'estado' => 'success'];
        }

        return response()->json($resultados);  
    }

    public function marcarExamenAdjunto(Request $request)
    {
        $examenes = (array) $request->Ids;

        $items = ItemPrestacion::with(['prestaciones', 'archivoEfector','examenes'])->whereIn('Id', $examenes)->get();

        $resultados = [];

        foreach($items as $item) {
        
            if($item->IdProfesional === 0) {
                $resultados[] = ['message' => 'EL exámen '.$item->proveedores->Nombre.' no tiene efector asignado', 'estado' => 'fail'];
                continue;
            }
            
            if($item->examenes->Adjunto == 0){
                $resultados[] = ['message' => 'EL exámen '.$item->proveedores->Nombre.' no se puede adjuntar porque el mismo no acepta adjuntos', 'estado' => 'fail'];
                continue;    
            }
            
            if($item && $item->prestaciones->Cerrado === 0 && is_array($item->archivosEfector) && count($item->archivosEfector) === 0) {    
                $resultados[] = ['message' => 'EL exámen '.$item->proveedores->Nombre.' no se puede adjuntar porque la prestación se encuentra Cerrada', 'estado' => 'fail'];
                continue;
            }

            $item->update(['CAdj' => 5,]);
            $resultados[] = ['message' => 'Se ha fijado como adjuntado el archivo aunque no posea un reporte adjuntado', 'estado' => 'success'];
        }
        return response()->json($resultados);
    }

    public function updateAsignado(Request $request)
    {
        $asignado = $request->Para === 'asignar' ? 'efector' : 'informador';

        $item = ItemPrestacion::with(['examenes', 'examenes.proveedor1', 'examenes.proveedor2', 'profesionales1', 'profesionales2'])->find($request->Id);

        if(!$item) {
            return response()->json(['msg' => 'No se ha podido actualizar el '. $asignado], 500);
        }
        
        $horaFin = $this->itemsHelper->HoraAsegundos(date("H:i:s")) + ($item->proveedores->Min * 60);
        $horaAsigFin = $this->itemsHelper->SegundosAminutos($horaFin).':00';

        if($request->Para === 'asignar'){

            $item->IdProfesional = $request->IdProfesional;
            $item->CAdj = ($request->IdProfesional === 0 && $item->examenes->Adjunto === 1) ? 1 : 2;

            $item->FechaAsignado = (($request->fecha == '0000-00-00' ||  $request->fecha == '0') ? '' : now()->format('Y-m-d'));
            $item->HoraAsignado = date("H:i:s");
            $item->HoraFAsignado = $horaAsigFin;

        }elseif($request->Para === 'asignarI'){
            $item->IdProfesional2 = $request->IdProfesional;

            $item->FechaAsignadoI = (($request->fecha == '0000-00-00' ||  $request->fecha == '0') ? '' : now()->format('Y-m-d'));
            $item->HoraAsignadoI = date("H:i:s");
            //$item->HoraFAsignado = $horaAsigFin;
            $item->CInfo = 1;
        }
        
        $item->save();
        $item->refresh();
        
        return response()->json(['msg' => 'Se ha actualizado el '. $asignado .' de manera correcta', 'data' => $item], 200);
        
    }

    public function updateAdjunto(Request $request):mixed 
    {
        $cadj = $request->CAdj ?? '';

        if(!empty($request->Id)) {
            return response()->json(['msg' => 'No se ha podido actualizar el efector'], 500);
        }

        ItemPrestacion::find($request->Id)->update(['CAdj' => $cadj]);

        return response()->json(['msg' => 'Se ha actualizado el efector de manera correcta'], 200);
    }

    public function paginacionGeneral(Request $request)
    {
        $Id = $request->Id;

        if ($request->tipo === 'efector') {

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
                    'itemsprestaciones.Anulado as Anulado'
                )
                ->where('archivosefector.IdEntidad', $Id)
                ->get();
        } else if ($request->tipo === 'informador') {

            $query = ItemPrestacion::join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
                ->leftJoin('archivosinformador', 'itemsprestaciones.Id', '=', 'archivosinformador.IdEntidad')
                ->join('proveedores', 'itemsprestaciones.IdProveedor', '=', 'proveedores.Id')
                ->select(
                    'archivosinformador.Id as IdI',
                    'archivosinformador.Ruta as Nombre',
                    'archivosinformador.Descripcion as DescripcionI',
                    'archivosinformador.Ruta as RutaI',
                    'examenes.Adjunto as Adjunto',
                    'itemsprestaciones.Anulado as Anulado'
                )
                ->where('archivosinformador.IdEntidad', $Id)
                ->get();
        }

        return response()->json(['resultado' => $query]);
    }

    public function paginacionByPrestacion(Request $request)
    {

        if($request->tipo === 'efector')
        {

            $query = ItemPrestacion::join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
            ->leftJoin('archivosefector', 'itemsprestaciones.Id', '=', 'archivosefector.IdEntidad')
            ->join('proveedores', 'itemsprestaciones.IdProveedor', '=', 'proveedores.Id')
            ->select(
                'archivosefector.Id as IdE',
                'archivosefector.IdEntidad as IdItem',
                'examenes.Nombre as NombreExamen',
                'archivosefector.Descripcion as DescripcionE',
                'archivosefector.Ruta as RutaE',
                'examenes.NoImprime as Adjunto',
                'proveedores.MultiE as MultiE',
                'itemsprestaciones.Anulado as Anulado',
                'itemsprestaciones.CAdj as CAdj'
            )
            ->where('archivosefector.IdPrestacion', $request->Id)
            // ->where('itemsprestaciones.IdProfesional', $request->IdProfesional)
            ->where('proveedores.Id', $request->especialidad)
            ->get();

        }else if($request->tipo === 'informador'){

            $query = ItemPrestacion::join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
                ->leftJoin('archivosinformador', 'itemsprestaciones.Id', '=', 'archivosinformador.IdEntidad')
                ->join('proveedores', 'itemsprestaciones.IdProveedor', '=', 'proveedores.Id')
                ->select(
                    'archivosinformador.Id as IdI',
                    'examenes.Nombre as NombreExamen',
                    'archivosinformador.Descripcion as DescripcionI',
                    'archivosinformador.Ruta as RutaI',
                    'examenes.Adjunto as Adjunto',
                    'itemsprestaciones.Anulado as Anulado'
                )
                ->where('archivosinformador.IdPrestacion', $request->Id)
                // ->where('itemsprestaciones.IdProfesional', $request->IdProfesional)
                ->where('proveedores.Id', $request->especialidad)
                ->get();

        }

        return response()->json(['resultado' => $query]);
    }

    public function updateExamen(Request $request):mixed
    {
        if(empty($request->Id)) {
            return response()->json(['msg' => 'No se han actualizado los datos. No se encuentra el identificador'], 500);
        }

        ItemPrestacion::where('Id', $request->Id)
            ->update([
                'Fecha' => $request->Fecha ?? '',
                'ObsExamen' => $request->ObsExamen ?? '',
                'IdProfesional2' => $request->Profesionales2 ?? ''
            ]
        );

        $prestacionInfo = ItemPrestacionInfo::where('IdIP' , $request->Id)->first();
        
        if($prestacionInfo) {
            $this->updateItemPrestacionInfo($request->Id, $request->Obs ?? '');
        }else{
            $this->createItemPrestacionInfo($request->Id, $request->Obs ?? '');
        }

        return response()->json(['msg' => 'Se han actualizado los datos correctamente', 'data' => $request->Id], 200);
    }

    public function uploadAdjunto(Request $request)
    {
        $result = null;
        $usuario = Auth::user()->name;
        $who = $request->who;

        $arr = [
            'efector' => [ArchivoEfector::max('Id') + 1, 'AEF', $this->rutainternaefectores, 'multiefector'],
            'informador' => [ArchivoInformador::max('Id') + 1, 'AINF', $this->rutainternainfo, 'multiInformador'],
        ];

        $arr['multiefector'] = &$arr['efector'];
        $arr['multiInformador'] = &$arr['informador'];

        // if($request->multi === 'success' && isset($arr[$who])) { 
        //     $who = $arr[3];
        // }
 
        if($request->hasFile('archivo')) {
            $fileName = $arr[$who][1].$arr[$who][0]. '_P'. $request->IdPrestacion .'.' . $request->archivo->extension();
            FileHelper::uploadFile(FileHelper::getFileUrl('escritura').'/'.$arr[$who][2].'/', $request->archivo, $fileName);
        }
        
        if(in_array($who, ['efector', 'informador'])){
            $this->registarArchivo(null, $request->IdEntidad, $request->Descripcion, $fileName, $request->IdPrestacion, $who);
            $this->updateEstado($who, $request->IdEntidad, $who === 'efector' ? $arr[$who][0] : null, $who === 'informador' ? $arr[$who][0] : null, null, null);
            Auditor::setAuditoria($request->IdPrestacion, 1, $who === 'efector' ? 36 : 37, $usuario);
        }
        
        if(in_array($who, ['multiefector', 'multiInformador'])){
            
            if($request->multi !== 'success'){
                $result = $this->multiArchivos($who, $request->IdPrestacion, $request->IdEntidad);
                foreach($result as $item) {
                    $this->registarArchivo(null, $item->Id, $request->Descripcion, $fileName, $request->IdPrestacion, $who);
                    $this->updateEstado($who, $item->Id, $who === 'multiefector' ? $arr[$who][0] : null, $who === 'multiInformador' ? $arr[$who][0] : null, 'multi', null);
                    Auditor::setAuditoria($request->IdPrestacion, 1, $who === 'multiefector' ? 36 : 37, $usuario); 
                }
            }
            
            if($request->multi === 'success'){
                $examenes = explode(',', $request->IdEntidad);

                foreach ($examenes as $examen) {

                    $item = ItemPrestacion::find($examen);
                    $this->registarArchivo(null, $examen, $request->Descripcion, $fileName, $item->IdPrestacion, $who);
                    $this->updateEstado(
                        $who, 
                        $examen, 
                        $who === 'multiefector' ? $arr[$who][0] : null, 
                        $who === 'multiInformador' ? $arr[$who][0] : null, 
                        'multi', 
                        $who === 'multiInformador' 
                            ? $request->anexoProfesional 
                            : ($who === 'multiefector' 
                                ? $request->anexoProfesional 
                                : null)) ;
                    Auditor::setAuditoria($item->IdPrestacion, 1, $who === 'efector' ? 36 : 37, $usuario); 
                }

                if (count($examenes) > 1) {
                    $this->marcarPrimeraCarga($request->Id, $request->who);
                }
            }
        }
    }

    public function archivosAutomatico(Request $request)
    {
        $examenes = (array) $request->Ids;

        if(empty($examenes)) {
            return response()->json(['msg' => 'No se han encontrado ids para subir archivos automaticamente'], 404);
        }

        $items = ItemPrestacion::with('prestaciones','proveedores')->whereIn('Id', $examenes)->first(['IdPrestacion', 'IdExamen', 'IdProveedor']);

        foreach ($items as $item) {
            $resultado = "";
            $item = ItemPrestacion::with('prestaciones','proveedores')->where('Id', $item->Id)->first(['IdPrestacion', 'IdExamen', 'IdProveedor']);

            if($item && $item->proveedores->Multi === 0)
            { 
                $archivo = $this->generarCodigo($item->IdPrestacion, $item->IdExamen, $item->prestaciones->IdPaciente);
                $ruta = $this->ruta . $archivo;
                $buscar = glob($ruta);

                if (count($buscar) === 1) {

                    copy($ruta, $this->ruta . "AdjuntadasAuto/" . $archivo);

                    $nuevoId = ArchivoEfector::max('Id') + 1;
                    $nuevoNombre = 'AEF' . $nuevoId . '_P' . $item->IdPrestacion . '.pdf';
                    $nuevaRuta = storage_path('app/public/ArchivosEfectores/' . $nuevoNombre);

                    $this->registarArchivo($nuevoId, $item->Id, "Cargado por automático", $nuevoNombre, $item->IdPrestacion, "efector");
                    ItemPrestacion::find($item->Id)->update(['CAdj' => 5]);
                    
                    copy($ruta, $nuevaRuta);
                    chmod($nuevaRuta, 0664);
                    Auditor::setAuditoria($item->IdPrestacion, 1, 36, Auth::user()->name);

                    $resultado = ["message" => "Se ha adjuntado automáticamente un archivo al exámen {$item->Id}", "estado" => "success"];
                
                }else {

                    $resultado = ["message" => "No hay archivo con coincidencias para el exámen {$item->Id}", "estado" => "fail"];
                }
            } else {

                $prestaciones = ItemPrestacion::where('IdPrestacion', $item->IdPrestacion)->get();

                if ($prestaciones) {

                    foreach ($prestaciones as $prestacion) {

                        $archivo = $this->generarCodigo($prestacion->IdPrestacion, $prestacion->IdExamen, $prestacion->prestaciones->IdPaciente);
                        $ruta = $this->ruta . $archivo;
                        $buscar = glob($ruta);
                        return response()->json($ruta);
                        exit;
                        if (count($buscar) === 1) {

                            copy($ruta, $this->ruta . "AdjuntadasAuto/" . $archivo);

                            $nuevoId = ArchivoEfector::max('Id') + 1;
                            $nuevoNombre = 'AEF' . $nuevoId . '_P' . $prestacion->IdPrestacion . '.pdf';
                            $nuevaRuta = storage_path('app/public/ArchivosEfectores/' . $nuevoNombre);

                            $actualizarItem = ItemPrestacion::where('IdPrestacion', $prestacion->IdPrestacion)->first();

                            if ($actualizarItem) {
                                $actualizarItem->CAdj = 5;
                                $actualizarItem->save();
                            }

                            copy($ruta, $nuevaRuta);
                            chmod($nuevaRuta, 0664);
                            Auditor::setAuditoria($prestacion->IdPrestacion, 1, 36, Auth::user()->name);

                            $resultado = ["message" => "Se ha adjuntado automáticamente un archivo a un MultiExamen {$examen}", "estado" => "success"];
                        } elseif (count($buscar) === 0) {

                            $resultado = ["message" => "No hay archivo con coincidencias para la prestacion {$prestacion->IdPrestacion} multi exámen", "estado" => "fail"];
                        }
                    }
                }
            }

            $resultados[] = $resultado;
        }

        return response()->json($resultados);
    }

    public function archivosAutomaticoI(Request $request)
    {
        $arr = [
            'efector' => [ArchivoEfector::max('Id') + 1, 'AEF', $this->rutainternaefectores],
            'informador' => [ArchivoInformador::max('Id') + 1, 'AINF', $this->rutainternainfo],
        ];

        $arr['multiefector'] = &$arr['efector'];
        $arr['multiInformador'] = &$arr['informador'];

        $examenes = (array) $request->Ids;

        $idsLaboratorios = Proveedor::where('Nombre', 'LIKE', 'LAB%')->pluck('Id')->toArray();
        $items = ItemPrestacion::with('prestaciones','examenes')->whereIn('Id', $examenes)->first();

        foreach ($items as $item) {
            $resultado = "";

            //Distinto a los laboratorios. Solo examenes sin MultiE
            if($item && $item->examenes->proveedor2->MultiE === 0 && !in_array($item->examenes->proveedor2->Id, $idsLaboratorios))
            {
                $archivo = $this->generarCodigo($item->IdPrestacion, $item->IdExamen, $item->prestaciones->IdPaciente);
                $ruta = $this->rutainf . $archivo;
                $buscar = glob($ruta);

                if (count($buscar) === 1) {

                    copy($ruta, $this->rutainf . "AdjuntadasAuto/" . $archivo);

                    $nuevoId = ArchivoEfector::max('Id') + 1;
                    $nuevoNombre = 'AEF' . $nuevoId . '_P' . $item->IdPrestacion . '.pdf';
                    $nuevaRuta = FileHelper::getFileUrl('lectura') . '/' . $this->rutainternaefectores . '/' . $nuevoNombre;

                    $this->registarArchivo($nuevoId, $item->Id, "Cargado por automático", $nuevoNombre, $item->IdPrestacion, "efector");
                    ItemPrestacion::find($item->Id)->update(['CInfo' => 3]);
                    
                    copy($ruta, $nuevaRuta);
                    chmod($nuevaRuta, 0664);
                    Auditor::setAuditoria($item->IdPrestacion, 1, 36, Auth::user()->name);

                    $resultado = ["message" => "Se ha adjuntado automáticamente un archivo al exámen {$item->Id}", "estado" => "success"];
                
                }else {

                    $resultado = ["message" => "No hay archivo con coincidencias para el exámen {$item->Id}", "estado" => "fail"];
                }
            } 
            
            //No son laboratorios pero si entra dentro de multi Efector. Multiples examenes con un mismo archivo
            if($item && $item->examenes->proveedor2->MultiE === 1 && !in_array($item->examenes->proveedor2->Id, $idsLaboratorios)) {

                $prestaciones = ItemPrestacion::where('IdPrestacion', $item->IdPrestacion)->get();

                if ($prestaciones) {

                    foreach ($prestaciones as $prestacion) {

                        $archivo = $this->generarCodigo($prestacion->IdPrestacion, $prestacion->IdExamen, $prestacion->prestaciones->IdPaciente);
                        $ruta = $this->rutainf . $archivo;
                        $buscar = glob($ruta);

                        if (count($buscar) === 1) {

                            copy($ruta, $this->rutainf . "AdjuntadasAuto/" . $archivo);

                                $nuevoId = ArchivoInformador::max('Id') + 1;
                                $nuevoNombre = 'AINF'.$nuevoId.'_P'.$prestacion->IdPrestacion.'.pdf';
                                $nuevaRuta = FileHelper::getFileUrl('lectura').'/'.$this->rutainternainfo.'/'.$nuevoNombre;
                            
                                $cat = ItemPrestacion::with(['examenes.proveedor2:Id'])->where('Id', $request->Ids)->first();
    
                                $exam = ItemPrestacion::with('examenes.proveedor2')
                                        ->where('itemsprestaciones.IdPrestacion', $request->IdPrestacion)
                                        ->whereHas('examenes.proveedor2', function ($query) use ($cat) {
                                            $query->where('Id', $cat->examenes->proveedor2->Id);
                                        })->get();
            
                                foreach($exam as $ex) {
                                    
                                    $this->registarArchivo(null, $ex->Id, $request->Descripcion, $nuevoNombre, $request->IdPrestacion, $request->who);
                                    $this->updateEstado($request->who, $ex->Id, null, $arr[$request->who][0], 'multi', null);
                                    Auditor::setAuditoria($request->IdPrestacion, 1, 37, Auth::user()->name);
                                }

                            copy($ruta, $nuevaRuta);
                            chmod($nuevaRuta, 0664);

                                $resultado = ["message" => "Se ha adjuntado automáticamente un archivo a un MultiExamen {$item->Id}", "estado" => "success"];

                            } elseif(count($buscar) === 0) {

                                $resultado = ["message" => "No hay archivo con coincidencias para la prestacion {$prestacion->IdPrestacion} multi exámen", "estado" => "fail"];
                            }

                        } 
                    }                       
            }
            
            if($item && $item->examenes->proveedor2->MultiE === 0 && in_array($item->examenes->proveedor2->Id, $idsLaboratorios)) {

                $formatFecha = str_replace('-', '', $item->Fecha);
                $archivoEncontrar = $formatFecha . '_' . $item->prestaciones->paciente->Documento;
                $ruta = $this->rutainf . $archivoEncontrar . '*.pdf';
                $buscar = glob($ruta);

                if (count($buscar) === 1) {

                    copy($ruta, $this->rutainf . "AdjuntadasAuto/" . $archivoEncontrar);

                    $nuevoId = ArchivoEfector::max('Id') + 1;
                    $nuevoNombre = 'AINF' . $nuevoId . '_P' . $item->IdPrestacion . '.pdf';
                    $nuevaRuta = FileHelper::getFileUrl('lectura') . '/' . $this->rutainternainfo . '/' . $nuevoNombre;

                    $this->registarArchivo($nuevoId, $item->Id, "Cargado por automático", $nuevoNombre, $item->IdPrestacion, "efector");
                    ItemPrestacion::find($item->Id)->update(['CInfo' => 3]);
                    
                    copy($ruta, $nuevaRuta);
                    chmod($nuevaRuta, 0664);
                    Auditor::setAuditoria($item->IdPrestacion, 1, 36, Auth::user()->name);

                    $resultado = ["message" => "Se ha adjuntado automáticamente un archivo al exámen {$item->Id}", "estado" => "success"];
                
                }else if(count($buscar) > 1) {
                
                    $resultado = ["message" => "Hay varios archivos {$item->Id} que tienen la misma fecha y dni. Verifique la carpeta por favor.", "estado" => "fail"];

                }else {

                    $resultado = ["message" => "No hay archivo con coincidencias para el exámen {$item->Id}", "estado" => "fail"];
                }
            
            }
            
            if($item && $item->examenes->proveedor2->MultiE === 1 && in_array($item->examenes->proveedor2->Id, $idsLaboratorios)) {
                
                $formatFecha = str_replace('-', '', $item->Fecha);
                $archivoEncontrar = $formatFecha . '_' . $item->prestaciones->paciente->Documento;
                $ruta = $this->rutainf . $archivoEncontrar . '*.pdf';
                $archivo = glob($ruta);

                if (count($archivo) === 1) {

                    $nuevaRuta = implode("", $archivo);
                    $filename = pathinfo($nuevaRuta, PATHINFO_FILENAME);

                    $nuevoDestino = $this->rutainf . "AdjuntadasAuto/" . $filename . '.pdf';
                    copy($nuevaRuta, $nuevoDestino);
                    chmod($nuevoDestino, 0664);

                    $nuevoId = ArchivoInformador::max('Id') + 1;
                    $nuevoNombre = 'AINF' . $nuevoId . '_P' . $item->IdPrestacion . '.pdf';
                    $dirStorage = FileHelper::getFileUrl('lectura') . '/' . $this->rutainternainfo . '/' . $nuevoNombre;

                    $cat = ItemPrestacion::with(['examenes.proveedor2:Id'])->where('Id', $request->Ids)->first();

                    $exam = ItemPrestacion::with('examenes.proveedor2')
                        ->where('itemsprestaciones.IdPrestacion', $request->IdPrestacion)
                        ->whereHas('examenes.proveedor2', function ($query) use ($cat) {
                            $query->where('Id', $cat->examenes->proveedor2->Id);
                        })->get();

                    foreach ($exam as $ex) {

                        $this->registarArchivo(null, $ex->Id, $request->Descripcion, $nuevoNombre, $request->IdPrestacion, $request->who);
                        $this->updateEstado($request->who, $ex->Id, null, $arr[$request->who][0], 'multi', null);
                        Auditor::setAuditoria($request->IdPrestacion, 1, 37, Auth::user()->name);
                    }

                    copy($nuevaRuta, $dirStorage);

                    $resultado = ["message" => "Se ha adjuntado automáticamente un archivo a un MultiExamen {$item->Id} xxxx", "estado" => "success"];
                    
                    
                }elseif(count($archivo) > 1) {

                    $resultado = ["message" => "Hay archivos duplicados para ese paciente. Verifique la carpeta de carga", "estado" => "fail"];
                } else {

                    $resultado = ["message" => "No hay archivo con coincidencias para el exámen {$item->Id}", "estado" => "fail"];
                }
            }

            $resultados[] = $resultado;
        }

        return response()->json($resultados);
    }

    public function deleteIdAdjunto(Request $request): mixed
    {    
        $examenes = (array) $request->Id;

        if ($request->multi === 'true') {

            $preExamen = $request->Tipo === 'efector'
                ? ArchivoEfector::find($request->Id, ['Ruta'])
                : ArchivoInformador::find($request->Id, ['Ruta']);

            if ($preExamen) {
                $examenes = $request->Tipo === 'efector'
                    ? ArchivoEfector::where('Ruta', $preExamen->Ruta)->pluck('Id')
                    : ArchivoInformador::where('Ruta', $preExamen->Ruta)->pluck('Id');
            } else {

                return response()->json(['msg' => 'No se encontró el registro para eliminar.'], 404);
            }
        }

        foreach ($examenes as $examen) {
            $adjunto = $request->Tipo === 'efector'
                ? ArchivoEfector::find($examen)
                : ArchivoInformador::find($examen);

            if ($adjunto instanceof \Illuminate\Database\Eloquent\Model) {
                try {
                    $adjunto->delete();
                    $this->updateEstado(
                        $request->Tipo,
                        $request->ItemP,
                        $request->Tipo === 'efector' ? $request->Id : null,
                        $request->Tipo === 'informador' ? $examen : null,
                        null,
                        null
                    );
                } catch (\Exception $e) {
                    return response()->json(['msg' => "Error al eliminar el adjunto: " . $e->getMessage()], 500);
                }
            } else {
                return response()->json(['msg' => "No se encontró el adjunto para ID: $examen"], 404);
            }
        }

        return response()->json(['msg' => 'Adjunto/s eliminados correctamente.'], 200);
    }


    public function replaceIdAdjunto(Request $request): mixed
    {

        $arr = [
            'efector' => [ArchivoEfector::find($request->Id), $this->rutainternaefectores],
            'informador' => [ArchivoInformador::find($request->Id), $this->rutainternainfo]
        ];

        if ($request->hasFile('archivo')) {
            $query = $arr[$request->who][0];

            $fotoExistente = $arr[$request->who][1] . "/" . $query->Ruta;
            if (Storage::exists($fotoExistente)) {
                Storage::delete($fotoExistente);
            }

            $filename = pathinfo($query->Ruta, PATHINFO_FILENAME) . '.' . $request->archivo->extension();
            FileHelper::uploadFile(FileHelper::getFileUrl('escritura') . '/' . $arr[$request->who][1] . '/', $request->archivo, $filename);

            $query->update(['Ruta' => $filename]);
            $data = ItemPrestacion::find($query->IdEntidad, ['Id', 'IdPrestacion']);

            return response()->json(['msg' => 'Se ha reemplazado el archivo de manera correcta. Se actualizará el contenido en unos segundos', 'data' => $data], 200);
        } else {
            return response()->json(['msg' => 'No se ha encontrado el archivo para reemplazar'], 500);
        }
    }

    public function deleteEx(Request $request)
    {
        $resultados = [];

        $examenIds = $request->Id;
        if (!is_array($examenIds)) {
            $examenIds = [$examenIds];
        }

        $examenIds = array_filter($examenIds, 'is_numeric');
        if (empty($examenIds)) {
            return response()->json(['msg' => 'No se proporcionaron IDs válidos.'], 400);
        }

        $items = ItemPrestacion::with(['prestaciones', 'examenes'])->whereIn('Id', $examenIds)->get();

        [$itemsConReglas, $itemsValidos] = $items->partition(function ($item) {
            return $item->prestaciones->Cerrado !== 0
                || $item->CInfo === 3
                || in_array($item->CAdj, [3, 5])
                || $item->IdProfesional !== 0
                || $item->IdProfesional2 !== 0
                || $this->adjuntoEfector($item->Id) === 1
                || $this->adjuntoInformador($item->Id) === 1;
        });

        foreach ($itemsConReglas as $item) {
            $resultados[] = ['id' => $item->Id, 'msg' => 'No se eliminó exámen ' . ($item->examenes->Nombre ?? '') . ' porque no cumple las condiciones (prestacion cerrada, examenes efectuados e informados, profesionales asignados o archivos adjuntos).', 'status' => 'warning'];
        }

        $idsParaEliminar = $itemsValidos->map(fn($itemValido) => $itemValido->Id);

        if (!empty($idsParaEliminar)) {
            $itemsAEliminar = $itemsValidos->whereIn('Id', $idsParaEliminar);
            $itemsAEliminar->map(fn($item) => ['IdPrestacion' => $item->IdPrestacion, 'IdExamen' => $item->IdExamen])->all();

            foreach ($itemsAEliminar as $item) {
                $this->deleteExaCuenta($item->IdPrestacion, $item->IdExamen);
            }

            $prestacionesIdsUnicos = $itemsAEliminar->pluck('IdPrestacion')->unique()->all();
            foreach ($prestacionesIdsUnicos as $prestacionId) {
                ItemPrestacion::InsertarVtoPrestacion($prestacionId);
            }

            ItemPrestacion::whereIn('Id', $idsParaEliminar)->delete();

            if (count($idsParaEliminar) > 0) {
                $resultados[] = ['msg' => count($idsParaEliminar) . ' examen(es) eliminado(s) correctamente.', 'status' => 'success'];
            }
        }

        if (empty($itemsConReglas) && empty($idsParaEliminar)) {
            $resultados[] = ['msg' => 'Ningún examen fue procesado o no se encontraron los IDs.', 'status' => 'info'];
        }

        return response()->json($resultados);
    }

    public function bloquearEx(Request $request)
    {
        $examenes = (array) $request->Id;

        $items = ItemPrestacion::with(['prestaciones','examenes'])->whereIn('Id', $examenes)->get();

        $resultados = [];

        foreach ($items as $item) {

            if ($item->Anulado === 1) {

                $resultado = ['message' => 'No se bloqueo el exámen ' . $item->examenes->Nombre . ' porque el mismo ya se encuentra en ese estado', 'estado' => 'fail'];
            } elseif ($item && ($item->prestaciones->Cerrado === 1)) {

                $resultado = ['message' => 'No se bloqueo el exámen ' . $item->examenes->Nombre . ' porque la prestación se encuentra cerrada ', 'estado' => 'fail'];
            } else {
                $fechaHoy  = Date::now()->format('Y-m-d');
                $item->update(['Anulado' => 1]);
                $item->update(['FechaAnulado' => $fechaHoy]);

                ItemPrestacion::InsertarVtoPrestacion($item->IdPrestacion);
                $resultado = ['message' => 'Se ha bloqueado con éxito el exámen de ' . $item->examenes->Nombre . '', 'estado' => 'success'];
            }
            
        }

        return response()->json($resultados);
    }

    public function getExamenesStd(Request $request)
    {
        if (!$this->hasPermission("prestaciones_edit")) {
            return response()->json(['msg' => 'No tienes permisos'], 403);
        }

        return DB::Select("CALL getExamenesEstandar(?,?)", [intval($request->Id), $request->tipo]);
    }

    public function getExamenes(Request $request)
    {
        if (!$this->hasPermission("prestaciones_edit")) {
            return response()->json(['msg' => 'No tienes permisos'], 403);
        }

        return DB::Select("CALL getExamenes(?,?)", [$request->Id, $request->tipo]);
    }

    public function show() {}

    public function save(Request $request): void
    {
        $examenes = (array) $request->idExamen;

        $this->crud->create($examenes, $request->idPrestacion, $request->idExaCta);
    }

    public function itemExamen(Request $request)
    {
        if (!$this->hasPermission("prestaciones_edit")) {
            return response()->json(['msg' => 'No tienes permisos'], 403);
        }

        $item = ItemPrestacion::find($request->Id);

        if ($item) {

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

            return response()->json(['msg' => 'Se ha actualizado el estado del exámen de manera correcta'], 200);
        }
    }


    public function asignarProfesional(Request $request): mixed
    {
        $examenes = (array) $request->Ids;
        $idProfesional = $request->IdProfesional ?? 0;
        $tipo = $request->tipo == 'asigEfector' ? 'IdProfesional' : 'IdProfesional2';
        $usuario = Auth::user()->name;

        if(empty($examenes)) {
            return response()->json(['message' => 'No hay examenes para asignar al profesional'], 409);
        }

        $filtroExamenes = ItemPrestacion::whereIn('Id', $examenes)->pluck('Id');
        ItemPrestacion::whereIn('Id', $filtroExamenes)->update([$tipo => $idProfesional]);

        foreach($filtroExamenes as $examen) {
                Auditor::setAuditoria($examen, 1, 34, $usuario);
        }
        return response()->json(
            ['message' => 'Se ha realizo la operación de manera correcta. Se actualizará la grilla'], 200);

    }

    public function getBloqueo(Request $request)
    {
        $item = ItemPrestacion::where('Id', $request->Id)->first(['Anulado']);
        return $item->Anulado === 1 ? response()->json(['prestacion' => true]) : null;
    }

    public function lstExamenes(Request $request)
    {
        $items = ItemPrestacion::join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
            ->select('examenes.Nombre as NombreExamen')
            ->where('itemsprestaciones.IdPrestacion', $request->Id)
            ->orderBy('examenes.Nombre')
            ->get();

        return response()->json($items);
    }

    public function preExamenes(Request $request): mixed
    {
        $examenes = (array) $request->Id;

        $listado = ExamenCuentaIt::join('examenes', 'pagosacuenta_it.IdExamen', '=', 'examenes.Id')
            ->join('proveedores', 'examenes.IdProveedor', '=', 'proveedores.Id')
            ->select(
                'examenes.Nombre as NombreExamen',
                'proveedores.Nombre as Especialidad',
                'examenes.DiasVencimiento as diasVencer',
                'pagosacuenta_it.Id as IdEx'
            )
            ->whereIn('pagosacuenta_it.Id', $examenes)
            ->get();

        return response()->json($listado);
    }

    public function checkAdjunto(Request $request)
    {
        $resultado = '';
        $query = ItemPrestacion::find($request->Id);

       if ($request->Tipo === 'efector') 
       {
            $resultado = $this->adjunto($request->Id, "Efector") && $query->examenes->Adjunto === 1; //true es que falta adjuntar el archivo

       }else{
            $resultado = $this->adjunto($request->Id, "Informador") && $query->profesionales2->InfAdj === 1;
       }

        return response()->json($resultado);
    }

    public function checkPrimeraCarga(Request $request)
    {
        $query = $request->who === 'efector'
            ? ArchivoEfector::join('itemsprestaciones', 'archivosefector.IdEntidad', '=', 'itemsprestaciones.Id')->join('proveedores', 'itemsprestaciones.IdProveedor', '=', 'proveedores.Id')->where('archivosefector.IdPrestacion', $request->Id)->where('proveedores.Multi', 1)->where('archivosefector.PuntoCarga', 1)->pluck('itemsprestaciones.Id')
            : ArchivoInformador::join('itemsprestaciones', 'archivosinformador.IdEntidad', '=', 'itemsprestaciones.Id')->join('proveedores', 'itemsprestaciones.IdProveedor', '=', 'proveedores.Id')->where('itemsprestaciones.IdPrestacion', $request->Id)->where('proveedores.MultiE', 1)->where('archivosinformador.PuntoCarga', 1)->pluck('itemsprestaciones.Id');

        return response()->json($query);
    }

    public function contadorExamenes(Request $request)
    {
        $query = ItemPrestacion::where('IdPrestacion', $request->Id)->count();
        return response()->json($query);
    }

    public function checkFacturaItemPrestacion(Request $request)
    {
        $examenCuenta = $this->checkFacturas->examenCuenta($request->IdPrestacion, $request->IdExamen);
        $facturaVenta = $this->checkFacturas->facturaDeVenta(($request->IdPrestacion));

        if ($examenCuenta) {
            return response()->json(['data' => $examenCuenta, 'tipo' => 'examenCuenta']);
        } elseif ($facturaVenta) {
            return response()->json(['data' => $facturaVenta, 'tipo' => 'facturaDeVenta']);
        }
    }

    private function generarCodigo(int $idprest, int $idex, int $idpac)
    {
        return 'A' . str_pad($idprest, 9, "0", STR_PAD_LEFT) . str_pad($idex, 5, "0", STR_PAD_LEFT) . str_pad($idpac, 7, "0", STR_PAD_LEFT) . ".pdf";
    }


    private function deleteExaCuenta($prestacion, $examen)
    {
        return ExamenCuentaIt::where('IdPrestacion', $prestacion)
            ->where('IdExamen', $examen)
            ->update(['IdPrestacion' => 0]);
    }

    //Tipo: efector, informador
    private function getProfesional(int $id)
    {
        $query = Profesional::leftJoin('users', 'profesionales.Id', '=', 'users.profesional_id')
            ->leftJoin('datos', 'users.datos_id', '=', 'datos.Id')
            ->select(
                'profesionales.Id as Id',
                'profesionales.Nombre as NombreProfesional',
                'profesionales.Apellido as ApellidoProfesional',
                'datos.Nombre as NombreDatos',
                'datos.Apellido as ApellidoDatos',
                'profesionales.RegHis as RegHis',
                'users.profesional_id as userProfesional'
            )->find($id);

        if ($query) {
            return collect(
                (object) [
                    'id' => $query->Id,
                    'NombreCompleto' => $query->RegHis === 1
                        ? $query->ApellidoProfesional . " " . $query->NombreProfesional
                        : (in_array($query->userProfesional, [0, '', null], true) ? '' : $query->ApellidoDatos . " " . $query->NombreDatos)
                ]
            );
        }
    }

    private function getNotaCredito(int $id): mixed
    {

        $item_nota_credito = NotaCreditoIt::where('IdIP', $id)->first();
        if ($item_nota_credito) {
            $notaCredito = NotaCredito::where('Id', $item_nota_credito->IdNC)->first();
        } else {
            $notaCredito = null;
        }

        return $notaCredito;
    }

    private function marcarPrimeraCarga(int $id, string $who): void
    {
        if ($who === 'multiefector') {

            $query = ArchivoEfector::where('IdEntidad', $id)->first();
            if ($query) {
                $query->PuntoCarga = 1;
                $query->save();
            }
        }

        if ($who === 'multiInformador') {
            $query = ArchivoInformador::where('IdEntidad', $id)->first();

            if ($query) {
                $query->PuntoCarga = 1;
                $query->save();
            }
        }
    }

    private function obtenerDatosItemPrestacion(int $id): array
    {
        $query = ItemPrestacion::with(['prestaciones', 'examenes', 'examenes.proveedor1', 'examenes.proveedor2', 'profesionales1', 'profesionales2', 'itemsInfo', 'notaCreditoIt.notaCredito', 'facturadeventa'])->find($id);
        $data = null;

        if ($query) {
            $paciente = $this->getPaciente($query->IdPrestacion);

            $data = [
                'itemprestacion' => $query,
                'paciente' => $paciente,
                'qrTexto' => Tools::generarQR('A', $query->IdPrestacion, $query->IdExamen, $paciente->Id, 'texto'),
                'adjuntoEfector' => $this->adjunto($query->Id, "Efector"),
                'adjuntoInformador' => $this->adjunto($query->Id, "Informador"),
                'multiEfector' => $this->multiEfector($query->IdPrestacion, $query->IdProfesional, $query->examenes->IdProveedor),
                'multiInformador' => $this->multiInformador($query->IdPrestacion, $query->IdProfesional2, $query->examenes->IdProveedor2),
                'efectores' => $this->getProfesional($query->IdProfesional),
                'informadores' => $this->getProfesional($query->IdProfesional2),
                'notacredito' => $this->getNotaCredito($id)
            ];
        }

        return $data;
    }

    private function multiArchivos(string $who, int $idPrestacion, int $idItemprestacion): mixed
    {
        $cat = $who === 'multiefector'
                    ? ItemPrestacion::with(['examenes.proveedor1:Id'])->where('Id', $idItemprestacion)->first()
                    : ItemPrestacion::with(['examenes.proveedor2:Id'])->where('Id', $idItemprestacion)->first();
    
        $exam = ItemPrestacion::with(['examenes.proveedor1', 'examenes.proveedor2'])
                ->where('itemsprestaciones.IdPrestacion', $idPrestacion);

        $exam->when($who === 'multiefector', function($exam) use ($cat) {
            $exam->whereHas('examenes.proveedor1', function ($query) use ($cat) {
                $query->where('Id', $cat->examenes->proveedor1->Id);
            });
        });

        $exam->when($who === 'multiInformador', function($exam) use ($cat){
            $exam->whereHas('examenes.proveedor2', function ($query) use ($cat) {
                $query->where('Id', $cat->examenes->proveedor2->Id);
            });
        });
                
        return $exam->get();
    }


}
