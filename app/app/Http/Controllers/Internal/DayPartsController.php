<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Year;
use Yajra\DataTables\DataTables;

class DayPartsController extends Controller
{
    public function getDayParts(Year $year)
    {
        return DataTables::make($year->day_parts())->make(true);
    }
}
