<?php

namespace App\Http\Controllers;

use App\Models\Pet_Device;
use http\Env;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\DeviceAir;
use App\Models\DeviceHum;
use App\Models\DeviceMov;
use App\Models\DeviceTemp;

class HumController extends Controller
{
    public function getHumData(Request $request){
        $client = new Client();

        $validate = Validator::make($request->all(), [
            'deviceCode' => 'required',
        ],
            [
                'deviceCode.required' => 'El código del dispositivo es requerido',
            ]);

        if ($validate->fails()) {
            return response()->json([
                "msg"   => "Error al validar los datos",
                "error" => $validate->errors()
            ], 422);
        }

        $PetDeviceId = $this->getPetDeviceId($request->input('deviceCode'));
        if(!$PetDeviceId){
            return response()->json([
                "msg" => "Registro no encontrado",
            ], 404);
        }

        try {
            $response = $client->request('GET','https://io.adafruit.com/api/v2/MarcoChavez/feeds/hum-value/data/last',[
                'headers' => [
                    'X-AIO-Key' => env('ADAFRUIT_IO_KEY')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "msg" => "Error al obtener datos de la API",
                "error" => $e->getMessage()
            ], 500);
        }

        $data = json_decode($response->getBody(), true);

        if (!isset($data['value']) || !isset($data['created_at']) || !isset($data['feed_id'])) {
            return response()->json([
                "msg" => "Datos inválidos de la API",
            ], 422);
        }

        $value = $data['value'];
        $date = $data['created_at'];
        $feedId = $data['feed_id'];

        $this->saveHumData($value, $date, $feedId, $PetDeviceId->id);

        return response()->json([
            'value' => $value
        ]);
    }

    public function saveHumData($value, $date, $feedId, $PetDeviceId){
        $deviceHum = new DeviceHum();
        $deviceHum->pet_device_id = $PetDeviceId;
        $deviceHum->value = $value;
        $deviceHum->created_at = $date;
        $deviceHum->feed_id = $feedId;
        $deviceHum->save();
    }


    public function getPetDeviceId($deviceCode){
        $PetDeviceId = DB::table('pet_device')
            ->join('devices', 'pet_device.device_id', '=', 'devices.id')
            ->select('pet_device.id')
            ->where( 'devices.codigo',  $deviceCode)
            ->first();

        if(!$PetDeviceId){
            return response()->json([
                "msg" => "Registro no encontrado",
            ], 404);
        }
        return $PetDeviceId;

    }


}
