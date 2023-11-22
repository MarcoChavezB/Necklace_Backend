<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    public function register(Request $request){
        $validate = Validator::make(
            $request->all(),
            [
                "nombre"    =>"required|max:100|min:4",
                "apellido"  =>"required|max:100|min:4",
                "email"      =>"required",
                "contraseña" =>"required|min:8"
            ]
        );

        if($validate->fails()){
            return response()->json([
                "msg"=>"Error al validar los datos",
                "error"=>$validate->errors()
            ],422);

        }

        $user = new User();
        $user->nombre = $request->nombre;
        $user->apellido = $request->apellido;
        $user->email = $request->email;
        $user->contraseña = Hash::make($request->contraseña);
        $user->save();

        return response()->json([
            "msg"=>"Usuario registrado",
            "activo" => false,
        ],201);

    }
}
