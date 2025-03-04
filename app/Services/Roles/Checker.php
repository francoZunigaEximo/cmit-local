<?php 

namespace App\Services\Roles;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class Checker 
{
    public function userAdmin(int $id): mixed
    {
        $query = User::with('role')->find($id);
        return $query->role->contains('nombre', 'Administrador');
    }

    public function roleList(?int $id, ?string $name)
    {
        $query = User::join('user_rol','users.id', '=', 'user_rol.user_id')
            ->join('roles', 'user_rol.rol_id', '=', 'roles.Id')
            ->select(DB::raw("GROUP_CONCAT(roles.nombre SEPARATOR ',') as NombreRol"));
        $user = null;

        if ($id) {
            $user = $query->where('Id', $id)->first();
        } else if ($name) {
            $user = $query->where('name', $name)->first();
        } else {
            return null;
        }

        if ($user) {
            $listado = explode(',', $user->NombreRol);
            return $listado;
        }

    }


}