<?php

namespace App\Http\Controllers;

use App\AgeGroup;
use App\Child;
use App\Family;
use App\ChildFamily;
use Illuminate\Support\Facades\Log;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;

class ChildrenController extends Controller
{
    public function show()
    {
        return view('children.index');
    }

    public function getChildren()
    {
        return Datatables::of(Child::query())->make(true);
    }

    public function showEditChildForm(Request $request)
    {
        $all_age_groups = [];
        foreach (AgeGroup::all() as $age_group) {
            $all_age_groups[$age_group->id] = $age_group->abbreviation . " - " . $age_group->name;
        }
        return view('children.edit_child_form')
            ->with('child', Child::findOrFail($request->input('child_id')))
            ->with('all_age_groups', $all_age_groups);
    }

    public function showEditFamiliesForm(Request $request)
    {
        $child = Child::findOrFail($request->input('child_id'));
        return view('children.edit_child_families_form')
            ->with('child', $child);
    }

    public function getChildFamilySuggestions(Request $request, $child_id)
    {
        $query = $request->input('q');
        $families = Family::search($query)
            ->with('child_families')
            ->with('children')
            ->whereDoesntHave("children", function ($query) use ($child_id) {
                $query->where('child_id', '=', $child_id);
            })
            ->get();
        return $families;
    }

    public function addChildFamily(Request $request, $child_id)
    {
        $family_id = $request->input('family_id');
        $child_family = new ChildFamily(['child_id' => $child_id, 'family_id' => $family_id]);
        $child_family->save();
        return $child_family;
    }
}
