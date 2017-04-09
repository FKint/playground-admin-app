<?php

namespace App\Http\Controllers;

use App\AgeGroup;
use Yajra\Datatables\Datatables;

class AgeGroupsController extends Controller
{
    public function getAgeGroups()
    {
        return Datatables::of(AgeGroup::query())->make(true);
    }
}
