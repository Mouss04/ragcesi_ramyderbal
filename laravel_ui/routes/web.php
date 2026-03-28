<?php

use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\RagAdminController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RagController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::resource('/users', UserController::class)->except(['show']);

        Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
        Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');

        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::post('/settings/profile',  [SettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
        Route::post('/settings/company',  [SettingsController::class, 'updateCompany'])->name('settings.company');
        Route::post('/settings/datetime', [SettingsController::class, 'updateDatetime'])->name('settings.datetime');

        Route::get('/rag',  [RagAdminController::class, 'index'])->name('rag');
        Route::post('/rag', [RagAdminController::class, 'ask'])->name('rag.ask');
    });

    Route::middleware('role:user,admin')->group(function (): void {
        Route::get('/start', [DashboardController::class, 'employee'])->name('employee.dashboard');
        Route::post('/rag/ask', [RagController::class, 'ask'])->name('rag.ask');
    });
});
