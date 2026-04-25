<?php

namespace App\Providers;

use App\Models\Company;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.default');

        // Share each authenticated user's company as $siteSetting so every
        // layout (admin, employee, app) automatically gets that company's
        // own logo, name and theme — with no view changes required.
        View::composer('*', function ($view) {
            try {
                $company = auth()->check() && auth()->user()->company_id
                    ? auth()->user()->company
                    : null;

                $view->with('siteSetting', $company ?? new Company([
                    'name'        => null,
                    'logo'        => null,
                    'theme_color' => '#0c7070',
                ]));
            } catch (\Throwable) {
                $view->with('siteSetting', new Company([
                    'name'        => null,
                    'logo'        => null,
                    'theme_color' => '#0c7070',
                ]));
            }
        });
    }
}
