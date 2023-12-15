<?php

namespace App\Http\Controllers;


use GuzzleHttp\Client;
use Illuminate\Http\Request;

class LedController extends Controller
{
    public function TurnOnLed($value){
        $client = new Client();

        try{

            $response = $client->request('POST','https://io.adafruit.com/api/v2/MarcoChavez/feeds/led/data',[
                'headers' => [
                    'X-AIO-Key' => env('ADAFRUIT_IO_KEY')
                ],
                'json' => [
                    'value' => $value
                ]
            ]);

            return response()->json([
                "on" => $response
            ]);

        }catch (\Exception $e){
            return response()->json([
                "msg" => "Error al obtener datos de la API",
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
