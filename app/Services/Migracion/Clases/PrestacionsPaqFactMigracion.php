<?php

namespace App\Services\Migration\Clases;

use App\Services\Migracion\ProcesadorInterface;
use App\Models\PrestacionPaqFact;
use Illuminate\Support\Facades\Log;

class PrestacionsPaqFactMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {

        $prestacionPaqFact = PrestacionPaqFact::firstOrNew(['Id' => $data['Id']]);

        if (!$prestacionPaqFact->exists) {

            $default = [
                'IdPrestacion'=> $data['IdPrestacion'],
                'IdItem' => $data['IdItem'],
                'IdExamen' => $data['IdExamen'],
                'IdPaqFact' => $data['IdPaqFact'],
            ];

            $prestacionPaqFact->fill($default);
        }

        $prestacionPaqFact->fill($data);
        $prestacionPaqFact->save();
        Log::info("Prestacion Paq Fact {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        PrestacionPaqFact::where('Id', $before['Id'])->delete();
        Log::info("Prestacion Paq Fact {$before['Id']} eliminada.");
    }

}