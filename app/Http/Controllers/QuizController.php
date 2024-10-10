<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuizRequest;
use App\Repositories\QuizRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QuizController extends Controller
{
    public function __construct(public QuizRepository $quizRepository)
    {

    }
    public function index()
    {
        return Inertia::render('Quiz/Index',$this->quizRepository->indexData());
    }

    public function generate(QuizRequest $request): RedirectResponse|Response
    {
        return $this->quizRepository->generateQuiz($request);
    }

    public function checkAnswers(Request $request)
    {
        if($request->type=='Vocabulary')
        {
            return $this->quizRepository->checkVocabularies($request);
        }else{
            return $this->quizRepository->checkGrammar($request);
        }
    }
}
