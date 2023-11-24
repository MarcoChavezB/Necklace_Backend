<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetDevices extends Model
{
    use HasFactory;

    protected $table = 'pet_device';
    protected $fillable = [
        'id',
        'pet_id',
        'device_id'
    ];
}
