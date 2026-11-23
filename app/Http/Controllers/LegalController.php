<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LegalController extends Controller
{
    public function terms(): View
    {
        return view('website.terms');
    }

    public function privacy(): View
    {
        return view('website.privacy');
    }
}
