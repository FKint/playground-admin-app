<?php

namespace App\Http\Controllers\Internal;

use App\AdminSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveAdminSessionRequest;
use App\Year;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class AdminSessionsController extends Controller
{

    public function showCloseAdminSession(Year $year)
    {
        $admin_session = $year->getActiveAdminSession();
        if ($admin_session == null) {
            return redirect()->route('open_new_admin_session');
        }
        return view('admin_sessions.close_session');
    }

    public function showSubmitCloseAdminSession(SaveAdminSessionRequest $request, Year $year)
    {
        $admin_session = $year->getActiveAdminSession();
        $request->validate();
        $data = array(
            "responsible_name" => $request->input('responsible_name'),
            "counted_cash" => $request->input('counted_cash'),
            "session_end" => Carbon::now(),
            "remarks" => $request->input('remarks')
        );
        $admin_session->update($data);
        $admin_session->save();
        $new_admin_session = new AdminSession();
        $new_admin_session->save();
        return redirect()->route('dashboard');
    }

    public function getAdminSessions(Year $year)
    {
        return DataTables::make($year->admin_sessions())->make(true);
    }

    public function showEditAdminSession(AdminSession $adminSession)
    {
        return view('admin_sessions.edit_session', ["admin_session" => $adminSession]);
    }

    public function showSaveEditAdminSession(SaveAdminSessionRequest $request, AdminSession $adminSession)
    {
        $request->validate();
        $data = array(
            "responsible_name" => $request->input('responsible_name'),
            "counted_cash" => $request->input('counted_cash'),
            "remarks" => $request->input('remarks')
        );
        $adminSession->update($data);
        return redirect(route('dashboard'));
    }
}
