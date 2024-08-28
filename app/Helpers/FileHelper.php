<?php

namespace App\Helpers;

use Icewind\SMB\BasicAuth;
use Icewind\SMB\ServerFactory;
use Icewind\SMB\Server;
use Icewind\SMB\Share;

class FileHelper
{
    public static function getFileUrl(?string $type)
    {
        $disk = env('FILESYSTEM_DISK');

        if ($disk  === 'smb') {
            
            $factory = new ServerFactory();
            $auth = new BasicAuth(env('USER_SMB'), env('DOMAIN_SMB'), env('PASS_SMB'));
            $server = $factory->createServer(env('HOST_SMB'), $auth);
            $this->share = $server->getShare(env('SHARE_SMB'));

        } else {
            return $type === 'lectura' ? asset('storage') : storage_path('app/public');
        }    
    }

    public function getFileContent(string $filePath): ?string
    {
        try {
            $file = $this->share->getFile($filePath);
            return $file->read();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getFileStream(string $filePath)
    {
        try {
            $file = $this->share->getFile($filePath);
            return $file->getStream();
        } catch (\Exception $e) {
            return null;
        }
    }
}