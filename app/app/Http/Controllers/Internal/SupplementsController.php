<?php

namespace App\Http\Controllers\Internal;

use App\Supplement;
use App\Year;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class SupplementsController extends Controller
{
    public function getSupplements(Year $year)
    {
        return DataTables::make($year->supplements())->make(true);
    }
}
