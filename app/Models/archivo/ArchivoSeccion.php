<?php

namespace App\Models\archivo;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed id
 * @property mixed clave
 * @property mixed nombre
 * @property mixed created_at
 * @property mixed updated_at
 * @property mixed series
 */
class ArchivoSeccion extends Model
{

    protected $table = 'archivo_secciones';

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    public function series()
    {
        return $this->hasMany(ArchivoSerie::class, 'clave_seccion', 'clave');
    }


}
