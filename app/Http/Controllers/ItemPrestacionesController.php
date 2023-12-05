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

    public function updateEfector(Request $request)
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

        $query = ItemPrestacion::join('examenes', 'itemsprestaciones.IdExamen', '=', 'examenes.Id')
            ->leftJoin('archivosefector', 'itemsprestaciones.Id', '=', 'archivosefector.IdEntidad')
            ->leftJoin('archivosinformador', 'itemsprestaciones.Id', '=', 'archivosinformador.IdEntidad')
            ->join('proveedores', 'itemsprestaciones.IdProveedor', '=', 'proveedores.Id')
            ->select(
                'examenes.Nombre as Nombre',
                'archivosefector.Descripcion as DescripcionE',
                'archivosinformador.Descripcion as DescripcionI',
                'examenes.Adjunto as Adjunto',
                'proveedores.MultiE as MultiE',
            )
            ->where(function($query) use ($request, $Id) {
                if ($request->tipo === 'efector') {
                    $query->where('archivosefector.IdEntidad', $Id);
                } elseif ($request->tipo === 'informador') {
                    $query->where('archivosinformador.IdEntidad', $Id);
                }
            })
            ->get();

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
        return $request->all();

        $nuevoId = ($request->who === 'efector') ? ArchivoEfector::max('Id') + 1 : ArchivoInformador::max('Id') + 1;
        $identificador = ($request->who === 'efector') ? 'AEF' : 'AINF';

        if($request->hasFile('archivo')) {
            $fileName = $identificador.$nuevoId. '.' . $request->archivo->extension();
            $request->archivo->storeAs('public/itemsprestaciones', $fileName);
        }

        if($request->who === 'efector'){

            ArchivoEfector::create([
                'Id' => $nuevoId,
                'IdEntidad' => $request->IdEntidad,
                'Descripcion' => $request->Descripcion ?? '',
                'Ruta' => $fileName,
                'IdPrestacion' => $request->IdPrestacion,
                'Tipo' => 0
            ]);
        
        }elseif($request->who === 'informador'){

            ArchivoInformador::create([
                'Id' => $nuevoId,
                'IdEntidad' => $request->IdEntidad,
                'Descripcion' => $request->Descripcion ?? '',
                'Ruta' => $fileName,
                'IdPrestacion' => $request->IdPrestacion
            ]);
        }
        
    }


}