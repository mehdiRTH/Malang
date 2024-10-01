<?php

namespace App\Http\Controllers;

use App\Http\Resources\ThemeResource;
use App\Models\Theme;
use App\Repositories\ThemeRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ThemeController extends Controller
{
    public function __construct(public ThemeRepository $themeRepository)
    {}
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Theme/Index',[
            'themes'=>ThemeResource::collection(Theme::all())
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->themeRepository->store($request);

        return back();
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Theme $theme)
    {
        $this->themeRepository->update($request,$theme);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Theme $theme)
    {
        $this->themeRepository->destroy($theme);
    }
}
