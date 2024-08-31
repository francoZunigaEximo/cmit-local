<?php

namespace App\Services;

class SmbClientService
{
    private $smbClient;

    public function __construct()
    {
        $this->smbClient = $this->createSmbClientContext();
    }

    private function createSmbClientContext()
    {
        return stream_context_create([
            'smb' => [
                'username' => 'cmit',
                'password' => 'HiperionCMT1973',
                'domain' => 'CMIT'
            ]
        ]);
    }

    public function createFile($remotePath, $localPath)
    {
        $stream = fopen($remotePath, 'w', false, $this->smbClient);

        if($stream === false) {
            throw new \Exception("No se pudo guardar el archivo");
        }

        $data = file_get_contents($localPath);

        if($data === false) {
            throw new \Exception("No se puede leer el archivo");
        }

        if(fwrite($stream, $data) === false) {
            throw new \Exception("No se puede escribir el archivo");
        }

        fclose($stream);
        
    }

    public function readFile($remotePath)
    {
        $stream = fopen($remotePath, 'r', $this->smbClient);

        if($stream === false) {
            throw new \Exception("No se leer el archivo");
        }

        $data = stream_get_contents($stream);
        fclose($stream);

        return $data;
        
    }

    public function listFiles($remoteDirectory)
    {
        $files = [];
        $dirStream = opendir($remoteDirectory, $this->smbClient);

        if ($dirStream === false) {
            throw new \Exception("No se pudo abrir el directorio en el servidor SMB.");
        }

        while (($file = readdir($dirStream)) !== false) {
            $files[] = $file;
        }

        closedir($dirStream);

        return $files;
    }




}