<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Theme extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function vocabularies() : HasMany
    {
        return $this->hasMany(Vocabulary::class,'theme');
    }
}
