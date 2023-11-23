<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DeviceSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array =[
        [
            'modelo' => 'Pechera',
            'codigo' => 'P23112023'
        ],
        [
            'modelo' => 'Pechera 2',
            'codigo' => 'P23112024'
        ],
        [
            'modelo' => 'Pechera 3',
            'codigo' => 'P23112025'
        ]
        ];
        foreach($array as $device){
            DB::table('devices')->insert($device);
        }
    }
}
