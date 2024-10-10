<?php


namespace App\Repositories;

use App\Http\Resources\ExamenQuizResource;
use App\Http\Resources\ExamenResource;
use App\Models\Exam;
use Carbon\CarbonPeriod;
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

    public function getMonthWeeks()
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

    public function generateExamData($start_date,$end_date)
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
                          ->whereDate('date','>=',$start_date)
                          ->whereDate('date','<=',$end_date)->inRandomOrder()->get()->toArray();

        $perChunk = ceil(count($vocabularies) / 4);

        return [
            'dividedVocabularies'=>new ExamenQuizResource(array_chunk($vocabularies,$perChunk)),
            'exam'=>$exam
        ];
    }

    public function check_answers(Request $request)
    {

        $examData=[
            ['wrong_answer_number'=>count($request->quiz_answer[0]),'score'=>0,'wrong_answers'=>[]],
            ['wrong_answer_number'=>count($request->quiz_answer[1]),'score'=>0,'wrong_answers'=>[]],
            ['wrong_answer_number'=>count($request->quiz_answer[2]),'score'=>0,'wrong_answers'=>[]],
            ['wrong_answer_number'=>count($request->quiz_answer[3]),'score'=>0,'wrong_answers'=>[]]
        ];
        $exam=Exam::find($request->exam);

        if($request->isDone==false)
        {
            $exam->increment('attempts');
            foreach($request->quiz_answer as $key=>$quizPart)
            {
                //Calling the function that return the wrong answer and score of the quiz
                $analyzedAnswers=(new QuizRepository())->analyzeVocabularies($quizPart);

                $examData[$key]['wrongAnswers']=$analyzedAnswers['wrongAnswers'];
                $examData[$key]['score']=$analyzedAnswers['score'];

            }

            return back()->with([
                'examen_results'=>$examData
            ]);
        }else{
            $wrongAnswers=[];

            foreach($request->quiz_answer as $key=>$quizPart)
            {
                //Calling the function that return the wrong answer and score of the quiz
                $analyzedAnswers=(new QuizRepository())->analyzeVocabularies($quizPart);

                //Push wrong Answers to this array to save after
                array_push($wrongAnswers,$analyzedAnswers['wrongAnswers']);
            }

            $exam->update([
                'score'=>$request->globalScore,
                'wrong_answers'=>$wrongAnswers
            ]);

            return redirect()->route('examen.index');
        }

    }

}
