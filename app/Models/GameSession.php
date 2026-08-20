<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_points',
        'entry_fee',
        'current_round',
        'current_question_index',
        'total_correct',
        'total_incorrect',
        'max_streak',
        'current_streak',
        'points_delta',
        'status',
        'questions_data',
        'answers_history',
    ];

    protected $casts = [
        'start_points' => 'integer',
        'entry_fee' => 'integer',
        'current_round' => 'integer',
        'current_question_index' => 'integer',
        'total_correct' => 'integer',
        'total_incorrect' => 'integer',
        'max_streak' => 'integer',
        'current_streak' => 'integer',
        'points_delta' => 'integer',
        'questions_data' => 'array',
        'answers_history' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(SessionAnswer::class);
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class);
    }
}
