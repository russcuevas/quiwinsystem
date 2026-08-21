<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'referral_code',
        'referred_by',
        'points',
        'quest_rewarded',
        'daily_streak',
        'last_played_date',
        'weekly_quest_claims',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'points' => 'integer',
        'daily_streak' => 'integer',
        'weekly_quest_claims' => 'integer',
        'last_played_date' => 'date',
        'is_active' => 'boolean',
        'quest_rewarded' => 'boolean',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function approvedReferrals()
    {
        return $this->hasMany(User::class, 'referred_by')->where('status', 'approved');
    }

    public function pendingReferrals()
    {
        return $this->hasMany(User::class, 'referred_by')->where('status', 'pending');
    }

    public function gameSessions()
    {
        return $this->hasMany(GameSession::class);
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class)->latest();
    }

    public function sessionAnswers()
    {
        return $this->hasMany(SessionAnswer::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class)->latest();
    }

    public function mails()
    {
        return $this->hasMany(UserMail::class)->latest();
    }

    public function unreadMails()
    {
        return $this->hasMany(UserMail::class)->where('is_read', false);
    }

    /**
     * Check if user has played a match today.
     */
    public function hasPlayedToday(): bool
    {
        if (!$this->last_played_date) {
            return false;
        }

        $lastDate = is_string($this->last_played_date)
            ? substr($this->last_played_date, 0, 10)
            : $this->last_played_date->format('Y-m-d');

        return $lastDate === now()->format('Y-m-d');
    }

    /**
     * Get active consecutive streak (accounting for missed days).
     */
    public function getActiveDailyStreak(): int
    {
        if (!$this->last_played_date || !$this->daily_streak) {
            return 0;
        }

        $lastDate = is_string($this->last_played_date)
            ? substr($this->last_played_date, 0, 10)
            : $this->last_played_date->format('Y-m-d');

        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');

        if ($lastDate === $today || $lastDate === $yesterday) {
            return (int) $this->daily_streak;
        }

        // Missed at least one day
        return 0;
    }

    /**
     * Update daily play streak and grant weekly quest reward (300 PTS) upon reaching 7 days.
     */
    public function updateDailyStreak(): array
    {
        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');

        $lastDate = null;
        if ($this->last_played_date) {
            $lastDate = is_string($this->last_played_date)
                ? substr($this->last_played_date, 0, 10)
                : $this->last_played_date->format('Y-m-d');
        }

        $rewardEarned = false;
        $rewardAmount = (int) GameSetting::getVal('weekly_quest_reward', 300);

        if ($lastDate === $today) {
            // Already counted today
            return [
                'streak' => (int) $this->daily_streak,
                'reward_earned' => false,
                'reward_amount' => 0,
                'status' => 'already_played_today',
            ];
        }

        if ($lastDate === $yesterday) {
            $this->daily_streak = ((int) $this->daily_streak) + 1;
        } else {
            $this->daily_streak = 1;
        }

        $this->last_played_date = $today;

        // Check if 7-day streak milestone reached
        if ($this->daily_streak >= 7 && $this->daily_streak % 7 === 0) {
            $this->points += $rewardAmount;
            $this->weekly_quest_claims = ((int) $this->weekly_quest_claims) + 1;
            $rewardEarned = true;

            PointTransaction::create([
                'user_id' => $this->id,
                'game_session_id' => null,
                'type' => 'quest_reward',
                'amount' => $rewardAmount,
                'balance_after' => $this->points,
                'description' => "7-Day Daily Play Quest Completed (+{$rewardAmount} PTS Bonus)",
            ]);

            UserMail::create([
                'user_id' => $this->id,
                'title' => "🎉 Weekly Daily Play Quest Completed! (+{$rewardAmount} PTS)",
                'message' => "Congratulations! You played Quiwin every day for 7 consecutive days. You have earned +{$rewardAmount} bonus points! Keep your daily streak going for the next weekly reward!",
                'type' => 'quest',
                'is_read' => false,
            ]);
        }

        $this->save();

        return [
            'streak' => (int) $this->daily_streak,
            'reward_earned' => $rewardEarned,
            'reward_amount' => $rewardEarned ? $rewardAmount : 0,
            'status' => 'streak_updated',
        ];
    }
}
