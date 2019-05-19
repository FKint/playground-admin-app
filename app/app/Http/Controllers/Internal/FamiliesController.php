<?php

namespace App\Http\Controllers\Internal;

use App\Child;
use App\Family;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveChildRequest;
use App\Http\Requests\UpdateFamilyInfoRequest;
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

    public function showChildFamilyInvoicePdf(Request $request, Year $year, Family $family, Child $child)
    {
        $view = $this->showChildFamilyInvoice($year, $family, $child);
        if ($request->has('html') && $request->input('html')) {
            return $view;
        }

        return \PDF::loadHtml($view->render())
            ->download('Uitnodiging tot betaling - '.$year->title.' '.$family->id.' '.$child->id.' '.$child->last_name.' '.$child->first_name.'.pdf');
    }

    /**
     * @param Year $year
     *
     * @throws \Exception
     *
     * @return mixed
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
            ->whereDoesntHave('families', function ($query) use ($family) {
                $query->where('family_id', '=', $family->id);
            })
            ->limit(5)
            ->get();

        return $children;
    }

    public function getFamilySuggestions(Request $request, Year $year)
    {
        $query = $request->input('q');

        return $year->families()
            ->search($query)
            ->groupBy('families.id')
            ->with('children')
            ->get();
    }

    public function addChildToFamily(Request $request, Year $year, Family $family, Child $child)
    {
        $family->children()->syncWithoutDetaching([$child->id => ['year_id' => $year->id]]);
        $family->refresh();

        return $family;
    }

    protected function showChildFamilyInvoice(Year $year, Family $family, Child $child)
    {
        $reference = $year->title.'-'.$family->id.'-'.$child->id;
        $invoiceEntries = [];
        $activities = $family->child_families()
            ->where('child_id', $child->id)
            ->firstOrFail()
            ->activity_lists()
            ->where('price', '>', 0)
            ->get()
            ->mapWithKeys(function ($activity) {
                return [$activity->id => $activity];
            })->all();
        $invoicedActivities = array_fill_keys(array_keys($activities), false);
        $tariff = $family->tariff;
        foreach ($year->weeks as $week) {
            $familyRegistrationData = \App\FamilyWeekRegistration::getRegistrationDataArray($week, $family);
            $childRegistrationData = $familyRegistrationData['children'][$child->id];
            if ($childRegistrationData['whole_week_registered']) {
                $weekEntry = [
                    'from' => $week->first_day(),
                    'until' => $week->last_day(),
                    'registration_price' => $childRegistrationData['whole_week_price'],
                    'supplements' => $year->supplements()->get()->mapWithKeys(function ($supplement) {
                        return [$supplement->id => 0];
                    })->toArray(),
                    'other' => ['total' => 0, 'items' => []],
                ];
                foreach ($childRegistrationData['days'] as $weekDayId => $dayData) {
                    foreach ($dayData['supplements'] as $supplementId => $supplementData) {
                        if ($supplementData['ordered']) {
                            $weekEntry['supplements'][$supplementId] += $supplementData['price'];
                        }
                    }
                }
                foreach ($activities as $activityId => $activity) {
                    if ($invoicedActivities[$activityId]) {
                        continue;
                    }
                    if (is_null($activity->date)) {
                        continue;
                    }
                    if ($activity->date < $week->first_day()->date() || $activity->date >= $week->last_day()->date()->addDay()) {
                        continue;
                    }
                    $invoicedActivities[$activityId] = true;
                    $weekEntry['other']['total'] += $activity->price;
                    $weekEntry['other']['items'][] = $activity;
                }
                $invoiceEntries[] = $weekEntry;
            } else {
                foreach ($childRegistrationData['days'] as $weekDayId => $dayData) {
                    $playgroundDay = $week->playground_days()->where('week_day_id', $weekDayId)->firstOrFail();
                    $nonEmpty = $dayData['registered'];
                    $dayEntry = [
                        'from' => $playgroundDay,
                        'registration_price' => $dayData['day_price'],
                        'supplements' => $year->supplements()->get()->mapWithKeys(function ($supplement) {
                            return [$supplement->id => 0];
                        })->toArray(),
                        'other' => ['total' => 0, 'items' => []],
                    ];
                    foreach ($dayData['supplements'] as $supplementId => $supplementData) {
                        if ($supplementData['ordered']) {
                            $nonEmpty = true;
                            $dayEntry['supplements'][$supplementId] += $supplementData['price'];
                        }
                    }
                    foreach ($activities as $activityId => $activity) {
                        if ($invoicedActivities[$activityId]) {
                            continue;
                        }
                        \Log:: info('activity: '.json_encode($activity));
                        if (is_null($activity->date)) {
                            continue;
                        }
                        if (!$activity->date->isSameDay($playgroundDay->date())) {
                            continue;
                        }
                        $invoicedActivities[$activityId] = true;
                        $dayEntry['other']['total'] += $activity->price;
                        $dayEntry['other']['items'][] = $activity;
                        $nonEmpty = true;
                    }
                    if ($nonEmpty) {
                        $invoiceEntries[] = $dayEntry;
                    }
                }
            }
        }
        foreach ($activities as $activityId => $activity) {
            if ($invoicedActivities[$activityId]) {
                continue;
            }
            $invoiceEntries[] = [
                'other' => ['total' => $activity->price, 'items' => [$activity]],
            ];
        }
        $globalTotal = 0;
        $footnotesRequired = false;
        foreach ($invoiceEntries as &$entry) {
            $entryTotal = 0;
            if (isset($entry['registration_price'])) {
                $entryTotal += $entry['registration_price'];
            }
            $entryTotal += $entry['other']['total'];
            if (isset($entry['supplements'])) {
                foreach ($entry['supplements'] as $supplementId => $supplementPrice) {
                    $entryTotal += $supplementPrice;
                }
            }
            $entry['total'] = $entryTotal;
            $globalTotal += $entryTotal;

            if (count($entry['other']['items'])) {
                $footnotesRequired = true;
            }
        }
        \Log::info('Computed invoice total for family: '.$family->id.' child: '.$child->id.'. Total: '.$globalTotal);

        return view('families.invoice.pdf', [
            'child' => $child,
            'family' => $family,
            'year' => $year,
            'reference' => $reference,
            'invoice' => $invoiceEntries,
            'total' => $globalTotal,
            'footnotesRequired' => $footnotesRequired,
        ]);
    }
}
