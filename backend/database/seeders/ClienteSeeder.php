<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('clientes')->insert([
            [
                'nombre_comercial'  => '90minutos',
                'rut'               => '76.111.111-1',
                'direccion'         => 'Providencia, Santiago',
                'categoria'         => 'Preferencial',
                'contacto_nombre'   => 'Carlos Ruiz',
                'contacto_email'    => 'carlos@90minutos.cl',
                'porcentaje_oferta' => 15.00,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'nombre_comercial'  => 'tdeportes',
                'rut'               => '76.222.222-2',
                'direccion'         => 'Maipú, Santiago',
                'categoria'         => 'Regular',
                'contacto_nombre'   => 'Ana Soto',
                'contacto_email'    => 'ana@tdeportes.cl',
                'porcentaje_oferta' => 0.00,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ]);
    }
}
