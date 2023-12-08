<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ActivationController extends Controller
{
    public function activate(Request $request, User $user)
    {
        if (!$request->hasValidSignature()) {
            return response()->json([
                "msg" => "Enlace de activacion invalido o expirado"
            ], 401);
        }

        $token = DB::table('tokens')->where('token', hash('sha256', $request->token))->first();

        if (!$token) {
            return response()->json([
                "msg" => "Enlace de activacion invalido o expirado"
            ], 401);
        }

        DB::table('tokens')->where('token', hash('sha256', $request->token))->delete();  // invalida el token

        $user->esta_activo = true;
        $user->save();

        return redirect()->route('activationMsg');
    }

}
