<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class QuizScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->whereJsonContainsKey('translations->nl')->whereJsonContainsKey('translations->en')->whereNotNull(['translations->en','translations->nl'])->where('user_id',auth()->user()->id);
    }
}
