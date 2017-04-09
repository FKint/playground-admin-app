<?php

namespace App\Http\Controllers;

use App\Supplement;
use Yajra\Datatables\Datatables;

class SupplementsController extends Controller
{
    public function getSupplements()
    {
        return Datatables::of(Supplement::query())->make(true);
    }
}
