<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Prometheus\RenderTextFormat;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\InMemory;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/slow', function () {
    usleep(rand(200000, 1500000)); // 200ms–1.5s
    return response()->json(['status' => 'slow ok']);
});

Route::get('/error', function () {
    abort(500);
});

Route::get('/metrics', [\App\Http\Middleware\PrometheusMiddleware::class, 'metrics']);


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
