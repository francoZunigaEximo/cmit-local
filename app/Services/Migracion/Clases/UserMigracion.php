<?php

namespace App\Services\Migration\Clases;

use App\Models\Telefono;
use App\Models\User;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class UserMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $user = User::firstOrCreate('Id', $data['Id']);

        if($user->exists) {
            $default = [
                'name'=>$data['name'],
                'email'=>$data['email'],
                'password'=>$data['password'],
                'profesional_id'=>$data['profesional_id'],
                'datos_id'=>$data['datos_id'],
                'inactivo'=>$data['inactivo'],
                'Anulado'=>$data['Anulado']
            ];

            $user->fill($default);
        }

        $user->fill($data);
        $user->save();
        Log::info("User {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        User::where('Id', $before['Id'])->delete();
        Log::info("User {$before['Id']} eliminado.");
    }

}