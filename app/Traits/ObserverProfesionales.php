<?php

namespace App\Traits;

use App\Models\Telefono;

trait ObserverProfesionales
{
    
    public function setTelefono(int $id, mixed $numero)
    {
        if (empty($numero)) return;

        Telefono::create([
            'Id' => Telefono::max('Id') + 1,
            'NumeroTelefono' => $numero,
            'IdProfesional' => $id
        ]);
    }
    
    public function updateTelefono(int $id, mixed $numero)
    {
        $telefono = Telefono::where('IdProfesional', $id)->first();

        if($telefono){

            $telefono->NumeroTelefono = $numero;
            $telefono->save();
        }

    }

    public function checkTelefono(int $id)
    {
        $telefono = Telefono::where('IdProfesional', $id)->exists();

        return $telefono;
    }

}