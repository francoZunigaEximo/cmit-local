<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\Noticia;
use Illuminate\Support\Facades\Log;

class NoticiaMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $noticia = Noticia::firstOrNew(['Id' => $data['Id']]);

        if (!$noticia->exists) {

            $default = [
                'Titulo'=> $data['Titulo'],
                'Subtitulo'=>$data['Subtitulo'],
                'Texto'=>$data['Texto'],
                'Urgente'=>$data['Urgente'],
                'Ruta'=>$data['Ruta']
            ];

            $noticia->fill($default);
        }

        $noticia->fill($data);
        $noticia->save();
        Log::info("Noticia {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        Noticia::where('Id', $before['Id'])->delete();
        Log::info("Noticia {$before['Id']} eliminado.");
    }

}