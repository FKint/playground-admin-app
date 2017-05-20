<?php

namespace App\Http\Controllers;

use App\AdminSession;
use App\AgeGroup;
use App\PlaygroundDay;
use App\Supplement;
use Illuminate\Http\Request;

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
        return view('dashboard.index')
            ->with('today_playground_day', PlaygroundDay::getPlaygroundDayForDate($selected_date))
            ->with('all_age_groups', AgeGroup::all())
            ->with('supplements', Supplement::all())
            ->with("active_admin_session", AdminSession::getActiveAdminSession())
            ->with("selected_menu_item", "dashboard");
    }
}
