<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceTemp extends Model
{
    use HasFactory;

    protected $table = 'device_temp';

    protected $fillable = [
        'pet_device_id',
        'value',
        'created_at',
        'feed_id',
    ];

    protected $dates = ['created_at'];

}
