<?php

use Illuminate\Support\Facades\Route;
use App\Models\MatchGame;
use App\Models\Player;
use App\Models\MatchTimer;
use App\Events\GameUpdate;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistema Mundial 2026 (FINAL BLINDADO)
|--------------------------------------------------------------------------
*/

// --------------------------------------------------------------------------
// 1. VISTAS (FRONTEND)
// --------------------------------------------------------------------------

Route::get('/', function () {
    // Ordenar: En Vivo > Descanso > Programado > Finalizado
    $matches = MatchGame::orderByRaw("FIELD(status, 'live', 'break', 'scheduled', 'finished')")
                        ->orderBy('created_at', 'desc')
                        ->get();
    return view('welcome', compact('matches'));
});

Route::get('/ver-partido/{id}', function ($id) {
    $match = MatchGame::findOrFail($id);
    $history = $match->events()->orderBy('created_at', 'desc')->get();
    $timer = MatchTimer::where('match_game_id', $match->id)->first();
    $currentMinute = $timer ? $timer->current_minute : 0;

    // Decodificar stats para la vista
    $stats = $match->stats;
    if (is_string($stats)) $stats = json_decode($stats, true);

    if(!$stats) {
        $stats = ['possession_home' => 50, 'possession_away' => 50, 'shots_home' => 0, 'shots_away' => 0, 'corners_home' => 0, 'corners_away' => 0, 'fouls_home' => 0, 'fouls_away' => 0, 'win_prob_home' => 33, 'win_prob_draw' => 34, 'win_prob_away' => 33];
    }
    // Asignamos de nuevo al objeto para que la vista lo lea fácil
    $match->stats = $stats;

    return view('estadio', compact('match', 'history', 'currentMinute'));
});

Route::get('/panel-control/{id}', function($id) {
    $match = MatchGame::findOrFail($id);
    $timer = MatchTimer::where('match_game_id', $match->id)->first();
    $currentMinute = $timer ? $timer->current_minute : 0;
    return view('arbitro', compact('match', 'currentMinute'));
});

// --------------------------------------------------------------------------
// 2. API DEL ÁRBITRO (LÓGICA BLINDADA ANTI-ERRORES)
// --------------------------------------------------------------------------

Route::get('/arbitro/{id}/{type}', function ($id, $type) {

    // 1. INTENTO DE CARGAR BASE DE DATOS
    try {
        $match = MatchGame::findOrFail($id);
        $timer = MatchTimer::firstOrCreate(['match_game_id' => $match->id]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error_msg' => 'Error BD: ' . $e->getMessage()], 500);
    }

    // --- CORRECCIÓN ERROR "STRING ON STRING" ---
    // Leemos stats de la BD
    $rawStats = $match->stats;

    // Si viene como texto (JSON), lo convertimos a Array
    if (is_string($rawStats)) {
        $stats = json_decode($rawStats, true);
    } else {
        $stats = $rawStats;
    }

    // Si está vacío o corrupto, ponemos los defaults
    if (!is_array($stats)) {
        $stats = [
            'possession_home' => 50, 'possession_away' => 50,
            'shots_home' => 0, 'shots_away' => 0,
            'corners_home' => 0, 'corners_away' => 0,
            'fouls_home' => 0, 'fouls_away' => 0,
            'win_prob_home' => 33, 'win_prob_draw' => 34, 'win_prob_away' => 33
        ];
    }
    // -------------------------------------------

    // Intentar Cargar Jugadores
    $playersHome = ['Jugador Local'];
    $playersAway = ['Jugador Visita'];
    try {
        $hp = Player::where('team_name', $match->team_home)->get();
        if($hp->count() > 0) $playersHome = $hp->pluck('name')->toArray();
        $ap = Player::where('team_name', $match->team_away)->get();
        if($ap->count() > 0) $playersAway = $ap->pluck('name')->toArray();
    } catch(\Exception $e) { /* Fallo silencioso */ }

    $minute = $timer->current_minute;
    $playerName = null;
    $msg = "Evento";
    $shouldBroadcast = true;

    switch ($type) {
        // --- CONTROL DE TIEMPO ---
        case 'inicio':
            $offset = ($timer->offset_minutes >= 45) ? 45 : 0;
            $timer->update(['started_at' => now(), 'offset_minutes' => $offset, 'is_running' => true]);
            $match->update(['status' => 'live']);
            $msg = "Inicio del partido";
            $minute = $offset;
            break;
        case 'descanso':
            $timer->update(['is_running' => false, 'started_at' => null, 'offset_minutes' => 45]);
            $match->update(['status' => 'break', 'start_time' => 'ET']);
            $msg = "Entretiempo";
            break;
        case 'fin':
            $finalMin = $timer->current_minute;
            $timer->update(['is_running' => false, 'started_at' => null, 'offset_minutes' => $finalMin]);
            $match->update(['status' => 'finished', 'start_time' => 'FINAL']);
            $msg = "Fin del Partido";
            break;
        case 'reset':
            $match->update(['score_home' => 0, 'score_away' => 0, 'cards_home' => 0, 'cards_away' => 0, 'red_cards_home' => 0, 'red_cards_away' => 0, 'status' => 'scheduled', 'start_time' => 'En espera', 'stats' => null]);
            $timer->update(['started_at' => null, 'offset_minutes' => 0, 'is_running' => false]);
            $match->events()->delete();
            $msg = "Reinicio Completo";
            $minute = 0;
            break;

        // --- GOLES ---
        case 'goal_home':
            $playerName = $playersHome[array_rand($playersHome)];
            $msg = "¡GOOOL DE " . $match->team_home . "!";
            $stats['shots_home']++; $stats['possession_home'] += 2;
            break;
        case 'goal_away':
            $playerName = $playersAway[array_rand($playersAway)];
            $msg = "¡GOOOL DE " . $match->team_away . "!";
            $stats['shots_away']++; $stats['possession_away'] += 2;
            break;

        // --- EVENTOS ESTADÍSTICOS ---
        case 'shot_home': $stats['shots_home']++; $stats['possession_home'] += 1; $msg = "Remate Local"; $shouldBroadcast = false; break;
        case 'shot_away': $stats['shots_away']++; $stats['possession_away'] += 1; $msg = "Remate Visita"; $shouldBroadcast = false; break;
        case 'corner_home': $stats['corners_home']++; $msg = "Córner Local"; break;
        case 'corner_away': $stats['corners_away']++; $msg = "Córner Visita"; break;
        case 'foul_home': $stats['fouls_home']++; $msg = "Falta Local"; $shouldBroadcast = false; break;
        case 'foul_away': $stats['fouls_away']++; $msg = "Falta Visita"; $shouldBroadcast = false; break;

        // --- TARJETAS Y CAMBIOS ---
        case 'yellow_card_home': $playerName = $playersHome[array_rand($playersHome)]; $msg = "Amarilla"; $stats['fouls_home']++; break;
        case 'yellow_card_away': $playerName = $playersAway[array_rand($playersAway)]; $msg = "Amarilla"; $stats['fouls_away']++; break;
        case 'red_card_home': $playerName = $playersHome[array_rand($playersHome)]; $msg = "Roja"; $stats['fouls_home']++; break;
        case 'red_card_away': $playerName = $playersAway[array_rand($playersAway)]; $msg = "Roja"; $stats['fouls_away']++; break;
        case 'substitution_home': $msg = "Cambio " . $match->team_home; $playerName = "🔺 " . $playersHome[array_rand($playersHome)] . " | 🔻 " . $playersHome[array_rand($playersHome)]; break;
        case 'substitution_away': $msg = "Cambio " . $match->team_away; $playerName = "🔺 " . $playersAway[array_rand($playersAway)] . " | 🔻 " . $playersAway[array_rand($playersAway)]; break;
    }

    // --- ALGORITMO IA: PROBABILIDAD DE VICTORIA ---
    $diff = $match->score_home - $match->score_away;
    $factor = ($minute / 90) * 1.5;

    if ($diff > 0) { // Gana Local
        $stats['win_prob_home'] = min(99, 50 + ($diff * 15) + ($factor * 20));
        $stats['win_prob_draw'] = max(1, 100 - $stats['win_prob_home'] - 5);
        $stats['win_prob_away'] = max(0, 100 - $stats['win_prob_home'] - $stats['win_prob_draw']);
    } elseif ($diff < 0) { // Gana Visita
        $stats['win_prob_away'] = min(99, 50 + (abs($diff) * 15) + ($factor * 20));
        $stats['win_prob_draw'] = max(1, 100 - $stats['win_prob_away'] - 5);
        $stats['win_prob_home'] = max(0, 100 - $stats['win_prob_away'] - $stats['win_prob_draw']);
    } else { // Empate
        $stats['win_prob_draw'] = max(10, 40 + ($factor * 30));
        $remain = 100 - $stats['win_prob_draw'];
        $stats['win_prob_home'] = $remain / 2;
        $stats['win_prob_away'] = $remain / 2;
    }

    // Normalizar Posesión
    $totalPossession = $stats['possession_home'] + $stats['possession_away'];
    if($totalPossession > 0) {
        $stats['possession_home'] = round(($stats['possession_home'] / $totalPossession) * 100);
        $stats['possession_away'] = 100 - $stats['possession_home'];
    }

    // Guardar Stats (Laravel se encargará de convertirlo a JSON al guardar)
    $match->stats = $stats;
    $match->save();

    if($timer->is_running && $type !== 'fin' && $type !== 'descanso') {
        $match->update(['start_time' => "VIVO " . intval($timer->current_minute) . "'"]);
    }

    // 2. INTENTO DE TRANSMISIÓN (REVERB) - CON PROTECCIÓN DE ERRORES
    $broadcastError = null;
    try {
        if($shouldBroadcast && !in_array($type, ['shot_home', 'shot_away', 'foul_home', 'foul_away'])) {
            GameUpdate::dispatch($match, $type, $msg, $minute, $playerName);
        } else {
            GameUpdate::dispatch($match, 'stats_update', 'Stats Update', $minute, null);
        }
    } catch (\Exception $e) {
        $broadcastError = $e->getMessage();
    }

    // 3. RESPUESTA FINAL
    return response()->json([
        'success' => true,
        'stats' => $stats,
        'message' => $msg,
        'broadcast_status' => $broadcastError ? 'ERROR: ' . $broadcastError : 'OK'
    ]);
});

// --------------------------------------------------------------------------
// 3. UTILS & SETUP (INSTALADORES)
// --------------------------------------------------------------------------

Route::get('/instalar-stats', function() {
    if (!Schema::hasColumn('match_games', 'stats')) {
        Schema::table('match_games', function ($table) { $table->json('stats')->nullable(); });
        return "✅ Columna 'stats' agregada a la base de datos.";
    }
    return "✅ El sistema de estadísticas ya está instalado.";
});

Route::get('/setup-rapido', function() {
    if(MatchGame::count() > 0) return "Ya existen partidos.";

    // Stats iniciales (Como array encodeado, por si acaso)
    $initStats = json_encode(['possession_home' => 50, 'possession_away' => 50, 'shots_home' => 0, 'shots_away' => 0, 'corners_home' => 0, 'corners_away' => 0, 'fouls_home' => 0, 'fouls_away' => 0, 'win_prob_home' => 33, 'win_prob_draw' => 34, 'win_prob_away' => 33]);

    // COPA AMÉRICA
    MatchGame::create(['league_name' => '🏆 Copa América', 'team_home' => 'Argentina', 'team_away' => 'Chile', 'stadium' => 'MetLife', 'referee' => 'Roldán', 'start_time' => '17:00', 'status' => 'scheduled', 'stats' => $initStats]);
    MatchGame::create(['league_name' => '🏆 Copa América', 'team_home' => 'Uruguay', 'team_away' => 'Perú', 'stadium' => 'SoFi', 'referee' => 'Cunha', 'start_time' => '22:00', 'status' => 'scheduled', 'stats' => $initStats]);

    // OTROS
    MatchGame::create(['league_name' => '🇵🇹 Liga Portugal', 'team_home' => 'Guimaraes', 'team_away' => 'Nacional', 'stadium' => 'Afonso Henriques', 'referee' => 'Soares Dias', 'start_time' => '19:00', 'status' => 'scheduled', 'stats' => $initStats]);
    MatchGame::create(['league_name' => '🤝 Amistoso', 'team_home' => 'Brasil', 'team_away' => 'Colombia', 'stadium' => 'Rose Bowl', 'referee' => 'Sampaio', 'start_time' => '21:00', 'status' => 'scheduled', 'stats' => $initStats]);
    MatchGame::create(['league_name' => '🇪🇸 El Clásico', 'team_home' => 'Real Madrid', 'team_away' => 'Barcelona', 'stadium' => 'Bernabéu', 'referee' => 'Sánchez', 'start_time' => 'Mañana', 'status' => 'scheduled', 'stats' => $initStats]);

    return "✅ Partidos creados. Ejecuta /llenar-plantillas.";
});

Route::get('/llenar-plantillas', function() {
    \App\Models\Player::truncate();
    $squads = [
        'Argentina' => ['Messi', 'Dibu', 'Julián', 'Enzo', 'De Paul', 'Mac Allister', 'Otamendi', 'Cuti', 'Molina', 'Tagliafico'],
        'Chile' => ['Bravo', 'Alexis', 'Vargas', 'Brereton', 'Suazo', 'Paulo Díaz', 'Maripán', 'Pulgar', 'Núñez', 'Isla'],
        'Uruguay' => ['Valverde', 'Darwin', 'Araújo', 'Giménez', 'Rochet', 'Ugarte', 'Bentancur', 'De la Cruz', 'Pellistri', 'Olivera'],
        'Perú' => ['Gallese', 'Lapadula', 'Guerrero', 'Advíncula', 'Quispe', 'Tapia', 'Callens', 'Zambrano', 'Trauco', 'Flores'],
        'Real Madrid' => ['Vinícius', 'Bellingham', 'Mbappé', 'Rodrygo', 'Valverde', 'Modric', 'Courtois', 'Rüdiger', 'Carvajal', 'Mendy'],
        'Barcelona' => ['Yamal', 'Lewandowski', 'Pedri', 'Gavi', 'Ter Stegen', 'Koundé', 'Araújo', 'Cubarsí', 'Raphinha', 'Gündogan'],
        'Brasil' => ['Alisson', 'Vinícius Jr', 'Rodrygo', 'Endrick', 'Paquetá', 'Bruno Guimarães', 'Marquinhos', 'Danilo', 'Militao', 'Raphinha'],
        'Colombia' => ['Luis Díaz', 'James', 'Richard Ríos', 'Vargas', 'Davinson', 'Arias', 'Lerma', 'Muñoz', 'Córdoba', 'Mina'],
        'Guimaraes' => ['Varela', 'Jota Silva', 'Tiago Silva', 'Handel', 'Mangas', 'Fernandes', 'André Silva', 'Oliveira', 'Borevkovic', 'Maga'],
        'Nacional' => ['França', 'Danilovic', 'Esteves', 'Witi', 'Gustavo Silva', 'Aurélio', 'Carlos Daniel', 'Ramírez', 'Ulisses', 'Raimar']
    ];

    $count = 0;
    foreach($squads as $team => $players) {
        foreach($players as $name) {
            \App\Models\Player::create(['name' => $name, 'team_name' => $team, 'number' => rand(1, 99)]);
            $count++;
        }
    }
    return "✅ Jugadores creados para: " . implode(', ', array_keys($squads));
});
