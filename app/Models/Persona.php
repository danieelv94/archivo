<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Departamento;
use App\Models\Area;
/**
 * @property mixed id
 * @property mixed titulo
 * @property mixed nombres
 * @property mixed primer_apellido
 * @property mixed segundo_apellido
 * @property mixed email
 * @property mixed telefono
 * @property mixed curp
 * @property mixed rfc
 * @property mixed sexo
 * @property mixed fecha_nacimiento
 * @property mixed user_id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 * @property User user
 * @property Area area
 * @property string nombre_completo
 * @property string nombre_completo_profesion
 * @property string departamento_id
 * @property string puesto
 * @property string unidad_presupuestal
 * @property string area_id
 * @property mixed es_titular
 */
class Persona extends Model
{

    use HasFactory, SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
        'deleted_at'       => 'datetime',
        'fecha_nacimiento' => 'datetime:Y-m-d',
    ];

    protected $appends  = [
        'nombre_completo',
        'nombre_completo_profesion'
    ];

    public function getEsTitularAttribute(){
        
        $departamentos = Departamento::where('titular_id',$this->id)->count();
        $areas = Area::where('titular_id',$this->id)->count();

        return $departamentos > 0 || $areas > 0;
    }

    protected static function booted()
    {
        static::saved(/**
         * @param Persona $persona
         * @return void
         */ function ($persona) {
            if ( $persona->user && $persona->user->id ) {

                $user = $persona->user;
                $user->update([
                    'email' => $persona->email,
                    'name' => $persona->nombre_completo,
                ]);
            }
        });
    }

    public function setCurpAttribute($value)
    {
        $this->attributes['curp'] = Str::upper( $value );
    }


    // public function getEsTitularAttribute(){
        
    //     $registros = Area::where('padre_id',$this->id)->count();

    //     return $registros > 0;
    // }


    /**
     * Regresa el nombre completo sin el título
     * @return string
     */
    public function getNombreCompletoAttribute()
    {
        $parts = [$this->nombres, $this->primer_apellido, $this->segundo_apellido];

        return trim(join(' ', $parts));

    }

    /**
     * Regresa el nombre completo incluyendo el título profesional
     * @return string
     */
    public function getNombreCompletoProfesionAttribute()
    {
        $parts = [$this->titulo, $this->nombres, $this->primer_apellido, $this->segundo_apellido];

        return trim(join(' ', $parts));

    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class );
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }


    public function create_user( $default_password = null )
    {

        if ( empty($default_password ) ) {
            $default_password = '1a2b3c';
        }

        $user = User::create([
            'email' => $this->email,
            'name' => $this->nombre_completo,
            'password' => $default_password
        ]);

        $this->update(['user_id' => $user->id ]);
        
        return $user;
    }


}
