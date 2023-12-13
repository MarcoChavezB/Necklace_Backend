<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountActivation;
class UserController extends Controller
{

    public function __construct(){
        $this->middleware('auth:api', ['except' => ['register', 'login']]);
    }

    public function login(){
        $credentials = request(['email', 'password']);

        $validate = Validator::make(
            $credentials,
            [
                "email"      =>"required|email",
                "password" =>"required|min:8"
            ],
            [
                "email.required" => "El email es requerido",
                "email.email" => "El email debe ser válido",
                "password.required" => "La contraseña es requerida",
                "password.min" => "La contraseña debe tener al menos 8 caracteres"
            ]
        );

        if($validate->fails()){
            return response()->json([
                "msg"=>"Error al validar los datos",
                "error"=>$validate->errors()
            ],422);
        }

        if(! $token = auth()->attempt($credentials)){
            return response()->json([
                'msg' => 'No autorizado'
            ], 401);
        }
        return $this->respondWithToken($token, $credentials['email']);
    }

    protected  function respondWithToken($token, $email){
        $isActive = $this->isActive($email);
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'isActive' => $isActive,
            'expires_in' => auth()->factory()->getTTL() * 60,
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

        return response()->json(['message' => 'Se ha cerrado sesión correctamente']);
    }

    public function register(Request $request){
        $validate = Validator::make(
            $request->all(),
            [
                "nombre"    =>"required|max:100|min:4",
                "apellido"  =>"required|max:100|min:4",
                "email"      =>"required|email",
                "password" =>"required|min:8"
            ],
            [
                "nombre.required" => "El nombre es requerido",
                "nombre.max" => "El nombre debe tener máximo 100 caracteres",
                "nombre.min" => "El nombre debe tener mínimo 4 caracteres",
                "apellido.required" => "El apellido es requerido",
                "apellido.max" => "El apellido debe tener máximo 100 caracteres",
                "apellido.min" => "El apellido debe tener mínimo 4 caracteres",
                "email.required" => "El email es requerido",
                "email.email" => "El email debe ser válido",
                "password.required" => "La contraseña es requerida",
                "password.min" => "La contraseña debe tener al menos 8 caracteres"
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

        Mail::to($user->email)->send(new AccountActivation($user));

        return response()->json([
            "msg"=>"Usuario registrado",
            "activo" => false,
        ],201);

    }

    public function InfoUsuario($id){
        $user = User::where('id', $id)->first();

        if (!$user) {
            return response()->json([
                "msg" => "No se encontró ningún usuario con el ID proporcionado"
            ], 404);
        }

        $Ndispositivos = DB::table('pets')
            ->join('pet_device', 'pets.id', '=', 'pet_device.pet_id')
            ->where('pets.user_id', $id)
            ->count();

        return response()->json([
            "nombre" => $user->nombre,
            "apellido" => $user->apellido,
            "email" => $user->email,
            "Ndispositivos" => $Ndispositivos,
        ], 200);
    }

    public function getUserDevices($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $devices = $user->pets->flatMap(function ($pet) {
            return $pet->PetDevices->map->device;
        })->map(function ($device){
            return $device->only(['id', 'modelo', 'codigo']);
        });

        return response()->json($devices);
    }

    public function getPetsWithoutDevice($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                "msg" => "No se encontró ningún usuario con el ID proporcionado"
            ], 404);
        }

        $pets = Pet::where('user_id', $user->id)->doesntHave('petDevices')->get(['id', 'nombre']);

        if ($pets->isEmpty()) {
            return response()->json([
                "pets" => null
            ], 404);
        }

        return $pets;
    }

}
