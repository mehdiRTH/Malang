<?php

use App\Http\Controllers\ExamenController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\VocabularyController;
use App\Imports\UsersImport;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //vocabularies
    Route::resource('vocabularies',VocabularyController::class);

    //Quiz

    Route::get('quiz',[QuizController::class,'index'])->name('quiz.index');
    Route::get('quiz/generate',[QuizController::class,'generate'])->name('quiz.generate');
    // Route::get('quiz_vocabularies/{vocabularies}',[QuizController::class,'quiz'])->name('quiz.quiz');
    Route::post('quiz_check_answers',[QuizController::class,'checkAnswers'])->name('quiz.check_answers');

    //Themes
    Route::resource('themes',ThemeController::class);

    //Examens
    Route::prefix('examen')->controller(ExamenController::class)->group(function(){
        Route::get('','index')->name('examen.index');
        Route::get('custom_examen/{start_date}/{end_date}','generateExam')->name('examen.custom_examen');
        Route::post('check_answers','checkAnswers')->name('examen.check_answers');
    });
});



// function (Request $request){

//     Excel::import(new UsersImport,$request->file('file')->getRealPath(),NULL,\Maatwebsite\Excel\Excel::XLSX);
//     dd($request->all());
// }
require __DIR__.'/auth.php';
