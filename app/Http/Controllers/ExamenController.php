<?php

namespace App\Http\Controllers;

use App\Mail\VocabularyMail;
use App\Models\Vocabulary;
use App\Repositories\ExamenRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;


class ExamenController extends Controller
{
    private $prefix;
    public function __construct(public ExamenRepository $examenRepository){
        $this->prefix='Examens/';
    }

    public function index()
    {
        // Mail::to(auth()->user()->email)->send(new VocabularyMail([['en'=>'english test','ar'=>'arabic test','nl'=>'Nederland test']],[['name'=>'Name test','presens'=>'Presens test','imperfectum'=>'imperfectum test','perfectum'=>'perfectum test']]));
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
       return $this->examenRepository->checkAnswers($request);

    }
}
