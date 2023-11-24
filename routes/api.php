<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PetControllerProvicional;
use App\Http\Controllers\DevicesController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::any('/ServerOn', function (){
    return response()->json([
        'message' => 'Ya esta jalando el server tilines bastardes'
    ]);
});



Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login'])->name('login');


//DevicesController
Route::get('/getcount/{id}', [DevicesController::class, 'getCountDispo']);
Route::get('/getdislinks', [PetControllerProvicional::class, 'getDisplinks'])->name('Dispositivos vinculados');


// Vinculacion de mascota con dispositivo
Route::post('/link-device', [PetControllerProvicional::class, 'linkPetToDisp'])->name('link-device');
Route::post('/unlink-device/{id}', [PetControllerProvicional::class, 'UnlinkPetToDisp'])->name('unlink-device');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');
Route::post('/InfoUsuario/{id}', [UserController::class, 'InfoUsuario']);
Route::post('/infoMascota/{id}', [PetControllerProvicional::class, 'detallesPerro']);
Route::post('/infoDispositivo/{id}', [PetControllerProvicional::class, 'detallesDispositivo']);
Route::post('/perrosxUsuario/{id}', [PetControllerProvicional::class, 'perrosxUsuario']);
Route::get('/user/{id}', [UserController::class, 'getUserDevices']);
