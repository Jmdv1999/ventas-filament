<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;
    protected $fillable = ["total", 'cliente_id'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    public function detalle()
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }
    protected static function booted(){
        static::saved(function ($venta) {
            foreach ($venta->detalle as $detallado) {
                $producto = Producto::find($detallado->producto_id);
                if ($producto) {
                    $producto->cantidad -= $detallado->cantidad;
                    $producto->save();
                }
            }
        });
        static::deleting(function ($venta) {
            foreach ($venta->detalle as $detallado) {
                $producto = Producto::find($detallado->producto_id);
                if ($producto) {
                    $producto->cantidad += $detallado->cantidad;
                    $producto->save();
                }
            }
            $venta->detalle()->delete();
        });
    
    }
}
