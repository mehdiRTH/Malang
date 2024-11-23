<?php

namespace App\Repositories;

use App\Http\Resources\DashboardResource;
use App\Models\Scopes\QuizScope;
use App\Models\Vocabulary;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DashboardRepository{
    private $user;
    public function __construct()
    {
        $this->user=auth()->user();
    }

    public function DashboardParams() : array
    {

        $vocabulariesData=Vocabulary::query()
                ->select(
                'vocabularies.date',
                DB::raw('COUNT(vocabularies.id) as vocabularies_number'),
                DB::raw('AVG(quiz_performances.success_rate) as score')
                )
                ->where('vocabularies.user_id',$this->user->id)
                ->leftJoin('quiz_performances', 'vocabularies.date','=', 'quiz_performances.quiz_date')
                ->groupBy('vocabularies.date')
                ->withoutGlobalScope(QuizScope::class)
                ->paginate(7);

        return [
            'dashboardCards' => $this->dashboardCards(),
            'vocabulariesData'=>DashboardResource::collection($vocabulariesData),
            'userNotice' => $this->userNotice()
        ];
    }

    public function userNotice() : array
    {
        $today=new DateTime(now());

        // Get the last vocabulary and quiz upload dates in fewer queries
        $lastTimeUpload = $this->user->vocabularies()->latest('date')->value('date');
        $lastTimeQuiz = $this->user->quiz_performances()->latest('created_at')->value('created_at');

        // Calculate the time differences
        $lastTimeUploadDiff = $lastTimeUpload ? $today->diff(new DateTime($lastTimeUpload))->format('%a') : null;
        $lastTimeQuizDiff = $lastTimeQuiz ? $today->diff(new DateTime($lastTimeQuiz))->format('%a') : null;

        return [
            'lastTimeVocabulary' => $lastTimeUploadDiff,
            'lastTimeQuiz' => $lastTimeQuizDiff
        ];
    }

    public function dashboardCards () : array
    {
        //Count Added Grammar
        $grammarAdded=$this->user->vocabularies()
        ->whereHas('theme', fn($builder) => $builder->where('name', 'Grammar'))
        ->count();

        //Quiz score rate
        $quizScore=$this->user->quiz_performances()->avg('success_rate');

        //Exam score rate
        $examScore=$this->user->exams()->avg('score');

        return [
            'countAddedGrammar' => $grammarAdded,
            'examScoreRate' => $examScore !== null ?  ceil($examScore) : null,
            'quizScoreRate' => $quizScore !== null ? ceil($quizScore) : null
        ];
    }


}
