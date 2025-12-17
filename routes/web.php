<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Temporary Route to Fix Database Migration
Route::get('/fix-db', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    return "<h1>Database Migrated Successfully!</h1><p>You can now go back and use the system.</p>";
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
