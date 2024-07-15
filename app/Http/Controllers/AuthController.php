<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Provincia;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        if (Auth::attempt([
            'name' => $request->usuario,
            'password' => $request->password,

        ])) {
            if(Auth::user()->inactivo === 1) {
                Session::flush();
                Auth::logout();
                return redirect()->route('login')->withFail('El usuario se encuentra bloqueado. Consulte con el administrador');
            }

            if(Auth::user()->Anulado === 1) {
                $fecha = Auth::user()->updated_at;
                $nuevaFecha= Carbon::parse($fecha)->format('d/m/Y \a \l\a\s H:i');
                Session::flush();
                Auth::logout();
                return redirect()->route('login')->withFail('El usuario fue eliminado el '. $nuevaFecha);
            }

            if (Auth::user()->role->isEmpty()) {
                Session::flush();
                Auth::logout();
                return redirect()->route('login')->withFail('El usuario no tiene ningun rol asignado. Consulte con el administrador');
            }

            $request->session()->regenerate();

            if(Auth::check()){

                $roles = Auth::user()->role;

                foreach ($roles as $rol) {
                    if ($rol->nombre == 'Efector' || $rol->nombre == 'Informador') {
                        return redirect()->route('profesionales.index');
                    }
                }
            }

            //return redirect('/home');
            return redirect()->route('noticias.index');
        } else {

            return redirect()->route('login')->withFail('Las credenciales, no son correctas');
        }
    }

    public function register(Request $request)
    {
        $pass = "cmit1234";

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($pass),
            'datos_id' => 0,
            'inactivo' => 1,
        ]);

        return response()->json(User::max('id'));
    }

    public function logout()
    {
        Session::flush();
        Auth::logout();

        return redirect()->route('login')->withSuccess('Se ha cerrado la sesion correctamente.');
    }

    //Vista para cambiar password
    public function profile()
    {
        $usuario = Auth::user();
        $query = User::join('datos', 'users.datos_id', '=', 'datos.Id')
            ->join('localidades', 'datos.IdLocalidad', '=', 'localidades.Id')
            ->select(
                "users.name as Name",
                "users.email as EMail",
                "users.inactivo as Inactivo",
                "datos.Id as IdDatos",
                "datos.Telefono as Telefono",
                "datos.TipoIdentificacion as TipoIdentificacion",
                "datos.Identificacion as Identificacion",
                "datos.TipoDocumento as TipoDocumento",
                "datos.Documento as Documento",
                "datos.Nombre as Nombre",
                "datos.Apellido as Apellido",
                "datos.FechaNacimiento as FechaNacimiento",
                "datos.Direccion as Direccion",
                "datos.IdLocalidad as ILocalidad",
                "datos.Provincia as Provincia",
                "datos.CP as CP",
                "datos.Id as Id",
                "localidades.Nombre as NombreLocalidad"
            )->find($usuario->id);
        $provincias = Provincia::all();

        return view('layouts.change', compact(['query', 'provincias']));
    }

    public function updatePass(Request $request)
    {
            $user = $request->user();
            $user->password = Hash::make($request->password);
            $user->save();
    }

    public function checkPassword(Request $request)
    {
        return response()->json(Hash::check($request->password, Auth::user()->password));
    }

}
