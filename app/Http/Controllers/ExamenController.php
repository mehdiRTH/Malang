<?php

namespace App\Http\Controllers;


use App\Repositories\ExamenRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;


class ExamenController extends Controller
{
    private $prefix;
    private $user;
    public function __construct(public ExamenRepository $examenRepository){
        $this->prefix='Examens/';
        $this->user=auth()->user();
    }

    public function index()
    {
        return Inertia::render($this->prefix.'Index',$this->examenRepository->indexData());
    }

    public function generateExam($start_date,$end_date)
    {
        return Inertia::render(
            $this->prefix.'Types/Vocabularies',
            $this->examenRepository->generateExamProps($start_date,$end_date));

    }

    public function checkAnswers(Request $request)
    {
       return $this->examenRepository->check_answers($request);

    }
}
