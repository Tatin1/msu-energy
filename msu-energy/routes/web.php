<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Routes for authenticated users and public pages.
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

    // Main dashboard routes
    Route::get('/home', [DashboardController::class, 'home'])->name('home');          // existing references
    Route::get('/dashboard', [DashboardController::class, 'home'])->name('dashboard'); // post-login redirect

    // Other pages
    Route::get('/map', [DashboardController::class, 'map'])->name('map');
    Route::get('/parameters', [DashboardController::class, 'parameters'])->name('parameters');
    Route::get('/billing', [DashboardController::class, 'billing'])->name('billing');
    Route::get('/tables', [DashboardController::class, 'tables'])->name('tables');
    Route::get('/graphs', [DashboardController::class, 'graphs'])->name('graphs');
    Route::get('/history', [DashboardController::class, 'history'])->name('history');
    Route::get('/options', [DashboardController::class, 'options'])->name('options');
    Route::get('/view', [DashboardController::class, 'view'])->name('view');
    Route::get('/help', [DashboardController::class, 'help'])->name('help');
    Route::get('/about', [DashboardController::class, 'about'])->name('about');

    // Export Routes
    Route::get('/export/building', [ExportController::class, 'exportBuilding'])->name('export.building');
    Route::get('/export/system', [ExportController::class, 'exportSystem'])->name('export.system');
});

require __DIR__.'/auth.php';
