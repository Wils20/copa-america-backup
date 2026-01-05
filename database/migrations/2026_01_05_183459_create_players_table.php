<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('players', function (Blueprint $table) {
        $table->id();
        $table->string('name');      // Ej: Lionel Messi
        $table->string('team_name'); // Ej: Argentina (Para relacionarlo con tu partido)
        $table->integer('number')->nullable(); // Ej: 10
        $table->timestamps();
    });
}
};
