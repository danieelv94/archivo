<?php

namespace App\Models\archivo;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed id
 * @property mixed clave
 * @property mixed nombre
 * @property mixed clave_seccion
 * @property mixed created_at
 * @property mixed updated_at
 */
class ArchivoSerie extends Model
{   
    /*
    *SE USA Compoships PARA CREAR LA RELACION BASADA EN 2 COLUMNAS CON EL INVETARIO CORRSPONDIENTE A LA SERIE 
    */
    use \Awobaz\Compoships\Compoships;

    protected $table = 'archivo_series';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = [
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];


    public function seccion()
    {
        return $this->belongsTo(ArchivoSeccion::class, 'clave', 'clave_seccion');
    }

}
