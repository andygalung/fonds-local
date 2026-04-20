<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvestasiController;
use App\Http\Controllers\PrioritasController;
use App\Http\Controllers\SyncController;
use Illuminate\Support\Facades\Route;

// Redirect root ke dashboard
Route::redirect('/', '/dashboard');

// ─── Dashboard ─────────────────────────────────────────────────────────────
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// ─── Data Investasi ────────────────────────────────────────────────────────
Route::get('/investasi', [InvestasiController::class, 'index'])->name('investasi.index');

// ─── Dashboard Prioritas PKS (Data OH) ────────────────────────────────────
Route::get('/prioritas', [PrioritasController::class, 'index'])->name('prioritas.index');
Route::post('/prioritas/refresh', [PrioritasController::class, 'refresh'])->name('prioritas.refresh');

// ─── Sinkronisasi Google Sheets ────────────────────────────────────────────
Route::prefix('sync')->name('sync.')->group(function () {
    Route::get('/', [SyncController::class, 'index'])->name('index');
    Route::post('/run', [SyncController::class, 'run'])->name('run');
});
