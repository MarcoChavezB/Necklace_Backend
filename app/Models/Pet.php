<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function petDevices()
    {
        return $this->hasMany(Pet_Device::class);
    }

    protected $table = 'pets';
    protected $fillable = [
        'id',
        'nombre',
        'raza',
        'genero',
        'user_id'
    ];
}
