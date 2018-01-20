<?php

namespace App\Http\Controllers\Internal;

use App\AgeGroup;

use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;

class AgeGroupsController extends Controller
{
    public function getAgeGroups()
    {
        return DataTables::make(AgeGroup::query())->make(true);
    }
}
