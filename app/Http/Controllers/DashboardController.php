<?php

namespace App\Http\Controllers;

use App\AdminSession;
use App\AgeGroup;
use App\PlaygroundDay;
use App\Supplement;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function show()
    {
        $today_playground_day = PlaygroundDay::first();
        return view('dashboard.index')
            ->with('today_playground_day', $today_playground_day)
            ->with('all_age_groups', AgeGroup::all())
            ->with('supplements', Supplement::all())
            ->with("active_admin_session", AdminSession::getActiveAdminSession())
            ->with("selected_menu_item", "dashboard");
    }
}
