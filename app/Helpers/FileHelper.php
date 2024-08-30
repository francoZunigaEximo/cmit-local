<?php

namespace App\Helpers;

class FileHelper
{
    public static function getFileUrl(?string $type)
    {
        $disk = config('filesystems.default');

        if ($disk  === 'smb') {
            
            return $type === 'lectura' ? config('filesystems.smb_link') : config('filesystems.smb_link');

        } else {
            return $type === 'lectura' ? asset('storage') : storage_path('app/public');
        }    
    }

}