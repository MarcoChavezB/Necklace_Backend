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


Route::any('/errormsg', function (){
    return response()->json([
        "msg" => "No estas logeado"
    ], 401);
})->name('errormsg');

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');
Route::post('/InfoUsuario/{id}', [UserController::class, 'InfoUsuario']);
Route::get('/user/{id}', [UserController::class, 'getUserDevices']);

//Route::middleware('auth:api')->group(function () {
    Route::post('/infoDispositivo/{id}', [PetController::class, 'detallesDispositivo']);
    Route::post('/perrosxUsuario/{id}', [PetController::class, 'perrosxUsuario']);
    Route::post('/link-device', [PetController::class, 'linkPetToDisp'])->name('link-device');
    Route::post('/unlink-device/{id}', [PetController::class, 'UnlinkPetToDisp'])->name('unlink-device');
    Route::post('/getcount/{id}', [DevicesController::class, 'getCountDispo']);
    Route::get('/getdislinks', [PetController::class, 'getDisplinks'])->name('Dispositivos vinculados');
    Route::post('/infoMascota/{id}', [PetController::class, 'detallesPerro']);
    Route::get('/firstDisp/{id}', [PetController::class, 'PrimerDispxUser']);
    Route::get('/getInfoPerro/{id}', [PetController::class, 'getInfoPerroXIdCollar']);

//});


