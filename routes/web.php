<?php

use App\Http\Controllers\{AuthController, DynamicScriptController, RulesController};
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::get('/', [AuthController::class, 'showLogin'])->name('showLogin');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('showRegister');
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Rules Route
Route::group(['prefix' => 'rules'], function () {
    Route::get('/dashboard', [RulesController::class, 'showDashboard'])->name('dashboard');
    Route::post('/store', [RulesController::class, 'storeRules'])->name('storeRules');
});

// Dynamic Script
Route::get('/script/dynamic/{userId}', [DynamicScriptController::class, 'serveDynamicScript']);

