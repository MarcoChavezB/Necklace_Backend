<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function register(Request $request){
        $validate = Validator::make(
            $request->all(),
            [
                "nombres"    =>"required|max:100|min:4",
                "apellidos"  =>"required|max:100|min:4",
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
        $user->nombres = $request->nombres;
        $user->apellidos = $request->apellidos;
        $user->email = $request->email;
        $user->contraseña = Hash::make($request->contraseña);
        $user->save();

        return response()->json([
            "msg"=>"Usuario registrado",
            "activo" => false,
        ],201);

    }
}
