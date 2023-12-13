<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;

class GpsController extends Controller
{
    public function getLocation(Request $request){
        $client = new Client();
        $feedName = "-gps";

        $validate = Validator::validate($request->all(), [
            'deviceCode' => 'required',
        ],
            [
                'coords.required' => 'Las coordenadas son requeridas',
            ]);
        if(!$validate){
            return response()->json([
                "msg"   => "Error al validar los datos",
            ], 422);
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

        if (!isset($data['value'])) {
            return response()->json([
                "msg" => "Datos inválidos de la API",
            ], 422);
        }

        $coords = $data['value'];
        $place = $this->getPlace($coords);

        return response()->json([
            'value' => $coords,
            'place' => $place
        ]);

    }


    public function getPlace($coords){
        $client = new Client();

        try{
            $response = $client->request('GET', 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?', [
                'query' => [
                    'location' => '25.533605,-103.311376'/*$coords*/,
                    'radius' => env('radius'),
                    'key' => env('key')
                ]
            ]);

        }catch (\Exception $e){
            return response()->json([
                "msg" => "Error al obtener datos de la API",
                "error" => $e->getMessage()
            ], 500);
        }

        $data = json_decode($response->getBody(), true);
        $place = $data['results'][1]['name'];

        return $place;

    }
}
