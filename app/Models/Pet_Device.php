<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet_Device extends Model
{
    use HasFactory;
    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    protected $table = 'pet_device';
    protected $fillable = [
        'id',
        'pet_id',
        'device_id'
    ];
}
