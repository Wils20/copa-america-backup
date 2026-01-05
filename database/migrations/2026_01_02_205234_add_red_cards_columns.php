<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('match_games', function (Blueprint $table) {
        $table->integer('red_cards_home')->default(0);
        $table->integer('red_cards_away')->default(0);
    });
}
};
