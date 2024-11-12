<?php

namespace App\Http\Controllers;

use App\Http\Requests\VocabularyRequest;
use App\Http\Resources\VocabularyResource;
use App\Models\Scopes\QuizScope;
use App\Models\Vocabulary;
use App\Repositories\VocabularyRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VocabularyController extends Controller
{

    public function __construct(public VocabularyRepository $vocabularyRepository)
    {

    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Vocabulary/Index',[
            'vocabularies'=>VocabularyResource::collection(auth()->user()->vocabularies()->withoutGlobalScope(QuizScope::class)->orderBy('created_at','desc')->paginate(5))
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return Inertia::render('Vocabulary/Create',[
            'vocabulary'=> new Vocabulary()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VocabularyRequest $request)
    {
        $this->vocabularyRepository->store($request);

        return redirect()->route('vocabularies.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vocabulary $vocabulary): Response
    {
        return Inertia::render('Vocabulary/Create',[
            'vocabulary'=>$vocabulary
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VocabularyRequest $request, Vocabulary $vocabulary) : RedirectResponse
    {
        $this->vocabularyRepository->update($request,$vocabulary);

        return redirect()->route('vocabularies.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vocabulary $vocabulary): RedirectResponse
    {
        $this->vocabularyRepository->destroy($vocabulary);

        return back();
    }

    /**
     * Remove Multiple Vocabularies
     */

    public function destroyMultiple(Request $request) : RedirectResponse
    {
        $this->vocabularyRepository->destroyMultiple($request);
        return back();
    }
}
