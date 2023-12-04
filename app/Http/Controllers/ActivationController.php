<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ActivationController extends Controller
{
    public function activate(Request $request,User $user){
      if(!$request->hasValidSignature()){
        return response()->json([
          "msg" => "Enlace de activacion invalido o expirado"
        ], 401);

      }

      $user->esta_activo = true;
      $user->save();

        return redirect()->route('activationMsg');
    }
}
