<?php

namespace App\Services\Facturas;

use App\Models\FichaPrestacionFactura;

class PrestaFichaFactura
{
    public function crear(array $data): ?int
    {
        $query = FichaPrestacionFactura::create($data);
        $query->refresh();
        return $query->id;
    }

    public function modificar(array $data, int $id): void
    {
        $query = FichaPrestacionFactura::find($id);

        if($query) {
            $query->fill($data)->save();
        }
    }
}