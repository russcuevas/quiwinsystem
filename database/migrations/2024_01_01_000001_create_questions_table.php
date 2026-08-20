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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('question_hash', 64)->unique();
            $table->string('category');
            $table->enum('difficulty', ['easy', 'medium', 'hard']);
            $table->string('type')->default('multiple');
            $table->text('question_text');
            $table->text('correct_answer');
            $table->json('incorrect_answers');
            $table->timestamps();

            $table->index(['difficulty', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
