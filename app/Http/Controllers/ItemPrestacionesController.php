<?php

namespace App\Http\Controllers;

use App\Models\ArchivoEfector;
use App\Models\ArchivoInformador;
use App\Models\ItemPrestacion;
use App\Models\ItemPrestacionInfo;
use App\Models\Profesional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ObserverItemsPrestaciones;

class ItemPrestacionesController extends Controller
{
    use ObserverItemsPrestaciones;

    public function index()
    {
        //
    }

    public function edit(ItemPrestacion $itemsprestacione): mixed
    {
        $paciente = $this->getPaciente($itemsprestacione->IdPrestacion);
        $qrTexto = $this->generarQR('A', $itemsprestacione->IdPrestacion, $itemsprestacione->IdExamen, $paciente->Id, 'texto');

        return view('layouts.itemsprestaciones.edit', compact(['itemsprestacione', 'qrTexto', 'paciente']));
    }

    public function updateItem(Request $request): void
    {
        $item = ItemPrestacion::find($request->Id);

        if($item) 
        {   
            if($request->Para === 'cerrar' || $request->Para === 'abrir')
            {
                $item->CAdj = $request->CAdj;
            
            }elseif($request->Para === 'cerrarI'){

                $item->CInfo = $request->CInfo;
            
            }
            $item->save();
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

    public function listGeneral(Request $request): mixed
    {

        $data = Profesional::join('proveedores', 'profesionales.IdProveedor', '=', 'proveedores.Id')
            ->select(
                'profesionales.Id as Id',
                DB::raw("CONCAT(profesionales.Apellido, ' ', profesionales.Nombre) AS NombreCompleto"),
            )
            ->where(function($query) use ($request) {
                if ($request->tipo === 'efector') {
                    $query->where('profesionales.T1', '1');
                } elseif ($request->tipo === 'informador') {
                    $query->where('profesionales.T2', '1');
                }
            })
            ->where('profesionales.IdProveedor', $request->proveedor)
            ->where('profesionales.Inactivo', '0')
            ->get();

        return response()->json(['resultados' => $data]);
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
                'examenes.Nombre as Nombre',
                'archivosefector.Descripcion as DescripcionE',
                'archivosefector.Ruta as RutaE',
                'examenes.Adjunto as Adjunto',
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
                    'examenes.Nombre as Nombre',
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
            $fileName = $arr[$request->who][1].$arr[$request->who][0]. '_'. $request->IdPrestacion .'.' . $request->archivo->extension();
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

}