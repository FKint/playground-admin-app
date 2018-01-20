<?php

namespace App\Http\Controllers\Internal;

use App\DayPart;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class DayPartsController extends Controller
{
    public function getDayParts()
    {
        return DataTables::make(DayPart::query())->make(true);
    }
}
