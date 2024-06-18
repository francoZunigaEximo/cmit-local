<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaMail;
use App\Models\Cliente;
use App\Models\EnviarModelo;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Traits\CheckPermission;
use App\Mail\EnvioResultadosMail;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendEmailJob;
use App\Enum\HttpStatus;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class MensajesController extends Controller
{
    use CheckPermission;

    public function index()
    {
        if (!$this->hasPermission("mensajeria_edit")) {
            return abort(403);
        }

        return view("layouts.mensajes.index");
    }

    public function search(Request $request)
    {
        if (!$this->hasPermission("mensajeria_edit")) {
            return response()->json([], 403);
        }

        if($request->ajax()){
            $query = Cliente::leftJoin('prestaciones', 'clientes.Id', '=', 'prestaciones.IdEmpresa')
            ->select(
                'clientes.Id as Id',
                'clientes.RazonSocial as RazonSocial',
                'clientes.ParaEmpresa as ParaEmpresa',
                'clientes.Identificacion as Identificacion',
                'clientes.TipoCliente as TipoCliente',
                'clientes.FPago as FPago',
                'clientes.EMailFactura as EMailFactura',
                'clientes.EMailResultados as EmailMasivo',
                'clientes.EMailInformes as EMailInformes',
                'clientes.Bloqueado as Bloqueado'
            );

            $query->when(!empty($request->NroDesde) && !empty($request->NroHasta), function($query) use ($request) {
                $query->whereBetween('clientes.Id', [$request->NroDesde, $request->NroHasta]);
            });

            $query->when(!empty($request->NroDesde) && empty($request->NroHasta), function($query) use ($request) {
                $query->where('clientes.Id', $request->NroDesde);
            });

            $query->when(!empty($request->Tipo) && ($request->Tipo === 'E' || $request->Tipo === 'A'), function($query) use ($request) {
                $query->where('clientes.TipoCliente', $request->Tipo);
            });

            $query->when(!empty($request->Tipo) && $request->Tipo === 'todos', function($query) {
                $query->whereIn('clientes.TipoCliente', ['A','E']);
            });

            $query->when(!empty($request->Pago) && ($request->Pago === 'A' || $request->Pago === 'B' || $request->Pago === 'C'), function($query) use ($request) {
                $query->where('clientes.FPago', $request->Pago);
            });

            $query->when(!empty($request->Pago) && $request->Pago === 'todos', function($query) {
                $query->whereIn('clientes.FPago', ['A','B','C']);
            });

            $query->when(!empty($request->Bloqueado) && $request->Bloqueado === 'bloqueado', function($query) use ($request) {
                $query->where('clientes.Bloqueado', 1);
            });

            $query->when(!empty($request->Bloqueado) && $request->Bloqueado === 'noBloqueado', function($query) use ($request) {
                $query->where('clientes.Bloqueado', 0);
            });

            $query->when(!empty($request->Bloqueado) && $request->Bloqueado === 'todos', function($query) {
                $query->whereIn('clientes.Bloqueado', [0,1]);
            });

            $query->when(!empty($request->FechaDesde) && !empty($request->FechaHasta), function($query) use ($request) {
                $query->whereBetween('prestaciones.Fecha', [$request->FechaDesde, $request->FechaHasta]);
            });

            $result = $query->where('clientes.Id', '<>', 0)
                            ->groupBy('clientes.Id')
                            ->orderBy('clientes.Id', 'DESC')
                            ->get();

            return Datatables::of($result)->make(true);
        }

        return view("layouts.mensajes.index");
    }

    public function edit(Cliente $mensaje)
    {
        if (!$this->hasPermission("mensajeria_edit")) {
            return abort(403);
        }

        return view("layouts.mensajes.edit", compact('mensaje'));
    }


    public function updateEmail(Request $request): mixed
    {   
        if (!$this->hasPermission("mensajeria_edit")) {
            return response()->json([], 403);
        }

        $cliente = Cliente::find($request->Id);
        if($cliente)
        {
            $cliente->EMailResultados = $request->EMailResultados;
            $cliente->EMailInformes = $request->EMailInformes;
            $cliente->EMailFactura = $request->EMailFactura;
            $cliente->save(); 
        }

        return response()->json(['msg' => 'Se ha actualizado todo correctamente'], 200);
    }

    public function loadModelos()
    {
        if (!$this->hasPermission("mensajeria_edit")) {
            return response()->json([], 403);
        }

        $modelos = EnviarModelo::whereNot('Id', 0)->get();

        return response()->json($modelos);
    }

    public function loadMensaje(Request $request)
    {
        if (!$this->hasPermission("mensajeria_edit")) {
            return response()->json([], 403);
        }

        $modelo = EnviarModelo::find($request->Id);

        return response()->json($modelo);
    }

    public function show(){}

    public function auditoria(Request $request)
    {
        if(!$this->hasPermission("mensajeria_edit")){
            return abort(403);
        }

        if($request->ajax()){

            $query = AuditoriaMail::query();

            $query->when(!empty($request->FechaDesde) && !empty($request->FechaHasta), function($query) use ($request) {
                $query->whereBetween('Fecha', [$request->FechaDesde, $request->FechaHasta]);
            });

            $result = $query->orderBy('Fecha', 'DESC')->get();

            return DataTables::of($result)->make(true);
        }

        return view("layouts.mensajes.auditoria");
    }

    public function modelos(Request $request)
    {
        if (!$this->hasPermission("mensajeria_edit")) {
            return abort(403);
        }

        if($request->ajax()){
            $modelos = EnviarModelo::whereNot('Id', 0)->get();

            return Datatables::of($modelos)->make(true);
        }

        return view("layouts.mensajes.modelos");
    }

    public function deleteModelo(Request $request)
    {
        if (!$this->hasPermission("mensajeria_edit")) {
            return response()->json([], 403);
        }

        $modelo = EnviarModelo::find($request->Id);
        
        if($modelo){
            $modelo->delete();
            return response()->json([], 200);
        }

        return response()->json([], 400);
    }

    public function createModelo()
    {
        if(!$this->hasPermission("mensajeria_edit")){
            return abort(403);
        }

        return view('layouts.mensajes.create');
    }

    public function saveModelo(Request $request)
    {
        if (!$this->hasPermission("mensajeria_edit")) {
            return response()->json([], 403);
        }

        $guardar = EnviarModelo::create([
            'Id' => EnviarModelo::max('Id') + 1,
            'Nombre' => $request->Nombre,
            'Asunto' => $request->Asunto,
            'Cuerpo' => $request->Cuerpo
        ]);

        if($guardar) {
            return response()->json(['msg' => 'Se ha creado el modelo correctamente'], 200);
        }
   
        return response()->json([], 500); 
    }

    public function editModelo(Request $request)
    {
        if (!$this->hasPermission("mensajeria_edit")) {
            return abort(403);
        }

        $modelo = EnviarModelo::find($request->Id);

        return view('layouts.mensajes.editModelo', compact('modelo'));
    }

    public function actualizarModelo(Request $request)
    {
        if (!$this->hasPermission("mensajeria_edit")) {
            return response()->json([], 403);
        }

        $modelo = EnviarModelo::find($request->Id);

        if($modelo){
            $modelo->Nombre = $request->Nombre;
            $modelo->Asunto = $request->Asunto;
            $modelo->Cuerpo = $request->Cuerpo;
            $modelo->save();

            return response()->json([], 200);
        }

        return response()->json(['msg' => 'No se ha podido actualizar el modelo'], 400);
    }

    public function verAuditoria(Request $request)
    {
        if (!$this->hasPermission("mensajeria_edit")) {
            return response()->json([], 403);
        }

        $auditoria = AuditoriaMail::find($request->Id);

        if($auditoria){
            $data = $request->Tipo === 'destinatario' ? $auditoria->Destinatarios : $auditoria->Detalle;
            return response()->json($data, 200);
        }
        
        return response()->json([], 500);
    }

    public function sendEmails(Request $request)
    {
        if (!$this->hasPermission("mensajeria_edit")) {
            return response()->json([], 403);
        }

        $Ids = $request->Id;

        if (!is_array($Ids)) {
            $Ids = [$Ids];
        }

        foreach ($Ids as $Id) {
        
            $cliente = Cliente::find($Id);

            if($cliente){

                $facturas = $request->Facturas == 'true' ? explode(",", $cliente->EMailFactura) : [];
                $informes = $request->Informes == 'true' ? explode(",", $cliente->EMailInformes) : [];
                $resultados = $request->Masivo == 'true' ? explode(",", $cliente->EMailResultados) : [];

                $correos = [];
                $correos = $this->addCorreos($correos, $facturas);
                $correos = $this->addCorreos($correos, $informes);
                $correos = $this->addCorreos($correos, $resultados);
                
                $correos = array_unique($correos);

                if($correos[0] === '' && count($correos) === 1){
                    return response()->json(['msg' => 'No hay correos activos para realizar el envio'], 400);
                }

                if(!$this->testing()){
                    return response()->json(['msg' => 'No se ha podido conectar con el servidor SMTP'], 500);
                }

                foreach ($correos as $correo) {

                    SendEmailJob::dispatch($correo, $request->Asunto, $request->Cuerpo);

                    AuditoriaMail::create([
                        'Id' => AuditoriaMail::max('Id') + 1,
                        'Fecha' => date('Y-m-d H:i:s'),
                        'Destinatarios' => $correo,
                        'Asunto' => $request->Asunto,
                        'Detalle' => $request->Cuerpo
                    ]);
                    //var_dump($correo, $request->Asunto, $request->Cuerpo);
            
                    /*$email = new EnvioResultadosMail(['subject' => $request->Asunto, 'content' => $request->Cuerpo]);
                    Mail::to($correo)->queue($email);*/
                } 
            }

            return response()->json(['msg' => 'Se han enviado los mensajes correctamente'], 200);
        }

        return response()->json([], 500);
    }

    public function testEmail(){

        if (!$this->hasPermission("mensajeria_edit")) {
            return response()->json([], 403);
        }

        $transport = new EsmtpTransport(
        config('app.mailhost'), 
        config('app.mailport'), 
        config('app.mailencryption')
        );
        $transport->setUsername(config('app.mailusername'));
        $transport->setPassword(config('app.mailpassword'));

        try { 
            $transport->start();
            return response()->json(['msg' => 'Ping al servidor SMTP realizado. Servidor activo'], 200);
        
        }catch (\Exception $e) {
            return response()->json(['msg' => 'No se ha podido conectar con el servidor: ' . $e->getMessage()]);
        }
    }

    private function testing()
    {
        $transport = new EsmtpTransport(
            config('app.mailhost'), 
            config('app.mailport'), 
            config('app.mailencryption')
            );
            $transport->setUsername(config('app.mailusername'));
            $transport->setPassword(config('app.mailpassword'));

            try { 
                $transport->start();
                return true;
            
            }catch (\Exception $e) {
                return false;
            }
    }

    private function addCorreos($correos, $nuevosCorreos) {

        if (is_array($nuevosCorreos)) {
            $correos = array_merge($correos, $nuevosCorreos);
        } else {
            $correos[] = $nuevosCorreos;
        }

        return $correos;
    }

}
