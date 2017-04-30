<?php

namespace App\Http\Controllers;

use App\AdminSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class AdminSessionsController extends Controller
{

    public function showCloseAdminSession()
    {
        $admin_session = AdminSession::getActiveAdminSession();
        if ($admin_session == null) {
            return redirect()->route('open_new_admin_session');
        }
        return view('admin_sessions.close_session');
    }

    public function showSubmitCloseAdminSession(Request $request)
    {
        $admin_session = AdminSession::getActiveAdminSession();
        $data = array(
            "responsible_name" => $request->input('responsible_name'),
            "counted_cash" => $request->input('counted_cash'),
            "session_end" => Carbon::now(),
            "remarks" => $request->input('remarks')
        );
        $admin_session->fill($data);
        $admin_session->save();
        $new_admin_session = new AdminSession();
        $new_admin_session->save();
        return redirect()->route('dashboard');
    }

    public function getAdminSessions(){
        return Datatables::of(AdminSession::query())->make(true);
    }
}
