<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;
    public function petDevices()
    {
        return $this->hasMany(Pet_Device::class);
    }

    protected $table = 'devices';
    protected $fillable = [
        'id',
        'name',
        'codigo'
    ];

}
