<?php

namespace App\Services;

use App\Models\Scopes\QuizScope;

class Analytics{


    public function calculateScoreRate($user,array $dateRange, string $type): int
    {
        return ceil(
            $user->quiz_performances()
                            ->whereBetween('created_at',$dateRange)
                            ->where('type',$type)
                            ->pluck('success_rate')
                            ->avg()
        );
    }

    public function countUploadedVocabularies($user,array $dateRange, string $grammarCondition): int
    {
        return $user->vocabularies()
            ->withoutGlobalScope(QuizScope::class)
            ->whereBetween('created_at', $dateRange)
            ->where('vocabulary_grammar', $grammarCondition, null)
            ->count();
    }
}
