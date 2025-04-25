<?php

namespace App\Http\Controllers;

use App\Models\Fichalaboral;
use App\Models\PrestacionesTipo;
use Illuminate\Http\Request;

class FichaAltaController extends Controller
{
    public function save(Request $request)
    {
        $data = $request->all();

        if (!empty($data['Id'])) {
            return $this->updateFichaLaboral($data);
        } else {
            return $this->createFichaLaboral($data);
        }
    }

    //Verificamos para emplear la vista full
    public function verificar(Request $request)
    {
        $fichaLaboral = Fichalaboral::with(['empresa','art'])->where('IdPaciente', $request->Id)->orderBy('Id', 'Desc')->first();

        if ($fichaLaboral) {

            return response()->json([
                'fichaLaboral' => $fichaLaboral, 
                'clienteArt' => $fichaLaboral->art ?? '', 
                'cliente' => $fichaLaboral->empresa ?? ''
            ]);
        }

    }

    public function checkObs(Request $request)
    {
        $fichaLaboral = Fichalaboral::with(['paciente','art','empresa'])->where('IdPaciente', $request->Id)->orderBy('Id', 'Desc')->first();

        if($fichaLaboral){
            return response()->json([
                'obsArt' => $fichaLaboral->art,
                'obsEmpresa' => $fichaLaboral->empresa,
                'obsPaciente' => $fichaLaboral
            ]);
        }
    }

    // Devuelve el listado de prestaciones segun el financiador seleccionado
    public function getTipoPrestacion()
    {
        $tiposPrestacion = PrestacionesTipo::all();
        $tiposPrestacion = $tiposPrestacion->map(function ($tipoPrestacion) {
            return [
                'id' => $tipoPrestacion->Id,
                'nombre' => $tipoPrestacion->Nombre,
            ];
        });
        
        return response()->json(['tiposPrestacion' => $tiposPrestacion]);
    }

    public function verFicha(Request $request)
    {
        $query = Fichalaboral::where('IdPaciente', $request->Id)->orderBy('Id', 'DESC')->first();
        return redirect()->route('fichalaboral.edit', ['fichalaboral' => $query->Id]);
    }

    private function createFichaLaboral(array $data)
    {
        $data['Id'] = Fichalaboral::max('Id') + 1;
        Fichalaboral::create($data);

        return response()->json(['msg' => 'Se ha creado el registro correctamente'], 200);
    }

    private function updateFichaLaboral(array $data)
    {
        $fichaLaboral = Fichalaboral::find($data['Id']);
        if (!$fichaLaboral) {
            return response()->json(['msg' => 'Registro no encontrado'], 404);
        }

        $fichaLaboral->fill($data)->save();
        return response()->json(['msg' => 'Se ha actualizado el registro correctamente'], 200);
    }

}
