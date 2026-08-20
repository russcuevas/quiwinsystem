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
        Schema::create('session_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_session_id')->constrained('game_sessions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('question_id')->nullable()->constrained('questions')->onDelete('set null');
            $table->integer('question_index'); // 1 to 30
            $table->integer('round'); // 1, 2, 3
            $table->string('difficulty'); // easy, medium, hard
            $table->text('user_answer')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->integer('points_awarded')->default(0);
            $table->integer('streak_at_answer')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_answers');
    }
};
