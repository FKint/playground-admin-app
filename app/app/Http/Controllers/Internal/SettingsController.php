<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function show()
    {
        return view('settings.index');
    }
}
