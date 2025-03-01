<?php

namespace App\Models\archivo;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed id
 * @property mixed created_at
 * @property mixed updated_at
 * @property boolean captura_abierta
 */
class ArchivoConfig extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'captura_abierta' => 'boolean'
    ];

}
