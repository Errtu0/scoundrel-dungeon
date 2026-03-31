<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('game_sessions', function (Blueprint $table) {
            $table->id();
            $table->integer('health')->default(20);
            $table->json('deck');          // Remaining cards in the dungeon
            $table->json('current_room');  // The 4 cards currently on the table
            $table->integer('weapon_val')->nullable();
            $table->integer('last_slain_val')->nullable(); // For weapon "durability" rules
            $table->boolean('can_flee')->default(true);
            $table->timestamps();
            $table->string('status')->default('active');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_sessions');
    }
};
