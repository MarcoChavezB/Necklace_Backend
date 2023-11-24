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
    public function detallesPerro($petId)
    {
        $pets = DB::table('pets')
            ->join('pet_device', 'pets.id', '=', 'pet_device.pet_id')
            ->join('devices', 'pet_device.device_id', '=', 'devices.id')
            ->select('pets.nombre', 'pets.raza', 'pets.genero', 'devices.codigo')
            ->where('pets.id', $petId)
            ->first();
        return $pets;
    }

    public function detallesDispositivo($deviceId){

        $devices = DB::table('pet_device')
            ->join('devices', 'devices.id', '=', 'pet_device.device_id')
            ->join('pets', 'pets.id', '=', 'pet_device.pet_id')
            ->select('devices.modelo', 'devices.codigo', 'pets.nombre')
            ->where('devices.id', $deviceId)
            ->first();
        return $devices;

    }

    public function dispositivosxUsuario($userId){
        $devices = DB::table('users')
            ->join('pets', 'users.id', '=', 'pets.user_id')
            ->join('pet_device', 'pets.id', '=', 'pet_device.pet_id')
            ->join('devices', 'devices.id', '=', 'pet_device.device_id')
            ->select('devices.modelo', 'devices.codigo')
            ->where('users.id', $userId)
            ->get();
        return $devices;
    }

    public function perrosxUsuario($userID){
        $pets = DB::table('pets')
            ->join('users', 'pets.user_id', '=', 'users.id')
            ->select('pets.nombre', 'pets.raza', 'pets.genero')
            ->where('users.id', $userID)
            ->get();
        return $pets;
    }

    public function linkPetToDisp(Request $request)
    {
        $validate = Validator::make(
            $request->all(),
            [
                "mascota"   => "required|exists:pets,id",
                "modelo"    => "required|exists:devices,id",
            ]
        );

        if ($validate->fails()) {
            return response()->json([
                "msg"   => "Error al validar los datos",
                "error" => $validate->errors()
            ], 422);
        }

        $pet = Pet::find($request->mascota);
        $device = Device::find($request->modelo);

        if (!$device) {
            return response()->json([
                "msg" => "Dispositivo no encontrado",
            ], 404);
        }

        // Verifica si el dispositivo ya está vinculado previamente
        if (PetDevices::where('device_id', $device->id)->where('pet_id', $pet->id)->exists()) {
            return response()->json([
                "msg" => "Dispositivo ya vinculado previamente",
            ], 422);
        }

        // Vincula el dispositivo a la mascota
        PetDevices::create([
            'device_id'   => $device->id,
            'pet_id' => $pet->id,
        ]);

        return response()->json([
            "msg" => "Dispositivo vinculado",
        ], 201);
    }


}
