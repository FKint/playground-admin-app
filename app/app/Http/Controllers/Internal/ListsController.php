<?php

namespace App\Http\Controllers\Internal;

use App\ActivityList;
use App\ChildFamily;
use App\Family;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\Year;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ListsController extends Controller
{
    public function show()
    {
        return view('lists.index');
    }

    public function showList(Request $request, Year $year, ActivityList $list)
    {
        return view('lists.list_details')
            ->with('list', $list);
    }

    public function showNewList()
    {
        return view('lists.new_list');
    }

    public function getLists(Year $year)
    {
        return DataTables::make($year->activity_lists())->make(true);
    }

    public function getListParticipants(Year $year, ActivityList $list)
    {
        return DataTables::make($list->child_families()->with('child')->with('family'))->make(true);
    }

    public function removeListParticipant(Request $request, Year $year, ActivityList $activity_list, ChildFamily $child_family)
    {
        $activity_list->child_families()->detach($child_family);
        $this->addTransaction(
            $year,
            $child_family->family,
            -$activity_list->price,
            0,
            'Kind '.$child_family->child->full_name().' uitgeschreven van lijst '.$activity_list->name.' (ID: '.$activity_list->id.')'
        );

        return ['success' => true];
    }

    public function getListChildFamilySuggestions(Request $request, Year $year, ActivityList $list)
    {
        $query = $request->input('q');
        $child_families = $year->child_families()->search($query)
            ->with('child')
            ->with('family')
            ->whereDoesntHave('activity_lists', function ($query) use ($list) {
                $query->where('activity_lists.id', '=', $list->id);
            })
            ->get();

        return $child_families;
    }

    public function addListChildFamily(Request $request, Year $year, ActivityList $activity_list, ChildFamily $child_family)
    {
        $activity_list->child_families()->syncWithoutDetaching([$child_family->id => ['year_id' => $year->id]]);
        $this->addTransaction(
            $year,
            $child_family->family,
            $activity_list->price,
            0,
            'Kind '.$child_family->child->full_name().' ingeschreven op lijst '.$activity_list->name.' (ID: '.$activity_list->id.')'
        );

        return ['success' => true];
    }

    public function submitNewList(Request $request, Year $year)
    {
        $list = new ActivityList($this->getListData($request));
        $list->year()->associate($year);
        $list->save();

        return redirect(route('internal.show_list', ['list' => $list]));
    }

    public function submitEditList(Request $request, Year $year, ActivityList $list)
    {
        $list->update($this->getListData($request));

        return redirect(route('internal.show_list', ['list' => $list]));
    }

    protected function addTransaction(Year $year, Family $family, $expected, $paid, string $remarks)
    {
        $transaction = new Transaction([
            'amount_paid' => $paid,
            'amount_expected' => $expected,
            'remarks' => $remarks,
            'year_id' => $year->id,
        ]);
        $admin_session = $year->getActiveAdminSession();
        $transaction->admin_session()->associate($admin_session);
        $transaction->family()->associate($family);
        $transaction->save();
    }

    protected function getListData(Request $request)
    {
        $date = \DateTimeImmutable::createFromFormat('Y-m-d', $request->input('date'));
        $price = $request->input('price');
        $show_on_attendance_form = 'on' === $request->input('show_on_attendance_form');
        $show_on_dashboard = 'on' === $request->input('show_on_dashboard');

        return [
            'name' => $request->input('name'),
            'date' => $date ? $date->format('Y-m-d') : null,
            'price' => $price,
            'show_on_attendance_form' => $show_on_attendance_form,
            'show_on_dashboard' => $show_on_dashboard,
        ];
    }
}
