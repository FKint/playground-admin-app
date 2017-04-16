<?php

namespace App\Http\Controllers;

use App\Child;
use App\ChildFamily;
use App\Family;
use App\Tariff;
use App\AgeGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\Datatables\Datatables;

class FamiliesController extends Controller
{
    public function show()
    {
        return view('families.index');
    }

    public function showNewFamilyWithChildren()
    {
        return view('families.new_family_with_children.index')
            ->with('all_tariffs', $this->getAllTariffs());
    }

    public function showSubmitNewFamilyWithChildren(Request $request)
    {
        Log::debug('$request->all(): ' . json_encode($request->all()));
        $family = new Family($request->all());
        $family->save();
        return redirect()->action('FamiliesController@showAddChildrenToFamily', ['family_id' => $family->id]);
    }

    public function showAddChildrenToFamily(Request $request, $family_id)
    {
        $family = Family::findOrFail($family_id);
        return view('families.new_family_with_children.add_child')
            ->with('family', $family)
            ->with('all_age_groups', $this->getAllAgeGroups());
    }

    public function showSubmitAddChildrenToFamily(Request $request, $family_id)
    {
        Family::findOrFail($family_id);
        $child = new Child($request->all());
        $child->save();
        $child_family = new ChildFamily(['child_id' => $child->id, 'family_id' => $family_id]);
        $child_family->save();
        return redirect()->action('FamiliesController@showAddChildrenToFamily', ['family_id' => $family_id]);
    }

    public function showRemoveChildFromNewFamilyWithChildren(Request $request, $family_id, $child_id)
    {
        $family = Family::findOrFail($family_id);
        $child_family = $family->child_families()->where('child_id', '=', $child_id)->firstOrFail();
        $child_family->delete();
        return redirect()->action('FamiliesController@showAddChildrenToFamily', ['family_id' => $family_id]);
    }

    public function getFamilies()
    {
        return Datatables::of(Family::query()->with('children'))->make(true);
    }

    public function loadFamilyChildrenForm(Request $request)
    {
        $family = Family::findOrFail($request->input('family_id'));
        return view('families.children.table')
            ->with('family', $family);
    }

    public function loadEditFamilyForm(Request $request)
    {
        return $this->loadEditFamilyFormForFamily($request->input('family_id'));
    }

    protected function getAllTariffs()
    {
        $all_tariffs = [];
        foreach (Tariff::all() as $tariff) {
            $all_tariffs[$tariff->id] = $tariff->abbreviation . " - " . $tariff->name;
        }
        return $all_tariffs;
    }

    protected function getAllAgeGroups()
    {
        $all_age_groups = [];
        foreach (AgeGroup::all() as $age_group) {
            $all_age_groups[$age_group->id] = $age_group->abbreviation . " - " . $age_group->name;
        }
        return $all_age_groups;
    }

    protected function loadEditFamilyFormForFamily($family_id)
    {

        $family = Family::findOrFail($family_id);
        return view('families.edit_family.form')
            ->with('family', $family)
            ->with('all_tariffs', $this->getAllTariffs());
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

    public function addChildToFamily(Request $request, $family_id)
    {
        $child_id = $request->input('child_id');
        $child_family = new ChildFamily(["family_id" => $family_id, "child_id" => $child_id]);
        $child_family->save();
        return $child_family;
    }

}
