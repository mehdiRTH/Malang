<?php


namespace App\Repositories;

use App\Http\Requests\QuizRequest;
use App\Http\Resources\GrammarResource;
use App\Http\Resources\QuizPerformanceResource;
use App\Http\Resources\QuizResource;
use App\Http\Resources\ThemeResource;
use App\Models\Scopes\QuizScope;
use App\Models\Theme;
use App\Models\Vocabulary;
use App\Services\Analytics;
use App\Services\AnalyzeVocabulariesService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuizRepository{

    private $user;
    public function __construct(){
        $this->user=auth()->user();
    }
    public function indexQuizData($type='Vocabulary') : array
    {
        $currentMonthRange=[
            now()->startOfMonth(),
            now()->endOfMonth()
        ];

        $lastMonthRange=[
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth()
        ];

        $isVocabulary= $type == 'Vocabulary';
        $grammarCondition= $isVocabulary ? '=' : '!=';
        $analyticsService = new Analytics();

        // Retrieve available dates based on type
        $availableDatesQuery= $isVocabulary
        ? $this->user->vocabularies()->whereHas('theme',fn(Builder $builder) => $builder->where('name','!=','Grammar'))
        : Vocabulary::whereHas('theme',fn(Builder $builder) => $builder->where('name','Grammar'));

        $availableDates = $availableDatesQuery
        ->selectRaw('DATE(created_at) as created_date')
        ->distinct()
        ->pluck('created_date');

        return [
            'quizVocabularies' => QuizPerformanceResource::collection(
                $this->user->quiz_performances()
                    ->where('type', $type)
                    ->orderByDesc('created_at')
                    ->paginate(5)
            ),
            'themes' => ThemeResource::collection(
                Theme::where('name', '!=', 'Grammar')
                    ->orderByDesc('created_at')
                    ->get()
            ),
            'thisMonthScoreRate' => $analyticsService->calculateScoreRate($this->user,$currentMonthRange, $type),
            'thisMonthUploadedVocabularies' => $analyticsService->countUploadedVocabularies($this->user, $currentMonthRange, $grammarCondition),
            'lastMonthScoreRate' => $analyticsService->calculateScoreRate($this->user, $lastMonthRange, $type),
            'lastMonthUploadedVocabularies' => $analyticsService->countUploadedVocabularies($this->user, $lastMonthRange, $grammarCondition),
            'type' => $type,
            'availableDates' => $availableDates
        ];
    }

    public function generateQuiz(QuizRequest $request)
    {
        $isGrammarQuiz=$request->isThemeGrammar=='false' ? false : true;

        if($request->type_search=='Date'){
            //Retrieve the requested vocabularies Directly With entered Date
            $requestedVocabularies=$this->user->vocabularies()->where('date',$request->quiz_date);

        }else{

            if($isGrammarQuiz){
                //Retrieve Grammar of Vocabularies
                $requestedVocabularies=$this->user->vocabularies()->whereHas('theme',function (Builder $query){
                    $query->where('name','Grammar');
                })->where('date',$request->quiz_date);
            }else{
                // Retrieve Vocabularies of a specific theme
                $requestedVocabularies=$this->user->vocabularies()->where('theme',$request->theme['id']);
            }
        }

        $countRequestVocabularies=count($requestedVocabularies->get());
        if($countRequestVocabularies < $request->vocabulary_number)
        {
            //If the requested number of vocabulary is less than the number of available vocabularies, an error is displayed in the user interface.
            return redirect()->back()->withErrors([
                'count_vocabulary_number'=>$countRequestVocabularies
            ]);

        }else{
            $examVocabularies=$requestedVocabularies->inRandomOrder()->limit($request->vocabulary_number)->get();
            //Otherwise, the quiz will be generated based on the type whether it’s Grammar or Vocabulary
            return Inertia::render($isGrammarQuiz ? 'Quiz/Types/Grammar' : 'Quiz/Types/Vocabulary',[
                'quiz_vocabularies'=>$isGrammarQuiz ? GrammarResource::collection($examVocabularies) : QuizResource::collection($examVocabularies)
            ]);

        }
    }

    public function createQuiz(Request $request,$percentage,$wrong_answers,$type) : RedirectResponse
    {
        // QuizPerformance
        if($request->isDone){
            $this->user->quiz_performances()->create([
                'success_rate'=>$percentage,
                'answer_language'=>$request->answers_lang ?? 'nl',
                'vocabulary_number'=>$request->vocabulary_number,
                'quiz_date'=>$request->quiz_date,
                'wrong_answers'=>$wrong_answers ?? null,
                'type'=>$request->type
            ]);

            return redirect()->route('quiz.index');
        }

        return back()->with([
            'success_percentage' => ceil($percentage),
            'wrong_answers' => $wrong_answers,
            'type_quiz'=>$request->type
        ]);
    }

    public function checkVocabularies(Request $request): RedirectResponse
    {
        $analyzedAnswers=(new AnalyzeVocabulariesService())->analyzedInputtedVocabularies($request->quiz_answers,$request->answers_lang);

        return $this->createQuiz($request,$analyzedAnswers['score'],$analyzedAnswers['wrongAnswers'],'Vocabulary');

    }

    public function checkGrammar(Request $request) : RedirectResponse
    {
        $count_grammar_answer=count($request->grammar_answer);
        $score=$count_grammar_answer;
        $wrongAnswers=[];
        $types=['presens','imperfectum','perfectum'];

        foreach($request->grammar_answer as $key=>$answer)
        {
            $isNotCorrect=false;

            //Check the wrong answer of each type
            foreach($types as $type)
            {
                $typeCheck=$this->checkTypeGrammar($answer['nl'],$answer,$type,$score);
                $score=$typeCheck['score'];
                $isNotCorrect= $isNotCorrect ? true :  $typeCheck['isNotCorrect'];
            }

            // Check if the function return null if not than push it to wrongAnswers array
            $analyzedWrongAnswers=$this->analyzeWrongAnswers($types,$answer,$isNotCorrect);
            if($analyzedWrongAnswers!=null){
                $wrongAnswers[$key]=$analyzedWrongAnswers;
            }

        }

        //Calculate The percentage of the score
        $percentage=($score/$count_grammar_answer)*100;

        return $this->createQuiz($request,$percentage,$wrongAnswers,'Grammar');
    }

    public function checkTypeGrammar($answer, $insertedAnswer, $type, $score) : array
    {
        $isNotCorrect=false;
        if(strtolower($answer[$type])!=strtolower($insertedAnswer['answer'][$type]))
        {
            $score-=0.33;
            $isNotCorrect=true;
        }

        return [
            'isNotCorrect'=>$isNotCorrect,
            'score'=>$score
        ];
    }

    public function analyzeWrongAnswers($types, $answer,$isNotCorrect) : mixed
    {
        $nlAnswer=$answer['nl'];
        //create a template to initialize the answers
        $answerTemplate=[
            'name'=>$answer['name'],
            'grammar'=>array_fill_keys($types, null)
        ];

        $wrongAnswer=$answerTemplate;

        $rightAnswer=$answerTemplate;

        $rightAnswer['grammar']=[
            'presens'=>$nlAnswer['presens'],
            'imperfectum'=>$nlAnswer['imperfectum'],
            'perfectum'=>$nlAnswer['perfectum']
        ];

        if($isNotCorrect)
        {
            //Initialize the wrong answer
            foreach($types as $type)
            {
                $wrongAnswer['grammar'][$type]=$answer['answer'][$type];
            }


            return [
                'answer'=>$wrongAnswer,
                'right_answer'=>$rightAnswer
            ];
        }

        return null;
    }
}
