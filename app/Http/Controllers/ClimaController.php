<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class ClimaController extends Controller
{
    public function getForecast(Request $request){
        $client = new Client();
        $response = $client->request('GET', 'http://api.weatherapi.com/v1/forecast.json', [
            'query' => [
                'key' => config('services.weatherapi.key'),
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
