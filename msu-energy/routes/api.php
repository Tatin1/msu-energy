
<?php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\ReadingController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\GraphController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardController::class,'apiDashboard']);
Route::get('/buildings', [BuildingController::class,'index']);
Route::get('/buildings/{code}/parameters', [ReadingController::class,'parameters']);
Route::get('/meters/{id}/readings', [ReadingController::class,'meterReadings']);
Route::get('/billing', [BillingController::class,'indexApi']);
Route::get('/meters/{id}/daily/{param}/{date?}', [GraphController::class,'daily']);
