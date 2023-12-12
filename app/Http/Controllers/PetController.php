<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceAir;
use App\Models\DeviceMov;
use App\Models\Pet;
use App\Models\Pet_Device;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
Use \DateTime;
class PetController extends Controller
{
    public function detallesPerro($petId)
    {
        $pets = DB::table('pets')
            ->join('pet_device', 'pets.id', '=', 'pet_device.pet_id')
            ->join('devices', 'pet_device.device_id', '=', 'devices.id')
            ->select('pets.id','pets.nombre', 'pets.raza', 'pets.genero', 'devices.codigo')
            ->where('pets.id', $petId)
            ->first();

        if(!$pets){
            return response()->json([
                "msg" => "Mascota no encontrada",
            ], 404);
        }
        return $pets;
    }

    public function detallesDispositivo($deviceId){

        $devices = DB::table('pet_device')
            ->join('devices', 'devices.id', '=', 'pet_device.device_id')
            ->join('pets', 'pets.id', '=', 'pet_device.pet_id')
            ->select('devices.id','devices.modelo', 'devices.codigo', 'pets.nombre')
            ->where('devices.id', $deviceId)
            ->first();
        if(!$devices){
            return response()->json([
                "msg" => "Dispositivo no encontrado",
            ], 404);
        }
        return $devices;

    }

    public function PrimerDispxUser($userId){

        $devices = DB::table('pet_device')
            ->join('devices', 'devices.id', '=', 'pet_device.device_id')
            ->join('pets', 'pets.id', '=', 'pet_device.pet_id')
            ->select('devices.id as device_id', 'pets.id as pet_id')
            ->where('pets.user_id', $userId)
            ->first();
        if(!$devices){
            return response()->json([
                "msg" => "Dispositivo no encontrado",
            ], 404);
        }
        return $devices;
    }

    public function getInfoPerroXIdCollar($dispId){
        $pets = DB::table('pets')
            ->join('pet_device', 'pets.id', '=', 'pet_device.pet_id')
            ->join('devices', 'pet_device.device_id', '=', 'devices.id')
            ->select('pets.id','pets.nombre', 'pets.raza', 'pets.genero')
            ->where('devices.id', $dispId)
            ->first();

        if(!$pets){
            return response()->json([
                "msg" => "Mascota no encontrada",
            ], 404);
        }
        return $pets;

    }

    public function perrosxUsuario($userID){
        $pets = DB::table('pets')
            ->join('users', 'pets.user_id', '=', 'users.id')
            ->select('pets.id','pets.nombre', 'pets.raza', 'pets.genero')
            ->where('users.id', $userID)
            ->get();
        if(!$pets){
            return response()->json([
                "msg" => "No tiene mascotas registradas",
            ], 404);
        }
        return $pets;
    }

    public function linkPetToDisp(Request $request)
    {
        $validate = Validator::make(
            $request->all(),
            [
                "mascota"   => "required|exists:pets,id",
                "codigo"    => "required|exists:devices,codigo",
            ]
        );

        if ($validate->fails()) {
            return response()->json([
                "msg"   => "Error al validar los datos",
                "error" => $validate->errors()
            ], 422);
        }

        $pet = Pet::where('id', $request->mascota)->first();
        $device = Device::where('codigo', $request->codigo)->first();

        if (!$device) {
            return response()->json([
                "msg" => "Codigo no encontrado",
            ], 404);
        }
        if (!$pet) {
            return response()->json([
                "msg" => "Mascota no encontrada",
            ], 404);
        }

        // Verifica si el dispositivo ya está vinculado previamente
        if (Pet_Device::where('device_id', $device->id)->where('pet_id', $pet->id)->exists()) {
            return response()->json([
                "msg" => "Dispositivo ya vinculado previamente",
            ], 422);
        }

        // Vincula el dispositivo a la mascota
        Pet_Device::create([
            'device_id'   => $device->id,
            'pet_id' => $pet->id,
        ]);

        return response()->json([
            "msg" => "Dispositivo vinculado",
        ], 201);
    }

    public function UnlinkPetToDisp($id)
    {
        $pet = Pet_Device::where('id', $id)->first();

        if (!$pet) {
            return response()->json([
                "msg"   => "Dispositivo no encontrado",
            ], 422);
        }

        $pet->delete();
        return response()->json([
            "msg" => "Dispositivo desvinculado",
        ], 201);
    }

    public function getDisplinks(){
        $pet = Pet_Device::all();
        return $pet;
    }

    public function registerPet(Request $request){
        $validate = Validator::make(
            $request->all(),
            [
                "nombre"   => "required",
                "raza"    => "required",
                "genero"    => "required",
                "user_id"    => "required",
            ],
            [
                "nombre.required" => "El nombre es requerido",
                "raza.required" => "La raza es requerida",
                "genero.required" => "El genero es requerido",
                "user_id.required" => "El id del usuario es requerido",
            ]
        );

        if ($validate->fails()) {
            return response()->json([
                "msg"   => "Error al validar los datos",
                "error" => $validate->errors()
            ], 422);
        }

        $pet = new Pet();
        $pet->nombre = $request->nombre;
        $pet->raza = $request->raza;
        $pet->genero = $request->genero;
        $pet->user_id = $request->user_id;
        $pet->save();

        return response()->json([
            "msg"=>"Mascota registrada",
        ],201);
    }

    public function getDogData(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'deviceCode' => 'required',
        ],
            [
                'deviceCode.required' => 'El código del dispositivo es requerido',
            ]);

        if ($validate->fails()) {
            return response()->json([
                "msg"   => "Error al validar los datos",
                "error" => $validate->errors()
            ], 422);
        }


        $activityData = $this->getActivityData($request->input('deviceCode')); // Datos de actividad del sensor

        if (!$activityData) {
            return response()->json([
                "msg" => "No se pudo obtener los datos de actividad",
            ], 500);
        }

        $restTime = $this->calculateRestTime($activityData); // Calcular el tiempo de reposo
        $happinessLevel = $this->calculateHappinessLevel($activityData); // Calcular el nivel de felicidad

        return response()->json([
            'restTime' => $restTime,
            'happinessLevel' => $happinessLevel
        ]);
    }

    private function getActivityData($deviceCode)
    {
        $client = new Client();
        $feedName = "-vel-value";

        $PetDeviceId = $this->getPetDeviceId($deviceCode);
        if(!$PetDeviceId){
            return response()->json([
                "msg" => "Registro no encontrado",
            ], 404);
        }

        $devCode = $deviceCode;
        $feedKey = $devCode.$feedName;

        try {
            $response = $client->request('GET','https://io.adafruit.com/api/v2/MarcoChavez/feeds/'.$feedKey.'/data/last',[
                'headers' => [
                    'X-AIO-Key' => env('ADAFRUIT_IO_KEY')
                ]
            ]);
        } catch (\Exception $e) {
            return null;
        }

        $data = json_decode($response->getBody(), true);

        $value = $data['value'];
        $date = $data['created_at'];
        $feedId = $data['feed_id'];

        $fechaUTC = new DateTime($date, new \DateTimeZone('UTC'));
        $fechaUTC -> setTimeZone(new \DateTimeZone('America/Monterrey'));
        $fechaLocal = $fechaUTC->format('Y-m-d H:i:s');

        $this->saveMovData($value, $fechaLocal, $feedId, $PetDeviceId->id);



        return $data['value'];
    }

    public function saveMovData($value, $date, $feedId, $PetDeviceId){
        $deviceHum = new DeviceMov();
        $deviceHum->pet_device_id = $PetDeviceId;
        $deviceHum->value = $value;
        $deviceHum->created_at = $date;
        $deviceHum->feed_id = $feedId;
        $deviceHum->save();
    }

    public function getPetDeviceId($deviceCode){
        $PetDeviceId = DB::table('pet_device')
            ->join('devices', 'pet_device.device_id', '=', 'devices.id')
            ->select('pet_device.id')
            ->where( 'devices.codigo',  $deviceCode)
            ->first();

        if(!$PetDeviceId){
            return null;
        }
        return $PetDeviceId;

    }



    private function calculateRestTime($activityData)
    {
        $state = (1 - $activityData) * 24;

        if ($state > 0.5) {
            return 'Movimiento';
        } else {
            return 'Reposo';
        }
    }

    private function calculateHappinessLevel($activityData)
    {
        if ($activityData > 0.7) {
            return 3; // Feliz
        } elseif ($activityData > 0.4) {
            return 2; // Medio
        } else {
            return 1; // Triste
        }
    }

    public function registerPetYDev(Request $request){
        $validate = Validator::make(
            $request->all(),
            [
                "nombre"   => "required",
                "raza"    => "required",
                "genero"    => "required",
                "codigo"    => "required",
                "user_id"    => "required",
            ],
            [
                "nombre.required" => "El nombre es requerido",
                "raza.required" => "La raza es requerida",
                "genero.required" => "El genero es requerido",
                "user_id.required" => "El id del usuario es requerido",
                "codigo.required" => "El codigo es requerido",
            ]
        );

        if ($validate->fails()) {
            return response()->json([
                "msg"   => "Error al validar los datos",
                "error" => $validate->errors()
            ], 422);
        }

        $pet = new Pet();
        $pet->nombre = $request->nombre;
        $pet->raza = $request->raza;
        $pet->genero = $request->genero;
        $pet->user_id = $request->user_id;
        $pet->save();

        $PetID = $pet->id;
        $deviceID = DB::table('devices')
            ->select('devices.id')
            ->where('devices.codigo', $request->input('codigo'))
            ->first();

        if( !$deviceID ){
            return response()->json([
                "msg" => "Codigo no encontrado",
            ], 404);
        }

        $pet_device = new Pet_Device();
        $pet_device->pet_id = $PetID;
        $pet_device->device_id = $deviceID->id;
        $pet_device->save();

        return response()->json([
            "msg"=>"Mascota registrada",
        ],201);
    }

    public function deletePet($petId){
        $pet = Pet::where('id', $petId)->first();
        if (!$pet) {
            return response()->json([
                "msg" => "Mascota no encontrada",
            ], 404);
        }
        $pet_device = Pet_Device::where('pet_id', $petId)->first();
        if(!$pet_device){
            $pet->delete();
            return response()->json([
                "msg" => "Mascota eliminada",
            ], 201);
        }

        return response()->json([
            "msg" => "No se puede eliminar por que ya esta enlazada a un dispositivo",
        ], 422);
    }

    public function UpdatePet(Request $request ,$petId){

        $validate = Validator::make(
            $request->all(),
            [
                "nombre"   => "required|min:3",
            ],
            [
                "nombre.required" => "El nombre es requerido",
                "nombre.min" => "El nombre debe tener al menos 3 caracteres",
            ]
        );

        if($validate->fails()){
           return response()->json([
               "msg" => "Error al validar los datos",
           ],422);
        }

        $pet = Pet::where('id', $petId)->first();
        if (!$pet) {
            return response()->json([
                "msg" => "Mascota no encontrada",
            ], 404);
        }

        $pet->nombre = $request->nombre;

        $pet->save();
        return response()->json([
            "msg" => "Mascota actualizada",
        ], 201);

    }




}
