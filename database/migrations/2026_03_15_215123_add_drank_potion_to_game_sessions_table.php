<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('game_sessions', function (Blueprint $table) {
        // We add the column here
        $table->boolean('drank_potion')->default(false);
    });
}

public function down(): void
{
    Schema::table('game_sessions', function (Blueprint $table) {
        $table->dropColumn('drank_potion');
    });
}
};
