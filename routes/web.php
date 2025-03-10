<?php

use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

Route::get('/language/{lang}', [LanguageController::class, 'switchLang'])->name('lang.switch');

Route::get('/', function () {
    return view('welcome');
});
