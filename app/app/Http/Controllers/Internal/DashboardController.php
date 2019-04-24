<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Year;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function show(Request $request, Year $year)
    {
        $selected_date = $request->input('date');
        if ($selected_date) {
            $selected_date = \DateTimeImmutable::createFromFormat('Y-m-d', $request->input('date'));
        } else {
            $selected_date = new \DateTimeImmutable();
        }
        $playground_day = $year->getPlaygroundDayForDate($selected_date);
        if ($playground_day) {
            $year = $playground_day->week->year;
        }
        return view('dashboard.index')
            ->with('today_playground_day', $playground_day)
            ->with('year', $year)
            ->with("selected_menu_item", "dashboard");
    }
}
