<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MatchTimer extends Model
{
    use HasFactory;

    protected $fillable = ['match_game_id', 'started_at', 'offset_minutes', 'is_running'];

    // Relación inversa
    public function matchGame()
    {
        return $this->belongsTo(MatchGame::class);
    }

    /**
     * Función Mágica: Calcula el minuto actual en tiempo real
     */
    public function getCurrentMinuteAttribute()
    {
        // Si el reloj está parado, devolvemos el minuto donde se quedó (offset)
        if (!$this->is_running || !$this->started_at) {
            return $this->offset_minutes;
        }

        // Si está corriendo: (Hora Actual - Hora Inicio) + Minutos Iniciales
        $start = Carbon::parse($this->started_at);
        $now = Carbon::now();

        // Diferencia en minutos
        $diff = $start->diffInMinutes($now);

        return (int) ($this->offset_minutes + $diff); // <--- AQUI ESTA LA MAGIA: (int)
    }
}
