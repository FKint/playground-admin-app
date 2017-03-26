<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegistrationsController extends Controller
{
    public function show(){
        return view('registrations.index');
    }
}
