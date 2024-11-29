<?php

namespace App\Services;

class AnalyzeVocabulariesService{

    public function analyzedInputtedVocabularies($answers,$lang='nl') : array
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
}
