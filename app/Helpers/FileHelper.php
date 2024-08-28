<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class FileHelper
{
    public static function getFileUrl(?string $type)
    {
        $disk = Storage::disk(config('filesystems.default'));

        if ($disk  === 'smb') {
            return 'smb://' . env('HOST_SMB') . '/' . env('SHARE_SMB') . '/';
        } else {
            return $type === 'lectura' ? asset('storage') : storage_path('app/public');
        }
    }
}