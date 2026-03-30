<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\RagHistory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): RedirectResponse
    {
        $role = auth()->user()?->role;

        if ($role === 'witrack_agent') {
            return redirect()->route('witrack.dashboard');
        }

        if ($role === 'admin' || $role === 'supervisor') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('employee.dashboard');
    }

    public function admin(): View
    {
        $companyId = auth()->user()->company_id;

        return view('admin.dashboard', [
            'usersCount'        => User::query()->where('company_id', $companyId)->count(),
            'documentsCount'    => Document::query()->count(), // already scoped via CompanyScope
            'todayQueriesCount' => RagHistory::query()->whereDate('created_at', today())->count(),
        ]);
    }

    public function employee(Request $request): View
    {
        // Build recent sessions for the right sidebar panel (own sessions only)
        $allRecent = RagHistory::query()
            ->where('user_id', auth()->id())
            ->orderBy('created_at')
            ->get()
            ->groupBy(fn ($r) => $r->chat_session_id ?? 'single_' . $r->id);

        $recentSessions = $allRecent->map(fn ($msgs, $key) => (object) [
            'session_key'    => $key,
            'first_question' => $msgs->first()->question,
            'messages_count' => $msgs->count(),
            'last_at'        => $msgs->last()->created_at,
        ])->sortByDesc('last_at')->values();

        // Pre-load a full session when session_id is in the query string
        $viewMessages = collect();
        if ($request->filled('session_id')) {
            $sid = $request->input('session_id');
            if (str_starts_with($sid, 'single_')) {
                $id = (int) substr($sid, 7);
                $viewMessages = RagHistory::query()
                    ->where('user_id', auth()->id())
                    ->where('id', $id)
                    ->get();
            } else {
                $viewMessages = RagHistory::query()
                    ->where('user_id', auth()->id())
                    ->where('chat_session_id', $sid)
                    ->orderBy('created_at')
                    ->get();
            }
        }

        return view('employee.dashboard', compact('recentSessions', 'viewMessages'));
    }
}
