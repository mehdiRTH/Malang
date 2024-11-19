<?php

namespace App\Repositories;

use App\Http\Resources\DashboardResource;
use App\Models\Scopes\QuizScope;
use App\Models\Vocabulary;
use DateTime;
use Illuminate\Support\Facades\DB;

class DashboardRepository{

    public function DashboardParams() : array{
        $today=new DateTime(now());
        $user=auth()->user();
        $vocabulariesData=Vocabulary::query()
            ->select(
                'vocabularies.date',
                DB::raw('COUNT(vocabularies.id) as vocabularies_number'),
                DB::raw('AVG(quiz_performances.success_rate) as score')
                )
                ->where('vocabularies.user_id',auth()->user()->id)
                ->leftJoin('quiz_performances', 'vocabularies.date','=', 'quiz_performances.quiz_date')
                ->groupBy('vocabularies.date')
                ->withoutGlobalScope(QuizScope::class)
                ->paginate(7);

        //check last time user upload vocabularies
        $uploaded=$user->vocabularies()->select('date')->get()->last();
        $lastTimeUpload=new DateTime($uploaded->date);

        // Check last time user upload vocabularies
        $quized=$user->quiz_performances()->select('created_at')->get()->last();
        $lastTimeQuiz= new DateTime($quized->created_at);

        return [
            'vocabulariesData'=>DashboardResource::collection($vocabulariesData),
            'lastTimeVocabulary'=>$today->diff(targetObject: $lastTimeUpload)->format('%a'),
            'lastTimeQuiz'=>$today->diff($lastTimeQuiz)->format('%a')
        ];
    }
}
