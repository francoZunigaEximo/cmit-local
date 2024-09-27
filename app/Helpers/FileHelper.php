<?php

namespace App\Helpers;

class FileHelper
{
    public static function getFileUrl(?string $type)
    {
        $disk = config('filesystems.default');

        // Configuración basada en el disco
        $config = [
            'smb' => [
                'lectura' => '/media/nas/',
                'escritura' => '/media/nas/',
            ],
            'local' => [
                'lectura' => asset('storage'),
                'escritura' => storage_path('app/public'),
            ],
        ];

        // Selecciona el tipo de URL
        return $config[$disk][$type] ?? $config['local']['lectura'];
    }

    public static function uploadFile(string $ruta, $archivo, string $nombre)
    {

        if ($archivo instanceof \Illuminate\Http\UploadedFile) {
            
            $archivo->move($ruta, $nombre);
        } else {
            $image_parts = explode(';base64,', $archivo);
            $image_base64 = base64_decode($image_parts[1]);

            file_put_contents($ruta . $nombre, $image_base64);
        }

        // Cambiar permisos del archivo
        chmod($ruta . $nombre, 0755);
    }
}
