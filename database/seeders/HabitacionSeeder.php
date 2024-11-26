<?php

namespace Database\Seeders;

use App\Models\Habitacion;
use App\Models\Hotel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HabitacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Habitacion::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $hoteles = Hotel::all();
        foreach ($hoteles as $h) {
            for ($i=0; $i < 20; $i++) { 
                Habitacion::create([
                    'numero'=> $i,
                    'tipo' => 'simple',
                    'precioNoche' => '34.99',
                    'hotel_id' => $h->id,
                    "updated_at"=> null
                ]
                );
            }
            for ($i=20; $i < 40; $i++) { 
                Habitacion::create([
                    'numero'=> $i,
                    'tipo' => 'doble',
                    'precioNoche' => '45.99',
                    'hotel_id' => $h->id,
                    "updated_at"=> null,
                ]
                );
            }
        }
    }
}
