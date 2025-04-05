<?php

use App\Http\Controllers\EventApplicationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventQuestionController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureOrganiser;
use App\Models\ActionLog;
use Illuminate\Support\Facades\Route;

Route::get('/language/{lang}', [LanguageController::class, 'switchLang'])->name('lang.switch');

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware(['auth'])->group(function () {
    Route::middleware([EnsureOrganiser::class])->group(function (){
        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
        Route::get('/events/manage', [EventController::class, 'manage'])->name('events.manage');
        Route::get('/events/manage/edit/{id}', [EventController::class, 'edit'])->name('events.edit');
        Route::put('/events/manage/edit/update/{id}', [EventController::class, 'update'])->name('events.update');
        Route::delete('/events/delete/{id}', [EventController::class, 'destroy'])->name('events.destroy');
        Route::post('/events/{event}/questions', [EventQuestionController::class, 'store'])->name('event_questions.store');
        Route::delete('/questions/{question}', [EventQuestionController::class, 'destroy'])->name('event_questions.destroy');
    });
    Route::get('/events/{event}/applications', [EventApplicationController::class, 'index'])->name('events.applications');
    Route::patch('/applications/{application}/status', [EventApplicationController::class, 'updateStatus'])->name('applications.updateStatus');
    Route::get('/events/{event}/apply', [EventApplicationController::class, 'showApplicationForm'])->name('events.apply');
    Route::post('/events/{event}/apply', [EventApplicationController::class, 'store'])->name('events.apply.store');
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

Route::middleware(['auth'])->group(function () {
    Route::middleware([EnsureAdmin::class])->group(function (){
        Route::get('/admin/logs', function () {
            return view('dashboard', ['logs' => ActionLog::latest()]);
        })->middleware('auth')->name('dashboard.logs');
        Route::get('/admin/logs', [LogController::class, 'index'])->name('logs.index');
    });
});

//pipeline test2

require __DIR__.'/auth.php';
