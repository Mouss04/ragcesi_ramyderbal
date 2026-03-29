<?php

namespace App\Http\Controllers\Witrack;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class WitrackController extends Controller
{
    /**
     * List all companies with their admin.
     */
    public function index(): View
    {
        $companies = Company::query()
            ->with(['users' => fn ($q) => $q->where('role', 'admin')])
            ->withCount('users')
            ->latest()
            ->get();

        return view('witrack.dashboard', compact('companies'));
    }

    /**
     * Show the create-company form.
     */
    public function create(): View
    {
        return view('witrack.companies.create');
    }

    /**
     * Create a new company and its admin in one atomic step.
     * Enforces: exactly one admin per company (company is always brand-new here).
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'company_name'           => ['required', 'string', 'max:255'],
            'admin_name'             => ['required', 'string', 'max:255', 'unique:users,name'],
            'admin_password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $company = Company::create(['name' => $data['company_name']]);

        User::create([
            'name'       => $data['admin_name'],
            'password'   => Hash::make($data['admin_password']),
            'role'       => 'admin',   // only witrack_agent can create admins
            'company_id' => $company->id,
        ]);

        return redirect()->route('witrack.dashboard')
            ->with('status', "Entreprise « {$company->name} » créée avec son administrateur.");
    }

    /**
     * Delete a company (cascades to its users/documents via FK).
     */
    public function destroy(Company $company): RedirectResponse
    {
        $company->delete();

        return redirect()->route('witrack.dashboard')
            ->with('status', "Entreprise supprimée.");
    }
}
