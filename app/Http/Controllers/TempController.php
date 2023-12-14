<?php

namespace App\Http\Controllers;

use App\Models\DeviceHum;
use App\Models\DeviceTemp;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
Use \DateTime;

class TempController extends Controller
{
    public function getTempData(Request $request){
        $client = new Client();
        $feedName = "-temp-value";

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

        $devCode = $request->deviceCode;
        $feedKey = $devCode.$feedName;

        try {
            $response = $client->request('GET','https://io.adafruit.com/api/v2/MarcoChavez/feeds/'.$feedKey.'/data/last',[
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

        $fechaUTC = new DateTime($date, new \DateTimeZone('UTC'));
        $fechaUTC -> setTimeZone(new \DateTimeZone('America/Monterrey'));
        $fechaLocal = $fechaUTC->format('Y-m-d H:i:s');

        $this->saveTempData($value, $fechaLocal, $feedId, $PetDeviceId->id);

        if($value < 0){
            return response()->json([
                "nivel" => 1, //Muy frio
                "value" => $value
            ], 200);
        }elseif (0 <= $value && $value < 10){
            return response()->json([
                "nivel" => 2, //Frio
                "value" => $value
            ], 200);
        }
        elseif (10 <= $value && $value < 20){
            return response()->json([
                "nivel" => 3, //Templado
                "value" => $value
            ], 200);
        }
        elseif (20 <= $value && $value < 30){
            return response()->json([
                "nivel" => 4, //Caliente
                "value" => $value
            ], 200);
        }
        elseif ($value >= 30){
            return response()->json([
                "nivel" => 5, //Muy caliente
                "value" => $value
            ], 200);
        }

    }


    public function saveTempData($value, $date, $feedId, $PetDeviceId){
        $deviceHum = new DeviceTemp();
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
            return null;
        }
        return $PetDeviceId;
    }

    public function getDevId($deviceCode){
        $devID = DB::table('devices')
            ->select('devices.id')
            ->where( 'devices.codigo',  $deviceCode)
            ->first();

        if(!$devID){
            return null;
        }
        return $devID->id;
    }


    public function getTempPerHour(Request $request){
        $deviceCode = $request->input('deviceCode');

        $devID = $this->getDevId($deviceCode);
        if(!$devID){
            return response()->json([
                "msg" => "Registro no encontrado",
            ], 404);
        }
        $pet_device_id = DB::table('pet_device')->where('device_id', $devID)->value('id');

        if (!$pet_device_id) {
            return response()->json([
                "msg" => "Registro no encontrado",
            ], 404);
        }

        $testDate = Carbon::now();

        $values = DB::table('device_temp')
            ->select(DB::raw('MIN(id) as id'))
            ->where('pet_device_id', $pet_device_id)
            ->whereDate('created_at', $testDate)
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->pluck('id');

        $records = DB::table('device_temp')
            ->whereIn('id', $values)
            ->orderBy('created_at')
            ->get()
            ->map(function ($record) {
                return [
                    'value' => $record->value,
                    'created_at' => Carbon::parse($record->created_at)->format('H:i:s')
                ];
            })
            ->values()
            ->all();

        return response()->json($records, 200);


    }


    public function getTemperatreFromBD(Request $request){
        $validate = Validator::make($request->all(), [
            'deviceCode' => 'required',
        ],
            [
                'deviceCode.required' => 'El código del dispositivo es requerido',
            ]);

        if ($validate->fails()) {
            return response()->json([
                "msg"   => "Error al validar los datos",
            ], 422);
        }


        $deviceCode = $request->input('deviceCode');
        $devId = $this->getDevId($deviceCode);

        if(!$devId){
            return response()->json([
                "msg" => "Registro no encontrado",
            ], 404);
        }

        $pet_device_id = DB::table('pet_device')->where('device_id', $devId)->value('id');

        if (!$pet_device_id) {
            return response()->json([
                "msg" => "Registro no encontrado",
            ], 404);
        }

        $testDate = Carbon::today();

        $values = DB::table('device_temp')
            ->select('value', 'created_at')
            ->where('pet_device_id', $pet_device_id)
            ->whereDate('created_at', $testDate)
            ->groupBy(DB::raw('HOUR(created_at)'));

        return response()->json($values, 200);
    }
}
