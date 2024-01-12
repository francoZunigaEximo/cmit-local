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
            ->where('prestaciones.Finalizado', 0)
            ->where('prestaciones.Entregado', 0)
            ->count();
    }

    public function contadorFinalizado(int $id): int
    {
        return Prestacion::join('mapas', 'prestaciones.IdMapa', 'mapas.Id')
            ->where('prestaciones.IdMapa', $id)
            ->where('prestaciones.Cerrado', 1)
            ->where('prestaciones.Finalizado', 1)
            ->where('prestaciones.Entregado', 0)
            ->count();
    }

    public function contadorEntregado(int $id): int
    {
        return Prestacion::join('mapas', 'prestaciones.IdMapa', 'mapas.Id')
            ->where('prestaciones.IdMapa', $id)
            ->where('prestaciones.Cerrado', 1)
            ->where('prestaciones.Finalizado', 1)
            ->where('prestaciones.Entregado', 1)
            ->count();
    }

    public function contadorConEstado(int $id)
    {
        $conteo = Prestacion::join('mapas', 'prestaciones.IdMapa', 'mapas.Id')
            ->where('prestaciones.IdMapa', $id)
            ->selectRaw('SUM(CASE WHEN prestaciones.Forma = 1 OR prestaciones.Incompleto = 1 OR prestaciones.Ausente = 1 OR prestaciones.Devol = 1 THEN 1 ELSE 0 END) as conEstados')
            ->first();

        return $conteo ? $conteo->conEstados : 0;
    }

    public function contadorCompletas(int $id): int
    {
        $totalPrestaciones = Prestacion::join('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
            ->join('mapas', 'prestaciones.IdMapa', '=', 'mapas.Id')
            ->where('mapas.Id', $id) 
            ->select('prestaciones.Id as prestacionId') 
            ->where(function ($query) {
                $query->where('itemsprestaciones.CAdj', 'IN', [3, 4, 5, 6])
                    ->orWhere('itemsprestaciones.Cinfo', '=', 3);
            })
            ->where('prestaciones.Cerrado', 0)
            ->where('prestaciones.Finalizado', 0)
            ->where('prestaciones.Entregado', 0)
            ->groupBy('prestacionId')
            ->havingRaw('COUNT(*) = COUNT(CASE WHEN itemsprestaciones.CAdj IN (3, 4, 5, 6) OR itemsprestaciones.Cinfo = 3 THEN 1 END)')
            ->count();

        return $totalPrestaciones;
    }


    public function contadorEnProceso(int $id): int
    {
        $totalProceso = Prestacion::join('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
            ->join('mapas', 'prestaciones.IdMapa', '=', 'mapas.Id')
            ->where('mapas.Id', $id) 
            ->select('prestaciones.Id as prestacionId') 
            ->where(function ($query) {
                $query->whereNotIn('itemsprestaciones.CAdj', [3, 4, 5, 6])
                    ->orWhere('itemsprestaciones.Cinfo', '<>', 3);
            })
            ->where('prestaciones.Cerrado', 0)
            ->where('prestaciones.Finalizado', 0)
            ->where('prestaciones.Entregado', 0)
            ->groupBy('prestacionId')
            ->havingRaw('COUNT(*) = COUNT(CASE WHEN 
                        (itemsprestaciones.CAdj NOT IN (3, 4, 5, 6) OR itemsprestaciones.Cinfo <> 3) 
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