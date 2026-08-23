<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\WindowController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\AboutController;

Route::get('/', function () {
    return redirect()->route('kiosk');
})->name('home');

// Secret capstone About page (open with Ctrl+Alt+Shift+A — not linked in menus)
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/about/photo/{file}', [AboutController::class, 'photo'])
    ->where('file', '[A-Za-z0-9._-]+')
    ->name('about.photo');

// Authentication
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Kiosk (public)
Route::get('/kiosk', [KioskController::class, 'index'])->name('kiosk');
Route::get('/kiosk/estimate', [KioskController::class, 'estimate'])->name('kiosk.estimate');
Route::post('/kiosk', [KioskController::class, 'store'])->name('kiosk.store');

// Public display
Route::get('/display', [DisplayController::class, 'index'])->name('display');
Route::get('/display/data', [DisplayController::class, 'data'])->name('display.data');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Admin-only routes (full transaction history, edit/delete, reports)
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/queues/waiting', [AdminDashboardController::class, 'waitingQueues'])
            ->name('queues.waiting');
        Route::resource('/users', UserManagementController::class)->names('users');
        Route::get('/history', [HistoryController::class, 'index'])->name('history');
        Route::get('/history/tickets', [HistoryController::class, 'tickets'])->name('history.tickets');
        Route::get('/history/reports', [HistoryController::class, 'reports'])->name('history.reports');
        Route::get('/history/{queue_call}/edit', [HistoryController::class, 'edit'])->name('history.edit');
        Route::put('/history/{queue_call}', [HistoryController::class, 'update'])->name('history.update');
        Route::delete('/history/{queue_call}', [HistoryController::class, 'destroy'])->name('history.destroy');
    });

    // Staff-only routes (own counter / served tickets only)
    Route::middleware('staff')->prefix('window')->name('window.')->group(function () {
        Route::get('/', [WindowController::class, 'index'])->name('dashboard');
        Route::get('/float', [WindowController::class, 'floatPanel'])->name('float');
        Route::post('/launch-float', [WindowController::class, 'launchFloat'])->name('launchFloat');
        Route::get('/state', [WindowController::class, 'state'])->name('state');
        Route::post('/call-next', [WindowController::class, 'callNext'])->name('callNext');
        Route::post('/recall', [WindowController::class, 'recall'])->name('recall');
        Route::post('/complete', [WindowController::class, 'complete'])->name('complete');
        Route::get('/history', [WindowController::class, 'history'])->name('history');
    });
});

