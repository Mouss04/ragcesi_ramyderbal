<?php

use App\Http\Controllers\RagController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/rag/ask', [RagController::class, 'ask'])->name('rag.ask');
