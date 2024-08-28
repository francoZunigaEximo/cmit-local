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
            
            try {
                $serverFactory = new ServerFactory();
                $auth = new BasicAuth(env('USER_SMB'), env('SHARE_SMB'), env('PASS_SMB'));
                $server = $serverFactory->createServer(env('HOST_SMB'), $auth);

                return $server->getShare('ROOT_SMB');

            } catch( Exception $e) {
                return response()->json(['msg' => 'Error SMB: ' . $e->getMessage()], 502);
            }

        } else {
            return $type === 'lectura' ? asset('storage') : storage_path('app/public');
        }    
    }

}