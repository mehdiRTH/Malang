<?php

namespace App\Repositories;

use App\Http\Resources\DashboardResource;
use App\Models\Scopes\QuizScope;
use App\Models\Vocabulary;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DashboardRepository{
    private $user;
    public function __construct()
    {
        $this->user=auth()->user();
    }

    public function DashboardParams($searchDate = 'This Month') : array
    {
        $dateRange=[];
        $format='Y-m-d';
        if($searchDate == 'This Month')
        {
            $dateRange=[Carbon::now()->startOfMonth()->format($format), Carbon::now()->endOfMonth()->format($format)];
        }else{
            $dateRange=[Carbon::now()->subMonth()->startOfMonth()->format($format), Carbon::now()->subMonth()->endOfMonth()->format($format)];
        }

        $vocabulariesData=Vocabulary::query()
                ->select(
                'vocabularies.date',
                DB::raw('COUNT(vocabularies.id) as vocabularies_number'),
                DB::raw('AVG(quiz_performances.success_rate) as score')
                )
                ->where('vocabularies.user_id',$this->user->id)
                ->whereBetween('vocabularies.created_at',$dateRange)
                ->leftJoin('quiz_performances', 'vocabularies.date','=', 'quiz_performances.quiz_date')
                ->groupBy('vocabularies.date')
                ->withoutGlobalScope(QuizScope::class)
                ->orderBy('vocabularies.date','desc')
                ->paginate(7);

        return [
            'dashboardCards' => $this->dashboardCards($dateRange),
            'vocabulariesData'=>DashboardResource::collection($vocabulariesData),
            'userNotice' => $this->userNotice($dateRange),
            'searchDate'=>$searchDate
        ];
    }

    public function userNotice($dateRange) : array
    {
        $today=new DateTime(now());

        // Get the last vocabulary and quiz upload dates
        $lastTimeUpload = $this->user->vocabularies()
                                     ->latest('date')
                                     ->value('date');

        // Get the last time the user exercise
        $lastTimeQuiz = $this->user->quiz_performances()
                                   ->latest('created_at')
                                   ->value('created_at');

        // Get the last time the user passed an exam
        $lastTimeExam=$this->user->exams()
                                 ->latest('created_at')
                                 ->value('created_at');

        // Calculate the time differences
        $lastTimeUploadDiff = $lastTimeUpload ? $today->diff(new DateTime($lastTimeUpload))->format('%a') : null;
        $lastTimeQuizDiff = $lastTimeQuiz ? $today->diff(new DateTime($lastTimeQuiz))->format('%a') : null;
        $lastTimeExamDiff = $lastTimeQuiz ? $today->diff(new DateTime($lastTimeExam))->format('%a') : null;

        return [
            'lastTimeVocabulary' => $lastTimeUploadDiff,
            'lastTimeQuiz' => $lastTimeQuizDiff,
            'lastTimeExam' => $lastTimeExamDiff
        ];
    }

    public function dashboardCards ($dateRange) : array
    {
        //Count Added Grammar
        $grammarAdded=$this->user->vocabularies()
                                 ->whereBetween('created_at',$dateRange)
                                ->whereHas('theme', fn($builder) => $builder->where('name', 'Grammar'))
                                ->count();

        //Quiz score rate
        $quizScore=$this->user->quiz_performances()
                              ->whereBetween('created_at',$dateRange)
                              ->avg('success_rate');

        //Exam score rate
        $examScore=$this->user->exams()
                              ->whereBetween('created_at',$dateRange)
                              ->avg('score');

        return [
            'countAddedGrammar' => $grammarAdded,
            'examScoreRate' => $examScore !== null ?  ceil($examScore) : 0,
            'quizScoreRate' => $quizScore !== null ? ceil($quizScore) : 0
        ];
    }


}
