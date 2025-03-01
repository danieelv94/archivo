<?php

namespace App\Models\archivo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Area;

/**
 * mixed area_id
 * mixed anio_captura
 * mixed mes_captura
 * mixed semaforo
 */
class ArchivoSupervision extends Model
{
    use HasFactory;

    protected static array $semaforo = [
        0 => 'No capturó datos',
        1 => 'Completo',
        2 => 'Incompleto',
    ];

    const NO_CAPTURA = 0;
    const COMPLETO = 1;
    const INCOMPLETO = 2;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
        'deleted_at'       => 'datetime',
    ];

    protected $with = ['area'];

    public function semaforo_texto($estado)
    {
        return static::$semaforo[ ($estado ?? self::NO_CAPTURA) ] ?? '--';
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }


}
