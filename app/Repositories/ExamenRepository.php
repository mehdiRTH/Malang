<?php


namespace App\Repositories;

use App\Http\Resources\ExamenResource;
use App\Models\Exam;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class ExamenRepository{

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

    public function check_answers(Request $request)
    {

        $partInsight=[
            ['wrong_answer_number'=>count($request->quiz_answer[0]),'score'=>0,'wrong_answers'=>[]],
            ['wrong_answer_number'=>count($request->quiz_answer[1]),'score'=>0,'wrong_answers'=>[]],
            ['wrong_answer_number'=>count($request->quiz_answer[2]),'score'=>0,'wrong_answers'=>[]],
            ['wrong_answer_number'=>count($request->quiz_answer[3]),'score'=>0,'wrong_answers'=>[]]
        ];
        $exam=Exam::find($request->exam);

        if($request->isDone==false)
        {

            $exam->update([
                'attempts'=>$exam->attempts++
            ]);
            foreach($request->quiz_answer as $key=>$part_items)
            {
                foreach($part_items as $item)
                {
                    if(strlen($item['nl'])!=strlen($item['answer']))
                    {
                        array_push(
                                    $partInsight[$key]['wrong_answers'],
                                    [
                                        'answer'=>$item['answer'],
                                        'right_answer'=>$item['nl'],
                                        'translation_answer'=>$item['en']
                                    ]
                                );
                        $partInsight[$key]['wrong_answer_number']--;
                    }
                }
                $partInsight[$key]['score']=ceil(($partInsight[$key]['wrong_answer_number']/count($part_items))*100);
            }

            return back()->with([
                'examen_results'=>$partInsight
            ]);
        }else{
            $wrong_answers=[];
            foreach($request->quiz_answer as $key=>$part_items)
            {
                foreach($part_items as $item)
                {
                    if(strlen($item['nl'])!=strlen($item['answer']))
                    {
                        array_push(
                            $wrong_answers,
                                    [
                                        'answer'=>$item['answer'],
                                        'right_answer'=>$item['nl'],
                                        'translation_answer'=>$item['en']
                                    ]
                                );
                    }
                }
            }

            $exam->update([
                'score'=>$request->globalScore,
                'wrong_answers'=>$wrong_answers
            ]);

            return redirect()->route('examen.index');
        }

    }

}
