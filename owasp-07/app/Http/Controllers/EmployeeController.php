<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(): View
    {
        $employees = User::query()->orderBy('name')->get();

        return view('employees.index', compact('employees'));
    }
}
