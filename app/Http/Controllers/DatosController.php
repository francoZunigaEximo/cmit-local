<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Personal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\CheckPermission;

class DatosController extends Controller
{
    use CheckPermission; 

    public function save(Request $request)
    {   
        if (!$this->hasPermission("datos_add")) {
            return response()->json(['msg' => 'No tiene permisos'], 403);
        }

        $Id =  in_array($request->Id, [NULL, 0]) ? Personal::max('Id') + 1 : $request->Id;

        if(in_array($request->Id, [NULL, 0])){
                
            Personal::create([
                'Id' => $Id,
                'TipoIdentificacion' => $request->TipoIdentificacion ?? '',
                'Apellido' => $request->Apellido ?? '',
                'Nombre' => $request->Nombre ?? '',
                'TipoDocumento' => $request->TipoDocumento ?? '',
                'Documento' => $request->Documento ?? '',
                'Identificacion' => $request->Identificacion ?? '',
                'Telefono' => $request->Telefono ?? '',
                'FechaNacimiento' => $request->FechaNacimiento ?? '0000-00-00',
                'Provincia' => $request->Provincia ?? '',
                'IdLocalidad' => $request->IdLocalidad ?? '', 
                'Direccion' => $request->Direccion ?? '',
                'CP' => $request->CP ?? ''
            ]);

            $user = User::find($request->UserId);

            if($user) {
                $user->email = $request->email ?? '';
                $user->datos_id = $Id;
                $user->save();
            }

            return response()->json(['msg' => 'Se ha creado el usuario correctamente'], 201);

        }else{

            $query = Personal::where('Id', $Id)->first();
                
            if($query) 
            {
                $query->TipoIdentificacion = $request->TipoIdentificacion;
                $query->Apellido = $request->Apellido;
                $query->Nombre = $request->Nombre;
                $query->TipoDocumento = $request->TipoDocumento;
                $query->Documento = $request->Documento;
                $query->Identificacion = $request->Identificacion;
                $query->Telefono = $request->Telefono;
                $query->FechaNacimiento = $request->FechaNacimiento;
                $query->Provincia = $request->Provincia;
                $query->IdLocalidad = $request->IdLocalidad;
                $query->Direccion = $request->Direccion; 
                $query->CP = $request->CP;
                $query->user->email = $request->email;
                $query->user->datos_id = $Id;
                $query->push();

                return response()->json(['msg' => 'Se han actualizado correctamente los datos del usuario'], 200);
            }else{
                return response()->json(['msg' => 'No se han actualizado los datos. Intentelo más tarde'], 500);
            }
        }
    }

}
