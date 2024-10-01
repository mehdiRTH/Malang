<?php

namespace App\Repositories;

use App\Models\Theme;
use App\Models\Vocabulary;
use Illuminate\Http\Request;

class ThemeRepository{
    public function store(Request $request): void
    {
        $request->validate([
            'name'=>'required'
        ]);

        Theme::create($request->all());
    }

    public function update(Request $request,Theme $theme) : void
    {
        $request->validate([
            'name'=>'required'
        ]);

        $theme->update($request->all());
    }

    public function destroy(Theme $theme)
    {
        Vocabulary::where('theme',$theme->id)->update([
            'theme'=>null
        ]);

        $theme->delete();
    }
}
