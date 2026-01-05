<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('match_timers', function (Blueprint $table) {
        $table->id();
        $table->foreignId('match_game_id')->constrained()->onDelete('cascade');

        // La hora exacta del servidor cuando el árbitro apretó el botón
        $table->timestamp('started_at')->nullable();

        // Desde qué minuto arrancamos (0 para 1er tiempo, 45 para 2do tiempo)
        $table->integer('offset_minutes')->default(0);

        // Estado del reloj
        $table->boolean('is_running')->default(false);

        $table->timestamps();
    });
}
};
