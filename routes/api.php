<?php

use App\Http\Controllers\AirController;
use App\Http\Controllers\CalorieController;
use App\Http\Controllers\ClimaController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\TempController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DevicesController;
use App\Http\Controllers\ActivationController;
use App\Http\Controllers\HumController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::any('/errormsg', function (){
    return response()->json([
        "msg" => "No estas logeado"
    ], 401);
})->name('errormsg');


Route::any('/activationMsg', function (){
    return view('emails.succes');
})->name('activationMsg');


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');
Route::post('/InfoUsuario/{id}', [UserController::class, 'InfoUsuario']);
Route::get('/user/{id}', [UserController::class, 'getUserDevices']);

Route::middleware('auth:api')->group(function () {
    Route::post('/infoDispositivo/{id}', [PetController::class, 'detallesDispositivo']);
    Route::post('/perrosxUsuario/{id}', [PetController::class, 'perrosxUsuario']);
    Route::post('/link-device', [PetController::class, 'linkPetToDisp'])->name('link-device');
    Route::post('/unlink-device/{id}', [PetController::class, 'UnlinkPetToDisp'])->name('unlink-device');
    Route::post('/getcount/{id}', [DevicesController::class, 'getCountDispo']);
    Route::get('/getdislinks', [PetController::class, 'getDisplinks'])->name('Dispositivos vinculados');
    Route::post('/infoMascota/{id}', [PetController::class, 'detallesPerro']);
    Route::get('/firstDisp/{id}', [PetController::class, 'PrimerDispxUser']);
    Route::get('/getInfoPerro/{id}', [PetController::class, 'getInfoPerroXIdCollar']);
    Route::post('/registerPet', [PetController::class, 'registerPet']);
    Route::get('/getHumData', [HumController::class, 'getHumData']); //Pendiente hasta tener sensor
    Route::get('/getAirQuality', [AirController::class, 'getAirQuality']);
    Route::get('/getCaloriesBurned', [CalorieController::class, 'getCaloriesBurned']); //Pendiente hasta tener sensor
    Route::get('/getDogData', [PetController::class, 'getDogData']); //Pendiente hasta tener sensor
    Route::get('/getTempData', [TempController::class, 'getTempData']); //Pendiente hasta tener sensor
    Route::post('/registerPetYDev', [PetController::class, 'registerPetYDev']);
    Route::get('/getForecast', [ClimaController::class, 'getForecast']);
    Route::get('/getTempPerHour', [TempController::class, 'getTempPerHour']);//Pendiente hasta tener sensor
});

Route::any('/activation/{user}', [ActivationController::class, 'activate'])->name('activation');

Route::any('/getServerTime', function (){
    return response()->json([
        "msg" => date("Y-m-d H:i:s")
    ], 200);
})->name('getServerTime');



