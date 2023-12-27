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

        return $result;
        
    }

    public function addFoto($foto, $id, $tipo)
    {
        if ($foto && ($tipo === 'update' || $tipo === 'create')) {
            $img = $foto;
            $folderPath = 'archivos/fotos/';

            $image_parts = explode(';base64,', $img);
            $image_type_aux = explode('image/', $image_parts[0]);
            $image_type = $image_type_aux[1];

            $image_base64 = base64_decode($image_parts[1]);
            $fileName = 'P'.$id.'.png';

            $filePath = $folderPath.$fileName;
            file_put_contents($filePath, $image_base64);
            chmod($filePath, 0755);

            return $fileName;

        } elseif(($foto === null || $foto === '') && $tipo === 'create') {
            
            return 'foto-default.png';

        }
    }

    public function addTelefono($telefono, $id, $tipo)
    {
        $telefonoClean = str_replace(['-', '(', ')'], '', $telefono);

        if (strlen($telefonoClean) >= 10) {
            $CodigoArea = substr($telefonoClean, 0, 3);
            $NumeroTelefono = substr($telefonoClean, 3);
        } else {
            $NumeroTelefono = $telefonoClean;
        }

        if($tipo === 'create')
        {
            Telefono::create([
                'Id' => Telefono::max('Id') + 1,
                'IdEntidad' => $id,
                'CodigoArea' => $CodigoArea ?? '',
                'NumeroTelefono' => $NumeroTelefono,
            ]);

        }elseif($tipo === 'update'){

            $telefono = Telefono::where('IdEntidad', $id)->first();

            $telefono->CodigoArea = $CodigoArea ?? '';
            $telefono->NumeroTelefono = $NumeroTelefono;
            $telefono->save();

        }
    }

}
