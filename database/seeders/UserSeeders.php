<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB:user::create([
            [
                'nombre' => 'Adrian',
                'apellido' => 'Lira',
                'email' => 'adrianlira@gmail.com',
                'password' => Hash::make('12345678')
            ],
            [
                'nombre' => 'Miguel',
                'apellido' => 'Villa',
                'email' => 'miguelon@gmail.com',
                'password' => Hash::make('12345678')
            ],
            [
                'nombre' => 'Luis',
                'apellido' => 'Garcia',
                'email' => 'luisitorey@gmail.com',
                'password' => Hash::make('12345678')
            ]
        ]);
    }
}
