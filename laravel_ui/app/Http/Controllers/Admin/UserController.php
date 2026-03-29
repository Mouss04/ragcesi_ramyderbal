<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()
                ->where('company_id', auth()->user()->company_id)
                ->where('id', '!=', auth()->id())
                ->latest()
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        // Admins and supervisors can only create supervisor/user accounts.
        // Only witrack_agent creates admin accounts (via WitrackController).
        $allowedRoles = auth()->user()->role === 'supervisor' ? 'supervisor,user' : 'supervisor,user';

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255', 'unique:users,name'],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', 'in:' . $allowedRoles],
            'avatar'   => ['nullable', 'image', 'max:2048'],
        ]);

        $payload = [
            'name'       => $data['name'],
            'password'   => Hash::make($data['password']),
            'role'       => $data['role'],
            'company_id' => auth()->user()->company_id,
        ];

        if ($request->hasFile('avatar')) {
            $payload['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        User::query()->create($payload);

        return redirect()->route('admin.users.index')->with('status', 'Utilisateur créé avec succès.');
    }

    public function edit(User $user): View
    {
        abort_unless($user->company_id === auth()->user()->company_id, 403);
        // Supervisors cannot edit admin accounts.
        abort_if(auth()->user()->role === 'supervisor' && $user->role === 'admin', 403);

        return view('admin.users.edit', ['user' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->company_id === auth()->user()->company_id, 403);
        // Supervisors cannot update admin accounts.
        abort_if(auth()->user()->role === 'supervisor' && $user->role === 'admin', 403);

        // Admin role is locked — only witrack_agent can assign it and it cannot be changed.
        // A supervisor cannot downgrade their own role.
        if ($user->role === 'admin') {
            $allowedRoles = 'admin';
        } elseif ($user->id === auth()->id()) {
            $allowedRoles = $user->role; // locked to current role
        } else {
            $allowedRoles = 'supervisor,user';
        }

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255', 'unique:users,name,'.$user->id],
            'password' => ['nullable', 'string', 'min:8'],
            'role'     => ['required', 'in:' . $allowedRoles],
            'avatar'   => ['nullable', 'image', 'max:2048'],
            'avatar'   => ['nullable', 'image', 'max:2048'],
        ]);

        $payload = [
            'name' => $data['name'],
            'role' => $data['role'],
        ];

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $payload['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($payload);

        return redirect()->route('admin.users.index')->with('status', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless($user->company_id === auth()->user()->company_id, 403);
        // Supervisors cannot delete admin accounts.
        abort_if(auth()->user()->role === 'supervisor' && $user->role === 'admin', 403);

        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'Vous ne pouvez pas supprimer votre propre compte.']);
        }

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'Utilisateur supprimé avec succès.');
    }
}
