<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;

Route::get('/', [DashboardController::class, 'home'])->name('home');
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
Route::get('/map', [DashboardController::class, 'map'])->name('map');
Route::get('/export/building', [ExportController::class, 'exportBuilding'])->name('export.building');
Route::get('/export/system', [ExportController::class, 'exportSystem'])->name('export.system');