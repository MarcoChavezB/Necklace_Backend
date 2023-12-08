<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ActivationController extends Controller
{
    public function activate(Request $request, User $user)
    {
        if (!$request->hasValidSignature()) {
            return view('emails.error');
        }

        $token = DB::table('tokens')->where('token', hash('sha256', $request->token))->first();

        if (!$token) {
            return view('emails.error');
        }

        DB::table('tokens')->where('token', hash('sha256', $request->token))->delete();  // invalida el token

        $user->esta_activo = true;
        $user->save();

        return redirect()->route('activationMsg');
    }

}
