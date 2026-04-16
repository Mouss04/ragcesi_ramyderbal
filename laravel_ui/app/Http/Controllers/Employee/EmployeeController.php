<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\RagHistory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function dashboard(): View
    {
        $recentHistory = RagHistory::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->limit(5)
            ->get();

        return view('employee.dashboard', compact('recentHistory'));
    }

    public function documents(): View
    {
        $documents = Document::query()->latest()->get();

        return view('employee.documents', compact('documents'));
    }

    public function documentContent(int $id): JsonResponse
    {
        $document = Document::query()->findOrFail($id);

        $projectRoot = dirname(base_path());
        $filePath = $projectRoot.'/'.$document->file_path;

        if (! File::exists($filePath)) {
            return response()->json(['error' => 'Fichier introuvable sur le serveur.'], 404);
        }

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (! in_array($ext, ['txt', 'md'])) {
            return response()->json(['error' => 'Format non pris en charge pour l\'aperçu texte.']);
        }

        $content = File::get($filePath);

        return response()->json(['content' => $content]);
    }

    public function documentView(int $id): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $document = Document::query()->findOrFail($id);

        $projectRoot = dirname(base_path());
        $filePath = $projectRoot.'/'.$document->file_path;

        if (! File::exists($filePath)) {
            abort(404, 'Fichier introuvable sur le serveur.');
        }

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'pdf'  => 'application/pdf',
            'txt'  => 'text/plain',
            'md'   => 'text/plain',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
            'svg'  => 'image/svg+xml',
        ];
        $mime = $mimeTypes[$ext] ?? 'application/octet-stream';

        return response()->file($filePath, [
            'Content-Type'        => $mime,
            'Content-Disposition' => 'inline; filename="'.basename($filePath).'"',
        ]);
    }

    public function history(): View
    {
        $allRecords = RagHistory::query()
            ->where('user_id', auth()->id())
            ->orderBy('created_at')
            ->get();

        $grouped = $allRecords->groupBy(
            fn ($r) => $r->chat_session_id ?? 'single_' . $r->id
        );

        $sessions = $grouped->map(fn ($msgs, $key) => (object) [
            'session_key'    => $key,
            'first_question' => $msgs->first()->question,
            'messages_count' => $msgs->count(),
            'started_at'     => $msgs->first()->created_at,
            'last_at'        => $msgs->last()->created_at,
        ])->sortByDesc('last_at')->values();

        $page      = (int) request()->get('page', 1);
        $perPage   = 15;
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $sessions->forPage($page, $perPage),
            $sessions->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('employee.history', ['sessions' => $paginator, 'isAdmin' => false]);
    }

    public function clearHistory(): RedirectResponse
    {
        RagHistory::query()
            ->where('user_id', auth()->id())
            ->delete();

        return back()->with('status', 'Historique vidé.');
    }

    public function settings(): View
    {
        return view('employee.settings', [
            'timezones' => \DateTimeZone::listIdentifiers(),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'name'   => ['required', 'string', 'max:100', 'unique:users,name,'.$user->id],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = ['name' => $request->name];

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return back()->with('status', 'Profil mis à jour.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mot de passe actuel incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('status', 'Mot de passe modifié.');
    }

    public function updateTimezone(Request $request): RedirectResponse
    {
        $request->validate(['timezone' => ['required', 'timezone']]);

        auth()->user()->update(['timezone' => $request->timezone]);

        return back()->with('status', 'Fuseau horaire mis à jour.');
    }

    public function updateLanguage(Request $request): RedirectResponse
    {
        $request->validate([
            'locale' => ['required', 'in:fr,en'],
        ]);

        session(['locale' => $request->locale]);

        return back()->with('status', 'Langue mise à jour.')->with('_lang_tab', true);
    }
}
