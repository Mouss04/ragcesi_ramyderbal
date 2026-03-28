<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.settings', [
            'timezones' => \DateTimeZone::listIdentifiers(),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'name'   => ['required', 'string', 'max:100', 'unique:users,name,' . $user->id],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = ['name' => $request->name];

        if ($request->hasFile('avatar')) {
            // remove old avatar
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

        $user->update(['password' => $request->password]);

        return back()->with('status', 'Mot de passe modifié.');
    }

    public function updateCompany(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'company_name' => ['nullable', 'string', 'max:100'],
            'company_logo' => ['nullable', 'image', 'max:4096'],
            'theme_color'  => ['nullable', 'string', 'max:20'],
        ]);

        $data = [
            'company_name' => $request->company_name,
            'theme_color'  => $request->theme_color ?? '#0c7070',
        ];

        if ($request->hasFile('company_logo')) {
            if ($user->company_logo) {
                Storage::disk('public')->delete($user->company_logo);
            }
            $data['company_logo'] = $request->file('company_logo')->store('logos', 'public');
        }

        $user->update($data);

        return back()->with('status', 'Paramètres entreprise mis à jour.');
    }

    public function updateDatetime(Request $request): RedirectResponse
    {
        $request->validate([
            'timezone' => ['required', 'timezone'],
        ]);

        auth()->user()->update(['timezone' => $request->timezone]);

        return back()->with('status', 'Fuseau horaire mis à jour.');
    }
}
