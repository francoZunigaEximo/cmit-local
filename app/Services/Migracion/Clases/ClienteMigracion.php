<?php

namespace App\Services\Migration\Clases;

use App\Models\Autorizado;
use App\Models\Cliente;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class ClienteMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $cliente = Cliente::firstOrCreate('Id', $data['Id']);

        if($cliente->exists) {
            $default = [
                'TipoIdentificacion'=> $data['TipoIdentificacion'],
                'TipoCliente'=> $data['TipoCliente'],
                'Identificacion'=> $data['Identificacion'],
                'ParaEmpresa'=> $data['ParaEmpresa'],
                'RazonSocial'=> $data['RazonSocial'],
                'Nacionalidad'=> $data['Nacionalidad'],
                'CondicionIva'=> $data['CondicionIva'],
                'TipoPersona'=> $data['TipoPersona'],
                'Envio'=> $data['Envio'],
                'Entrega'=> $data['Entrega'],
                'IdActividad'=> $data['IdActividad'],
                'NombreFantasia'=> $data['NombreFantasia'],
                'Logo'=> $data['Logo'],
                'Bloqueado'=> $data['Bloqueado'],
                'Motivo'=> $data['Motivo'],
                'Direccion'=> $data['Direccion'],
                'IdLocalidad'=> $data['IdLocalidad'],
                'Provincia'=> $data['Provincia'],
                'CP'=> $data['CP'],
                'EMail'=> $data['EMail'],
                'ObsEMail'=> $data['ObsEMail'],
                'EMailResultados'=> $data['EMailResultados'],
                'Telefono'=> $data['Telefono'],
                'LogoCertificado'=> $data['LogoCertificado'],
                'Oreste'=> $data['Oreste'],
                'FPago'=> $data['FPago'],
                'ObsEval'=> $data['ObsEval'],
                'ObsCE'=> $data['ObsCE'],
                'ObsCO'=> $data['ObsCO'],
                'Generico'=> $data['Generico'],
                'SEMail'=> $data['SEMail'],
                'IdAsignado'=> $data['IdAsignado'],
                'EMailFactura'=> $data['EMailFactura'],
                'EnvioFactura'=> $data['EnvioFactura'],
                'EMailInformes'=> $data['EMailInformes'],
                'EnvioInforme'=> $data['EnvioInforme'],
                'Ajuste'=> $data['Ajuste'],
                'SinPF'=> $data['SinPF'],
                'SinEval'=> $data['SinEval'],
                'RF'=> $data['RF'],
                'Observaciones'=> $data['Observaciones'],
                'Estado'=> $data['Estado'],
                'Anexo'=> $data['Anexo'],
                'EMailAnexo'=> $data['EMailAnexo'],
                'Descuento'=> $data['Descuento'],
                'provincia2'=> $data['provincia2'],
                'ciudad'=> $data['ciudad']
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