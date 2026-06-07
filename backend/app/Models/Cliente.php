<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'nombre_comercial',
        'rut',
        'direccion',
        'categoria',
        'contacto_nombre',
        'contacto_email',
        'porcentaje_oferta'
    ];
}
