<?php

namespace App\Models;

use App\LanguageEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizPerformance extends Model
{
    use HasFactory;

    protected $guarded=[];

    protected $casts=[
        'wrong_answers'=>'array',
        'answer_language'=>LanguageEnum::class
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
