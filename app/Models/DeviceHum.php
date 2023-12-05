<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceHum extends Model
{
    use HasFactory;

    protected $table = 'device_hum';
    public $timestamps = false;

    protected $fillable = [
        'pet_device_id',
        'value',
        'created_at',
        'feed_id',
    ];

    protected $dates = ['created_at'];

}
