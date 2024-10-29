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
use App\Models\Profesional;
use App\Models\User;

class ItemPrestacionesController extends Controller
{
    use ObserverItemsPrestaciones, CheckPermission;

    private $ruta = '/var/IMPORTARPDF/SALIDA/';
    private $rutainf = '/var/IMPORTARPDF/SALIDAINFORMADOR/';
    private $rutainternaefectores = 'AdjuntosEfector';
    private $rutainternainfo = 'AdjuntosInformador';
    
    public function edit(ItemPrestacion $itemsprestacione): mixed
    {
        $data = $this->obtenerDatosItemPrestacion($itemsprestacione->Id);
        
        if (!$data) {
            return abort(404, 'No se encuentra la información solicitada');
        }
    
        return view('layouts.itemsprestaciones.edit', compact(['itemsprestacione'] + $data));
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
        $examenes = $request->Id;

        $lstAbrir = [3 => 0, 4 => 1, 5 => 2];
        $lstCerrar = [0 => 3, 2 => 5, 1 => 4];

        if (!is_array($examenes)) {
            $examenes = [$examenes];
        }

        foreach ($examenes as $examen) {
            $item = ItemPrestacion::with(['examenes.proveedor1','examenes.proveedor2','profesionales2'])->find($examen);

            if ($item) 
            {   
                if ($request->Para === 'abrir')
                {  
                    $item->CAdj = $lstAbrir[$item->CAdj] ?? $request->CAdj;

                } elseif ($request->Para === 'cerrar' ) {

                    $item->CAdj = $lstCerrar[$item->CAdj] ?? $request->CAdj;
                    $item->examenes->Informe === 0 && $item->profesionales2->InfAdj === 0 ? $item->CInfo = 0 : null;

                } elseif ($request->Para === 'cerrarI'){

                    $item->CInfo = $request->CInfo;
                
                }
                $item->save();
                $item->refresh();
            
            }

            $adjuntoEfector = $this->adjuntoEfector($item->Id);
            $adjuntoInformador = $this->adjuntoInformador($item->Id);

            return response()->json(['data' => $item, 'adjuntoEfector' => $adjuntoEfector, 'adjuntoInformador' => $adjuntoInformador]);

        }

    }

    public function updateEstadoItem(Request $request)
    {
        $examenes = $request->Ids;
        
        $lstAbrir = [3 => 0, 4 => 1, 5 => 2];

        if (!is_array($examenes)) {
            $examenes = [$examenes];
        }

        foreach($examenes as $examen) {

            $item = ItemPrestacion::with(['examenes', 'prestaciones'])->find($examen);

            if ($item && $item->prestaciones->Cerrado === 0) 
            {   
                if(in_array($item->CAdj, [0,1,2]))
                {
                    $resultado = ['message' => 'El exámen no se encuentra cerrado', 'estado' => 'fail'];

                }else{
                    $item->CAdj = $lstAbrir[$item->CAdj];
                    $item->save();
                    $resultado = ['message' => 'Se ha realizado el cambio de estado al examen '.$item->proveedores->Nombre.' correctamente', 'estado' => 'success'];
                }
            } else {

                $resultado = ['message' => 'EL exámen '.$item->examenes->Nombre.' no se puede abrir porque la prestación se encuentra Cerrada', 'estado' => 'fail'];
            }

            $resultados[] = $resultado;
        }

        return response()->json($resultados);
    }

    public function liberarExamen(Request $request)
    {
        $examenes = $request->Ids;

        if (!is_array($examenes)) {
            $examenes = [$examenes];
        }

        foreach($examenes as $examen) {

            $item = ItemPrestacion::with('prestaciones')->find($examen);

            if ($item && $item->prestaciones->Cerrado === 0) 
            {  
                if($item->IdProfesional === 0)
                {
                    $resultado = ['message' => 'No puede liberar porque el exámen no tiene efector asignado', 'estado' => 'fail'];

                }elseif(in_array($item->CAdj, [3,4,5])) {

                    $resultado = ['message' => 'No puede liberar porque el exámen se encuentra cerrado. Debe abrirlo antes', 'estado' => 'fail'];

                }else{

                    $item->IdProfesional = 0;
                    $item->FechaAsignado = '0000-00-00';
                    $item->save();
                    $resultado = ['message' => 'Se ha liberado al efector del examen '.$item->proveedores->Nombre.' correctamente', 'estado' => 'success'];
                }

            }else{

                $resultado = ['message' => 'EL exámen no se puede liberar porque la prestación se encuentra cerrada', 'estado' => 'fail'];
            }

            $resultados[] = $resultado;
        }
        return response()->json($resultados);
        
    }

    public function marcarExamenAdjunto(Request $request)
    {
        $examenes = $request->Ids;

        if (!is_array($examenes)) {
            $examenes = [$examenes];
        }

        foreach($examenes as $examen) {
        
            $item = ItemPrestacion::with(['prestaciones', 'archivoEfector','examenes'])->find($examen);

            if($item->IdProfesional === 0) 
            {
                $resultado = ['message' => 'EL exámen '.$item->proveedores->Nombre.' no tiene efector asignado', 'estado' => 'fail'];
            
            }elseif($item->examenes->Adjunto == 0){

                $resultado = ['message' => 'EL exámen '.$item->proveedores->Nombre.' no se puede adjuntar porque el mismo no acepta adjuntos', 'estado' => 'fail'];
                  
            }elseif($item && $item->prestaciones->Cerrado === 0 && is_array($item->archivosEfector) && count($item->archivosEfector) === 0) {
                
                $resultado = ['message' => 'EL exámen '.$item->proveedores->Nombre.' no se puede adjuntar porque la prestación se encuentra Cerrada', 'estado' => 'fail'];

            }else{

                $item->CAdj = 5;
                $item->save();
                $resultado = ['message' => 'Se ha fijado como adjuntado el archivo aunque no posea un reporte adjuntado', 'estado' => 'success'];
            }
            $resultados[] = $resultado;
        }
        
        return response()->json($resultados);
    }

    public function updateAsignado(Request $request)
    {

        $item = ItemPrestacion::with(['examenes','examenes.proveedor1', 'examenes.proveedor2','profesionales1', 'profesionales2'])->find($request->Id);

        $asignado = $request->Para === 'asignar' ? 'efector' : 'informador';

        if($item)
        {
            $horaFin = $this->HoraAsegundos(date("H:i:s")) + ($item->proveedores->Min * 60);
            $horaAsigFin = $this->SegundosAminutos($horaFin).':00';

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
        }else{
            return response()->json(['msg' => 'No se ha podido actualizar el '. $asignado], 500);
        }
    }

    public function updateAdjunto(Request $request):mixed 
    {

        $adjunto = ItemPrestacion::find($request->Id);

        if ($adjunto)
        {   
            $adjunto->CAdj = $request->CAdj ?? '';
            $adjunto->save();
            
            return response()->json(['msg' => 'Se ha actualizado el efector de manera correcta'], 200);
        }else{
            return response()->json(['msg' => 'No se ha podido actualizar el efector'], 500);
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
                'itemsprestaciones.Anulado as Anulado'
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
                    'itemsprestaciones.Anulado as Anulado'
                )
                ->where('archivosinformador.IdEntidad', $Id)
                ->get();

        }

        return response()->json(['resultado' => $query]);
    }

    public function updateExamen(Request $request):mixed
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
            $query->refresh();
            return response()->json(['msg' => 'Se han actualizado los datos correctamente', 'data' => $query], 200);

        }else{
            return response()->json(['msg' => 'No se han actualizado los datos. No se encuentra el identificador'], 500);
        }
    }

    public function uploadAdjunto(Request $request)
    {
        $result = null;

        $who = $request->who;
        if ($request->who === 'efector' && $request->multi === 'success') {
            $who = 'multiefector';
        } elseif ($request->who === 'informador' && $request->multi === 'success') {
            $who = 'multiInformador';
        }

        $arr = [
            'efector' => [ArchivoEfector::max('Id') + 1, 'AEF', $this->rutainternaefectores],
            'informador' => [ArchivoInformador::max('Id') + 1, 'AINF', $this->rutainternainfo],
            'multiefector'  => [ArchivoEfector::max('Id') + 1, 'AEF', $this->rutainternaefectores],
            'multiInformador' => [ArchivoInformador::max('Id') + 1, 'AINF', $this->rutainternainfo],
        ];
        
        $arr['multiefector'] = &$arr['efector'];
        $arr['multiInformador'] = &$arr['informador'];

        if($request->hasFile('archivo')) {
            $fileName = $arr[$who][1].$arr[$who][0]. '_P'. $request->IdPrestacion .'.' . $request->archivo->extension();
            FileHelper::uploadFile(FileHelper::getFileUrl('escritura').'/'.$arr[$who][2].'/', $request->archivo, $fileName);
        }
        
        if(in_array($who, ['efector', 'informador'])){

            $this->registarArchivo(null, $request->IdEntidad, $request->Descripcion, $fileName, $request->IdPrestacion, $who);
            $this->updateEstado($who, $request->IdEntidad, $who === 'efector' ? $arr[$who][0] : null, $who === 'informador' ? $arr[$who][0] : null, null, null);
            Auditor::setAuditoria($request->IdPrestacion, 1, $who === 'efector' ? 36 : 37, Auth::user()->name);
        
        }elseif(in_array($who, ['multiefector', 'multiInformador'])){
            
            if($request->multi !== 'success'){

                $cat = $who === 'multiefector'
                                ? ItemPrestacion::with(['examenes.proveedor1:Id'])->where('Id', $request->IdEntidad)->first()
                                : ItemPrestacion::with(['examenes.proveedor2:Id'])->where('Id', $request->IdEntidad)->first();
                
                $exam = ItemPrestacion::with(['examenes.proveedor1', 'examenes.proveedor2'])
                        ->where('itemsprestaciones.IdPrestacion', $request->IdPrestacion);

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
                        
                $result = $exam->get();
                foreach($result as $item) {
                    
                    $this->registarArchivo(null, $item->Id, $request->Descripcion, $fileName, $request->IdPrestacion, $who);
                    $this->updateEstado($who, $item->Id, $who === 'multiefector' ? $arr[$who][0] : null, $who === 'multiInformador' ? $arr[$who][0] : null, 'multi', null);
                    Auditor::setAuditoria($request->IdPrestacion, 1, $who === 'multiefector' ? 36 : 37, Auth::user()->name);
                    
                }
            }elseif($request->multi === 'success'){
  
                $examenes = explode(',', $request->IdEntidad);
            
                foreach($examenes as $examen){

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
                    Auditor::setAuditoria($item->IdPrestacion, 1, $who === 'efector' ? 36 : 37, Auth::user()->name); 
                }

                if(count($examenes) > 1) {
                    $this->marcarPrimeraCarga($request->Id, $request->who);
                }
                
                   
            }
        }
    }

    public function archivosAutomatico(Request $request)
    {
        $examenes = $request->Ids;

        if (!is_array($examenes)) {
            $examenes = [$examenes];
        }

        foreach ($examenes as $examen) {
            $resultado = "";
            $item = ItemPrestacion::with('prestaciones','proveedores')->where('Id', $examen)->first(['IdPrestacion', 'IdExamen', 'IdProveedor']);

            if($item && $item->proveedores->Multi === 0)
            {
               
                $archivo = $this->generarCodigo($item->IdPrestacion, $item->IdExamen, $item->prestaciones->IdPaciente);
                $ruta = $this->ruta.$archivo;
                $buscar = glob($ruta);
                
                if (count($buscar) === 1) {

                    copy($ruta, $this->ruta."AdjuntadasAuto/".$archivo);

                    $nuevoId = ArchivoEfector::max('Id') + 1;
                    $nuevoNombre = 'AEF'.$nuevoId.'_P'.$item->IdPrestacion.'.pdf';
                    $nuevaRuta = storage_path('app/public/ArchivosEfectores/'.$nuevoNombre);

                    $this->registarArchivo($nuevoId, $examen, "Cargado por automático", $nuevoNombre, $item->IdPrestacion, "efector");

                    $actualizarItem = ItemPrestacion::find($examen);
                    
                    if ($actualizarItem) {
                        $actualizarItem->CAdj = 5;
                        $actualizarItem->save();
                    }

                    copy($ruta, $nuevaRuta);
                    chmod($nuevaRuta, 0664);
                    Auditor::setAuditoria($item->IdPrestacion, 1, 36, Auth::user()->name);

                    $resultado = ["message" => "Se ha adjuntado automáticamente un archivo al exámen {$examen}", "estado" => "success"];
                
                }else {

                    $resultado = ["message" => "No hay archivo con coincidencias para el exámen {$examen}", "estado" => "fail"];
                }

            } else {

                $prestaciones = ItemPrestacion::where('IdPrestacion', $item->IdPrestacion)->get();

                if ($prestaciones) {

                    foreach($prestaciones as $prestacion) {

                        $archivo = $this->generarCodigo($prestacion->IdPrestacion, $prestacion->IdExamen, $prestacion->prestaciones->IdPaciente);
                        $ruta = $this->ruta.$archivo;
                        $buscar = glob($ruta);
                        return response()->json($ruta);exit;
                        if (count($buscar) === 1) {

                            copy($ruta, $this->ruta."AdjuntadasAuto/".$archivo);

                            $nuevoId = ArchivoEfector::max('Id') + 1;
                            $nuevoNombre = 'AEF'.$nuevoId.'_P'.$prestacion->IdPrestacion.'.pdf';
                            $nuevaRuta = storage_path('app/public/ArchivosEfectores/'.$nuevoNombre);

                            $actualizarItem = ItemPrestacion::where('IdPrestacion', $prestacion->IdPrestacion)->first();
                        
                            if ($actualizarItem) {
                                $actualizarItem->CAdj = 5;
                                $actualizarItem->save();
                            }

                            copy($ruta, $nuevaRuta);
                            chmod($nuevaRuta, 0664);
                            Auditor::setAuditoria($prestacion->IdPrestacion, 1, 36, Auth::user()->name);

                            $resultado = ["message" => "Se ha adjuntado automáticamente un archivo a un MultiExamen {$examen}", "estado" => "success"];

                        } elseif(count($buscar) === 0) {

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
            'multiefector'  => [ArchivoEfector::max('Id') + 1, 'AEF', $this->rutainternaefectores],
            'multiInformador' => [ArchivoInformador::max('Id') + 1, 'AINF', $this->rutainternainfo],
        ];

        $examenes = $request->Ids;

        if (!is_array($examenes)) {
            $examenes = [$examenes];
        }

        foreach ($examenes as $examen) {
            $resultado = "";
            $item = ItemPrestacion::with('prestaciones','examenes')->where('Id', $examen)->first();

            //Distinto a los laboratorios. Solo examenes sin MultiE
            if($item && $item->examenes->proveedor2->MultiE === 0 && !in_array($item->examenes->proveedor2->Id, [3,38,23,36,39]))
            {
                $archivo = $this->generarCodigo($item->IdPrestacion, $item->IdExamen, $item->prestaciones->IdPaciente);
                $ruta = $this->rutainf.$archivo;
                $buscar = glob($ruta);

                if (count($buscar) === 1) {

                    copy($ruta, $this->rutainf."AdjuntadasAuto/".$archivo);

                    $nuevoId = ArchivoEfector::max('Id') + 1;
                    $nuevoNombre = 'AEF'.$nuevoId.'_P'.$item->IdPrestacion.'.pdf';
                    $nuevaRuta = FileHelper::getFileUrl('lectura').'/'.$this->rutainternaefectores.'/'.$nuevoNombre;

                    $this->registarArchivo($nuevoId, $examen, "Cargado por automático", $nuevoNombre, $item->IdPrestacion, "efector");

                    $actualizarItem = ItemPrestacion::find($examen);
                    
                    if ($actualizarItem) {
                        $actualizarItem->CInfo = 3;
                        $actualizarItem->save();
                    }

                    copy($ruta, $nuevaRuta);
                    chmod($nuevaRuta, 0664);
                    Auditor::setAuditoria($item->IdPrestacion, 1, 36, Auth::user()->name);

                    $resultado = ["message" => "Se ha adjuntado automáticamente un archivo al exámen {$examen}", "estado" => "success"];
                
                }else {

                    $resultado = ["message" => "No hay archivo con coincidencias para el exámen {$examen}", "estado" => "fail"];
                }

            //No son laboratorios pero si entra dentro de multi Efector. Multiples examenes con un mismo archivo
            } elseif($item && $item->examenes->proveedor2->MultiE === 1 && !in_array($item->examenes->proveedor2->Id, [3,38,23,36,39])) {

                $prestaciones = ItemPrestacion::where('IdPrestacion', $item->IdPrestacion)->get();
                
                    if ($prestaciones) {

                        foreach($prestaciones as $prestacion) {

                            $archivo = $this->generarCodigo($prestacion->IdPrestacion, $prestacion->IdExamen, $prestacion->prestaciones->IdPaciente);
                            $ruta = $this->rutainf.$archivo;
                            $buscar = glob($ruta);

                            if (count($buscar) === 1) {

                                copy($ruta, $this->rutainf."AdjuntadasAuto/".$archivo);

                                $nuevoId = ArchivoInformador::max('Id') + 1;
                                $nuevoNombre = 'AINF'.$nuevoId.'_P'.$prestacion->IdPrestacion.'.pdf';
                                $nuevaRuta = FileHelper::getFileUrl('lectura').'/'.$this->rutainternainfo.'/'.$nuevoNombre;

                                $actualizarItem = ItemPrestacion::where('IdPrestacion', $prestacion->IdPrestacion)->first();
                            
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

                                $resultado = ["message" => "Se ha adjuntado automáticamente un archivo a un MultiExamen {$examen}", "estado" => "success"];

                            } elseif(count($buscar) === 0) {

                                $resultado = ["message" => "No hay archivo con coincidencias para la prestacion {$prestacion->IdPrestacion} multi exámen", "estado" => "fail"];
                            }

                        } 
                    }  
                                   
            } elseif($item && $item->examenes->proveedor2->MultiE === 0 && in_array($item->examenes->proveedor2->Id, [3,38,23,36,39])) {

                $formatFecha = str_replace('-', '', $item->Fecha);
                $archivoEncontrar = $formatFecha.'_'.$item->prestaciones->paciente->Documento;
                $ruta = $this->rutainf.$archivoEncontrar.'*.pdf';
                $buscar = glob($ruta);

                if (count($buscar) === 1) {

                    copy($ruta, $this->rutainf."AdjuntadasAuto/".$archivoEncontrar);

                    $nuevoId = ArchivoEfector::max('Id') + 1;
                    $nuevoNombre = 'AINF'.$nuevoId.'_P'.$item->IdPrestacion.'.pdf';
                    $nuevaRuta = FileHelper::getFileUrl('lectura').'/'.$this->rutainternainfo.'/'.$nuevoNombre;

                    $this->registarArchivo($nuevoId, $examen, "Cargado por automático", $nuevoNombre, $item->IdPrestacion, "efector");

                    $actualizarItem = ItemPrestacion::find($examen);
                    
                    if ($actualizarItem) {
                        $actualizarItem->CInfo = 3;
                        $actualizarItem->save();
                    }

                    copy($ruta, $nuevaRuta);
                    chmod($nuevaRuta, 0664);
                    Auditor::setAuditoria($item->IdPrestacion, 1, 36, Auth::user()->name);

                    $resultado = ["message" => "Se ha adjuntado automáticamente un archivo al exámen {$examen}", "estado" => "success"];
                
                }else if(count($buscar) > 1) {
                
                    $resultado = ["message" => "Hay varios archivos {$examen} que tienen la misma fecha y dni. Verifique la carpeta por favor.", "estado" => "fail"];

                }else {

                    $resultado = ["message" => "No hay archivo con coincidencias para el exámen {$examen}", "estado" => "fail"];
                }
            
            } elseif($item && $item->examenes->proveedor2->MultiE === 1 && in_array($item->examenes->proveedor2->Id, [3,38,23,36,39])) {
                
                $formatFecha = str_replace('-', '', $item->Fecha);
                $archivoEncontrar = $formatFecha.'_'.$item->prestaciones->paciente->Documento;
                $ruta = $this->rutainf.$archivoEncontrar.'*.pdf';
                $archivo = glob($ruta);

                if(count($archivo) === 1) {
                   
                    $nuevaRuta = implode("", $archivo);
                    $filename = pathinfo($nuevaRuta, PATHINFO_FILENAME);

                    $nuevoDestino = $this->rutainf."AdjuntadasAuto/".$filename.'.pdf';
                    copy($nuevaRuta, $nuevoDestino);
                    chmod($nuevoDestino, 0664);

                    $nuevoId = ArchivoInformador::max('Id') + 1;
                    $nuevoNombre = 'AINF'.$nuevoId.'_P'.$item->IdPrestacion.'.pdf';
                    $dirStorage = FileHelper::getFileUrl('lectura').'/'.$this->rutainternainfo.'/'.$nuevoNombre;

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

                    copy($nuevaRuta, $dirStorage);

                    $resultado = ["message" => "Se ha adjuntado automáticamente un archivo a un MultiExamen {$examen} xxxx", "estado" => "success"];
                    
                    
                }elseif(count($archivo) > 1) {

                    $resultado = ["message" => "Hay archivos duplicados para ese paciente. Verifique la carpeta de carga", "estado" => "fail"];
                }else{

                    $resultado = ["message" => "No hay archivo con coincidencias para el exámen {$examen}", "estado" => "fail"];
                }
                
            }
            
            $resultados[] = $resultado;
        }

        return response()->json($resultados);
    }

    public function deleteIdAdjunto(Request $request): mixed
    {    
        $examenes = $request->Id;

        if (!is_array($examenes)) {
            $examenes = [$examenes]; 
        }

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
                    $this->updateEstado($request->Tipo, $request->ItemP, 
                        $request->Tipo === 'efector' ? $request->Id : null, 
                        $request->Tipo === 'informador' ? $examen : null, 
                        null, null);
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
        
        if ($request->hasFile('archivo')) 
        {
            $query = $arr[$request->who][0];
            
            $fotoExistente = $arr[$request->who][1] ."/". $query->Ruta;
            if (Storage::exists($fotoExistente)) {
                Storage::delete($fotoExistente);
            }
            
            $filename = pathinfo($query->Ruta, PATHINFO_FILENAME). '.' . $request->archivo->extension();
            FileHelper::uploadFile(FileHelper::getFileUrl('escritura').'/'.$arr[$request->who][1].'/', $request->archivo, $filename);

            $query->update(['Ruta' => $filename]);
            $data = ItemPrestacion::find($query->IdEntidad, ['Id', 'IdPrestacion']);

            return response()->json(['msg' => 'Se ha reemplazado el archivo de manera correcta. Se actualizará el contenido en unos segundos', 'data' => $data], 200);

        }else{
            return response()->json(['msg' => 'No se ha encontrado el archivo para reemplazar'], 500);
        }

    }

    public function deleteEx(Request $request): mixed
    {
        $examenes = $request->Id;

        if (!is_array($examenes)) {
            $examenes = [$examenes];
        }
        
        foreach ($examenes as $examen) {

            $item = ItemPrestacion::with(['prestaciones','examenes'])->find($examen);
           
            if ($item && ($item->prestaciones->Cerrado === 0 && $item->CInfo != 3 && !in_array($item->CAdj,[3,5]) && $item->IdProfesional === 0 && $item->IdProfesional2 === 0)) {

                $item->delete();
                ItemPrestacion::InsertarVtoPrestacion($item->IdPrestacion);
                $this->deleteExaCuenta($item->IdPrestacion, $item->IdExamen);
                
                $resultado = ['message' => 'Se ha eliminado con éxito el exámen '.$item->examenes->Nombre.'', 'estado' => 'success'];
            
            }else{
                $resultado = ['message' => 'No se elimino exámen '.$item->examenes->Nombre.' porque se encuentra cerrada o el exámen efectuado, informado o con profesionales asignados', 'estado' => 'fail'];
            }
            $resultados[] = $resultado;   
        }
        return response()->json($resultados);
    }

    public function bloquearEx(Request $request)
    {
        $examenes = $request->Id;

        if (!is_array($examenes)) {
            $examenes = [$examenes];
        }
        
        foreach ($examenes as $examen) {

            $item = ItemPrestacion::with(['prestaciones','examenes'])->find($examen);

            if($item->Anulado === 1){

                $resultado = ['message' => 'No se bloqueo el exámen '.$item->examenes->Nombre.' porque el mismo ya se encuentra en ese estado', 'estado' => 'fail'];
            
            }elseif ($item && ($item->prestaciones->Cerrado === 1)) {
                
                $resultado = ['message' => 'No se bloqueo el exámen '.$item->examenes->Nombre.' porque la prestación se encuentra cerrada ', 'estado' => 'fail'];
            
            }else{

                $item->update(['Anulado' => 1]);
                ItemPrestacion::InsertarVtoPrestacion($item->IdPrestacion);
                $resultado = ['message' => 'Se ha bloqueado con éxito el exámen de '.$item->examenes->Nombre.'', 'estado' => 'success']; 
            }
            
            $resultados[] = $resultado;
        }
        return response()->json($resultados);
    }

        
    public function getExamenes(Request $request): mixed
    {
        if (!$this->hasPermission("prestaciones_edit")) {
            return response()->json(['msg' => 'No tienes permisos'], 403);
        }

        //$resultados = Cache::remember('itemsprestaciones', 5, function () use ($request) {

            $query = ItemPrestacion::join('profesionales as efector', 'itemsprestaciones.IdProfesional', '=','efector.Id')
                ->join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
                ->join('proveedores as proveedor2', 'examenes.IdProveedor', '=', 'proveedor2.Id')
                ->join('prestaciones', 'itemsprestaciones.IdPrestacion', '=', 'prestaciones.Id')
                ->join('profesionales as informador', 'itemsprestaciones.IdProfesional2', '=', 'informador.Id')
                ->leftJoin('archivosefector', 'itemsprestaciones.Id', '=', 'archivosefector.IdEntidad')
                ->leftJoin('archivosinformador', 'itemsprestaciones.Id', '=', 'archivosinformador.IdEntidad')
                ->select(
                    'examenes.Nombre as Nombre',
                    'examenes.Id as IdExamen',
                    'examenes.Adjunto as ExaAdj',
                    'examenes.Informe as Informe',
                    'informador.InfAdj as InfAdj',
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
                    DB::raw('(SELECT COUNT(*) FROM archivosefector WHERE IdEntidad = itemsprestaciones.Id) as archivos'),
                    DB::raw('(SELECT COUNT(*) FROM archivosinformador WHERE IdEntidad = itemsprestaciones.Id) as archivosI'),
                );                

            if ($request->tipo === 'listado' && is_array($request->IdExamen)) {

                    $query->whereIn('examenes.Id', $request->IdExamen)
                            ->where('itemsprestaciones.IdPrestacion', $request->Id);
            } 

            return $query->orderBy('efector.IdProveedor', 'ASC')
                         ->orderBy('examenes.Nombre', 'ASC')
                         //->orderBy('itemsprestaciones.Fecha', 'ASC')
                         //->groupBy('itemsprestaciones.Id')
                ->get();
        //});
 
        return response()->json(['examenes' => $resultados]);
    }

    public function show(){}

    public function save(Request $request): void
    {

        $examenes = $request->idExamen;

        if (!is_array($examenes)) {
            $examenes = [$examenes];
        }

        foreach ($examenes as $examen) {
            
            $itemPrestacion = ItemPrestacion::where('IdPrestacion', $request->idPrestacion)->where('IdExamen', $examen)->first();

            if(!$itemPrestacion){

                $examen = Examen::find($examen);
                $honorarios = $this->honorarios($examen->Id, $examen->IdProveedor);

                ItemPrestacion::create([
                    'Id' => ItemPrestacion::max('Id') + 1,
                    'IdPrestacion' => $request->idPrestacion,
                    'IdExamen' => $examen->Id,
                    'Fecha' => now()->format('Y-m-d'),
                    'CAdj' => $examen->Cerrado === 1 
                               ? ($examen->Adjunto === 0 ? 3 : 4) 
                               : 1,
                    'CInfo' => $examen->Informe,
                    'IdProveedor' => $examen->IdProveedor,
                    'VtoItem' => $examen->DiasVencimiento,
                    'SinEsc' => $examen->SinEsc,
                    'Forma' => $examen->Forma,
                    'Ausente' => $examen->Ausente,
                    'Devol' => $examen->Devol,
                    'IdProfesional' => $examen->Cerrado === 1 ? 26 : 0,
                    'Honorarios' => $honorarios == 'true' ? $honorarios->Honorarios : 0
                ]);

                ItemPrestacion::InsertarVtoPrestacion($request->idPrestacion);
            }   
        }
    }

    public function check(Request $request): mixed
    {
        if (!$this->hasPermission("prestaciones_edit")) {
            return response()->json(['msg' => 'No tienes permisos'], 403);
        }

        $examenes = ItemPrestacion::where('IdPrestacion', $request->Id)->get() ?? '';

        $idExamenes = [];

        foreach ($examenes as $examen) {
            $idExamenes[] = $examen->IdExamen;
        }

        return response()->json(['respuesta' => ! $examenes->isEmpty(), 'examenes' => $idExamenes]);
    }

    public function itemExamen(Request $request): mixed
    {
        if (!$this->hasPermission("prestaciones_edit")) {
            return response()->json(['msg' => 'No tienes permisos'], 403);
        }

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

            return response()->json(['msg' => 'Se ha actualizado el estado del exámen de manera correcta'], 200);
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
                Auditor::setAuditoria($examen, 1, 34, Auth::user()->name);
                array_push($listado, $examen);
            }
        }

        return response()->json(['message' => 'Se ha realizo la operación de manera correcta. Se actualizará la grilla']);

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
        $examenes = $request->Id;

        if (!is_array($examenes)) {
            $examenes = [$examenes];
        }

        $listado = [];

        foreach ($examenes as $examen) {
            $item = ExamenCuentaIt::join('examenes', 'pagosacuenta_it.IdExamen', '=', 'examenes.Id')->join('proveedores', 'examenes.IdProveedor', '=', 'proveedores.Id')
                ->select(
                    'examenes.Nombre as NombreExamen',
                    'proveedores.Nombre as Especialidad',
                    'examenes.DiasVencimiento as diasVencer',
                    'pagosacuenta_it.Id as IdEx'
                )
                ->where('pagosacuenta_it.Id', $examen)->first();
                array_push($listado, $item);
        }

        return response()->json($listado);
    }

    public function checkAdjunto(Request $request)
    {
        $resultado = '';
        $query = ItemPrestacion::find($request->Id);

       if ($request->Tipo === 'efector') 
       {
            $resultado = $this->adjuntoEfector($request->Id) === 0 && $query->examenes->Adjunto === 1; //true es que falta adjuntar el archivo

       }else{
            $resultado = $this->adjuntoInformador($request->Id) === 0 && $query->profesionales2->InfAdj === 1;
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

    private function generarCodigo(int $idprest, int $idex, int $idpac)
    {
        return 'A'.str_pad($idprest, 9, "0", STR_PAD_LEFT).str_pad($idex, 5, "0", STR_PAD_LEFT).str_pad($idpac, 7, "0", STR_PAD_LEFT).".pdf";
    }

    private function HoraAsegundos($hora){
        list($h,$m,$s) = explode(":",$hora);
        $segundos = ($h*3600)+($m*60) + $s;
        return $segundos;
    }

    private function SegundosAminutos($segundos){
        $horas = floor($segundos / 3600);
        $minutos = floor(($segundos - ($horas * 3600)) / 60);
        return str_pad($horas,2, "0", STR_PAD_LEFT).':'.str_pad($minutos,2, "0", STR_PAD_LEFT);
    }
    
    private function honorarios(int $idExamen, int $idProveedor)
    {
        return ExamenPrecioProveedor::where('IdExamen', $idExamen)->where('IdProveedor', $idProveedor)->first(['Honorarios']);
    }

    private function deleteExaCuenta($prestacion, $examen)
    {
        $exaCuenta = ExamenCuentaIt::where('IdPrestacion', $prestacion)->where('IdExamen', $examen)->first();

        if($exaCuenta) {
            $exaCuenta->IdPrestacion = 0;
            $exaCuenta->save();

        } 
    }

    private function multiEfector(int $idPrestacion, int $idProfesional, int $idProveedor): mixed
    {
        //$itemsprestacione->examenes->IdProveedor
        return ItemPrestacion::join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
        ->join('proveedores', 'examenes.IdProveedor', '=', 'proveedores.Id')
        ->select(
            'itemsprestaciones.Id as Id',
            DB::raw('(SELECT COUNT(*) FROM archivosefector WHERE archivosefector.IdEntidad = itemsprestaciones.Id) as archivos_count'),
            'examenes.Nombre as NombreExamen'
            )
        ->where('itemsprestaciones.IdPrestacion', $idPrestacion)
        ->whereIn('itemsprestaciones.IdProfesional', [$idProfesional, 0])
        ->where('examenes.IdProveedor', $idProveedor)
        ->where('proveedores.Multi', 1)
        ->whereNot('itemsprestaciones.Anulado', 1)
        ->orderBy('proveedores.Nombre', 'DESC')
        ->get();
    }

    private function multiInformador(int $idPrestacion, int $idProfesional, int $idProveedor): mixed
    {
        return ItemPrestacion::join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
        ->join('proveedores', 'examenes.IdProveedor2', '=', 'proveedores.Id')
        ->join('profesionales', 'itemsprestaciones.IdProfesional2', '=', 'profesionales.Id')
        ->select('itemsprestaciones.Id', 
            DB::raw('(SELECT COUNT(*) FROM archivosinformador WHERE archivosinformador.IdEntidad = itemsprestaciones.Id) as archivos_count'),
            'examenes.Nombre as NombreExamen',
            'proveedores.Nombre as NombreProveedor'
        )
        ->where('itemsprestaciones.IdPrestacion', $idPrestacion)
        ->whereIn('itemsprestaciones.IdProfesional2', [$idProfesional, 0])
        ->where('examenes.IdProveedor2', $idProveedor)
        ->where('proveedores.MultiE', 1)
        ->where('profesionales.InfAdj', 1)
        ->whereNot('itemsprestaciones.Anulado', 1)
        ->whereNot('itemsprestaciones.CInfo', 0)
        ->orderBy('proveedores.Nombre', 'DESC')
        ->get();
    }

    //Tipo: efector, informador
    private function getProfesional(int $id, string $tipo, int $proveedor): mixed
    {
        $query = Profesional::find($id);
        $result = null;

        if (!$query)
        {
            $result = [
                'id' => $query->Id,
                'data' => $query->RegHis === 1 
                    ? $query->Apellido . " ". $query->Nombre
                    : Auth::users()->personal->Apellido . " " . Auth::users()->personal->Nombre
            ];
        } else {

            $result = User::join('user_rol', 'users.id', '=', 'user_rol.user_id')
                        ->join('roles', 'user_rol.rol_id', '=', 'roles.Id')
                        ->join('profesionales', 'users.profesional_id', '=', 'profesionales.Id')
                        ->join('proveedores', 'profesionales.IdProveedor', '=', 'proveedores.Id')
                        ->where('roles.nombre', $tipo)
                        ->where('profesionales.IdProveedor', $proveedor)
                        ->get();
        }

        return response()->json($result);
    }

    private function marcarPrimeraCarga(int $id, string $who): void
    {
        if($who === 'multiefector'){
            
            $query = ArchivoEfector::where('IdEntidad', $id)->first(); 
            if ($query) {
                $query->PuntoCarga = 1;
                $query->save();
            }
        }

        if($who === 'multiInformador') {
            $query = ArchivoInformador::where('IdEntidad', $id)->first();

            if ($query) {
                $query->PuntoCarga = 1;
                $query->save();
            }
        }
    }

    private function obtenerDatosItemPrestacion($id)
    {
        $query = ItemPrestacion::with(['prestaciones', 'examenes', 'examenes.proveedor1', 'examenes.proveedor2', 'profesionales1', 'profesionales2', 'itemsInfo', 'notaCreditoIt.notaCredito', 'facturadeventa'])->find($id);
        $data = null;

        if ($query) {
            $paciente = $this->getPaciente($query->IdPrestacion);
            
            $data = [
                'itemprestacion' => $query,
                'paciente' => $paciente,
                'qrTexto' => $this->generarQR('A', $query->IdPrestacion, $query->IdExamen, $paciente->Id, 'texto'),
                'adjuntoEfector' => $this->adjuntoEfector($query->Id),
                'adjuntoInformador' => $this->adjuntoInformador($query->Id),
                'multiEfector' => $this->multiEfector($query->IdPrestacion, $query->IdProfesional, $query->examenes->IdProveedor),
                'multiInformador' => $this->multiInformador($query->IdPrestacion, $query->IdProfesional2, $query->examenes->IdProveedor2),
                'efectores' => $this->getProfesional($query->IdProfesional, "Efector", $query->IdProveedor),
            ];
        }

        return $data;
    }


}