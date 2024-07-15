<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;
    protected $fillable = ['descripcion','cantidad','precio', 'categoria_id'];

    public function categoria(){
        return $this->belongsTo(Categoria::class);
    }
    public function detalle(){
        return $this->hasMany(DetalleVenta::class, 'producto_id');
    }
}
