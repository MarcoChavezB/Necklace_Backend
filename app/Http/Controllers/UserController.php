<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class UserController extends Controller
{

    public function __construct(){//Aqui se especifica que metodos necesitan autenticacion
        $this->middleware('auth:api', ['except' => ['register', 'login']]); //Aqui se especifica que metodos no necesitan autenticacion
    }

    public function login(){
        $credentials = request(['email', 'password']);//Aqui se obtienen las credenciales del usuario

        $validate = Validator::make(
            $credentials,
            [
                "email"      =>"required|email",
                "password" =>"required|min:8"
            ]
        );

        if($validate->fails()){
            return response()->json([
                "msg"=>"Error al validar los datos",
                "error"=>$validate->errors()
            ],422);
        }

        if(! $token = auth()->attempt($credentials)){//Aqui se verifica si las credenciales son correctas
            return response()->json([
                'msg' => 'No autorizado'
            ], 401);
        }
        return $this->respondWithToken($token, $credentials['email']);//Aqui se genera el token
    }

    protected  function respondWithToken($token, $email){//Aqui se genera el token
        $isActive = $this->isActive($email);
        return response()->json([
            'access_token' => $token,//Aqui se especifica el token
            'token_type' => 'bearer',//Aqui se especifica el tipo de token
            'isActive' => $isActive,
            //Que el token dure 5 segundos?
            'expires_in' => auth()->factory()->getTTL() * 1,//Aqui se especifica el tiempo de expiracion del token
        ]);
    }

    public function isActive($email){
        $user = User::where('email', $email)->first();
        if($user){
            $isActive = $user->esta_activo;
            return $isActive;
        }
        else {
            return response()->json([
                'msg' => 'Usuario no encontrado'
            ], 404);
        }
    }
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function register(Request $request){
        $validate = Validator::make(
            $request->all(),
            [
                "nombre"    =>"required|max:100|min:4",
                "apellido"  =>"required|max:100|min:4",
                "email"      =>"required|email",
                "password" =>"required|min:8"
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
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            "msg"=>"Usuario registrado",
            "activo" => false,
        ],201);

    }

    public function InfoUsuario(Request $request){
        $validate = Validator::make(
            $request->all(),
            [
                "id" => "required"
            ]
        );

        if($validate->fails()){
            return response()->json([
                "msg"=>"Error al validar el ID",
                "error"=>$validate->errors()
            ],422);
        }
        $id = $request->id;

        $user = User::where('id', $id)->first();
        if(!$user){
            return response()->json([
                "msg"=>"Usuario no encontrado",
            ],404);
        }

        $Ndispositivos = DB::table('pets')
            ->join('pet_devices', 'pets.id', '=', 'pet_devices.pet_id')
            ->where('pets.user_id', $id)
            ->count();

        return response()->json([
            "nombre" => $user->nombre,
            "apellido" => $user->apellido,
            "email" => $user->email,
            "Ndispositivos" => $Ndispositivos,
        ], 200);
    }






    public function LinkDispo(Request $request)
    {
        $validate = Validator::make(
            $request->all(),
            [
                "user"   => "required|exists:users,id",
                "device" => "required|exists:devices,id"
            ]
        );

        if ($validate->fails()) {
            return response()->json([
                "msg"   => "Error al validar los datos",
                "error" => $validate->errors()
            ], 422);
        }

        $user = User::find($request->user);
        $device = \DB::table('devices')->find($request->device);

        if (!$device) {
            return response()->json([
                "msg" => "Dispositivo no encontrado",
            ], 404);
        }

        if (\DB::table('user_device')->where('user_id', $user->id)->where('device_id', $device->id)->first()) {
            return response()->json([
                "msg" => "Dispositivo ya vinculado previamente",
            ], 422);
        }

        \DB::table('user_device')->insert([
            'user_id'   => $user->id,
            'device_id' => $device->id,
        ]);

        return response()->json([
            "msg" => "Dispositivo vinculado",
        ], 201);
    }


}
