<?php

namespace App\Http\Controllers;

use App\AgeGroup;
use App\Child;
use App\Family;
use App\ChildFamily;
use App\Tariff;
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
        $child = Child::findOrFail($request->input('child_id'));
        return $this->showEditChildFormForChild($child);
    }

    public function submitEditChildForm(Request $request, $child_id){
        $child = Child::findOrFail($child_id);
        $child->update($request->all());
        return $this->showEditChildFormForChild($child);
    }

    protected function showEditChildFormForChild($child){
        $all_age_groups = [];
        foreach (AgeGroup::all() as $age_group) {
            $all_age_groups[$age_group->id] = $age_group->abbreviation . " - " . $age_group->name;
        }
        return view('children.edit_child.child_form')
            ->with('child', $child)
            ->with('all_age_groups', $all_age_groups);
    }
    public function showEditFamiliesForm(Request $request)
    {
        $child = Child::findOrFail($request->input('child_id'));
        $all_tariffs = [];
        foreach (Tariff::all() as $tariff) {
            $all_tariffs[$tariff->id] = $tariff->abbreviation . " - " . $tariff->name;
        }
        return view('children.edit_child.families_form')
            ->with('child', $child)
            ->with('all_tariffs', $all_tariffs);
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

    public function removeChildFamily(Request $request, $child_id)
    {
        $child_family_id = $request->input('child_family_id');
        $child_family = ChildFamily::find($child_family_id);
        $child_family->delete();
        return response()->json(['success' => true]);
    }
}
