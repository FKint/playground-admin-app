<?php

namespace App\Http\Controllers;

use App\ActivityList;
use App\AgeGroup;
use App\ChildFamilyDayRegistration;
use App\ChildFamilyWeekRegistration;
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

    public function submitRegistrationData(Request $request, $week_id, $family_id)
    {
        $week = Week::findOrFail($week_id);
        $family = Family::findOrFail($family_id);
        $family_week_registration = $week->family_week_registrations()
            ->where('family_id', '=', $family_id)->first();

        $data = $request->all();
        RegistrationsController::cleanRegistrationData($data);
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
                            'week_day_id' => $playground_day->week_day_id,
                        ]);
                    }
                    $day_part = null;
                    $age_group = null;
                    $attended = false;
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
            $child_data['whole_week_registered'] = (bool)$child_family_week_registration && $child_family_week_registration->whole_week_price;
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
                    'registered' => true
                ];
            }
            $result['children'][$child->id] = $child_data;
        }
        $this->computeRegistrationPrices($week, $result);
        return $result;
    }

    public function submitRegistrationDataForPrices(Request $request, $week_id, $family_id)
    {
        $week = Week::findOrFail($week_id);
        Family::findOrFail($family_id);
        $data = $request->all();
        RegistrationsController::cleanRegistrationData($data);
        $this->computeRegistrationPrices($week, $data);
        return $data;
    }

    protected static function setToBoolean(&$value)
    {
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    protected static function cleanRegistrationData(&$data)
    {
        foreach ($data['children'] as &$child_data) {
            RegistrationsController::setToBoolean($child_data['whole_week_registered']);
            foreach ($child_data['days'] as &$day_data) {
                RegistrationsController::setToBoolean($day_data['registered']);
                RegistrationsController::setToBoolean($day_data['attended']);
                foreach ($day_data['supplements'] as &$supplement) {
                    RegistrationsController::setToBoolean($supplement['ordered']);
                }
            }
            foreach ($child_data['activity_lists'] as &$activity_list_data) {
                RegistrationsController::setToBoolean($activity_list_data['registered']);
            }
        }
    }


    protected function computeRegistrationPrices($week, &$week_registration_data)
    {
        $this->computeWeekRegistrationPrices($week, $week_registration_data);
        $this->computeSupplementPrices($week_registration_data);
        $this->computeActivityListPrices($week_registration_data);
    }

    protected function computeSupplementPrices(&$week_registration_data)
    {
        foreach ($week_registration_data['children'] as $child_id => &$child_data) {
            foreach ($child_data['days'] as $day_id => &$day_data) {
                foreach ($day_data['supplements'] as $supplement_id => &$supplement_data) {
                    $supplement = Supplement::findOrFail($supplement_id);
                    $price = 0;
                    if ($supplement_data['ordered']) {
                        $price = $supplement->price;
                    }
                    $supplement_data['price'] = $price;
                }
            }
        }
    }

    protected function computeActivityListPrices(&$week_registration_data)
    {
        foreach ($week_registration_data['children'] as $child_id => &$child_data) {
            foreach ($child_data['activity_lists'] as $activity_list_id => &$activity_list_data) {
                $activity_list = ActivityList::findOrFail($activity_list_id);
                $price = 0;
                if ($activity_list_data['registered']) {
                    $price = $activity_list->price;
                }
                $activity_list_data['price'] = $price;
            }
        }
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
                $week_registration_data['children'][$child_id]['days'][$playground_day->week_day_id]['registered'] = false;
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
