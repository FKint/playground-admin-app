<?php

namespace App\Http\Controllers;

use App\AgeGroup;
use App\Child;
use App\Family;
use App\ChildFamily;
use App\Http\Requests\SaveChildRequest;
use App\Tariff;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;

class ChildrenController extends Controller
{
    public function show()
    {
        return view('children.index')
            ->with('selected_menu_item', 'children');
    }

    public function getChildren()
    {
        return Datatables::of(Child::query()->with('age_group'))->make(true);
    }

    public function showNewChild()
    {
        return view('children.new_child.index')
            ->with('all_age_groups_by_id', AgeGroup::getAllAgeGroupsById())
            ->with('all_age_groups', AgeGroup::all());
    }

    public function showSubmitNewChild(SaveChildRequest $request)
    {
        $request->validate();
        $data = array(
            'first_name' => ucfirst($request->input('first_name')),
            'last_name' => ucfirst($request->input('last_name')),
            'birth_year' => $request->input('birth_year'),
            'age_group_id' => $request->input('age_group_id'),
            'remarks' => $request->input('remarks')
        );

        $child = new Child($data);
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

    public function submitEditChildForm(SaveChildRequest $request, $child_id)
    {
        $request->validate();
        $child = Child::findOrFail($child_id);
        $child->update($request->all());
        return array("succes" => true);
    }

    protected function loadEditChildFormForChild($child)
    {
        return view('children.edit_child.child_details')
            ->with('child', $child)
            ->with('all_age_groups_by_id', AgeGroup::getAllAgeGroupsById());
    }

    public function loadEditFamiliesForm(Request $request)
    {
        $child = Child::findOrFail($request->input('child_id'));
        return view('children.edit_child.families')
            ->with('child', $child)
            ->with('all_tariffs_by_id', Tariff::getAllTariffsById());
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
        $child = Child::findOrFail($child_id);
        $family = new Family($request->all());
        $family->save();
        $family->children()->attach($child);
        return $family;
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
        $child = Child::findorFail($child_id);
        $family = Family::findOrFail($request->input('family_id'));
        $child->families()->attach($family);
        return $child->child_families()->where('family_id', '=', $family->id)->firstOrFail();
    }

    public function removeChildFamily(Request $request, $child_id)
    {
        $child_family_id = $request->input('child_family_id');
        $child_family = ChildFamily::find($child_family_id);
        $child_family->delete();
        return response()->json(['success' => true]);
    }
}
