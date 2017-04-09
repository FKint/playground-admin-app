<?php

namespace App\Http\Controllers;

use App\Tariff;
use Yajra\Datatables\Datatables;

class TariffsController extends Controller
{
    public function getTariffs()
    {
        return Datatables::of(Tariff::query())->make(true);
    }
}
