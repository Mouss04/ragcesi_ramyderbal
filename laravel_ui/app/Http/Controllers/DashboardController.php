<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): RedirectResponse
    {
        if (auth()->user()?->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('employee.dashboard');
    }

    public function admin(): View
    {
        return view('admin.dashboard', [
            'usersCount'        => User::query()->count(),
            'documentsCount'    => Document::query()->count(),
            'todayQueriesCount' => 0,
        ]);
    }

    public function employee(): View
    {
        return view('user.dashboard');
    }
}
