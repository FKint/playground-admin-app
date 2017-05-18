<?php

namespace App\Http\Controllers;

use App\AgeGroup;
use App\Child;
use App\Family;
use App\ChildFamily;
use App\Tariff;
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
        return Datatables::of(Child::query()->with('age_group'))->make(true);
    }

    public function showNewChild()
    {
        return view('children.new_child.index')
            ->with('all_age_groups_by_id', AgeGroup::getAllAgeGroupsById());
    }

    public function showSubmitNewChild(Request $request)
    {
        $child = new Child($request->all());
        $child->save();
        return redirect()->action('ChildrenController@showEditChild', ['child_id' => $child->id]);
    }

    public function showEditChild($child_id)
    {
        $child = Child::findOrFail($child_id);
        return view('children.edit_child.index')->with('child', $child);
    }

    public function loadChildInfoForm(Request $request)
    {
        $child = Child::findOrFail($request->input('child_id'));
        return view('children.info_child.modal_content')
            ->with('child', $child)
            ->with('all_age_groups_by_id', AgeGroup::getAllAgeGroupsById())
            ->with('all_tariffs_by_id', Tariff::getAllTariffsById());
    }

    public function loadEditChildForm(Request $request)
    {
        $child = Child::findOrFail($request->input('child_id'));
        return $this->loadEditChildFormForChild($child);
    }

    public function submitEditChildForm(Request $request, $child_id)
    {
        $child = Child::findOrFail($child_id);
        $child->update($request->all());
        return $this->loadEditChildFormForChild($child);
    }

    protected function loadEditChildFormForChild($child)
    {
        return view('children.edit_child.child_form')
            ->with('child', $child)
            ->with('all_age_groups_by_id', AgeGroup::getAllAgeGroupsById());
    }

    public function loadEditFamiliesForm(Request $request)
    {
        $child = Child::findOrFail($request->input('child_id'));
        return view('children.edit_child.families_form')
            ->with('child', $child);
    }

    public function loadLinkNewChildFamilyForm(Request $request, $child_id)
    {
        $child = Child::findOrFail($child_id);
        return view('children.edit_child.new_family.form')
            ->with('child', $child)
            ->with('all_tariffs_by_id', Tariff::getAllTariffsById());
    }

    public function submitLinkNewChildFamilyForm(Request $request, $child_id)
    {
        $family = new Family($request->all());
        $family->save();
        $child_family = new ChildFamily(['child_id' => $child_id, 'family_id' => $family->id]);
        $child_family->save();
        return view('children.edit_child.new_family.succeeded');
    }

    public function getChildFamilySuggestions(Request $request, $child_id)
    {
        $query = $request->input('q');
        $families = Family::search($query)
            ->groupBy('families.id')
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
