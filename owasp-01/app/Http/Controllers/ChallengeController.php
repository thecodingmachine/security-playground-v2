<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ChallengeController extends Controller
{
    public function index(): View
    {
        return view('challenges.index');
    }
}
