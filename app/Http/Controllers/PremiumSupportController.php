<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PremiumSupportController extends Controller
{
    public function __invoke()
    {
        return view('premium-support.index');
    }
}
