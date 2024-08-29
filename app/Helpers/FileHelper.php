<?php

namespace App\Helpers;

class FileHelper
{
    public static function getFileUrl(?string $type)
    {
        $disk = env('FILESYSTEM_DISK');

        if ($disk  === 'smb') {
            
            return env('LINK_SMB').'/';

        } else {
            return $type === 'lectura' ? asset('storage') : storage_path('app/public');
        }    
    }

}