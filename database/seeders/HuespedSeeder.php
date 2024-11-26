<?php

namespace Database\Seeders;

use App\Models\Huesped;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HuespedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Huesped::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
    }
}
