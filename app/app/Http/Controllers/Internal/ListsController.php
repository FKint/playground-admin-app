<?php

namespace App\Http\Controllers\Internal;

use App\ActivityList;
use App\AdminSession;
use App\ChildFamily;
use App\Transaction;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class ListsController extends Controller
{
    public function show()
    {
        return view('lists.index');
    }

    public function showList(Request $request, $list_id)
    {
        $list = ActivityList::findOrFail($list_id);
        return view('lists.list_details')
            ->with('list', $list);
    }

    public function showNewList()
    {
        return view('lists.new_list');
    }

    public function getLists(Request $request)
    {
        return DataTables::make(ActivityList::query())->make(true);
    }

    public function getListParticipants($list_id)
    {
        $list = ActivityList::findOrFail($list_id);
        return DataTables::make($list->child_families()->with('child')->with('family'))->make(true);
    }

    public function removeListParticipant(Request $request, $list_id)
    {
        $list = ActivityList::findOrFail($list_id);
        $child_family = ChildFamily::findOrFail($request->input('child_family_id'));
        $list->child_families()->detach($child_family);
        $this->addTransaction($child_family->family, -$list->price, 0, "Kind " . $child_family->child->full_name() . " uitgeschreven van lijst " . $list->name . " (ID: " . $list->id . ")");
        return array("success" => true);
    }

    public function getListChildFamilySuggestions(Request $request, $list_id)
    {
        $list = ActivityList::findOrFail($list_id);
        $query = $request->input('q');
        $child_families = ChildFamily::search($query)
            ->with('child')
            ->with('family')
            ->whereDoesntHave("activity_lists", function ($query) use ($list_id) {
                $query->where('activity_lists.id', '=', $list_id);
            })
            ->get();
        return $child_families;
    }

    public function addListChildFamily(Request $request, $list_id)
    {
        $list = ActivityList::findOrFail($list_id);
        $child_family = ChildFamily::findOrFail($request->input('child_family_id'));
        $list->child_families()->attach($child_family);
        $this->addTransaction($child_family->family, $list->price, 0, "Kind " . $child_family->child->full_name() . " ingeschreven op lijst " . $list->name . " (ID: " . $list->id . ")");
        return array("success" => true);
    }

    protected function addTransaction($family, $expected, $paid, $remarks)
    {
        $transaction = new Transaction(array(
            'amount_paid' => $paid,
            'amount_expected' => $expected,
            'remarks' => $remarks
        ));
        $admin_session = AdminSession::getActiveAdminSession();
        $transaction->admin_session()->associate($admin_session);
        $transaction->family()->associate($family);
        $transaction->save();
    }

    protected function getListData(Request $request)
    {
        $date = \DateTimeImmutable::createFromFormat("Y-m-d", $request->input('date'));
        $price = $request->input('price');
        $show_on_attendance_form = $request->input('show_on_attendance_form') === 'on';
        $show_on_dashboard = $request->input('show_on_dashboard') === 'on';
        $data = [
            "name" => $request->input('name'),
            "date" => $date ? $date->format("Y-m-d") : null,
            "price" => $price,
            "show_on_attendance_form" => $show_on_attendance_form,
            "show_on_dashboard" => $show_on_dashboard
        ];
        return $data;
    }

    public function submitNewList(Request $request)
    {
        $list = new ActivityList($this->getListData($request));
        $list->save();
        return redirect(route('show_list', ['list_id' => $list->id]));
    }

    public function submitEditList(Request $request, $list_id)
    {
        $list = ActivityList::findOrFail($list_id);
        $list->update($this->getListData($request));
        return redirect(route('show_list', ['list_id' => $list->id]));
    }
}
