<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('match_events', function (Blueprint $table) {
        $table->id();
        $table->foreignId('match_game_id')->constrained(); // Relación
        $table->string('type'); // goal, card, etc.
        $table->text('message');
        $table->integer('minute');
        $table->timestamps();
    });
}
};
