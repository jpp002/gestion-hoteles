<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habitacion extends Model
{
    use HasFactory;

    protected $table = 'habitaciones';

    protected $fillable = ['numero', 'tipo', 'precioNoche', 'hotel_id'];

    //Relaciones
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function huespedes()
    {
        return $this->hasMany(Huesped::class);
    }

    public function isDisponible()
    {
        // Define la capacidad máxima según el tipo de habitación
        $capacidades = [
            'simple' => 1,
            'doble' => 2,
        ];

        $capacidadMaxima = $capacidades[$this->tipo] ?? 1;


        // Contar los huéspedes actuales que no han hecho checkout
        $huespedesActuales = $this->huespedes()->count();

        return $huespedesActuales < $capacidadMaxima;
    }
}
