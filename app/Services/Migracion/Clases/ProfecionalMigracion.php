<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\ParametroReporte;
use App\Models\Permiso;
use App\Models\Personal;
use App\Models\PrecioPorCodigo;
use App\Models\Prestacion;
use App\Models\PrestacionAtributo;
use App\Models\PrestacionComentario;
use App\Models\PrestacionesTipo;
use App\Models\Profesional;
use Illuminate\Support\Facades\Log;

class ProfecionalMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $profecional = Profesional::firstOrNew(['Id' => $data['Id']]);

        if (!$profecional->exists) {

            $default = [
                'IdProveedor'=> $data['IdProveedor'],
                'TipoIdentificacion' => $data['TipoIdentificacion'],
                'Identificacion' => $data['Identificacion'],
                'TipoDocumento' => $data['TipoDocumento'],
                'Documento' => $data['Documento'],
                'Nombre' => $data['Nombre'],
                'Apellido' => $data['Apellido'],
                'Direccion' => $data['Direccion'],
                'IdLocalidad' => $data['IdLocalidad'],
                'Provincia' => $data['Provincia'],
                'CP' => $data['CP'],
                'Firma' => $data['Firma'],
                'Foto' => $data['Foto'],
                'T1' => $data['T1'],
                'T2' => $data['T2'],
                'T3' => $data['T3'],
                'T4' => $data['T4'],
                'TLP' => $data['TLP'],
                'TMP' => $data['TMP'],
                'Pago' => $data['Pago'],
                'wImage' => $data['wImage'],
                'hImage' => $data['hImage'],
                'InfAdj' => $data['InfAdj'],
                'RegHis' => $data['RegHis'],
                'T5' => $data['T5'],
                'SeguroMP' => $data['SeguroMP'],
                'MN' => $data['MN'],
                'MP' => $data['MP'],
            ];

            $profecional->fill($default);
        }

        $profecional->fill($data);
        $profecional->save();
        Log::info("Profesional {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        Profesional::where('Id', $before['Id'])->delete();
        Log::info("Profesional {$before['Id']} eliminado.");
    }

}