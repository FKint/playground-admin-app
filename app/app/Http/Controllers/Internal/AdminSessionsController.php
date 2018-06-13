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
            return redirect()->route('internal.open_new_admin_session');
        }
        return view('admin_sessions.close_session');
    }

    public function showSubmitCloseAdminSession(SaveAdminSessionRequest $request, Year $year)
    {
        $admin_session = $year->getActiveAdminSession();
        $validated_data = $request->validated();
        $data = array(
            "responsible_name" => $validated_data['responsible_name'],
            "counted_cash" => $validated_data['counted_cash'],
            "session_end" => Carbon::now(),
            "remarks" => $validated_data['remarks']
        );
        $admin_session->update($data);
        $admin_session->save();
        $new_admin_session = new AdminSession();
        $new_admin_session->year()->associate($year);
        $new_admin_session->save();
        return redirect()->route('internal.dashboard');
    }

    public function getAdminSessions(Year $year)
    {
        return DataTables::make($year->admin_sessions())->make(true);
    }

    public function showEditAdminSession(Year $year, AdminSession $adminSession)
    {
        return view('admin_sessions.edit_session', ["admin_session" => $adminSession]);
    }

    public function showSaveEditAdminSession(SaveAdminSessionRequest $request, Year $year, AdminSession $adminSession)
    {
        $validated_data = $request->validated();
        $adminSession->update($validated_data);
        return redirect(route('internal.dashboard'));
    }
}
