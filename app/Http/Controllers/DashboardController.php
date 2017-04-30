<?php

namespace App\Http\Controllers;

use App\AdminSession;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function show()
    {
        return view('dashboard.index', ["active_admin_session" => AdminSession::getActiveAdminSession()]);
    }
}
