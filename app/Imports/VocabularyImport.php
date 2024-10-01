<?php

namespace App\Imports;

use App\Models\Theme;
use App\Models\User;
use App\Models\Vocabulary;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;

class VocabularyImport implements ToCollection
{

    public function __construct(public User $user){}

    public function collection(Collection $collection)
    {
        unset($collection[0]);
        $grammar=null;
        foreach($collection as $key=>$vocabulary)
        {
            if($vocabulary[0]!=null)
            {
                $theme=null;
                if($vocabulary[4])
                {
                    $theme=Theme::where('name',$vocabulary[4])->first('id');

                    if($theme==null){
                        $theme=Theme::create([
                            'name'=>$vocabulary[4]
                        ]);
                    }
                }

                if($vocabulary[5]!=null && $vocabulary[6]!=null && $vocabulary[6]!=null)
                {
                    $grammar=[
                        'nl'=>[
                            'presens'=>$vocabulary[5],
                            'imperfectum'=>$vocabulary[6],
                            'perfectum'=>$vocabulary[7],
                        ]
                        ];
                }

                Vocabulary::create([
                    'name'=>$vocabulary[0],
                    'translations'=>[
                        'nl'=>$vocabulary[3],
                        'ar'=>$vocabulary[2],
                        'en'=>$vocabulary[1]
                    ],
                    'vocabulary_grammar'=>$grammar,
                    'date'=>date('Y-m-d'),
                    'user_id'=>$this->user->id,
                    'theme'=>$theme?->id
                ]);
            }

        }

    }
}
