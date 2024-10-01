<?php

namespace App\Models;

use App\Models\Scopes\QuizScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy([QuizScope::class])]


class Vocabulary extends Model
{
    use HasFactory;

    protected $guarded=[];

    protected $casts=[
        'translations'=>'array',
        'vocabulary_grammar'=>'array'
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
