<?php

namespace Database\Seeders;

use App\Models\PointTransaction;
use App\Models\Question;
use App\Models\User;
use App\Services\OpenTdbService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin Account
        $admin = User::firstOrCreate(
            ['email' => 'admin@quiwin.com'],
            [
                'name' => 'Quiwin Admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'points' => 999999,
                'is_active' => true,
            ]
        );

        // 2. Create Demo Players
        $player1 = User::firstOrCreate(
            ['email' => 'player@quiwin.com'],
            [
                'name' => 'Alex Champion',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'points' => 450,
                'is_active' => true,
            ]
        );

        $player2 = User::firstOrCreate(
            ['email' => 'russel@quiwin.com'],
            [
                'name' => 'Russel Quickshot',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'points' => 620,
                'is_active' => true,
            ]
        );

        $player3 = User::firstOrCreate(
            ['email' => 'maria@quiwin.com'],
            [
                'name' => 'Maria Mastermind',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'points' => 280,
                'is_active' => true,
            ]
        );

        // Record initial points transactions for demo users
        foreach ([$player1, $player2, $player3] as $p) {
            PointTransaction::firstOrCreate(
                ['user_id' => $p->id, 'type' => 'register_bonus'],
                [
                    'amount' => 200,
                    'balance_after' => $p->points,
                    'description' => 'Welcome registration bonus (+200 Quiwin Points)',
                ]
            );
        }

        // 3. Seed Initial High-Quality Questions via OpenTdbService
        $openTdb = new OpenTdbService();
        $openTdb->fetchQuestionsForDifficulty('easy', 15);
        $openTdb->fetchQuestionsForDifficulty('medium', 15);
        $openTdb->fetchQuestionsForDifficulty('hard', 15);
    }
}
