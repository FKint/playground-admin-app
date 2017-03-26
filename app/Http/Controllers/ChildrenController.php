<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChildrenController extends Controller
{
    public function show(){
        return view('children.index');
    }
}
