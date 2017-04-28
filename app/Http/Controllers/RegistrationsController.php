<?php

namespace App\Http\Controllers;

use App\ActivityList;
use App\AgeGroup;
use App\ChildFamilyDayRegistration;
use App\ChildFamilyWeekRegistration;
use App\DayPart;
use App\Family;
use App\FamilyWeekRegistration;
use App\PlaygroundDay;
use App\Supplement;
use App\Tariff;
use App\Week;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegistrationsController extends Controller
{
    protected function getLastPlaygroundDayUntil($upper_bound_date)
    {
        // TODO: find correct date
        return PlaygroundDay::find(1);
    }

    public function show()
    {
        return redirect()->route('registrations_for_day',
            ['date' => (new \DateTimeImmutable())->format('Y-m-d')]);
    }

    public function showDate(Request $request, $date)
    {
        if (date_parse($date) === FALSE)
            return $this->show();
        $playground_day = $this->getLastPlaygroundDayUntil($date);

        return view('registrations.index', ['playground_day' => $playground_day]);
    }

    public function showFindFamily(Request $request, $week_id)
    {
        return view('registrations.find_family', [
            'week' => Week::findOrFail($week_id),
            'all_weeks' => Week::all()
        ]);
    }

    public function showEditRegistration(Request $request, $week_id)
    {
        $family_id = $request->input('family_id');
        $family = Family::findOrFail($family_id);
        $week = Week::findOrFail($week_id);
        return view('registrations.edit_week_registration', [
            'family' => $family,
            'week' => $week,
            'all_supplements' => Supplement::all(),
            'all_activity_lists' => ActivityList::all(),
            'all_tariffs_by_id' => Tariff::getAllTariffsById(),
            'all_age_groups' => AgeGroup::all(),
            'all_day_parts' => DayPart::all()
        ]);
    }

    public function submitRegistrationData(Request $request, $week_id, $family_id)
    {
        $week = Week::findOrFail($week_id);
        $family = Family::findOrFail($family_id);
        $family_week_registration = $week->family_week_registrations()
            ->where('family_id', '=', $family_id)->first();

        $data = $request->all();
        $data = FamilyWeekRegistration::cleanRegistrationData($data);
        $tariff = Tariff::findOrFail($data['tariff_id']);
        if (!$family_week_registration) {
            $family_week_registration = new FamilyWeekRegistration([
                'family_id' => $family->id,
                'week_id' => $week->id,
                'tariff_id' => $tariff->id
            ]);
        } else {
            $family_week_registration->tariff()->associate($tariff);
        }
        $family_week_registration->save();
        $children_data = $data['children'];
        $default_day_part = DayPart::getDefaultDayPart();

        foreach ($family->child_families as $child_family) {
            // TODO: check other registrations for $child in $week (e.g. through other families)
            $child = $child_family->child;
            $child_family_week_registration = $family_week_registration
                ->child_family_week_registrations()
                ->where('child_id', '=', $child->id)
                ->first();
            if (!$child_family_week_registration) {
                $child_family_week_registration = new ChildFamilyWeekRegistration([
                    'child_id' => $child->id,
                    'family_id' => $family->id,
                    'week_id' => $week->id
                ]);
            }
            $child_data = $children_data[$child->id];
            if (!$child_data) {
                $child_family_week_registration->whole_week_price = false;
            } else {
                $child_family_week_registration->whole_week_price = $child_data['whole_week_registered'];
            }
            $child_family_week_registration->save();
            foreach ($week->playground_days as $playground_day) {
                $day_data = ($child_data && $child_data['days']) ? $child_data['days'][$playground_day->week_day_id] : null;
                $child_family_day_registration = $child_family_week_registration
                    ->child_family_day_registrations()
                    ->where('week_day_id', '=', $playground_day->week_day_id)
                    ->first();
                if ($child_family_week_registration->whole_week_price || ($day_data && $day_data['registered'])) {
                    if (!$child_family_day_registration) {
                        $child_family_day_registration = new ChildFamilyDayRegistration([
                            'child_id' => $child->id,
                            'family_id' => $family->id,
                            'week_id' => $week->id,
                            'week_day_id' => $playground_day->week_day_id
                        ]);
                    }
                    $day_part = null;
                    $age_group = null;
                    $attended = false;
                    Log::debug("Attended: ".json_encode($day_data));
                    if ($day_data) {
                        $day_part = DayPart::find($day_data['day_part_id']);
                        $age_group = AgeGroup::find($day_data['age_group_id']);
                        if ($day_data['attended']) {
                            $attended = true;
                        }
                    }
                    $child_family_day_registration->day_part()->associate($day_part ? $day_part : $default_day_part);
                    $child_family_day_registration->age_group()->associate($age_group ? $age_group : $child->age_group());
                    $child_family_day_registration->attended = $attended;

                    $child_family_day_registration->save();

                    $supplements_data = $day_data ? $day_data['supplements'] : [];
                    foreach (Supplement::all() as $supplement) {
                        if (key_exists($supplement->id, $supplements_data) && $supplements_data[$supplement->id]['ordered']) {
                            if (!$child_family_day_registration->supplements->contains($supplement)) {
                                $child_family_day_registration->supplements()->attach($supplement);
                            }
                        } else {
                            if ($child_family_day_registration->supplements->contains($supplement)) {
                                $child_family_day_registration->supplements()->detach($supplement);
                            }
                        }
                    }
                } elseif ($child_family_day_registration) {
                    $child_family_day_registration->delete();
                }
            }

            $activity_lists = $child_data ? $child_data['activity_lists'] : [];
            foreach ($activity_lists as $activity_list_id => $activity_list_data) {
                $activity_list = ActivityList::findOrFail($activity_list_id);
                if ($activity_list_data['registered']) {
                    if (!$child_family->activity_lists->contains($activity_list_id)) {
                        $child_family->activity_lists()->attach($activity_list);
                    }
                } else {
                    if ($child_family->activity_lists->contains($activity_list_id)) {
                        $child_family->activity_lists()->detach($activity_list);
                    }
                }
            }
        }
        return $this->getRegistrationData($week_id, $family_id);
    }


    public function getRegistrationData($week_id, $family_id)
    {
        $week = Week::findOrFail($week_id);
        $family = Family::findOrFail($family_id);
        return FamilyWeekRegistration::getRegistrationDataArray($week, $family);
    }

    public function submitRegistrationDataForPrices(Request $request, $week_id, $family_id)
    {
        $week = Week::findOrFail($week_id);
        $family = Family::findOrFail($family_id);
        $data = $request->all();
        $data = FamilyWeekRegistration::cleanRegistrationData($data);
        $data = FamilyWeekRegistration::computeRegistrationPrices($week, $data);
        Log::info("Data children length after computing prices: " . count($data['children']));
        Log::info("Data with prices: " . json_encode($data['children'][1]));
        $data['price_difference'] = FamilyWeekRegistration::computeTotalPriceDifference($family, $week, $data);
        $data['saldo'] = $family->getCurrentSaldo();
        return $data;
    }


}
