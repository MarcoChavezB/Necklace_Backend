<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User  extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;



    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */

    public function getJWTIdentifier(){
        return $this->getKey();
    }


    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(){
        return [];
    }


/**
* Los atributos que se pueden asignar en masa.
*
* @var array<int, string>
*/
protected $fillable = [
    'nombre',
    'apellido',
    'email',
    'contraseña',
];


/**
 * Los atributos que deben ocultarse para la serialización.
 *
 * @var array<int, string>
 */
protected $hidden = [
    'contraseña',
];


}
