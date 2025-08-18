<?php

namespace App\Traits;

use App\Helpers\FileHelper;
use App\Models\Fichalaboral;
use App\Models\Telefono;
use App\Models\Localidad;
use App\Models\Cliente;
use App\Models\Prestacion;

use Illuminate\Support\Facades\DB;

trait ObserverPacientes
{
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
        $ficha = Fichalaboral::where('IdPaciente', $id)->orderBy('Id', 'desc')->first() ?? '';

        $tipos = [
            'art' => $ficha->IdART,
            'empresa' => $ficha->IdEmpresa
        ];

        if(!$ficha) {
            return response()->json(['msg' => 'No se ha encontrado la ficha laboral'], 404);
        }

        if(!in_array($tipo, ['art', 'empresa'])) {
            return $ficha;
        }

        return Cliente::find($tipos[$tipo]);
    }

    public function getPrestacion($id):  mixed
    {
        return Prestacion::join('pacientes', 'prestaciones.IdPaciente', '=', 'pacientes.Id')
            ->join('clientes as emp', 'prestaciones.IdEmpresa', '=', 'emp.Id')
            ->join('clientes as art', 'prestaciones.IdART', '=', 'art.Id')
            ->join('itemsprestaciones', 'prestaciones.Id', '=', 'itemsprestaciones.IdPrestacion')
            ->select(
                DB::raw('(SELECT RazonSocial FROM clientes WHERE Id = prestaciones.IdART) AS Art'),
                DB::raw('(SELECT RazonSocial FROM clientes WHERE Id = prestaciones.IdEmpresa) AS Empresa'),
                DB::raw('COUNT(itemsprestaciones.IdPrestacion) as Total'),
                DB::raw('COALESCE(COUNT(CASE WHEN itemsprestaciones.CAdj = 5 THEN itemsprestaciones.IdPrestacion END), 0) as CerradoAdjunto'),
                'emp.ParaEmpresa as ParaEmpresa',
                'emp.Identificacion as Identificacion',
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
                'prestaciones.Estado as Estado',
                'prestaciones.Facturado as Facturado'
            )
            ->where('IdPaciente', $id)
            ->where('prestaciones.Estado', '=', '1')
            ->orderBy('prestaciones.Id', 'DESC')
            ->groupBy('prestaciones.Id')
            ->cursorPaginate(500); 
    }

    public function addFoto($foto, $id, $tipo)
    {
        if ($foto && ($tipo === 'update' || $tipo === 'create')) {
            $fileName = 'P'.$id.'.jpg';

            FileHelper::uploadFile(FileHelper::getFileUrl('escritura').'/Fotos/', $foto, $fileName);

            return $fileName;

        } elseif((empty($foto)) && $tipo === 'create') {
            
            return 'foto-default.png';

        }
    }

    public function addTelefono($telefono, $id)
    {
        $codigoArea = null;
        $numeroTelefono = null;

        $telefonoClean = str_replace(['-', '(', ')'], '', $telefono);

        if (strlen($telefonoClean) >= 10) {
            $codigoArea = substr($telefonoClean, 0, 3);
            $numeroTelefono = substr($telefonoClean, 3);
        } else {
            $numeroTelefono = $telefonoClean;
        }

        $telefono = Telefono::where('IdEntidad', $id)->first();

        if($telefono) {

            $telefono->CodigoArea = $codigoArea ?? '';
            $telefono->NumeroTelefono = $numeroTelefono;
            $telefono->save();

        }else{
            Telefono::create([
                'Id' => Telefono::max('Id') + 1,
                'IdEntidad' => $id,
                'CodigoArea' => $codigoArea ?? '',
                'NumeroTelefono' => $numeroTelefono,
            ]);
        }

    }

}
