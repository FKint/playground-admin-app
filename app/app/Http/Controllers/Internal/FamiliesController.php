<?php

namespace App\Http\Controllers\Internal;

use App\AgeGroup;
use App\Child;
use App\Family;
use App\Http\Controllers\Controller;
use App\Tariff;
use App\Year;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

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

    public function showAddChildrenToFamily(Request $request, Family $family)
    {
        return view('families.new_family_with_children.add_child')
            ->with('family', $family)
            ->with('all_age_groups_by_id', AgeGroup::getAllAgeGroupsById())
            ->with('all_age_groups', AgeGroup::all());
    }

    public function showSubmitAddChildrenToFamily(Request $request, Family $family)
    {
        $child = new Child($request->all());
        $child->save();
        $family->children()->attach($child);
        return redirect()->action('FamiliesController@showAddChildrenToFamily', ['family' => $family]);
    }

    public function showRemoveChildFromNewFamilyWithChildren(Request $request, Year $year, Family $family, Child $child)
    {
        $child_family = $family->child_families()->where('child_id', '=', $child->id)->firstOrFail();
        $child_family->delete();
        return redirect()->action('FamiliesController@showAddChildrenToFamily', ['family' => $family]);
    }

    public function showTransactions(Family $family)
    {
        return view('families.transactions.index')
            ->with('family', $family);
    }

    /**
     * @param Year $year
     * @return mixed
     * @throws \Exception
     */
    public function getFamilies(Year $year)
    {
        return DataTables::make(
            $year->families()
                ->with('children')
                ->with('tariff')
                ->with('child_families')
                ->with('child_families.child')
                ->get()
        )->make(true);
    }

    public function getFamilyTransactions(Year $year, Family $family)
    {
        return DataTables::make(
            $family->transactions()->with('admin_session')
        )->make(true);
    }

    public function loadEditFamilyForm(Request $request, Year $year, Family $family)
    {
        return $this->loadEditFamilyFormForFamily($family);
    }

    protected function loadEditFamilyFormForFamily(Family $family)
    {
        return view('families.edit_family.form')
            ->with('family', $family)
            ->with('all_tariffs_by_id', $family->year->getAllTariffsById());
    }

    public function submitEditFamilyForm(Request $request, Year $year, Family $family)
    {
        $family->update($request->all());
        return $this->loadEditFamilyFormForFamily($family);
    }

    public function getFamilyChildren(Year $year, Family $family)
    {
        return DataTables::make($family->children)->make(true);
    }

    public function loadFamilyChildrenTable(Request $request, Year $year, Family $family)
    {
        return view('families.children.table', ['family' => $family]);
    }

    public function getChildSuggestionsForFamily(Request $request, Year $year, Family $family)
    {
        $query = $request->input('q');
        $children = Child::search($query)
            ->groupBy('children.id')
            ->with('child_families')
            ->with('families')
            ->whereDoesntHave("families", function ($query) use ($family) {
                $query->where('family_id', '=', $family->id);
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

    public function addChildToFamily(Request $request, Year $year, Family $family, Child $child)
    {
        $family->children()->syncWithoutDetaching([$child->id => ['year_id' => $year->id]]);
        return $family;
    }

}
