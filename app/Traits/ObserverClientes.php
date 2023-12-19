<?php

namespace App\Traits;

use App\Models\Telefono;

trait ObserverClientes
{
    public function setTelefono(int $id, mixed $numero)
    {
        if (empty($numero)) return '';

        if (! empty($numero) && is_array($numero)) {
            foreach ($numero as $telefonoJSON) {
                $telefonoArray = json_decode($telefonoJSON, true);
                if (is_array($telefonoArray) && count($telefonoArray) === 3) {

                    Telefono::create([
                        'Id' => Telefono::max('Id') + 1,
                        'IdCliente' => $id,
                        'CodigoArea' => $telefonoArray[0], //Prefijo
                        'NumeroTelefono' => $telefonoArray[1], // Número
                        'Observaciones' => $telefonoArray[2], // Observación
                        'TipoEntidad' => 'i',
                    ]);
                }
            }
        }
    }
}