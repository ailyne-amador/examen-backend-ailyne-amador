<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TallaSeeder extends Seeder
{
    public function run(): void
    {
        $tallas = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
        foreach ($tallas as $t) {
            DB::table('tallas')->insert(['nombre' => $t, 'created_at' => now(), 'updated_at' => now()]);
        }
    }
}
