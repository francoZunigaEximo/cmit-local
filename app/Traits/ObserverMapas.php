<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Constanciase;
use App\Models\Prestacion;
use App\Models\Mapa;
trait ObserverMapas
{
    
    public function contadorRemitos(int $id): mixed
    {
        $conteo = Prestacion::select(
            'NroCEE', 
            DB::raw('COUNT(*) as contadorRemitos'
            ))
            ->where('IdMapa', $id)
            ->where('Entregado', 0)
            ->groupBy('NroCEE')
            ->get();
        
        return $conteo;
    }

    public function contadorCerrado(int $id): int
    {
        return Prestacion::join('mapas', 'prestaciones.IdMapa', 'mapas.Id')
            ->where('prestaciones.IdMapa', $id)
            ->where('prestaciones.Cerrado', 1)
            ->count();
    }

    public function contadorFinalizado(int $id): int
    {
        return Prestacion::join('mapas', 'prestaciones.IdMapa', 'mapas.Id')
            ->where('prestaciones.IdMapa', $id)
            ->where('prestaciones.Cerrado', 1)
            ->where('prestaciones.Finalizado', 1)
            ->count();
    }

    public function contadorEntregado(int $id): int
    {
        return Prestacion::join('mapas', 'prestaciones.IdMapa', 'mapas.Id')
            ->where('prestaciones.IdMapa', $id)
            ->where('prestaciones.Entregado', 1)
            ->count();
    }

    public function contadorConEstado(int $id): int
    {
        return Prestacion::join('mapas as m', 'prestaciones.IdMapa', '=', 'm.Id')
        ->join('itemsprestaciones as i', 'prestaciones.Id', '=', 'i.IdPrestacion')
        ->where('m.Id', $id)
        ->where(function($query) {
            $query->where('i.Forma', 1)
                  ->orWhere('i.Devol', 1)
                  ->orWhere('i.Incompleto', 1)
                  ->orWhere('i.Ausente', 1);
        })
        ->count();
    }

    public function contadorCompletas(int $id): int
    {
        return Prestacion::join('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
            ->join('mapas', 'prestaciones.IdMapa', '=', 'mapas.Id')
            ->where('mapas.Id', $id)
            ->where('prestaciones.Cerrado', 0)
            ->where('prestaciones.Finalizado', 0)
            ->where('prestaciones.Entregado', 0)
            
            ->groupBy('prestaciones.Id')
            ->havingRaw('COUNT(*) = COUNT(CASE WHEN itemsprestaciones.CAdj IN (3, 5) AND itemsprestaciones.Cinfo IN (3, 0) THEN 1 END)')
            ->select('prestaciones.Id') // Seleccionamos explícitamente la columna Id de prestaciones
            ->count();
    }




    public function contadorEnProceso(int $id): int
    {
        $totalProceso = Prestacion::join('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
            ->join('mapas', 'prestaciones.IdMapa', '=', 'mapas.Id')
            ->where('mapas.Id', $id) 
            ->select('prestaciones.Id as prestacionId') 
            ->where(function ($query) {
                $query->whereNotIn('itemsprestaciones.CAdj', [3, 5])
                    ->orWhere('itemsprestaciones.Cinfo', '<>', 3)
                    ->orWhere('itemsprestaciones.Cinfo', '<>', 0);
            })
            ->where('prestaciones.Cerrado', 0)
            ->where('prestaciones.Finalizado', 0)
            ->where('prestaciones.Entregado', 0)
            ->groupBy('prestacionId')
            ->havingRaw('COUNT(*) = COUNT(CASE WHEN 
                        (itemsprestaciones.CAdj NOT IN (3, 5) OR itemsprestaciones.CInfo NOT IN (3,0)) 
                        AND prestaciones.Cerrado = 0 
                        AND prestaciones.Finalizado = 0 
                        AND prestaciones.Entregado = 0 
                    THEN 1 END)')
            ->count();

        return $totalProceso;
    }

    public function actualizarRemitoPrestacion(int $id, int $nroRemito): void
    {
        $prestacion = Prestacion::find($id);
        if($prestacion)
        {
            $prestacion->NroCEE = $nroRemito;
            $prestacion->save();
        }
    }

}