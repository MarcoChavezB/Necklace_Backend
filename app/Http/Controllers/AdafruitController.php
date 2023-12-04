<?php

namespace App\Http\Controllers;

use http\Env;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class AdafruitController extends Controller
{

    public function getHumData(){
        $client = new Client();

        $response = $client->request('GET','https://io.adafruit.com/api/v2/MarcoChavez/feeds/hum-value/data',[
            'headers' => [
                'X-AIO-Key' => env('ADAFRUIT_IO_KEY')
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        $value = $data[0]['value'];
        $date = $data[0]['created_at'];

        return response()->json([
            'value' => $value
        ]);
    }

}
