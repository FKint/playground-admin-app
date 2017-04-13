<?php

namespace App\Http\Controllers;

use App\Family;
use App\Tariff;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class FamiliesController extends Controller
{
    public function show()
    {
        return view('families.index');
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

    protected function loadEditFamilyFormForFamily($family_id)
    {
        $all_tariffs = [];
        foreach (Tariff::all() as $tariff) {
            $all_tariffs[$tariff->id] = $tariff->abbreviation . " - " . $tariff->name;
        }
        $family = Family::findOrFail($family_id);
        return view('families.edit_family.form')
            ->with('family', $family)
            ->with('all_tariffs', $all_tariffs);
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
}
