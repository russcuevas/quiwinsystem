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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('start_points')->default(0);
            $table->integer('entry_fee')->default(50);
            $table->integer('current_round')->default(1);
            $table->integer('current_question_index')->default(1);
            $table->integer('total_correct')->default(0);
            $table->integer('total_incorrect')->default(0);
            $table->integer('max_streak')->default(0);
            $table->integer('current_streak')->default(0);
            $table->integer('points_delta')->default(0);
            $table->string('status')->default('active'); // active, completed, abandoned, bankrupt_paused
            $table->json('questions_data')->nullable();
            $table->json('answers_history')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
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
