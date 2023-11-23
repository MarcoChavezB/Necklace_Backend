<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class PetControllerProvicional extends Controller
{
    public function detallesPerro($userId)
    {
        $pets = DB::table('pets')
            ->join('users', 'pets.user_id', '=', 'users.id')
            ->join('pet_device', 'pets.id', '=', 'pet_device.pet_id')
            ->join('devices', 'pet_device.device_id', '=', 'devices.id')
            ->select('pets.nombre', 'pets.raza', 'pets.genero', 'users.nombre as dueño', 'devices.modelo as collar')
            ->where('users.id', $userId)
            ->get();
        return $pets;
    }

}
