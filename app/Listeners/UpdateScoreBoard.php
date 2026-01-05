<?php

namespace App\Listeners;

use App\Events\GameUpdate;
use App\Models\MatchEvent;
use Illuminate\Support\Str;

class UpdateScoreBoard
{
    public function handle(GameUpdate $event): void
    {
        // 1. Guardar Historial
        MatchEvent::create([
            'match_game_id' => $event->matchGame->id,
            'type'          => $event->type,
            'message'       => $event->message,
            'minute'        => $event->minute,
            'player_name'   => $event->playerName,
        ]);

        // 2. Actualizar Goles
        if ($event->type === 'goal_home') $event->matchGame->increment('score_home');
        if ($event->type === 'goal_away') $event->matchGame->increment('score_away');

        // 3. Actualizar Amarillas
        if ($event->type === 'yellow_card_home') $event->matchGame->increment('cards_home');
        if ($event->type === 'yellow_card_away') $event->matchGame->increment('cards_away');

        // 4. Actualizar Rojas
        if ($event->type === 'red_card_home') $event->matchGame->increment('red_cards_home');
        if ($event->type === 'red_card_away') $event->matchGame->increment('red_cards_away');
    }
}
