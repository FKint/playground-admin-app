<?php

namespace App\Http\Controllers;

use App\ActivityList;
use App\AgeGroup;
use App\Day;
use App\DayPart;
use App\Family;
use App\FamilyWeekRegistration;
use App\PlaygroundDay;
use App\Supplement;
use App\Tariff;
use App\Week;
use Illuminate\Http\Request;

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

    public function getRegistrationData(Request $request, $week_id, $family_id)
    {
        $week = Week::findOrFail($week_id);
        $family = Family::findOrFail($family_id);
        $family_week_registration = $week->family_week_registrations()
            ->where('family_id', '=', $family_id)->first();
        $result = [
            'children' => []
        ];
        $default_day_part = DayPart::getDefaultDayPart();
        $result['tariff_id'] = $family_week_registration ? $family_week_registration->tariff_id : $family->tariff_id;
        foreach ($family->child_families as $child_family) {
            $child = $child_family->child;
            $child_data = [
                'days' => [],
                'activity_lists' => []
            ];
            $child_family_week_registration = $family_week_registration ?
                $family_week_registration
                    ->child_family_week_registrations()
                    ->where('child_id', '=', $child->id)
                    ->first()
                : null;
            $child_data['whole_week_registered'] = $child_family_week_registration && $child_family_week_registration->whole_week_price;
            foreach ($week->playground_days as $playground_day) {
                $day_data = ['supplements' => []];
                $child_family_day_registration = $child_family_week_registration
                    ? $child_family_week_registration
                        ->child_family_day_registrations()
                        ->where('week_day_id', '=', $playground_day->week_day_id)
                        ->first()
                    : null;
                if ($child_family_day_registration) {
                    $day_data['registered'] = !$child_data['whole_week_registered'];
                    $day_data['age_group_id'] = $child_family_day_registration->age_group_id;
                    $day_data['day_part_id'] = $child_family_day_registration->day_part_id;
                    foreach ($child_family_day_registration->supplements as $supplement) {
                        $day_data['supplements'][$supplement->id] = [
                            'ordered' => true,
                            'price' => $supplement->price
                        ];
                    }
                } else {
                    $day_data['registered'] = false;
                    $day_data['age_group_id'] = $child->age_group_id;
                    $day_data['day_part_id'] = $default_day_part->id;
                }
                $child_data['days'][$playground_day->week_day_id] = $day_data;
            }
            foreach ($child_family->activity_lists as $activity_list) {
                $child_data['activity_lists'][$activity_list->id] = [
                    'price' => $activity_list->price,
                    'registered' => true
                ];
            }
            $result['children'][$child->id] = $child_data;
        }
        $this->computeWeekRegistrationPrices($week, $result);
        return $result;
    }

    protected function computeWeekRegistrationPrices($week, &$week_registration_data)
    {
        $tariff = Tariff::findOrFail($week_registration_data['tariff_id']);
        $week_children = [];
        $not_week_children = [];
        foreach ($week_registration_data['children'] as $child_id => $child_data) {
            if ($child_data['whole_week_registered']) {
                $week_children[] = $child_id;
            } else {
                $nb_days = 0;
                foreach ($child_data['days'] as $day_data) {
                    if ($day_data['registered']) {
                        $nb_days++;
                    }
                }
                if (!array_key_exists($nb_days, $not_week_children)) {
                    $not_week_children[$nb_days] = [$child_id];
                } else {
                    $not_week_children[$nb_days][] = $child_id;
                }
            }
        }
        krsort($not_week_children);

        $registered_for_day = [];
        foreach ($week->playground_days as $playground_day) {
            $registered_for_day[$playground_day->week_day_id] = count($week_children) > 0;
        }

        $first_week_child = true;
        foreach ($week_children as $child_id) {
            if ($first_week_child) {
                $price = $tariff->week_first_child;
            } else {
                $price = $tariff->week_later_children;
            }
            $week_registration_data['children'][$child_id]['whole_week_price'] = $price;
            $first_week_child = false;
            foreach ($week->playground_days as $playground_day) {
                $week_registration_data['children'][$child_id]['days'][$playground_day->week_day_id]['day_price'] = 0;
            }
        }

        foreach ($not_week_children as $nb_days => $child_ids) {
            foreach ($child_ids as $child_id) {
                $week_registration_data['children'][$child_id]['whole_week_price'] = 0;
                foreach ($week->playground_days as $playground_day) {
                    if ($week_registration_data['children'][$child_id]['days'][$playground_day->week_day_id]['registered']) {
                        if (!$registered_for_day[$playground_day->week_day_id]) {
                            $price = $tariff->day_first_child;
                        } else {
                            $price = $tariff->day_later_children;
                        }
                        $registered_for_day[$playground_day->week_day_id] = true;
                    } else {
                        $price = 0;
                    }
                    $week_registration_data['children'][$child_id]['days'][$playground_day->week_day_id]['day_price'] = $price;
                }
            }
        }
    }
}
