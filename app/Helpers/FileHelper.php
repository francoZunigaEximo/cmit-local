<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Icewind\SMB\BasicAuth;
use Icewind\SMB\ServerFactory;
use League\Flysystem\Filesystem;
use RobGridley\Flysystem\Smb\SmbAdapter;

class FileHelper
{
    public static function getFileUrl(?string $type)
    {
        $disk = env('FILESYSTEM_DISK');

        if ($disk  === 'smb') {
            $factory = new ServerFactory();
            $auth = new BasicAuth(env('USER_SMB'), env('DOMAIN_SMB'), env('PASS_SMB'));
            $server = $factory->createServer(env('HOST_SMB'), $auth);
            $share = $server->getShare(env('SHARE_SMB'));
            //return 'smb://' . env('HOST_SMB') . '/' . env('SHARE_SMB') . '/';
            return 'smb://' . env('HOST_SMB') . '/' . env('SHARE_SMB') . '/';
        } else {
            return $type === 'lectura' ? asset('storage') : storage_path('app/public');
        }
    }
}