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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('daily_streak')->default(0)->after('quest_rewarded');
            $table->date('last_played_date')->nullable()->after('daily_streak');
            $table->unsignedInteger('weekly_quest_claims')->default(0)->after('last_played_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['daily_streak', 'last_played_date', 'weekly_quest_claims']);
        });
    }
};
