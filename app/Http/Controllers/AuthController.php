<?php

namespace App\Http\Controllers;

use App\Events\LstProfesionalesEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Provincia;
use App\Models\UserSession;
use App\Services\Llamador\Profesionales;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

use function PHPUnit\Framework\isEmpty;

class AuthController extends Controller
{
    CONST PASS = "cmit1234";

    protected $listadoProfesionales;

    public function __construct(Profesionales $listadoProfesionales)
    {
        $this->listadoProfesionales = $listadoProfesionales;
    }

    public function login(Request $request)
    {
        if (Auth::attempt([
            'name' => $request->usuario,
            'password' => $request->password,

        ])) {
            $user = Auth::user();

            if($user->inactivo === 1) {
                Session::flush();
                Auth::logout();
                return redirect()
                    ->route('login')
                    ->withFail('El usuario se encuentra bloqueado. Consulte con el administrador');
            }

            if($user->Anulado === 1) {
                $fecha = Auth::user()->updated_at;
                $nuevaFecha= Carbon::parse($fecha)->format('d/m/Y \a \l\a\s H:i');
                Session::flush();
                Auth::logout();
                return redirect()
                    ->route('login')
                    ->withFail('El usuario fue eliminado el '. $nuevaFecha);
            }

            if ($user->role->isEmpty()) {
                Session::flush();
                Auth::logout();
                return redirect()
                    ->route('login')
                    ->withFail('El usuario no tiene ningun rol asignado. Consulte con el administrador');
            }

            $request->session()->regenerate();
            
            $this->session_user(); //registramos el inicio de sesión
            $this->session_user_duplicados($request->password); //eliminamos sesiones duplicadas

            $roles = Auth::user()->role->pluck('nombre'); 

            if ($roles->contains('Efector') || $roles->contains('Informador')) {

                $efectores = $this->listadoProfesionales->listado('Efector');
                event(new LstProfesionalesEvent($efectores));

                return redirect()->route('mapas.index');
            }

            if ($roles->contains('Evaluador') || $roles->contains('Evaluador ART')) {
                return redirect()->route('mapas.index');
            }
            
            return redirect()->route('noticias.index');
            
        } else {

            return redirect()
                ->route('login')
                ->withFail('Las credenciales, no son correctas');
        }
    }

    public function register(Request $request)
    {
        try {
            $register = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make(SELF::PASS),
                'datos_id' => 0,
                'inactivo' => 1,
            ]);
    
            $register->refresh();
            return response()->json(['msg' => 'Se ha creado el usuario correctamente', 'id' => $register->id], 201);
    
        } catch (QueryException $e) {
            return response()->json([
                'msg' => 'No se ha podido registrar al usuario. Por favor, intente nuevamente más tarde.'
            ], 500);
    
        } catch (\Exception $e) {
            return response()->json([
                'msg' => 'Se ha producido un error inesperado. Por favor, intente nuevamente más tarde.'
            ], 500);
        }
    }

    public function logoutId(Request $request)
    {
        $sessions = Redis::keys("session:*");
        $userId = intval($request->Id);


        $this->session_user_logout($userId);

        $efectores = $this->listadoProfesionales->listado('Efector');
        event(new LstProfesionalesEvent($efectores));

        foreach ($sessions as $session) {
            $user = Redis::hget($session, 'user_id'); //En redis obtengo la sesion

            if($user === intval($request->Id)) {
                Redis::del($session);
            }
        }

        //Sesion temporal para hacer el logout
        if (Auth::check() && Auth::id() === $userId) {
            
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

        } 

        return response()->json(['msg' => 'Se ha cerrado la sesión por inactividad'], 200);
    }
    
    public function logout()
    {
        $this->session_user_logout(Auth::user()->id);
        Session::flush();//Invalidamos la sesión actual
        Auth::logout();
        request()->session()->regenerateToken();// Regenera el token CSRF

        $efectores = $this->listadoProfesionales->listado('Efector');
        event(new LstProfesionalesEvent($efectores));

        return redirect()
                ->route('login')
                ->withSuccess('Se ha cerrado la sesion correctamente.');
    }

    //Vista para cambiar password
    public function profile()
    {
        $query = $this->get_usuario(Auth::user()->id);
        $provincias = Provincia::all();

        return view('layouts.change', compact(
            [
                'query', 
                'provincias'
            ])
        );
    }

    public function updatePass(Request $request)
    {
            $user = $request->user();
            $passActual = $user->password;

            if (Hash::check($request->password, $passActual)) {
                return response()->json(['msg' => 'La nueva contraseña no puede ser la misma que la actual'], 409);
            }

            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json(['msg' => 'Se ha actualizado la contraseña correctamente'], 200);
    }

    public function checkPassword(Request $request)
    {
        return response()->json(Hash::check($request->password, Auth::user()->password));
    }

    public function forzarLogout($userId)
    {
        Artisan::call('session:clear-user-sessions', [$userId]);
        UserSession::where('user_id', $userId)
            ->whereNull('logout_at')
            ->update(['logout_at' => now()]);

        return response()->json(['msg' => 'Sesion cerrada correctamente'], 200);
    }

    private function session_user()
    {
        $getId = auth()->user()->id;

        return UserSession::create([
            'user_id' => $getId,
            'session_id' => session()->getId(),
            'ip_address' => request()->ip(),
            'user_agent' => substr((string)request()->userAgent(), 0, 255),
            'login_at' => now(),
        ]);
    }

    private function session_user_logout($user)
    {
        Log::info("Intentando cerrar sesión del usuario ID: " . $user); 

        return UserSession::where('user_id', $user)
            ->whereNull('logout_at')
            ->update([
                'logout_at' => now()
            ]);
    }

    private function session_user_duplicados(string $password)
    {
        $userId = auth()->user()->id;
        Auth::logoutOtherDevices($password); //cerramos todas las sesiones duplicadas

        return UserSession::where('user_id',$userId)
            ->whereNull('logout_at')
            ->where('session_id', '!=', session()->getId())
            ->update([
                'logout_at' => now()
            ]);
    }

    private function get_usuario(int $id)
    {
        return User::join('datos', 'users.datos_id', '=', 'datos.Id')
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
            )->find($id);
    }

}
