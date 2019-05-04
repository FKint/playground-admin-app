<?php

namespace App\Http\Controllers\Internal;

use App\Child;
use App\Family;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveChildRequest;
use App\Http\Requests\UpdateFamilyInfoRequest;
use App\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class FamiliesController extends Controller
{
    public function show()
    {
        return view('families.index')
            ->with('selected_menu_item', 'families');
    }

    public function showNewFamilyWithChildren(Year $year)
    {
        return view('families.new_family_with_children.index')
            ->with('all_tariffs_by_id', $year->getAllTariffsById());
    }

    public function showSubmitNewFamilyWithChildren(Request $request, Year $year)
    {
        $family = new Family($request->all());
        $family->year()->associate($year);
        $family->save();
        return redirect(route('internal.show_add_child_to_family', ['family' => $family]));
    }

    public function showAddChildrenToFamily(Year $year, Family $family)
    {
        return view('families.new_family_with_children.add_child')
            ->with('family', $family)
            ->with('year', $year);
    }

    public function showSubmitAddChildrenToFamily(SaveChildRequest $request, Year $year, Family $family)
    {
        $child = new Child($request->validated());
        $child->year()->associate($year);
        $child->save();
        $child->families()->syncWithoutDetaching([$family->id => ['year_id' => $year->id]]);
        return redirect(route('internal.show_add_child_to_family', ['family' => $family]));
    }

    public function showRemoveChildFromNewFamilyWithChildren(Request $request, Year $year, Family $family, Child $child)
    {
        $child_family = $family->child_families()->where('child_id', '=', $child->id)->firstOrFail();
        $child_family->delete();
        return redirect(route('internal.show_add_child_to_family', ['family' => $family]));
    }

    public function showTransactions(Year $year, Family $family)
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
        return view('families.edit_family.form')
            ->with('family', $family)
            ->with('all_tariffs_by_id', $family->year->getAllTariffsById());
    }

    public function updateFamily(UpdateFamilyInfoRequest $request, Year $year, Family $family)
    {
        $validated = $request->validated();
        $family->update($validated);
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
        $children = $year->children()
            ->search($query, 0)
            ->groupBy('children.id')
            ->with('child_families')
            ->with('families')
            ->whereDoesntHave("families", function ($query) use ($family) {
                $query->where('family_id', '=', $family->id);
            })
            ->limit(5)
            ->get();
        return $children;
    }

    public function getFamilySuggestions(Request $request, Year $year)
    {
        $query = $request->input('q');
        $families = $year->families()
            ->search($query)
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
