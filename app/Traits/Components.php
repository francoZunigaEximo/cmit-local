<?php

namespace App\Traits;

use DateTime;
use App\Models\Fichalaboral;
trait Components
{
    public function getAge(string $age): int
    {
        if ($age === null) return null; 
        
        $dateBirth = new DateTime($age);
        $today = new DateTime();
        $newAge = $today->diff($dateBirth);

        return $newAge->y;
    }

}
