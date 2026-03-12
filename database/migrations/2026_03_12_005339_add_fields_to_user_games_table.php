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
        Schema::table('user_games', function (Blueprint $table) {
            $table->string('title'); 
            $table->text('description');  
            $table->string('cover');
            $table->string('released_date');
            $table->integer('metacritic_score');
            $table->string('developers');
            $table->string('publisher');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_games', function (Blueprint $table) {
            $table->dropColumn(['title', 'cover', 'released_date', 'metacritic_score']);
        });
    }
};
