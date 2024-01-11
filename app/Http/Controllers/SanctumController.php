<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SanctumController extends Controller
{
    public  function serverTimeSanctum ()
    {
        return response()->json([
            "msg" => date("Y-m-d H:i:s"),
            "info" => "Esta ruta es para probar el middleware de sanctum"
        ], 200);
    }


    public function loginSanctum()
    {
        $credentials = request(['email', 'password']);

        $validate = Validator::make(
            $credentials,
            [
                "email" => "required|email",
                "password" => "required|min:8"
            ],
            [
                "email.required" => "El email es requerido",
                "email.email" => "El email debe ser válido",
                "password.required" => "La contraseña es requerida",
                "password.min" => "La contraseña debe tener al menos 8 caracteres"
            ]
        );

        if ($validate->fails()) {
            return response()->json([
                "msg" => "Error al validar los datos",
                "error" => $validate->errors()
            ], 422);
        }

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'msg' => 'No autorizado'
            ], 401);
        }

        $user = User::where('email', $credentials['email'])->first();

        return response()->json([
            'message' => 'Login correcto',
            'access_token' => $user->createToken('API TOKEN')->plainTextToken,
        ]);

    }
}
