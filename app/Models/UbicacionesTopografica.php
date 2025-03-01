<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UbicacionesTopografica extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
        'deleted_at'       => 'datetime'
    ];

    /**
     * Regresa el nombre completo incluyendo el título profesional
     * @return string
     */
    public function getUbicacionCompletaAttribute()
    {
        $inventario = $this->no_inventario ? ", No. Inventario ".$this->no_inventario : "";

        $bien_mueble = $this->bien_mueble ? ", ".$this->bien_mueble : "";

        $ubicacion = $this->ubicacion.$bien_mueble.$inventario;

        return $ubicacion;

    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }
}
