<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Pet;
use App\Models\Pet_Device;
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
            ->select('pets.id','pets.nombre', 'pets.raza', 'pets.genero', 'devices.codigo')
            ->where('pets.id', $petId)
            ->first();

        if(!$pets){
            return response()->json([
                "msg" => "Mascota no encontrada",
            ], 404);
        }
        return $pets;
    }

    public function detallesDispositivo($deviceId){

        $devices = DB::table('pet_device')
            ->join('devices', 'devices.id', '=', 'pet_device.device_id')
            ->join('pets', 'pets.id', '=', 'pet_device.pet_id')
            ->select('devices.id','devices.modelo', 'devices.codigo', 'pets.nombre')
            ->where('devices.id', $deviceId)
            ->first();
        if(!$devices){
            return response()->json([
                "msg" => "Dispositivo no encontrado",
            ], 404);
        }
        return $devices;

    }

    public function perrosxUsuario($userID){
        $pets = DB::table('pets')
            ->join('users', 'pets.user_id', '=', 'users.id')
            ->select('pets.id','pets.nombre', 'pets.raza', 'pets.genero')
            ->where('users.id', $userID)
            ->get();
        if(!$pets){
            return response()->json([
                "msg" => "No tiene mascotas registradas",
            ], 404);
        }
        return $pets;
    }

    public function linkPetToDisp(Request $request)
    {
        $validate = Validator::make(
            $request->all(),
            [
                "mascota"   => "required|exists:pets,id",
                "codigo"    => "required|exists:devices,codigo",
            ]
        );

        if ($validate->fails()) {
            return response()->json([
                "msg"   => "Error al validar los datos",
                "error" => $validate->errors()
            ], 422);
        }

        $pet = Pet::where('id', $request->mascota)->first();
        $device = Device::where('codigo', $request->codigo)->first();

        if (!$device) {
            return response()->json([
                "msg" => "Codigo no encontrado",
            ], 404);
        }
        if (!$pet) {
            return response()->json([
                "msg" => "Mascota no encontrada",
            ], 404);
        }

        // Verifica si el dispositivo ya está vinculado previamente
        if (Pet_Device::where('device_id', $device->id)->where('pet_id', $pet->id)->exists()) {
            return response()->json([
                "msg" => "Dispositivo ya vinculado previamente",
            ], 422);
        }

        // Vincula el dispositivo a la mascota
        Pet_Device::create([
            'device_id'   => $device->id,
            'pet_id' => $pet->id,
        ]);

        return response()->json([
            "msg" => "Dispositivo vinculado",
        ], 201);
    }

    public function UnlinkPetToDisp($id)
    {
        $pet = Pet_Device::where('id', $id)->first();

        if (!$pet) {
            return response()->json([
                "msg"   => "Dispositivo no encontrado",
            ], 422);
        }

        $pet->delete();
        return response()->json([
            "msg" => "Dispositivo desvinculado",
        ], 201);
    }

    public function getDisplinks(){
        $pet = Pet_Device::all();
        return $pet;
    }

}
