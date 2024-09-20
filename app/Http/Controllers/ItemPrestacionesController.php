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

class ItemPrestacionesController extends Controller
{
    use ObserverItemsPrestaciones, CheckPermission;

    const RUTA = '/var/IMPORTARPDF/SALIDA/';
    const RUTAINF = '/var/IMPORTARPDF/SALIDAINFORMADOR/';
    const RUTAINTERNAEFECTORES = 'public/ArchivosEfectores';
    const RUTAINTERNAINFO = 'public/ArchivosInformadores';
    

    public function edit(ItemPrestacion $itemsprestacione): mixed
    {
        $paciente = $this->getPaciente($itemsprestacione->IdPrestacion);
        $qrTexto = $this->generarQR('A', $itemsprestacione->IdPrestacion, $itemsprestacione->IdExamen, $paciente->Id, 'texto');
        $adjuntoEfector = $this->adjuntoEfector($itemsprestacione->Id);
        $adjuntoInformador = $this->adjuntoInformador($itemsprestacione->Id);
        
        $multiEfector = ItemPrestacion::join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
        ->join('proveedores', 'examenes.IdProveedor', '=', 'proveedores.Id')
        ->select('itemsprestaciones.*', DB::raw('(SELECT COUNT(*) FROM archivosefector WHERE archivosefector.IdEntidad = itemsprestaciones.Id) as archivos_count'))
        ->where('itemsprestaciones.IdPrestacion', $itemsprestacione->IdPrestacion)
        ->whereIn('itemsprestaciones.IdProfesional', [$itemsprestacione->IdProfesional, 0])
        ->where('examenes.IdProveedor', $itemsprestacione->examenes->IdProveedor)
        ->where('proveedores.Multi', 1)
        ->whereNot('itemsprestaciones.Anulado', 1)
        ->orderBy('proveedores.Nombre', 'DESC')
        ->get();

        $multiInformador = ItemPrestacion::join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
        ->join('proveedores', 'examenes.IdProveedor2', '=', 'proveedores.Id')
        ->select('itemsprestaciones.*', DB::raw('(SELECT COUNT(*) FROM archivosinformador WHERE archivosinformador.IdEntidad = itemsprestaciones.Id) as archivos_count'))
        ->where('itemsprestaciones.IdPrestacion', $itemsprestacione->IdPrestacion)
        ->whereIn('itemsprestaciones.IdProfesional2', [$itemsprestacione->IdProfesional2, 0])
        ->where('examenes.IdProveedor2', $itemsprestacione->examenes->IdProveedor)
        ->where('proveedores.MultiE', 1)
        ->where('proveedores.InfAdj', 1)
        ->whereNot('itemsprestaciones.Anulado', 1)
        ->orderBy('proveedores.Nombre', 'DESC')
        ->get();

        return view('layouts.itemsprestaciones.edit', compact(['itemsprestacione', 'qrTexto', 'paciente', 'adjuntoEfector','multiEfector', 'multiInformador', 'adjuntoInformador']));
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
            $item = ItemPrestacion::with(['examenes','profesionales2'])->find($examen);

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
            }
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

        $item = ItemPrestacion::with(['profesionales2', 'proveedores'])->find($request->Id);

        $asignado = $request->Para === 'asignar' ? 'efector' : 'informador';

        if($item)
        {
            $horaFin = $this->HoraAsegundos(date("H:i:s")) + ($item->proveedores->Min * 60);
            $horaAsigFin = $this->SegundosAminutos($horaFin).':00';

            if($request->Para === 'asignar'){
                $item->IdProfesional = $request->IdProfesional;

                $item->FechaAsignado = (($request->fecha == '0000-00-00' ||  $request->fecha == '0') ? '' : now()->format('Y-m-d'));
                $item->HoraAsignado = date("H:i:s");
                $item->HoraFAsignado = $horaAsigFin;


            }elseif($request->Para === 'asignarI'){
                $item->IdProfesional2 = $request->IdProfesional;

                $item->FechaAsignadoI = (($request->fecha == '0000-00-00' ||  $request->fecha == '0') ? '' : now()->format('Y-m-d'));
                $item->HoraAsignadoI = date("H:i:s");
                //$item->HoraFAsignado = $horaAsigFin;
            }
            
            $item->save();
            
            return response()->json(['msg' => 'Se ha actualizado el '. $asignado .' de manera correcta'], 200);
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
            return response()->json(['msg' => 'Se han actualizado los datos correctamente'], 200);

        }else{
            return response()->json(['msg' => 'No se han actualizado los datos. No se encuentra el identificador'], 500);
        }
    }

    public function uploadAdjunto(Request $request)
    {
        $who = $request->who;
        if ($request->who === 'efector' && $request->multi === 'success') {
            $who = 'multiefector';
        } elseif ($request->who === 'informador' && $request->multi === 'success') {
            $who = 'multiInformador';
        }

        $arr = [
            'efector' => [ArchivoEfector::max('Id') + 1, 'AEF', self::RUTAINTERNAEFECTORES],
            'informador' => [ArchivoInformador::max('Id') + 1, 'AINF', self::RUTAINTERNAINFO],
            'multiefector'  => [ArchivoEfector::max('Id') + 1, 'AEF', self::RUTAINTERNAEFECTORES],
            'multiInformador' => [ArchivoInformador::max('Id') + 1, 'AINF', self::RUTAINTERNAINFO],
        ];
        
        $arr['multiefector'] = &$arr['efector'];
        $arr['multiInformador'] = &$arr['informador'];

        if($request->hasFile('archivo')) {
            $fileName = $arr[$who][1].$arr[$who][0]. '_P'. $request->IdPrestacion .'.' . $request->archivo->extension();
            $request->archivo->storeAs($arr[$who][2], $fileName);
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
                    $this->updateEstado($who, $examen, $who === 'multiefector' ? $arr[$who][0] : null, $who === 'multiInformador' ? $arr[$who][0] : null, 'multi', $who === 'multiInformador' ? $request->anexoProfesional : ($who === 'multiefector'? $request->anexoProfesional : null)) ;
                    Auditor::setAuditoria($item->IdPrestacion, 1, $who === 'efector' ? 36 : 37, Auth::user()->name);
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
                $ruta = self::RUTA.$archivo;
                $buscar = glob($ruta);
                
                if (count($buscar) === 1) {

                    copy($ruta, self::RUTA."AdjuntadasAuto/".$archivo);

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
                        $ruta = self::RUTA.$archivo;
                        $buscar = glob($ruta);
                        return response()->json($ruta);exit;
                        if (count($buscar) === 1) {

                            copy($ruta, self::RUTA."AdjuntadasAuto/".$archivo);

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
            'efector' => [ArchivoEfector::max('Id') + 1, 'AEF', self::RUTAINTERNAEFECTORES],
            'informador' => [ArchivoInformador::max('Id') + 1, 'AINF', self::RUTAINTERNAINFO],
            'multiefector'  => [ArchivoEfector::max('Id') + 1, 'AEF', self::RUTAINTERNAEFECTORES],
            'multiInformador' => [ArchivoInformador::max('Id') + 1, 'AINF', self::RUTAINTERNAINFO],
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
                $ruta = self::RUTAINF.$archivo;
                $buscar = glob($ruta);

                if (count($buscar) === 1) {

                    copy($ruta, self::RUTAINF."AdjuntadasAuto/".$archivo);

                    $nuevoId = ArchivoEfector::max('Id') + 1;
                    $nuevoNombre = 'AEF'.$nuevoId.'_P'.$item->IdPrestacion.'.pdf';
                    $nuevaRuta = storage_path('app/public/ArchivosInformadores/'.$nuevoNombre);

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
                            $ruta = self::RUTAINF.$archivo;
                            $buscar = glob($ruta);

                            if (count($buscar) === 1) {

                                copy($ruta, self::RUTAINF."AdjuntadasAuto/".$archivo);

                                $nuevoId = ArchivoInformador::max('Id') + 1;
                                $nuevoNombre = 'AINF'.$nuevoId.'_P'.$prestacion->IdPrestacion.'.pdf';
                                $nuevaRuta = storage_path('app/public/ArchivosInformador/'.$nuevoNombre);

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
                $ruta = self::RUTAINF.$archivoEncontrar.'*.pdf';
                $buscar = glob($ruta);

                if (count($buscar) === 1) {

                    copy($ruta, self::RUTAINF."AdjuntadasAuto/".$archivoEncontrar);

                    $nuevoId = ArchivoEfector::max('Id') + 1;
                    $nuevoNombre = 'AINF'.$nuevoId.'_P'.$item->IdPrestacion.'.pdf';
                    $nuevaRuta = storage_path('app/public/ArchivosInformadores/'.$nuevoNombre);

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
                $ruta = self::RUTAINF.$archivoEncontrar.'*.pdf';
                $archivo = glob($ruta);

                if(count($archivo) === 1) {
                   
                    $nuevaRuta = implode("", $archivo);
                    $filename = pathinfo($nuevaRuta, PATHINFO_FILENAME);

                    $nuevoDestino = self::RUTAINF."AdjuntadasAuto/".$filename.'.pdf';
                    copy($nuevaRuta, $nuevoDestino);
                    chmod($nuevoDestino, 0664);

                    $nuevoId = ArchivoInformador::max('Id') + 1;
                    $nuevoNombre = 'AINF'.$nuevoId.'_P'.$item->IdPrestacion.'.pdf';
                    $dirStorage = storage_path('app/public/ArchivosInformadores/'.$nuevoNombre);

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
            $adjunto = $request->Tipo === 'efector' ? ArchivoEfector::find($request->Id) : ArchivoInformador::find($request->Id);

            if ($adjunto) {
                $adjunto->delete();
                $this->updateEstado($request->Tipo, $request->ItemP, $request->Tipo === 'efector' ? $request->Id : null, $request->Tipo === 'informador' ? $request->Id : null, null, null);
            
                return response()->json(['msg' => 'Se ha eliminado el adjunto de manera correcta'], 200);
            }else{
                return response()->json(['msg' => 'No se ha podido eliminar el adjunto'], 500);
            }
            
    }

    public function replaceIdAdjunto(Request $request): mixed
    {

        $arr = [
            'efector' => [ArchivoEfector::find($request->Id), self::RUTAINTERNAEFECTORES],
            'informador' => [ArchivoInformador::find($request->Id), self::RUTAINTERNAINFO]
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

            return response()->json(['msg' => 'Se ha reemplazado el archivo de manera correcta. Se actualizará el contenido en unos segundos'], 200);

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
           
            if ($item && ($item->prestaciones->Cerrado === 0 && $item->CInfo != 3 && !in_array($item->CAdj,[3,5]) && $item->IdProfesional2 === 0 && $item->IdProfesional2 === 0)) {

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

        $resultados = Cache::remember('itemsprestaciones', 5, function () use ($request) {

            $query = ItemPrestacion::join('profesionales as efector', 'itemsprestaciones.IdProfesional', '=','efector.Id')
                ->join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
                ->join('proveedores as proveedor2', 'examenes.IdProveedor', '=', 'proveedor2.Id')
                ->join('prestaciones', 'itemsprestaciones.IdPrestacion', '=', 'prestaciones.Id')
                ->join('profesionales as informador', 'itemsprestaciones.IdProfesional2', '=', 'informador.Id')
                ->leftJoin('archivosefector', 'itemsprestaciones.Id', '=', 'archivosefector.IdEntidad')
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
                    DB::raw('(SELECT COUNT(*) FROM archivosefector WHERE IdEntidad = itemsprestaciones.Id) as archivos')
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
        });
 
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
        
        if($item->Anulado === 1)
        {
            return response()->json(['prestacion' => true]);
        }
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

}