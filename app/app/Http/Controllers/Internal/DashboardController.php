<?php

namespace App\Http\Controllers\Internal;

use App\AdminSession;
use App\AgeGroup;
use App\PlaygroundDay;
use App\Supplement;
use App\Year;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function show(Request $request)
    {
        $selected_date = $request->input('date');
        if ($selected_date) {
            $selected_date = \DateTimeImmutable::createFromFormat('Y-m-d', $request->input('date'));
        } else {
            $selected_date = new \DateTimeImmutable();
        }
        $playground_day = PlaygroundDay::getPlaygroundDayForDate($selected_date);
        $year = Year::first();
        if ($playground_day) {
            $year = $playground_day->week->year;
        }
        return view('dashboard.index')
            ->with('today_playground_day', $playground_day)
            ->with('year', $year)
            ->with('all_age_groups', AgeGroup::all())
            ->with('supplements', Supplement::all())
            ->with("active_admin_session", AdminSession::getActiveAdminSession())
            ->with("selected_menu_item", "dashboard");
    }
}
