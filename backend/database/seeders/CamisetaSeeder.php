<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CamisetaSeeder extends Seeder
{
    public function run(): void
    {
        $camiseta1 = DB::table('camisetas')->insertGetId([
            'titulo'          => 'Camiseta Local 2025 – Selección Chilena',
            'club'            => 'Selección Chilena',
            'pais'            => 'Chile',
            'tipo'            => 'Local',
            'color'           => 'Rojo y Azul',
            'precio'          => 45000,
            'precio_oferta'   => 38000,
            'detalles'        => 'Edición aniversario 2025',
            'codigo_producto' => 'SCL2025L',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        $camiseta2 = DB::table('camisetas')->insertGetId([
            'titulo'          => 'Camiseta Visita 2025 – Colo-Colo',
            'club'            => 'Colo-Colo',
            'pais'            => 'Chile',
            'tipo'            => 'Visita',
            'color'           => 'Blanco y Negro',
            'precio'          => 42000,
            'precio_oferta'   => null,
            'detalles'        => 'Temporada 2025',
            'codigo_producto' => 'CC2025V',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // Asociar tallas (S=2, M=3, L=4, XL=5)
        $tallas = [2, 3, 4, 5];
        foreach ($tallas as $tallaId) {
            DB::table('camiseta_talla')->insert([
                'camiseta_id' => $camiseta1,
                'talla_id'    => $tallaId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
            DB::table('camiseta_talla')->insert([
                'camiseta_id' => $camiseta2,
                'talla_id'    => $tallaId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }
}
