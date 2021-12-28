<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Year;
use Carbon\CarbonImmutable;
use Yajra\DataTables\Facades\DataTables;

class TransactionsController extends Controller
{
    public function showTransactionsForDate(Year $year, $date = null)
    {
        $date = isset($date)
            ? CarbonImmutable::createFromFormat('Y-m-d', $date)
            : CarbonImmutable::now();

        return view('transactions.index', [
            'date' => $date,
        ]);
    }

    public function getTransactionsForDate(Year $year, $date)
    {
        $date = CarbonImmutable::createFromFormat('Y-m-d', $date);
        $startTime = $date->toMutable()->hour(0)->minute(0)->second(0)->microsecond(0);
        $endTime = $startTime->copy()->addDay();

        return DataTables::eloquent(
            $year->transactions()
                ->with('admin_session')
                ->with('family')
                ->whereBetween('created_at', [$startTime, $endTime])
        )->make(true);
    }
}
