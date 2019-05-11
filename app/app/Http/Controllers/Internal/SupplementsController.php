<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Supplement;
use App\Year;
use Yajra\DataTables\DataTables;

class SupplementsController extends Controller
{
    public function getSupplements(Year $year)
    {
        return DataTables::make($year->supplements())->make(true);
    }
}
