<?php

namespace App\Services\Migracion;

interface ProcesadorInterface
{
    public function insertar(array $data);

    public function actualizar(array $before, array $data);

    public function eliminar(array $data);
}