<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Year;
use Yajra\DataTables\DataTables;

class TariffsController extends Controller
{
    public function getTariffs(Year $year)
    {
        return DataTables::make($year->tariffs())->make(true);
    }
}
