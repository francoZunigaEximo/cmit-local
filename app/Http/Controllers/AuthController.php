<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class AuthController extends Controller
{
    public function login(Request $request) 
    {

        if(Auth::attempt([
            'name' => $request->usuario, 
            'password' => $request->password

            ])){
                $request->session()->regenerate();
                return redirect('/home');

        }else{

            return redirect()->route('login')->withFail("Las credenciales, no son correctas");
        }
    }

    public function register(Request $request) {

        $validated = Validator::make($request->all(),[
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|max:255|unique:users,email,'.Auth::id(),
            'password'   => 'required|min:8|same:repassword',
        ]);

        if($validated->fails()) return redirect()->route("register")->withErrors($validated->errors())->withInput();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route("login")->withSuccess("Se ha registrado con éxito. Inicie sesión para continuar.");
    }


    public function logout()
    {
        Auth::logout();
        return redirect()->route("login")->withSuccess("Se ha cerrado la sesion correctamente.");
    }

    public function testCreate()
    {
        echo Hash::make("Dicom1975");
    }

    public function cambiarPass()
    {
        return view('layouts.change');
    }

    public function cambiarPostPass(Request $request)
    {   

    }
}
