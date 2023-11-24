<?php

use App\Http\Controllers\PetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
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






// Vinculacion de mascota con dispositivo
Route::post('/link-device', [PetController::class, 'linkPetToDisp'])->name('link-device');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');
Route::post('/InfoUsuario/{id}', [UserController::class, 'InfoUsuario']);
Route::post('/infoMascota/{id}', [PetController::class, 'detallesPerro']);
Route::post('/infoDispositivo/{id}', [PetController::class, 'detallesDispositivo']);
Route::post('/perrosxUsuario/{id}', [PetController::class, 'perrosxUsuario']);
Route::get('/user/{id}', [UserController::class, 'getUserDevices']);
