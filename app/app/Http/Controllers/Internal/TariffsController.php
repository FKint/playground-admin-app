<?php

namespace App\Http\Controllers\Internal;

use App\Tariff;
use App\Year;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class TariffsController extends Controller
{
    public function getTariffs(Year $year)
    {
        return DataTables::make($year->tariffs())->make(true);
    }
}
