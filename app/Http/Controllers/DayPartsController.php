<?php

namespace App\Http\Controllers;

use App\DayPart;
use Yajra\Datatables\Datatables;

class DayPartsController extends Controller
{
    public function getDayParts()
    {
        return Datatables::of(DayPart::query())->make(true);
    }
}
