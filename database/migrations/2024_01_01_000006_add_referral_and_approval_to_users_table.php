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
            $table->string('referral_code', 30)->nullable()->unique()->after('email');
            $table->unsignedBigInteger('referred_by')->nullable()->after('referral_code');
            $table->string('status', 20)->default('pending')->after('role'); // pending, approved, rejected
            $table->boolean('quest_rewarded')->default(false)->after('points');

            $table->foreign('referred_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropColumn(['referral_code', 'referred_by', 'status', 'quest_rewarded']);
        });
    }
};
