<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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
Route::middleware('auth:api')->post('/link-device', [UserController::class, 'linkDevice'])->name('link-device');

Route::middleware('auth:api')->group(function (){
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});


Route::post('/InfoUsuario', [UserController::class, 'InfoUsuario']);
