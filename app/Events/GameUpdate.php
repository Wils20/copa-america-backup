<?php

namespace App\Events;

use App\Models\MatchGame;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // <--- IMPORTANTE: NOW
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Implementamos ShouldBroadcastNow para envío inmediato sin colas
class GameUpdate implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $matchGame;
    public $type;
    public $message;
    public $minute;
    public $playerName;

    public function __construct(MatchGame $matchGame, $type, $message, $minute, $playerName = null)
    {
        $this->matchGame = $matchGame;
        $this->type = $type;
        $this->message = $message;
        $this->minute = $minute;
        $this->playerName = $playerName;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('partido.' . $this->matchGame->id),
        ];
    }

    public function broadcastAs()
    {
        return 'evento.nuevo';
    }
}
