<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Prestacion;

class Constanciase extends Model
{
    use HasFactory;

    protected $table = 'constanciase';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Id',
        'NroC',
        'Fecha',
        'Obs'
    ];

    public $timestamps = false;

    public function prestacion()
    {
        return $this->belongsToMany(Prestacion::class, 'constanciase_it', 'IdC', 'IdP');
    }

    public static function addRemito(int $NroRemito): void
    {
        Constanciase::create([
            'Id' => Constanciase::max('Id') + 1,
            'NroC' => $NroRemito,
            'Fecha' => now(),
            'Obs' => null,
        ]);
    }

    public static function obsRemito(int $NroRemito, string $obs): void
    {
        $observacion = Constanciase::where('NroC', $NroRemito)->first();

        if($observacion)
        {
            $observacion->Obs = $obs ?? '';
            $observacion->save();
        }
    }
}


