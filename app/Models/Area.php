<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Departamento;

/**
 * @property mixed id
 * @property mixed nombre
 * @property mixed siglas
 * @property mixed titular_id
 * @property mixed tipo_area
 * @property mixed area_padre_id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class Area extends Model
{
    use SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at','deleted_at'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'activo' => 'boolean',
    ];


    public function getTieneHijosAttribute(){
        $registros = Departamento::where('area_id',$this->id)->count();
        return $registros > 0;
    }


    public function titular()
    {
        return $this->belongsTo(Persona::class);
    }

}
