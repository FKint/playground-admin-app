<?php

namespace App\Http\Controllers\Internal;

use App\Supplement;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class SupplementsController extends Controller
{
    public function getSupplements()
    {
        return DataTables::make(Supplement::query())->make(true);
    }
}
