<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchGame extends Model
{
    use HasFactory;

    protected $guarded = [];

    // --- AGREGA ESTO ---
    protected $casts = [
        'stats' => 'array',  // <--- ¡ESTO ES LA CLAVE MÁGICA!
        'is_running' => 'boolean',
    ];

    public function events()
    {
        return $this->hasMany(MatchEvent::class);
    }
}
