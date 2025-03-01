<?php

namespace App\Models\archivo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Area;

class ArchivoSerieAutorizada extends Model
{
    use HasFactory,softDeletes;

    protected $casts = [
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
        'deleted_at'       => 'datetime',
    ];

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['seccion','serie','area'];

    public function seccion(){
        return $this->belongsTo(ArchivoSeccion::class);
    }

    public function serie(){
        return $this->belongsTo(ArchivoSerie::class);
    }

    public function area(){
        return $this->belongsTo(Area::class);
    }
}
