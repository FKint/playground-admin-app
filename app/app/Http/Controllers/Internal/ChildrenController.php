<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveChildRequest;
use App\Http\Requests\UpdateFamilyInfoRequest;
use App\Models\Child;
use App\Models\Family;
use App\Models\Year;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ChildrenController extends Controller
{
    /**
     * @return $this
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Year $year)
    {
        $this->authorize('view', $year);

        return view('children.index')
            ->with('selected_menu_item', 'children')
            ->with('year', $year);
    }

    /**
     * @return $this
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showNewChild(Year $year)
    {
        $this->authorize('create_child', $year);

        return view('children.new_child.index')
            ->with('year', $year);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showSubmitNewChild(SaveChildRequest $request, Year $year)
    {
        $this->authorize('create_child', $year);
        $data = $request->validated();
        $data = [
            'first_name' => ucfirst($data['first_name']),
            'last_name' => ucfirst($data['last_name']),
            'birth_year' => $data['birth_year'],
            'age_group_id' => $data['age_group_id'],
            'remarks' => $data['remarks'],
        ];

        $child = new Child($data);
        $child->year()->associate($year);
        $child->save();

        return redirect()->action('show_edit_child', ['child' => $child]);
    }

    /**
     * @return $this
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showEditChild(Child $child, Year $year)
    {
        $this->authorize('update', $child);

        return view('children.edit_child.index')->with('child', $child);
    }

    /**
     * @return $this
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function loadChildInfoForm(Year $year, Child $child)
    {
        $this->authorize('view', $child);

        return view('children.info_child.modal_content')
            ->with('child', $child)
            ->with('year', $year);
    }

    /**
     * @return $this
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function loadEditChildForm(Year $year, Child $child)
    {
        $this->authorize('update', $child);

        return $this->loadEditChildFormForChild($year, $child);
    }

    /**
     * @return $this
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function loadEditFamiliesForm(Request $request, Year $year, Child $child)
    {
        $this->authorize('view', $child);

        return view('children.edit_child.families')
            ->with('child', $child)
            ->with('year', $year);
    }

    /**
     * @return $this
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function loadLinkNewChildFamilyForm(Year $year, Child $child)
    {
        $this->authorize('view', $child);

        return view('children.edit_child.new_family.form')
            ->with('child', $child)
            ->with('all_tariffs_by_id', $year->getAllTariffsById());
    }

    /**
     * @return mixed
     *
     * @throws \Exception
     */
    public function getChildren(Year $year)
    {
        return DataTables::make($year->children()->with('age_group'))->make(true);
    }

    /**
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function submitNewChild(SaveChildRequest $request, Year $year)
    {
        $this->authorize('create_child', $year);
        $data = $request->validated();
        $data = [
            'first_name' => ucfirst($data['first_name']),
            'last_name' => ucfirst($data['last_name']),
            'birth_year' => $data['birth_year'],
            'age_group_id' => $data['age_group_id'],
            'remarks' => $data['remarks'],
            'year_id' => $year->id,
        ];
        $child = Child::create($data);
        $child->save();

        return $child;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function submitEditChildForm(SaveChildRequest $request, Year $year, Child $child)
    {
        $this->authorize('update', $child);
        $data = $request->validated();
        $child->update($data);

        return response()->json(['success' => true]);
    }

    /**
     * @return Family
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function submitLinkNewChildFamilyForm(UpdateFamilyInfoRequest $request, Year $year, Child $child)
    {
        $this->authorize('create_family', $year);
        $family = new Family($request->validated());
        $family->year()->associate($year);
        $family->save();
        $child->families()->syncWithoutDetaching([$family->id => ['year_id' => $year->id]]);

        return $family;
    }

    public function getChildFamilySuggestions(Request $request, Year $year, Child $child)
    {
        $query = $request->input('q');
        $families = $year
            ->families()
            ->search($query)
            ->groupBy('families.id')
            ->with('child_families')
            ->with('children')
            ->whereDoesntHave('children', function ($query) use ($child) {
                $query->where('child_id', '=', $child->id);
            })
            ->get();

        return $families;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|static
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function addChildFamily(Request $request, Year $year, Child $child, Family $family)
    {
        $this->authorize('create_child_family', $year);
        $child->families()->syncWithoutDetaching([$family->id => ['year_id' => $year->id]]);

        return $child->child_families()->where('family_id', '=', $family->id)->firstOrFail();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function removeChildFamily(Request $request, Year $year, Child $child, Family $family)
    {
        $child_family = $child->child_families()->where('family_id', '=', $family->id)->firstOrFail();
        $this->authorize('delete', $child_family);
        $child_family->delete();

        return response()->json(['success' => true]);
    }

    protected function loadEditChildFormForChild(Year $year, Child $child)
    {
        return view('children.edit_child.child_details')
            ->with('child', $child)
            ->with('year', $child->year);
    }
}
