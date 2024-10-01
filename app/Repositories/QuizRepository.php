<?php


namespace App\Repositories;

use App\Http\Requests\QuizRequest;
use App\Http\Resources\GrammarResource;
use App\Http\Resources\QuizPerformanceResource;
use App\Http\Resources\QuizResource;
use App\Http\Resources\ThemeResource;
use App\Models\Scopes\QuizScope;
use App\Models\Theme;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QuizRepository{

    private $user;
    public function __construct(){
        $this->user=auth()->user();
    }
    public function indexData() : array
    {
        $this_month_start_of_month=now()->startOfMonth()->format('Y-m-d');
        $this_month_end_of_month=now()->endOfMonth()->format('Y-m-d');
        $last_month_start_of_month=now()->startOfMonth()->subMonth()->format('Y-m-d');
        $last_month_end_of_month=now()->endOfMonth()->subMonth()->format('Y-m-d');

        return [
            'quiz_vocabularies'=>QuizPerformanceResource::collection($this->user->quiz_performances()->orderBy('created_at','desc')->paginate(5)),
            'themes'=>ThemeResource::collection(Theme::all()),
            'this_month_success_rate'=>ceil($this->user->quiz_performances()->whereDate('created_at','>=',$this_month_start_of_month)->whereDate('created_at','<=',$this_month_end_of_month)->pluck('success_rate')->avg()),
            'this_month_uploaded_vocabularies'=>$this->user->vocabularies()->withoutGlobalScope(QuizScope::class)->whereDate('created_at','>=',$this_month_start_of_month)->whereDate('created_at','<=',$this_month_end_of_month)->count(),
            'last_month_success_rate'=>ceil($this->user->quiz_performances()->whereDate('created_at','>=',$last_month_start_of_month)->whereDate('created_at','<=',$last_month_end_of_month)->pluck('success_rate')->avg()),
            'last_month_uploaded_vocabularies'=>$this->user->vocabularies()->withoutGlobalScope(QuizScope::class)->whereDate('created_at','>=',$last_month_start_of_month)->whereDate('created_at','<=',$last_month_end_of_month)->count(),
        ];
    }

    public function generateQuiz(QuizRequest $request)
    {
        $isGrammarQuiz=$request->theme ? $request->theme['name']=='Grammar' : false && $request->isThemeGrammar=='true';
        if($request->type_search=='Date'){
            $user_vocabularies=$this->user->vocabularies()->where('date',$request->quiz_date);
        }else{
            if($isGrammarQuiz){
                $user_vocabularies=$this->user->vocabularies()->where('theme',$request->theme['id'])->where('date',$request->quiz_date);
            }else{
                $user_vocabularies=$this->user->vocabularies()->where('theme',$request->theme['id']);
            }

            $user_vocabularies=$this->user->vocabularies()->where('theme',$request->theme['id']);
        }

        $count_user_vocabularies=count($user_vocabularies->get());

        if($count_user_vocabularies < $request->vocabulary_number)
        {
            return redirect()->back()->withErrors([
                'count_vocabulary_number'=>$count_user_vocabularies
            ]);
        }else{
            if($isGrammarQuiz)
            {
                return Inertia::render('Quiz/Types/Grammar',[
                    'quiz_vocabularies'=>GrammarResource::collection($user_vocabularies->inRandomOrder()->limit($request->vocabulary_number)->get())
                ]);
            }else {
                return Inertia::render('Quiz/Types/Vocabulary',[
                    'quiz_vocabularies'=>QuizResource::collection($user_vocabularies->inRandomOrder()->limit($request->vocabulary_number)->get())
                ]);
            }

        }
    }

    public function createQuiz(Request $request,$percentage,$wrong_answers,$type):RedirectResponse
    {
        // QuizPerformance
        if($request->isDone){
            $this->user->quiz_performances()->create([
                'success_rate'=>$percentage,
                'answer_language'=>$request->answers_lang,
                'vocabulary_number'=>$request->vocabulary_number,
                'quiz_date'=>$request->quiz_date,
                'wrong_answers'=>$wrong_answers ?? null
            ]);

            return redirect()->route('quiz.index');
        }

        return back()->with([
            'success_percentage' => ceil($percentage),
            'wrong_answers' => $wrong_answers,
            'type_quiz'=>$type
        ]);
    }

    public function checkVocabularies(Request $request): RedirectResponse
    {
        $count_quiz_answers=count($request->quiz_answers);
        $checked_answers=$count_quiz_answers;
        $wrong_answers=[];

        foreach($request->quiz_answers as $answer)
        {
            if(strtolower($answer[$request->answers_lang])!=strtolower($answer['answer']))
            {
                array_push(
                    $wrong_answers,
                    [
                        'answer'=>$answer['answer'],
                        'right_answer'=>$answer[$request->answers_lang],
                        'translation_answer'=>$answer[$request->answers_lang=='nl' ? 'en' : 'nl']
                    ]);

                --$checked_answers;
            }
        }

        $percentage=($checked_answers/$count_quiz_answers)*100;

        return $this->createQuiz($request,$percentage,$wrong_answers,'Vocabulary');

    }

    public function checkGrammar(Request $request) : RedirectResponse
    {
        $count_grammar_answer=count($request->grammar_answer);
        $checked_answers=$count_grammar_answer;
        $wrong_answers=[];

        foreach($request->grammar_answer as $answer)
        {
            $nlAnswer=$answer['nl'];
            $vocabularyName=$answer['name'];
            $isNotCorrect=false;
            $wrong_answer=[
                'name'=>$vocabularyName,
                'grammar'=>[
                    'presens'=>null,
                    'imperfectum'=>null,
                    'perfectum'=>null
                ]
            ];
            $right_answer=[
                'name'=>$vocabularyName,
                'grammar'=>[
                    'presens'=>$nlAnswer['presens'],
                    'imperfectum'=>$nlAnswer['imperfectum'],
                    'perfectum'=>$nlAnswer['perfectum']
                ]
            ];

            if(strtolower($nlAnswer['presens'])!=strtolower($answer['answer']['presens']))
            {
                $checked_answers-=0.33;
                $isNotCorrect=true;
            }

            if(strtolower($nlAnswer['imperfectum'])!=strtolower($answer['answer']['imperfectum']))
            {
                $checked_answers-=0.33;
                $isNotCorrect=true;
            }

            if(strtolower($nlAnswer['perfectum'])!=strtolower($answer['answer']['perfectum']))
            {
                $checked_answers-=0.33;
                $isNotCorrect=true;
            }

            if($isNotCorrect==true)
            {
                $wrong_answer['grammar']['presens']=$answer['answer']['presens'];
                $wrong_answer['grammar']['imperfectum']=$answer['answer']['imperfectum'];
                $wrong_answer['grammar']['perfectum']=$answer['answer']['perfectum'];

                array_push(
                $wrong_answers,
                [
                    'answer'=>$wrong_answer,
                    'right_answer'=>$right_answer
                ]);
            }

        }

        $percentage=($checked_answers/$count_grammar_answer)*100;

        return $this->createQuiz($request,$percentage,$wrong_answers,'Grammar');
    }
}
