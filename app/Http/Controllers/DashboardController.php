<?php

namespace App\Http\Controllers;

use App\Models\QuizPerformance;
use App\Models\Scopes\QuizScope;
use App\Models\Vocabulary;
use App\Repositories\DashboardRepository;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request) : Response
    {
        return Inertia::render('Dashboard',
        (new DashboardRepository)->DashboardParams($request->searchDate ?? 'This Month')
        );
    }
}
