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
        $this_month_start_of_month=now()->startOfMonth()->format('Y-m-d');
        $this_month_end_of_month=now()->endOfMonth()->format('Y-m-d');
        $last_month_start_of_month=now()->startOfMonth()->subMonth()->format('Y-m-d');
        $last_month_end_of_month=now()->endOfMonth()->subMonth()->format('Y-m-d');
        $available_dates=null;

        if($type=='Grammar')
        {
            $available_dates=Vocabulary::whereHas('theme',function(Builder $builder){
                $builder->where('name','Grammar');
                })->selectRaw('DATE(created_at) as created_date')->distinct()->pluck('created_date');
        }else{
            $available_dates=$this->user->vocabularies()->whereHas('theme',function(Builder $builder){
                $builder->where('name','!=','Grammar');
                })->selectRaw('DATE(created_at) as created_date')->distinct()->pluck('created_date');
        }

        return [
            'quiz_vocabularies'=>QuizPerformanceResource::collection($this->user->quiz_performances()->orderBy('created_at','desc')->where('type',$type)->paginate(5)),
            'themes'=>ThemeResource::collection(Theme::where('name','!=','Grammar')->orderBy('created_at','desc')->get()),
            'this_month_success_rate'=>ceil($this->user->quiz_performances()->whereBetween('created_at',[$this_month_start_of_month, $this_month_end_of_month])->where('type',$type)->pluck('success_rate')->avg()),
            'this_month_uploaded_vocabularies'=>$this->user->vocabularies()->withoutGlobalScope(QuizScope::class)->whereBetween('created_at',[$this_month_start_of_month, $this_month_end_of_month])->where('vocabulary_grammar',$type=='Vocabulary' ? '=' : '!=',null)->count(),
            'last_month_success_rate'=>ceil($this->user->quiz_performances()->whereBetween('created_at',[$last_month_start_of_month, $last_month_end_of_month])->where('type',$type)->pluck('success_rate')->avg()),
            'last_month_uploaded_vocabularies'=>$this->user->vocabularies()->withoutGlobalScope(QuizScope::class)->whereBetween('created_at',[$last_month_start_of_month, $last_month_end_of_month])->where('vocabulary_grammar',$type=='Vocabulary' ? '=' : '!=',null)->count(),
            'type'=>$type,
            'available_dates'=>$available_dates
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
        $analyzedAnswers=$this->analyzeVocabularies($request->quiz_answers,$request->answers_lang);

        return $this->createQuiz($request,$analyzedAnswers['score'],$analyzedAnswers['wrongAnswers'],'Vocabulary');

    }

    public function analyzeVocabularies($answers,$lang='nl') : array
    {
        $count_quiz_answers=count($answers);
        $count_wrong_answers=$count_quiz_answers;
        $wrong_answers=[];

        foreach($answers as $answer)
        {
            if(strtolower($answer[$lang])!=strtolower($answer['answer']))
            {
                array_push(
                    $wrong_answers,
                    [
                        'answer'=>$answer['answer'],
                        'right_answer'=>$answer[$lang],
                        'translation_answer'=>$answer[$lang=='nl' ? 'en' : 'nl']
                    ]);

                --$count_wrong_answers;
            }
        }

        $score=ceil(($count_wrong_answers/$count_quiz_answers)*100);

        return [
            'wrongAnswers'=>$wrong_answers,
            'score'=>$score
        ];
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
