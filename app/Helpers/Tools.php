<?php

namespace App\Helpers;

class Tools 
{
    public static function randomCode(int $longitud = 10)
    {
        return bin2hex(random_bytes($longitud/2));
    }
}