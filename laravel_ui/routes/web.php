<?php

use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\RagAdminController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RagController;
use App\Http\Controllers\Witrack\WitrackController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'))->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Notifications ──────────────────────────────────────────────────────────

    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::get('/notifications/unread-count',   [NotificationController::class, 'unreadCount'])->name('notifications.unreadCount');

    // ── Witrack Agent ──────────────────────────────────────────────────────────
    Route::middleware('role:witrack_agent')->prefix('witrack')->name('witrack.')->group(function (): void {
        Route::get('/dashboard', [WitrackController::class, 'index'])->name('dashboard');
        Route::get('/companies/create', [WitrackController::class, 'create'])->name('companies.create');
        Route::post('/companies', [WitrackController::class, 'store'])->name('companies.store');
        Route::delete('/companies/{company}', [WitrackController::class, 'destroy'])->name('companies.destroy');
        Route::get('/settings', [WitrackController::class, 'settings'])->name('settings');
        Route::post('/settings/profile',  [WitrackController::class, 'updateProfile'])->name('settings.profile');
        Route::post('/settings/password', [WitrackController::class, 'updatePassword'])->name('settings.password');
    });

    // ── Admin / Supervisor ─────────────────────────────────────────────────────
    Route::middleware('role:admin,supervisor')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::resource('/users', UserController::class)->except(['show']);

        Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
        Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
        Route::get('/documents/consult', [DocumentController::class, 'consult'])->name('documents.consult');

        // Admin-only document management
        Route::middleware('role:admin')->group(function (): void {
            Route::delete('/documents', [DocumentController::class, 'destroyAll'])->name('documents.destroyAll');
            Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
        });

        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::post('/settings/profile',  [SettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
        Route::post('/settings/datetime', [SettingsController::class, 'updateDatetime'])->name('settings.datetime');
        Route::post('/settings/language', [SettingsController::class, 'updateLanguage'])->name('settings.language');

        // Company & theme — admin only
        Route::middleware('role:admin')->post('/settings/company', [SettingsController::class, 'updateCompany'])->name('settings.company');

        Route::get('/rag',  [RagAdminController::class, 'index'])->name('rag');
        Route::post('/rag', [RagAdminController::class, 'ask'])->name('rag.ask');

        // Comments – history & moderation
        Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');
        Route::patch('/comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve');
        Route::patch('/comments/{comment}/reject',  [CommentController::class, 'reject'])->name('comments.reject');
    });

    // ── Employees / Supervisors / Admins ───────────────────────────────────────
    Route::middleware('role:user,supervisor,admin')->group(function (): void {
        Route::get('/start', [DashboardController::class, 'employee'])->name('employee.dashboard');
        Route::post('/rag/ask', [RagController::class, 'ask'])->name('rag.ask');
    });

    Route::middleware('role:user,supervisor,admin')->prefix('employee')->name('employee.')->group(function (): void {
        Route::get('/documents', [EmployeeController::class, 'documents'])->name('documents');
        Route::get('/documents/{id}/content', [EmployeeController::class, 'documentContent'])->name('documents.content');
        Route::get('/documents/{id}/view', [EmployeeController::class, 'documentView'])->name('documents.view');
        Route::post('/documents/{id}/comments', [CommentController::class, 'store'])->name('documents.comments.store');
        Route::get('/history', [EmployeeController::class, 'history'])->name('history');
        Route::delete('/history', [EmployeeController::class, 'clearHistory'])->name('history.clear');
        Route::get('/settings', [EmployeeController::class, 'settings'])->name('settings');
        Route::post('/settings/profile',  [EmployeeController::class, 'updateProfile'])->name('settings.profile');
        Route::post('/settings/password', [EmployeeController::class, 'updatePassword'])->name('settings.password');
        Route::post('/settings/timezone', [EmployeeController::class, 'updateTimezone'])->name('settings.timezone');
        Route::post('/settings/language', [EmployeeController::class, 'updateLanguage'])->name('settings.language');
    });
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin,supervisor')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::resource('/users', UserController::class)->except(['show']);

        Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
        Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');

        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::post('/settings/profile',  [SettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
        Route::post('/settings/datetime', [SettingsController::class, 'updateDatetime'])->name('settings.datetime');
        Route::post('/settings/language', [SettingsController::class, 'updateLanguage'])->name('settings.language');

        // Company & theme — admin only
        Route::middleware('role:admin')->post('/settings/company', [SettingsController::class, 'updateCompany'])->name('settings.company');

        Route::get('/rag',  [RagAdminController::class, 'index'])->name('rag');
        Route::post('/rag', [RagAdminController::class, 'ask'])->name('rag.ask');
    });

    Route::middleware('role:user,supervisor,admin')->group(function (): void {
        Route::get('/start', [DashboardController::class, 'employee'])->name('employee.dashboard');
        Route::post('/rag/ask', [RagController::class, 'ask'])->name('rag.ask');
    });

    Route::middleware('role:user,supervisor,admin')->prefix('employee')->name('employee.')->group(function (): void {
        Route::get('/documents', [EmployeeController::class, 'documents'])->name('documents');
        Route::get('/documents/{id}/content', [EmployeeController::class, 'documentContent'])->name('documents.content');
        Route::get('/documents/{id}/view', [EmployeeController::class, 'documentView'])->name('documents.view');
        Route::get('/history', [EmployeeController::class, 'history'])->name('history');
        Route::delete('/history', [EmployeeController::class, 'clearHistory'])->name('history.clear');
        Route::get('/settings', [EmployeeController::class, 'settings'])->name('settings');
        Route::post('/settings/profile',  [EmployeeController::class, 'updateProfile'])->name('settings.profile');
        Route::post('/settings/password', [EmployeeController::class, 'updatePassword'])->name('settings.password');
        Route::post('/settings/timezone', [EmployeeController::class, 'updateTimezone'])->name('settings.timezone');
    });
});
