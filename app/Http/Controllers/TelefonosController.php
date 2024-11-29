<?php

namespace App\Http\Controllers;

use App\Models\Telefono;
use Illuminate\Http\Request;

class TelefonosController extends Controller
{
    public function getTelefonos(Request $request)
    {

        if ($request->tipo == 'all') {

            $telefono = Telefono::where('IdCliente', $request->Id)->orderBy('Id', 'ASC')->get();

        } else {
            $telefono = Telefono::where('Id', $request->Id)->first();
        }

        return response()->json($telefono);

    }

    public function deleteTelefono(Request $request)
    {
        $telefono = Telefono::find($request->Id);

        if ($telefono) {
            return $telefono->delete();
        }
    }

    public function saveTelefono(Request $request)
    {
        $telefono = Telefono::where('Id', $request->IdRegistro)->first();

        if ($telefono) {

            $telefono->CodigoArea = $request->prefijo;
            $telefono->NumeroTelefono = $request->numero;
            $telefono->Observaciones = $request->observacion;
            $telefono->save();
        } else {

            Telefono::create([
                'Id' => Telefono::max('Id') + 1,
                'IdCliente' => $request->IdCliente,
                'CodigoArea' => $request->prefijo,
                'NumeroTelefono' => $request->numero,
                'Observaciones' => $request->observacion,
                'TipoEntidad' => 'i',
            ]);

        }
    }
}
