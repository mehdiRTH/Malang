<?php


namespace App\Repositories;

use App\Http\Requests\VocabularyRequest;
use App\Imports\VocabularyImport;
use App\Jobs\VocabularyImpotQueue;
use App\Models\Vocabulary;
use Maatwebsite\Excel\Facades\Excel;

class VocabularyRepository{

    public function store(VocabularyRequest $request)
    {
        $user=auth()?->user();
        if($request->createByUpload)
        {
            Excel::import(new VocabularyImport($user),$request->file('sheet')->getRealPath(),NULL,\Maatwebsite\Excel\Excel::XLSX);
        }else{
            Vocabulary::create([
                'name'=>$request->name,
                'translations'=>$this->setTranslations($request->translations),
                'date'=>date('Y-m-d'),
                'user_id'=>$user->id
            ]);
        }

    }

    public function update(VocabularyRequest $request,Vocabulary $vocabulary)
    {
        $vocabulary->update([
            'name'=>$request->name,
            'translations'=>$this->setTranslations($request->translations)
        ]);

    }

    public function setTranslations($translations)
    {
        $filteredTranslation=[];

        foreach($translations as $trans)
        {
            $filteredTranslation= array_merge($filteredTranslation,[$trans['language']=='Dutch' ? 'nl' : 'en'=>$trans['vocabulary']]);
        }

        return (object)$filteredTranslation;
    }

    public function destroy(Vocabulary $vocabulary)
    {
        $vocabulary->delete();
    }

}
