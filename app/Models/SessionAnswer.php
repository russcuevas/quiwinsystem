<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_session_id',
        'user_id',
        'question_id',
        'question_index',
        'round',
        'difficulty',
        'user_answer',
        'is_correct',
        'points_awarded',
        'streak_at_answer',
    ];

    protected $casts = [
        'question_index' => 'integer',
        'round' => 'integer',
        'is_correct' => 'boolean',
        'points_awarded' => 'integer',
        'streak_at_answer' => 'integer',
    ];

    public function gameSession()
    {
        return $this->belongsTo(GameSession::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
