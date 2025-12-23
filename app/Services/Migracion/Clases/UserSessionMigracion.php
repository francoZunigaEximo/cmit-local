<?php

namespace App\Services\Migration\Clases;

use App\Models\Telefono;
use App\Models\User;
use App\Models\UserSession;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class UserSessionMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $user = UserSession::firstOrCreate('Id', $data['Id']);

        if($user->exists) {
            $default = [
                'user_id'=>$data['user_id'],
                'session_id'=>$data['session_id'],
                'ip_address'=>$data['ip_address'],
                'user_agent'=>$data['user_agent'],
                'login_at'=>$data['login_at'],
                'logout_at'=>$data['logout_at'],
                'last_heartbeat_at'=>$data['last_heartbeat_at']
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
        UserSession::where('Id', $before['Id'])->delete();
        Log::info("UserSession {$before['Id']} eliminado.");
    }

}