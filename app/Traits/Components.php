<?php

namespace App\Traits;

use DateTime;
use App\Models\Fichalaboral;
trait Components
{
    public function getAge($age)
    {
        if ($age === '') return null; 
        
        $dateBirth = new DateTime($age);
        $today = new DateTime();
        $newAge = $today->diff($dateBirth);

        return $newAge->y;
    }

}
