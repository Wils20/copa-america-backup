<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\MatchGame;
use App\Models\Player;

/*
|--------------------------------------------------------------------------
| API Routes - Para Postman
|--------------------------------------------------------------------------
*/

// 1. ENDPOINT: CREAR PARTIDO
Route::post('/crear-partido', function (Request $request) {

    // Validamos que envíen los datos correctos
    $validated = $request->validate([
        'league_name' => 'required|string',
        'team_home'   => 'required|string',
        'team_away'   => 'required|string',
        'stadium'     => 'required|string',
        'referee'     => 'nullable|string',
        'start_time'  => 'required|string',
    ]);

    // Creamos el partido
    $match = MatchGame::create([
        'league_name' => $validated['league_name'],
        'team_home'   => $validated['team_home'],
        'team_away'   => $validated['team_away'],
        'stadium'     => $validated['stadium'],
        'referee'     => $validated['referee'] ?? 'Por definir',
        'start_time'  => $validated['start_time'],
        'status'      => 'scheduled',
        'score_home'  => 0,
        'score_away'  => 0,
        'cards_home'  => 0,
        'cards_away'  => 0,
        'red_cards_home' => 0,
        'red_cards_away' => 0
    ]);

    return response()->json([
        'success' => true,
        'message' => '¡Partido creado exitosamente!',
        'data' => $match
    ], 201);
});


// 2. ENDPOINT: CREAR JUGADOR (FICHAJES)
// URL para Postman: http://127.0.0.1:8000/api/crear-jugador
Route::post('/crear-jugador', function (Request $request) {

    $request->validate([
        'name' => 'required|string',
        'team_name' => 'required|string', // Debe coincidir con el nombre del equipo en el partido
    ]);

    $player = Player::create([
        'name' => $request->name,
        'team_name' => $request->team_name,
        'number' => rand(1, 99) // Asigna dorsal aleatorio si no se envía
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Jugador fichado correctamente',
        'data' => $player
    ], 201);
});
