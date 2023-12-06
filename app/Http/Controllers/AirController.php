<?php

namespace App\Http\Controllers;

use App\Models\DeviceAir;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AirController extends Controller
{
    public function getAirQuality(Request $request){
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
            $response = $client->request('GET','https://io.adafruit.com/api/v2/MarcoChavez/feeds/air-value/data/last',[
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

        $this->saveAirData($value, $date, $feedId, $PetDeviceId->id);

        if($value >= 0 && $value <= 50){
            return response()->json([
                'nivel' => 1
            ]);
        }elseif ($value >= 51 && $value <= 100){
            return response()->json([
                'nivel' => 2
            ]);
        }elseif ($value >= 101 && $value <= 150){
            return response()->json([
                'nivel' => 3
            ]);
        }elseif ($value >= 151 && $value <= 200){
            return response()->json([
                'nivel' => 4
            ]);
        }elseif ($value >= 201 && $value <= 300){
            return response()->json([
                'nivel' => 5
            ]);
        }elseif ($value >= 301){
            return response()->json([
                'nivel' => 6
            ]);
        }

    }

    public function saveAirData($value, $date, $feedId, $PetDeviceId){
        $deviceAir = new DeviceAir();
        $deviceAir->pet_device_id = $PetDeviceId;
        $deviceAir->value = $value;
        $deviceAir->created_at = $date;
        $deviceAir->feed_id = $feedId;
        $deviceAir->save();
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
