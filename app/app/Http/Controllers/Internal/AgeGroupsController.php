<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Year;
use Yajra\DataTables\DataTables;

class AgeGroupsController extends Controller
{
    public function getAgeGroups(Year $year)
    {
        return DataTables::make($year->age_groups())->make(true);
    }
}
