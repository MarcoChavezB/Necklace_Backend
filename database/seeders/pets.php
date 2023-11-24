<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class pets extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array=[
        [
            'nombre' => 'Pipo',
            'raza' => 'Pitbull',
            'genero' => 'Macho',
            'user_id' => '1'
        ],
        [
            'nombre' => 'Muñe',
            'raza' => 'Chihuahua',
            'genero' => 'hembra',
            'user_id' => '2'
        ],
        [
            'nombre' => 'Rodolfo',
            'raza' => 'snauzer',
            'genero' => 'Macho',
            'user_id' => '3'
        ]
        ];
        foreach($array as $pet){
            DB::table('pets')->insert($pet);
        }
        
    }
}
