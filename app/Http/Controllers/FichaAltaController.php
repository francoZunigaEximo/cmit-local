<?php

namespace App\Http\Controllers;

use App\Models\Fichalaboral;
use App\Models\PrestacionesTipo;
use App\Services\Facturas\PrestaFichaFactura;
use Illuminate\Http\Request;
class FichaAltaController extends Controller
{

    protected $detalleFactura;

    public function __construct(PrestaFichaFactura $detalleFactura)
    {
        $this->detalleFactura = $detalleFactura;
    }

    public function save(Request $request)
    {
        $data = $request->all();

        if (!empty($data['Id'])) {
            return $this->update($data);
        } else {
            return $this->create($data);
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

    private function create(array $data)
    {
        $nuevoId = Fichalaboral::max('Id') + 1;

        $guardar = [
            'Sucursal' => $data['Sucursal'],
            'Tipo' => $data['Tipo'],
            'NroFactura' => $data['NroFactura'],
            'NroFactProv' => $data['NroFactProv'],
            'fichalaboral_id' => $nuevoId,
            'prestacion_id' => 0
        ];

        //Verificamos si es un pago contado para guardar los datos de la factura
        $idFactura = in_array($data['Pago'], ['A','B']) 
            ? $this->detalleFactura->crear($guardar)
            : null;

        $data['Id'] = $nuevoId;
        $data['datos_facturacion_id'] = $idFactura ?? 0;
        Fichalaboral::create($data);

        return response()->json([
            'msg' => 'Se ha creado el registro correctamente', 
            'datos_facturacion_id' => $idFactura
        ], 200);
    }

    private function update(array $data)
    {
        $fichaLaboral = Fichalaboral::where('Id', $data['Id'])->orderBy('Id', 'desc')->first();
        if (!$fichaLaboral) {
            return response()->json(['msg' => 'Registro no encontrado'], 404);
        }

        $idFactura = null;

        $guardar = [
            'Sucursal' => $data['Sucursal'],
            'Tipo' => $data['Tipo'],
            'NroFactura' => $data['NroFactura'],
            'NroFactProv' => $data['NroFactProv'],
            'fichalaboral_id' => $data['Id']
        ];

        $fichaLaboral->datos_facturacion_id == 0 || empty($fichaLaboral->datos_facturacion_id)
            ? $idFactura = $this->detalleFactura->crear($guardar)
            : $this->detalleFactura->modificar($guardar, $fichaLaboral->datos_facturacion_id);


        $data['datos_facturacion_id'] = $idFactura;   
        $fichaLaboral->fill($data)->save();
        
        return response()->json([
            'msg' => 'Se ha actualizado el registro correctamente', 
            'datos_facturacion_id' => $idFactura
        ], 200);
    }

}


