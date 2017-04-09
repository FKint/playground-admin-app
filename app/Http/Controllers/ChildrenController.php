<?php

namespace App\Http\Controllers;

use App\Child;
use Yajra\Datatables\Datatables;

class ChildrenController extends Controller
{
    public function show()
    {
        return view('children.index');
    }

    public function getChildren()
    {
        return Datatables::of(Child::query())->make(true);
    }
}
