<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exam extends Model
{
    use HasFactory;

    protected $guarded;

    protected $casts=[
        'wrong_answers'=>'array'
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
