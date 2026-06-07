<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Camiseta extends Model
{
    protected $fillable = [
        'titulo',
        'club',
        'pais',
        'tipo',
        'color',
        'precio',
        'precio_oferta',
        'detalles',
        'codigo_producto'
    ];

    public function tallas()
    {
        return $this->belongsToMany(Talla::class, 'camiseta_talla');
    }

    public function getPrecioFinal(Cliente $cliente): float
    {
        if (
            $cliente->categoria === 'Preferencial' &&
            $this->precio_oferta !== null
        ) {
            return (float) $this->precio_oferta;
        }
        return (float) $this->precio;
    }
}
