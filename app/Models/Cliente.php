<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    protected $fillable = [
        'tipo_identificacion',
        'identificacion',
        'nombre',
        'direccion',
        'email',
        'telefono',
        'nacimiento'
    ];

    public function compras(){
        return $this->hasMany(Venta::class);
    }
}
