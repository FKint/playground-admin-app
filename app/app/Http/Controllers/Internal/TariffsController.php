<?php

namespace App\Http\Controllers\Internal;

use App\Tariff;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class TariffsController extends Controller
{
    public function getTariffs()
    {
        return DataTables::make(Tariff::query())->make(true);
    }
}
