<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClimaController extends Controller
{
    public function getForecast(Request $request){
        $client = new Client();


        $validate = Validator::validate($request->all(), [
            'coords' => 'required',
        ],
            [
                'coords.required' => 'Las coordenadas son requeridas',
            ]);
        if(!$validate){
            return response()->json([
                "msg"   => "Error al validar los datos",
            ], 422);
        }

        $response = $client->request('GET', 'http://api.weatherapi.com/v1/forecast.json', [
            'query' => [
                'key' => env('WEATHER_API_KEY'),
                'q' => $request->input('coords'),
                'days' => 1,
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        $CitiLocation = $data['location']['name'];
        $CitiRegion = $data['location']['region'];
        $temp = $data['current']['temp_c'];
        $maxTemp = $data['forecast']['forecastday'][0]['day']['maxtemp_c'];
        $minTemp = $data['forecast']['forecastday'][0]['day']['mintemp_c'];
        $dailyChanceOfRain = $data['forecast']['forecastday'][0]['day']['daily_chance_of_rain'];
        $feelLike = $data['current']['feelslike_c'];

        return response()->json([
            'CitiLocation' => $CitiLocation,
            'CitiRegion' => $CitiRegion,
            'temp' => $temp,
            'maxTemp' => $maxTemp,
            'minTemp' => $minTemp,
            'dailyChanceOfRain' => $dailyChanceOfRain,
            'feelLike' => $feelLike,
        ]);

    }


}
