<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'description',
    ];

    /**
     * Default game pointing and economy values
     */
    public static function defaultSettings(): array
    {
        return [
            // Round 1 (Easy)
            'easy_correct_points' => 2,
            'easy_wrong_penalty' => 3,
            'easy_timer_seconds' => 5,

            // Round 2 (Normal)
            'medium_correct_points' => 3,
            'medium_wrong_penalty' => 5,
            'medium_timer_seconds' => 5,

            // Round 3 (Hard)
            'hard_correct_points' => 5,
            'hard_wrong_penalty' => 10,
            'hard_timer_seconds' => 5,

            // Economy, Streaks & Quests
            'entry_fee' => 50,
            'welcome_bonus' => 200,
            'streak_3_bonus' => 1,
            'streak_5_bonus' => 2,
            'streak_8_bonus' => 5,
            'weekly_quest_reward' => 300,
            'referral_quest_reward' => 1000,
        ];
    }

    /**
     * Get all active settings merged with defaults
     */
    public static function getSettings(): array
    {
        $defaults = self::defaultSettings();
        try {
            $dbSettings = self::pluck('value', 'key')->toArray();
            foreach ($defaults as $key => $defaultVal) {
                if (isset($dbSettings[$key])) {
                    $defaults[$key] = is_numeric($defaultVal) ? (int)$dbSettings[$key] : $dbSettings[$key];
                }
            }
        } catch (\Exception $e) {}

        return $defaults;
    }

    /**
     * Get a specific setting value with fallback
     */
    public static function getVal(string $key, $fallback = null)
    {
        $settings = self::getSettings();
        return $settings[$key] ?? $fallback;
    }

    /**
     * Save/Update a setting
     */
    public static function setVal(string $key, $value, string $group = 'scoring', ?string $description = null)
    {
        return self::updateOrCreate(
            ['key' => $key],
            [
                'value' => (string)$value,
                'group' => $group,
                'description' => $description,
            ]
        );
    }
}
