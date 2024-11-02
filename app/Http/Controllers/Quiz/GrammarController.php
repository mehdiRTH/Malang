<?php

namespace App\Http\Controllers\Quiz;

use App\Http\Controllers\Controller;
use App\Repositories\QuizRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GrammarController extends Controller
{
    public function __construct(public QuizRepository $quizRepository)
    {

    }
    public function index() : Response
    {
        return Inertia::render('Quiz/Index',$this->quizRepository->indexQuizData('Grammar'));
    }
}
