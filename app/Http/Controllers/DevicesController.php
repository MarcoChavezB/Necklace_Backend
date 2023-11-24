<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Device;
use App\Models\PetDevices;
use App\Models\Pet;
use App\Models\User;

class DevicesController extends Controller
{
    public function linkPetToDisp($id)
    {
        $pet = Pet::where('user_id', $id)->first();
        if (!$pet) {
            return response()->json([
                "msg" => "Usuario no encontrado",
            ], 404);
        }
        $device = Device::where('id', $petdevice->device_id)->first();
        if (!$device) {
            return response()->json([
                "msg" => "Dispositivo no encontrado",
            ], 404);
        } 
        $petdevice = PetDevices::where('pet_id', $id)->first();
        if ($petdevice) {
            return response()->json([
                "msg" => "Ya existe un dispositivo vinculado a esta mascota",
            ], 404);
        } 
    }

    public function getCountDispo($id){
        $pet=Pet::where('user_id', $id)->first();
        if (!$pet) {
            return response()->json([
                "msg" => "Usuario no encontrado",
            ], 404);
        }
        $count = PetDevices::where('pet_id', $id)->count();
        return response()->json([
            "msg" => "Cantidad de dispositivos",
            "count" => $count
        ], 200);
    }
    }