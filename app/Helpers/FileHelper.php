<?php

namespace App\Helpers;

use Exception;
use Icewind\SMB\BasicAuth;
use Icewind\SMB\ServerFactory;

class FileHelper
{
    public static function getFileUrl(?string $type)
    {
        $disk = env('FILESYSTEM_DISK');

        if ($disk  === 'smb') {
            
            return 'http://localhost:8005/';

        } else {
            return $type === 'lectura' ? asset('storage') : storage_path('app/public');
        }    
    }

}