<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExamenQuizResource;
use App\Models\Exam;
use App\Repositories\ExamenRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;


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
        $exam=$this->user->exam()
                    ->whereDate('start_date','>=',$start_date)
                    ->whereDate('end_date','<=',$end_date)
                    ->firstOrCreate([
                        'start_date'=>$start_date,
                        'end_date'=>$end_date,
                        'score'=>0
                    ]);


        $vocb= $this->user->vocabularies()
                          ->whereDate('date','>=',$start_date)
                          ->whereDate('date','<=',$end_date)->inRandomOrder()->get()->toArray();

        $perChunk = ceil(count($vocb) / 4);

        return Inertia::render($this->prefix.'Types/Vocabularies',[
            'dividedVocabularies'=>new ExamenQuizResource(array_chunk($vocb,$perChunk)),
            'exam'=>$exam
        ]);

    }

    public function check_answers(Request $request)
    {
        $this->examenRepository->check_answers($request);

    }
}
