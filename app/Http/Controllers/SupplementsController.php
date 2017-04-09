<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Supplement;
use Yajra\Datatables\Datatables;

class SupplementsController extends Controller
{
    public function getSupplements()
    {
        return Datatables::of(Supplement::query())->make(true);
    }
}
