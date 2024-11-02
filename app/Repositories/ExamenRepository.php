<?php


namespace App\Repositories;

use App\Http\Resources\ExamenQuizResource;
use App\Http\Resources\ExamenResource;
use App\Models\Exam;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamenRepository{

    private $user;
    public function __construct(){
        $this->user=Auth::user();
    }


    public function indexData(): array
    {
        return [
            'examens'=>ExamenResource::collection($this->getMonthWeeks())
        ];
    }

    public function getMonthWeeks(): array
    {
        $period = CarbonPeriod::create(now()->addMonth(-1)->startOfMonth()->format('Y-m-d'), '1 week', now()->endOfMonth()->format('Y-m-d'));
            $weekList = [];
            $dateFormat = 'Y-m-d';
            foreach ($period as $key=>$date) {
                if((count($period)-1)==$key)
                {
                    $weekList[] = [
                        'weekNumber' => ++$key,
                        'startDate'  => $date->format($dateFormat),
                        'endDate'    => $date->endOfMonth()->format($dateFormat),
                    ];
                }else{
                    $weekList[] = [
                        'weekNumber' => ++$key,
                        'startDate'  => $date->format($dateFormat),
                        'endDate'    => $date->addDays(6)->format($dateFormat),
                    ];
                }

            }

            return $weekList;
    }

    public function generateExamProps($start_date,$end_date): array
    {
        $exam=$this->user->exams()
                    ->whereDate('start_date','>=',$start_date)
                    ->whereDate('end_date','<=',$end_date)
                    ->firstOrCreate(
                        [
                            'start_date' => $start_date,
                            'end_date' => $end_date,
                        ],
                        [
                            'score' => 0
                        ]
                    );

        $vocabularies= $this->user->vocabularies()
                          ->whereBetween('date',[$start_date, $end_date])
                          ->inRandomOrder()
                          ->get()
                          ->toArray();

        $perChunk = ceil(count($vocabularies) / 4);

        return [
            'dividedVocabularies'=>new ExamenQuizResource(array_chunk($vocabularies,$perChunk)),
            'exam'=>$exam
        ];
    }

    public function checkAnswers(Request $request) : RedirectResponse
    {
        $exam=Exam::find($request->exam);

        if($request->isDone==false)
        {
            $exam->increment('attempts');

            return back()->with([
                'examen_results'=>$this->analyzeExam($request->quiz_answer,false)
            ]);
        }else{

            $exam->update([
                'score'=>$request->globalScore,
                'wrong_answers'=>$this->analyzeExam($request->quiz_answer,true)
            ]);

            return redirect()->route('exams.index');
        }

    }

    public function analyzeExam($examAnswers,$storeExamData) : array
    {
        $examResults=[];
        foreach($examAnswers as $key=>$quizPart)
        {
            //Calling the function that return the wrong answer and score of the quiz
            $analyzedAnswers=(new QuizRepository())->analyzeVocabularies($quizPart);

            //Check if it for showcase exam results or only the wrong answers to save them into exam infos
            if(!$storeExamData)
            {
                $examResults[$key]['wrongAnswers']=$analyzedAnswers['wrongAnswers'];
                $examResults[$key]['score']=$analyzedAnswers['score'];
            }else{
                $examResults[$key]=$analyzedAnswers['wrongAnswers'];
            }

        }

        return $examResults;
    }

}
