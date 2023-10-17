<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use App\Models\PrestacionPaqFact;
use Illuminate\Http\Request;

class PrestacionPaqFactController extends Controller
{
    //Eliminamos el examen de la prestación
    public function deleteExamen(Request $request)
    {
        $examen = PrestacionPaqFact::where('IdPrestacion', $request->Id)->where('IdExamen', $request->IdExamen);

        if ($examen) {
            return $examen->delete();
        }
    }
}
