<?php

namespace App\Http\Controllers;

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
    
        return view('layouts.itemsprestaciones.edit', compact(['itemsprestacione']));
    }

    public function updateItem(Request $request): void
    {
        $item = ItemPrestacion::find($request->Id);

        if($item) 
        {
            $item->CAdj = $request->CAdj;
            $item->save();
        }
    }

    public function updateEfector(Request $request): void
    {
        $item = ItemPrestacion::find($request->Id);

        if($item)
        {
            $item->IdProfesional = $request->IdProfesional;
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
            ->where('profesionales.T1', '1')
            ->where(function($query) use ($request) {
                if ($request->tipo === 'efector') {
                    $query->where('profesionales.T1', '1');
                } elseif ($request->tipo === 'informador') {
                    $query->where('profesionales.T2', '1');
                }
            })
            ->where('profesionales.IdProveedor', $request->proveedor)
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
            ->where('itemsprestaciones.Id', $Id)
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

}