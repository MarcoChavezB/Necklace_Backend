<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;


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
