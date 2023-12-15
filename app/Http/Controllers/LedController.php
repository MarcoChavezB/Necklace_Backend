<?php

namespace App\Http\Controllers;


use GuzzleHttp\Client;
use Illuminate\Http\Request;

class LedController extends Controller
{
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
}
