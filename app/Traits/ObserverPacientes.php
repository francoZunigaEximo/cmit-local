<?php

namespace App\Traits;

use App\Models\Fichalaboral;
use App\Models\Provincia;
use App\Models\Telefono;
use App\Models\Localidad;
use App\Models\Cliente;
use App\Models\Prestacion;

use Illuminate\Support\Facades\DB;

trait ObserverPacientes
{

    public function fixerProvincia($id)
    {
        return Provincia::where('Id', $id)->orWhere('Nombre', $id)->first(['Id', 'Nombre']);
    }

    public function getTelefono($id): mixed
    {
        return Telefono::where('IdEntidad', $id)->first(['CodigoArea', 'NumeroTelefono']);
    }

    public function getLocalidad($id):  mixed
    {
        return Localidad::where('Id', $id)->first(['Nombre', 'CP', 'Id']);
    }

    public function getFichaLaboral($id, $tipo):  mixed
    {

        $result = null;
        $ficha = Fichalaboral::where('IdPaciente', $id)->first() ?? '';

        if ($ficha && $tipo == 'art')
        {
            $result = Cliente::where('Id', $ficha->IdART)->first();
        
        } elseif ($ficha && $tipo == 'empresa')
        {
            $result = Cliente::where('Id', $ficha->IdEmpresa)->first();
        
        } else if ($ficha && $tipo == null)
        {
            $result = $ficha;
        }

        return $result;
    }

    public function getPrestacion($id):  mixed
    {
        $result = Prestacion::join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->join('clientes', 'prestaciones.IdEmpresa', '=', 'clientes.Id')
            ->select(
                DB::raw('(SELECT RazonSocial FROM clientes WHERE Id = prestaciones.IdART) AS Art'),
                DB::raw('(SELECT RazonSocial FROM clientes WHERE Id = prestaciones.IdEmpresa) AS RazonSocial'),
                'clientes.ParaEmpresa as ParaEmpresa',
                'clientes.Identificacion as Identificacion',
                'prestaciones.Fecha as FechaAlta',
                'prestaciones.Id as Id',
                'pacientes.Nombre as Nombre',
                'pacientes.Apellido as Apellido',
                'prestaciones.TipoPrestacion as Tipo',
                'prestaciones.Anulado as Anulado',
                'prestaciones.Pago as Pago',
                'prestaciones.Ausente as Ausente',
                'prestaciones.Incompleto as Incompleto',
                'prestaciones.Devol as Devol',
                'prestaciones.Forma as Forma',
                'prestaciones.SinEsc as SinEsc',
                'prestaciones.Estado as Estado'
            )
            ->where('IdPaciente', $id)
            ->where('prestaciones.Estado', '=', '1')
            ->orderBy('prestaciones.Id', 'DESC')
            ->cursorPaginate(15);

        return $result;
        
    }
}
