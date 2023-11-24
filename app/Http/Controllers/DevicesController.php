<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Device;
use App\Models\Pet;

class DevicesController extends Controller
{
    public function linkDispo(Request $request)
    {
        $validate = Validator::make(
            $request->all(),
            [
                "user"   => "required|exists:pets,id_user", 
            ]
        );

        if ($validate->fails()) {
            return response()->json([
                "msg"   => "Usuario no encontrado",
                "error" => $validate->errors()
            ], 422);
        }


    }
}