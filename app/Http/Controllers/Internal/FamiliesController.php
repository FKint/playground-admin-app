<?php

namespace App\Http\Controllers;

use App\Child;
use App\Family;
use App\Tariff;
use App\AgeGroup;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class FamiliesController extends Controller
{
    public function show()
    {
        return view('families.index')
            ->with('selected_menu_item', 'families');
    }

    public function showNewFamilyWithChildren()
    {
        return view('families.new_family_with_children.index')
            ->with('all_tariffs_by_id', Tariff::getAllTariffsById());
    }

    public function showSubmitNewFamilyWithChildren(Request $request)
    {
        $family = new Family($request->all());
        $family->save();
        return redirect()->action('FamiliesController@showAddChildrenToFamily', ['family_id' => $family->id]);
    }

    public function showAddChildrenToFamily(Request $request, $family_id)
    {
        $family = Family::findOrFail($family_id);
        return view('families.new_family_with_children.add_child')
            ->with('family', $family)
            ->with('all_age_groups_by_id', AgeGroup::getAllAgeGroupsById())
            ->with('all_age_groups', AgeGroup::all());
    }

    public function showSubmitAddChildrenToFamily(Request $request, $family_id)
    {
        $family = Family::findOrFail($family_id);
        $child = new Child($request->all());
        $child->save();
        $family->children()->attach($child);
        return redirect()->action('FamiliesController@showAddChildrenToFamily', ['family_id' => $family_id]);
    }

    public function showRemoveChildFromNewFamilyWithChildren(Request $request, $family_id, $child_id)
    {
        $family = Family::findOrFail($family_id);
        $child_family = $family->child_families()->where('child_id', '=', $child_id)->firstOrFail();
        $child_family->delete();
        return redirect()->action('FamiliesController@showAddChildrenToFamily', ['family_id' => $family_id]);
    }

    public function showTransactions($family_id)
    {
        $family = Family::findOrFail($family_id);
        return view('families.transactions.index')
            ->with('family', $family);
    }

    public function getFamilies()
    {
        return Datatables::of(
            Family::query()
                ->with('children')
                ->with('tariff')
                ->with('child_families')
                ->with('child_families.child')
                ->get()
        )->make(true);
    }

    public function getFamilyTransactions($family_id)
    {
        $family = Family::findOrFail($family_id);
        return Datatables::of(
            $family->transactions()->with('admin_session')
        )->make(true);
    }

    public function loadEditFamilyForm(Request $request)
    {
        return $this->loadEditFamilyFormForFamily($request->input('family_id'));
    }

    protected function loadEditFamilyFormForFamily($family_id)
    {

        $family = Family::findOrFail($family_id);
        return view('families.edit_family.form')
            ->with('family', $family)
            ->with('all_tariffs_by_id', Tariff::getAllTariffsById());
    }

    public function submitEditFamilyForm(Request $request, $family_id)
    {
        Family::findOrFail($family_id)->update($request->all());
        return $this->loadEditFamilyFormForFamily($family_id);
    }

    public function getFamilyChildren($family_id)
    {
        return Datatables::of(Family::findOrFail($family_id)->children)->make(true);
    }

    public function loadFamilyChildrenTable(Request $request)
    {
        $family = Family::findOrFail($request->input('family_id'));
        return view('families.children.table', ['family' => $family]);
    }

    public function getChildSuggestionsForFamily(Request $request, $family_id)
    {
        $query = $request->input('q');
        $children = Child::search($query)
            ->groupBy('children.id')
            ->with('child_families')
            ->with('families')
            ->whereDoesntHave("families", function ($query) use ($family_id) {
                $query->where('family_id', '=', $family_id);
            })
            ->get();
        return $children;
    }

    public function getFamilySuggestions(Request $request)
    {
        $query = $request->input('q');
        $families = Family::search($query)
            ->groupBy('families.id')
            ->with('children')
            ->get();
        return $families;
    }

    public function addChildToFamily(Request $request, $family_id)
    {
        $child = Child::findOrFail($request->input('child_id'));
        $family = Family::findOrFail($family_id);
        $family->children()->attach($child);
        return $family;
    }

}
