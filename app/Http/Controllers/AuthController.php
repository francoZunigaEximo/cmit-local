<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        if (Auth::attempt([
            'name' => $request->usuario,
            'password' => $request->password,

        ])) {
            $request->session()->regenerate();

            if(!empty(Auth::user()->IdProfesional) && Auth::user()->IdProfesional !== 0){

                session()->put('mProf', '0');
                session()->put('choiseT', '0');
                return redirect()->route('profesionales.index');
            }

            //return redirect('/home');
            return redirect()->route('prestaciones.index');
        } else {

            return redirect()->route('login')->withFail('Las credenciales, no son correctas');
        }
    }

    public function register(Request $request)
    {

        $rules = [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users,email,'.Auth::id(),
            'password' => 'required|min:8|same:repassword',
        ];

        $validated = Validator::make($request->all(), $rules);

        if ($validated->fails()) {
            return redirect()->route('register')->withErrors($validated->errors())->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')->withSuccess('Se ha registrado con éxito. Inicie sesión para continuar.');
    }

    public function logout()
    {
        Session::flush();
        Auth::logout();

        return redirect()->route('login')->withSuccess('Se ha cerrado la sesion correctamente.');
    }

    //Vista para cambiar password
    public function cambiarPass()
    {
        return view('layouts.change');
    }

    public function cambiarPostPass(Request $request)
    {
        $rules = [
            'passactual' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    if (! Hash::check($value, $request->user()->password)) {
                        $fail('La contraseña es incorrecta');
                    }
                },
            ],
            'newpass' => 'required|min:8|max:20|string',
            'newpass_confirmation' => 'required|same:newpass',
        ];

        $msg = [
            'passactual.required' => 'Debe ingresar su contraseña actual para poder realizar el cambio',
            'newpass.min' => 'La contraseña debe tener un minimo de 8 caracteres y un maximo de 20', 'newpass.max' => 'La contraseña debe tener un minimo de 8 caracteres y un maximo de 20',
            'newpass.required' => 'La nueva contraseña es un campo requerido',
            'newpass_confirmation.required' => 'Debe volver a escribir la contraseña para confirmar su cambio',
            'newpass_confirmation.same' => 'Las nuevas contraseñas deben coincidir. Las mismas no son iguales',
        ];

        $validated = Validator::make($request->all(), $rules, $msg);

        if ($validated->fails()) {
            return back()->withInput()->withErrors($validated->messages());
        } else {

            $user = $request->user();
            $user->password = Hash::make($request->newpass);
            $user->save();

            return redirect()->back()->withSuccess('¡La contraseña se ha actualizado correctamente!');

        }
    }
}
