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
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'points' => 'integer',
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
}
