<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SoundController extends Controller
{
    public function getSoundValue(Request $request){


        $validate = Validator::make(
            $request->all(),
            [
                "deviceCode"   => "required",
            ],
            [
                "deviceCode.required" => "El código del dispositivo es requerido",
            ]
        );

        if ($validate->fails()) {
            return response()->json([
                "msg"   => "Error al validar los datos",
            ], 422);
        }

        $client = new Client();
        $feedName = "-son-value";

        $PetDeviceId = $this->getPetDeviceId($request->deviceCode);
        if(!$PetDeviceId){
            return response()->json([
                "msg" => "Registro no encontrado",
            ], 404);
        }

        $devCode = $request->deviceCode;
        $feedKey = $devCode.$feedName;

        try {
            $response = $client->request('GET','https://io.adafruit.com/api/v2/MarcoChavez/feeds/'.$feedKey.'/data/last',[
                'headers' => [
                    'X-AIO-Key' => env('ADAFRUIT_IO_KEY')
                ]
            ]);
        } catch (\Exception $e) {
            return null;
        }

        $data = json_decode($response->getBody(), true);
        $value = $data['value'];

        return response()->json([
            'value' => $value
        ]);

    }


    public function getPetDeviceId($deviceCode){
        $PetDeviceId = DB::table('pet_device')
            ->join('devices', 'pet_device.device_id', '=', 'devices.id')
            ->select('pet_device.id')
            ->where( 'devices.codigo',  $deviceCode)
            ->first();

        if(!$PetDeviceId){
            return null;
        }
        return $PetDeviceId;

    }
}
