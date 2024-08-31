<?php

namespace App\Helpers;

class FileHelper
{
    public static function getFileUrl(?string $type)
    {
        $disk = config('filesystems.default');

        if ($disk  === 'smb') {
            
            return $type === 'lectura' ? config('filesystems.smb_link') : '//192.168.1.253/GestionCMIT';

        } else {
            return $type === 'lectura' ? asset('storage') : storage_path('app/public');
        }    
    }

}