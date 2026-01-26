<?php

namespace App\Services\Migration\Clases;

use App\Models\Paciente;
use App\Services\Migracion\ProcesadorInterface;
use Illuminate\Support\Facades\Log;

class PacientesMigracion implements ProcesadorInterface
{
    public function insertar(array $data)
    {
        $paciente = Paciente::firstOrCreate('Id', $data['Id']);

        if($paciente->exists) {
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

            $paciente->fill($default);
        }

        $paciente->fill($data);
        $paciente->save();
    
        Log::info("Paciente {$data['Id']} migrado/creado.");
    }

    public function actualizar(array $before, array $data)
    {
        $this->insertar($data);
    }

    public function eliminar(array $before)
    {
        Paciente::where('Id', $before['Id'])->delete();
        Log::info("Paciente {$before['Id']} eliminado.");
    }

}