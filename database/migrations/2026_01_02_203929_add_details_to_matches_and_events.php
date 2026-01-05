<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    // Agregar Árbitro al Partido
    Schema::table('match_games', function (Blueprint $table) {
        $table->string('referee')->default('Wilmar Roldán'); // Valor por defecto
    });

    // Agregar Nombre del Jugador al Evento
    Schema::table('match_events', function (Blueprint $table) {
        $table->string('player_name')->nullable(); // Ej: "Lionel Messi"
    });
}
};
