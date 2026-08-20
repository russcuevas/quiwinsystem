<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_hash',
        'category',
        'difficulty',
        'type',
        'question_text',
        'correct_answer',
        'incorrect_answers',
    ];

    protected $casts = [
        'incorrect_answers' => 'array',
    ];

    public function sessionAnswers()
    {
        return $this->hasMany(SessionAnswer::class);
    }
}
