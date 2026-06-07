<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Talla extends Model
{
    protected $fillable = ['nombre'];

    public function camisetas()
    {
        return $this->belongsToMany(Camiseta::class, 'camiseta_talla');
    }
}
