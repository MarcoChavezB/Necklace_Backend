<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            [
                'nombre' => 'Adrian',
                'apellido' => 'lira',
                'email' => 'adrian@gmail.com',
                'password' => Hash::make('12345678')
            ],
            [
                'nombre' => 'Juan',
                'apellido' => 'Perez',
                'email' => 'perez@gmail.com',
                'password' => Hash::make('12345678')
            ],
            [
                'nombre' => 'Pedro',
                'apellido' => 'Gonzalez',
                'email' => 'pedro@gmail.com',
                'password' => Hash::make('12345678')
            ]
            ];
            foreach($array as $user){
                DB::table('users')->insert($user);
            }
        
}
}