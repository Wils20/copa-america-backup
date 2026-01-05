<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchEvent extends Model
{
    use HasFactory;

    // AGREGA ESTO:
    protected $fillable = [
        'match_game_id',
        'type',
        'message',
        'minute',
        'player_name'
    ];
}
