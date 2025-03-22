<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureOrganiser;
use Illuminate\Support\Facades\Route;

Route::get('/language/{lang}', [LanguageController::class, 'switchLang'])->name('lang.switch');

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware(['auth'])->group(function () {
    Route::middleware([EnsureOrganiser::class])->group(function (){
        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
        Route::get('/events/manage', [EventController::class, 'manage'])->name('events.manage');
        Route::delete('/events/{id}', [EventController::class, 'destroy'])->name('events.destroy');
    });
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/search', [EventController::class, 'search'])->name('events.search');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

require __DIR__.'/auth.php';
