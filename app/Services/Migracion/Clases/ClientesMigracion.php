<?php

namespace App\Services\Migration\Clases;

use App\Models\Cliente;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class ClientesMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $cliente = Cliente::firstOrCreate('Id', $data['data']);

        if($cliente->exists) {

            $default = [
                'RazonSocial' => $data['RazonSocial'],
                'Nacionalidad' => $data['Nacionalidad'],
                'CondicionIva' => $data['CondicionIva'],
                'TipoIdentificacion' => $data['TipoIdentificacion'],
                'Identificacion' => $data['Identificacion'],
                'Observaciones' => $data['Observaciones'],
                'TipoPersona' => $data['TipoPersona'],
                'Envio' => $data['Envio'],
                'Entrega' => $data['Entrega'],
                'ParaEmpresa' => $data['ParaEmpresa'],
                'IdActividad' => $data['IdActividad'],
                'NombreFantasia' => $data['NombreFantasia'],
                'Logo' => $data['Logo'],
                'Bloqueado' => $data['Bloqueado'],
                'Motivo' => $data['Motivo'],
                'Direccion' => $data['Direccion'],
                'IdLocalidad' => $data['IdLocalidad'],
                'Provincia' => $data['Provincia'],
                'CP' => $data['CP'],
                'Email' => $data['Email'],
                'ObsEmail' => $data['ObsEmail'],
                'EMailResultados' => $data['EMailResultados'],
                'Telefono' => $data['Telefono'],
                'LogoCertificado' => $data['LogoCertificado'],
                'Oreste' => $data['Oreste'],
                'TipoCliente' => $data['TipoCliente'],
                'FPago' => $data['FPago'],
                'ObsVal' => $data['ObsVal'],
                'ObsCE' => $data['ObsCE'],
                'Generico' => $data['Generico'],
                'SEMail' => $data['SEMail'],
                'ObsCO' => $data['ObsCO'],
                'IdAsignado' => $data['IdAsignado'],
                'EMailFactura' => $data['EMailFactura'],
                'EnvioFactura' => $data['EnvioFactura'],
                'EMailInformes' => $data['EMailInformes'],
                'EnvioInforme' => $data['EnvioInforme'],
                'Ajuste' => $data['Ajuste'],
                'SinPF' => $data['SinPF'],
                'SinEval' => $data['SinEval'],
                'RF' => $data['RF']
            ];

            $cliente->fill($default);
        }

        $cliente->fill($data);
        $cliente->save();

        Log::info("Cliente {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        Cliente::where('Id', $before['Id'])->delete();
        Log::info("Cliente {$before['Id']} eliminado.");
    }

}