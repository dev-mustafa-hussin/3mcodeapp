<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientsController;
use Illuminate\Support\Facades\Route;

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
    Route::get('/clients', [ClientsController::class, 'index'])->name('clients.index');
});

require __DIR__.'/auth.php';

// --- TEMPORARY ROUTES FOR SHARED HOSTING SETUP ---
use Illuminate\Support\Facades\Artisan;

Route::get('/setup-migrate', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        return '<h1>Migrations ran successfully!</h1><pre>' . Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return '<h1>Error!</h1><pre>' . $e->getMessage() . '</pre>';
    }
});

Route::get('/setup-seed', function () {
    try {
        Artisan::call('db:seed', ['--force' => true]);
        return '<h1>Seeders ran successfully!</h1><pre>' . Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return '<h1>Error!</h1><pre>' . $e->getMessage() . '</pre>';
    }
});
// -------------------------------------------------
