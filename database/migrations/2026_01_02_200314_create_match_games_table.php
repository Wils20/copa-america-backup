<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('match_games', function (Blueprint $table) {
        $table->id();
        $table->string('league_name');
        $table->string('stadium')->nullable(); // NUEVO: Campo de juego
        $table->string('team_home');
        $table->string('team_away');
        $table->integer('score_home')->default(0);
        $table->integer('score_away')->default(0);
        $table->integer('cards_home')->default(0); // NUEVO: Tarjetas local
        $table->integer('cards_away')->default(0); // NUEVO: Tarjetas visita
        $table->string('status')->default('scheduled');
        $table->string('start_time')->nullable();
        $table->timestamps();
    });
}
};
