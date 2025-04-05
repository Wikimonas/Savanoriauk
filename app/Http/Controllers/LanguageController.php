<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switchLang($lang): RedirectResponse
    {
        session(['locale' => $lang]);
        //dd(session()->all());
        return redirect()->back();
    }
}
