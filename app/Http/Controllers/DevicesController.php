<?php

namespace App\Http\Controllers;

use App\Models\Pet_Device;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Device;
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
        $petdevice = Pet_Device::where('pet_id', $id)->first();
        if ($petdevice) {
            return response()->json([
                "msg" => "Ya existe un dispositivo vinculado a esta mascota",
            ], 404);
        }
    }

    public function getCountDispo($id)
    {

        $userexists = User::where('id', $id)->first();
        if (!$userexists) {
            return response()->json([
                "msg" => "Usuario no encontrado",
            ], 404);
        }

        $Ndispositivos = DB::table('pets')
            ->join('pet_device', 'pets.id', '=', 'pet_device.pet_id')
            ->where('pets.user_id', $id)
            ->count();

        return response()->json([
            "count" => $Ndispositivos,
        ], 200);

    }


    public function IsDeviceLinked(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'deviceCode' => 'required',
        ],
            [
                'deviceCode.required' => 'El código del dispositivo es requerido',
            ]);

        if ($validate->fails()) {
            return response()->json([
                "msg" => "Error al validar los datos",
                "error" => $validate->errors()
            ], 422);
        }

        $DevID = Device::where('codigo', $request->input('deviceCode'))->pluck('id')->first();
        if (!$DevID) {
            return response()->json([
                "linked" => null,
            ], 404);
        }

        $pet_devID = Pet_Device::where('device_id', $DevID)->pluck('pet_id')->first();
        if (!$pet_devID) {

            return response()->json(['linked' => true], 200);

        }

        return response()->json(['linked' => false], 200);
    }


    public function updateDevicePet($newPetId, $deviceID){
        $PetID = Pet::where('id', $newPetId)->pluck('id')->first();
        if(!$PetID){
            return response()->json([
                "msg" => "Mascota no encontrada",
            ], 404);

        }

        $DeviceID = Device::where('id', $deviceID)->pluck('id')->first();
        if(!$DeviceID){
            return response()->json([
                "msg" => "Dispositivo no encontrado",
            ], 404);

        }

        $petdevice = Pet_Device::where('device_id', $DeviceID)->first();
        $petdevice->pet_id = $PetID;
        $petdevice->save();

        return response()->json([
            "msg" => "Dispositivo vinculado a nueva mascota",
        ], 200);

    }




    public function TurnOnLed($value){
        $client = new Client();

        try{

            $client->request('POST','https://io.adafruit.com/api/v2/MarcoChavez/feeds/led/data',[
                'headers' => [
                    'X-AIO-Key' => env('ADAFRUIT_IO_KEY')
                ],
                'json' => [
                    'value' => $value
                ]
            ]);
            if($value == 1){
                return response()->json([
                    "on" => true
                ]);
            }
            else if($value == 0) {
                return response()->json([
                    "on" => false
                ]);
            }
        }catch (\Exception $e){
            return response()->json([
                "msg" => "Error al obtener datos de la API",
                "error" => $e->getMessage()
            ], 500);
        }
    }


    public function TurnOnBuzzer($value){
        $client = new Client();

        try{

            $client->request('POST','https://io.adafruit.com/api/v2/MarcoChavez/feeds/buzzer/data',[
                'headers' => [
                    'X-AIO-Key' => env('ADAFRUIT_IO_KEY')
                ],
                'json' => [
                    'value' => $value
                ]
            ]);
            if($value == 1){
                sleep(1);
                $this->TurnOffBuzzer(0);
                return response()->json([
                    "on" => true
                ]);
            }
            else if($value == 0) {
                return response()->json([
                    "on" => false
                ]);
            }
        }catch (\Exception $e){
            return response()->json([
                "msg" => "Error al obtener datos de la API",
                "error" => $e->getMessage()
            ], 500);
        }
    }


    public function TurnOffBuzzer($value){
        $client = new Client();

        try{

            $client->request('POST','https://io.adafruit.com/api/v2/MarcoChavez/feeds/buzzer/data',[
                'headers' => [
                    'X-AIO-Key' => env('ADAFRUIT_IO_KEY')
                ],
                'json' => [
                    'value' => $value
                ]
            ]);
        }catch (\Exception $e){
            return response()->json([
                "msg" => "Error al obtener datos de la API",
                "error" => $e->getMessage()
            ], 500);
        }
    }





    // public function desvincularDispositivo($id)
    // {
    //     $petdevice = Pet_Device::where('pet_id', $id)->first();
    //     if (!$petdevice) {
    //         return response()->json([
    //             "msg" => "No existe un dispositivo vinculado a esta mascota",
    //         ], 404);
    //     }
    //     $petdevice->delete();
    //     return response()->json([
    //         "msg" => "Dispositivo desvinculado",
    //     ], 200);
    // }
}
