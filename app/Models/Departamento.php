<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Departamento extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
        'deleted_at'       => 'datetime',
        'activo'           => 'boolean',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['titular','area','padre'];

    public function getTieneHijosAttribute(){

        $registros = Departamento::where('padre_id',$this->id)->count();

        return $registros > 0;
    }

    public function titular()
    {
        return $this->belongsTo(Persona::class);
    }

    public function padre()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function validar_datos($request){
        $validated = $request->safe()->only([
            'clave',
            'nombre',
            'iniciales',
            'titular_id',
            'padre_id',
            'area_id',
            'activo'
        ]);

        return $validated;
    }
}
