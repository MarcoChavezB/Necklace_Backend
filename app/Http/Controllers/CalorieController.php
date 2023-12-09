<?php

namespace App\Http\Controllers;

use App\Models\DeviceAir;
use App\Models\DeviceMov;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CalorieController extends Controller
{

    public function getCaloriesBurned(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'deviceCode' => 'required',
            'peso' => 'required',
        ],
            [
                'deviceCode.required' => 'El código del dispositivo es requerido',
                'peso.required' => 'El peso es requerido',
            ]);

        if ($validate->fails()) {
            return response()->json([
                "msg"   => "Error al validar los datos",
                "error" => $validate->errors()
            ], 422);
        }

        $weight = $request->input('peso');
        $activityData = $this->getActivityData($request->input('deviceCode'));

        if (!$activityData) {
            return response()->json([
                "msg" => "No se pudo obtener los datos de actividad",
            ], 500);
        }

        $bmr = $this->calculateBMR($weight); // Calcular la tasa metabólica basal
        $activeCalories = $this->calculateActiveCalories($activityData, $weight); // Calcular las calorías activas

        $totalCalories = $bmr + $activeCalories;

        return response()->json([
            'bmr' => $bmr,
            'activeCalories' => $activeCalories,
            'totalCalories' => $totalCalories
        ]);
    }


    public function getActivityData($deviceCode){
        $client = new Client();
        $feedName = "-vel-value";

        $PetDeviceId = $this->getPetDeviceId($deviceCode);
        if(!$PetDeviceId){
            return response()->json([
                "msg" => "Registro no encontrado",
            ], 404);
        }

        $devCode = $deviceCode;
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
        $date = $data['created_at'];
        $feedId = $data['feed_id'];

        $this->saveMovData($value, $date, $feedId, $PetDeviceId->id);


        return $data['value'];

    }

    private function calculateBMR($weight)
    {
        return 70 * pow($weight, 0.75);
    }

    private function calculateActiveCalories($activityData, $weight)
    {
        return $activityData * $weight * 0.0005;
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

    public function saveMovData($value, $date, $feedId, $PetDeviceId){
        $deviceHum = new DeviceMov();
        $deviceHum->pet_device_id = $PetDeviceId;
        $deviceHum->value = $value;
        $deviceHum->created_at = $date;
        $deviceHum->feed_id = $feedId;
        $deviceHum->save();
    }
}
