<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchGame extends Model
{
    use HasFactory;

    // OPCIÓN RECOMENDADA: Usar $guarded = []
    // Esto le dice a Laravel: "Deja guardar datos en CUALQUIER columna".
    // Es mucho más fácil que estar escribiendo cada nombre en $fillable.
    protected $guarded = [];

    // ¡ESTO ES LO MÁS IMPORTANTE!
    // Convierte automáticamente el JSON de la base de datos a un Array PHP usable.
    // Sin esto, el sistema falla al intentar leer las estadísticas.
    protected $casts = [
        'stats' => 'array',
    ];

    // Relación: Un partido tiene muchos eventos
    public function events()
    {
        return $this->hasMany(\App\Models\MatchEvent::class)->orderBy('id', 'desc');
    }
}
