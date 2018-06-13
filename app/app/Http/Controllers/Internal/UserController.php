<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Show the user dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return view('user.dashboard');
    }
}
