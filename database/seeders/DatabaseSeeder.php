<?php

// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\MatchGame;

class DatabaseSeeder extends Seeder
{
   public function run(): void
{
    // Partido 1
    \App\Models\MatchGame::create([
        'league_name' => '🇵🇹 Primeira Liga',
        'stadium' => 'Estádio D. Afonso Henriques', // Estadio agregado
        'team_home' => 'Guimaraes',
        'team_away' => 'Nacional',
        'start_time' => '20:26',
        'status' => 'scheduled'
    ]);

    // Partido En Vivo de prueba
    \App\Models\MatchGame::create([
        'league_name' => '🇪🇸 La Liga',
        'stadium' => 'Santiago Bernabéu',
        'team_home' => 'Real Madrid',
        'team_away' => 'Barcelona',
        'start_time' => "VIVO 65'",
        'score_home' => 2,
        'score_away' => 1,
        'cards_home' => 2, // Ya tienen 2 amarillas
        'cards_away' => 3, // Ya tienen 3 amarillas
        'status' => 'live'
    ]);
}
}
