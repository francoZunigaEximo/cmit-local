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
                'lectura' => config('filesystems.link_smb'),
                'escritura' => '/media/nas/',
            ],
            'test' => [
                'lectura' => asset('storage'),
                'escritura' => storage_path('app/public'),
            ],
        ];

        // Selecciona el tipo de URL
        return $config[$disk][$type] ?? $config['test']['lectura'];
    }
}
