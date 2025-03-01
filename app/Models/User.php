<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use App\Notifications\ResetPasswordNotification;




/**
 * @property mixed id
 * @property mixed name
 * @property mixed email
 * @property mixed email_verified_at
 * @property mixed password
 * @property mixed avatar
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 * @property Persona persona
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // protected $with = ['roles'];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make( $value );

    }

    public function getAvatarAttribute()
    {
        if ( ! empty( $this->avatar)  ) {
            return $this->avatar;
        }

        return asset('img/default-user.png');
    }


    public function persona()
    {
        return $this->hasOne(Persona::class);
    }

    public function validar_datos($request){
        $validated = $request->safe()->only([
            'titulo',
            'nombres',
            'primer_apellido',
            'segundo_apellido',
            'email',
            'telefono',
            'curp',
            'rfc',
            'sexo',
            'fecha_nacimiento',
            'departamento_id',
            'puesto',
            'area_id',
        ]);

        return $validated;
    }


    public function getRol(Persona $persona){
        return $persona->user->getRoleNames()->first();
    }

    public function getRoles(){
        return Role::all()->pluck('name');
    }

    public function setRol(User $user, $rol){
        $user->syncRoles($rol);
    }


}
