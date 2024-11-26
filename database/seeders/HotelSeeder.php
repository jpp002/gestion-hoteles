<?php

namespace Database\Seeders;

use App\Models\Hotel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Hotel::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;'); 

        for ($i=0; $i < 50; $i++) { 
            Hotel::create([
                'nombre' => "Hotel $i",
                'direccion' => "Direccion $i",
                'telefono' => "1234$i",
                'email' => "$i@email.com",
                'sitioWeb' => "$i.es",
                "updated_at"=> null,
                ]
            );
        }
    }
}
