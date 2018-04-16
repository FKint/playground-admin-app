<?php

namespace App\Http\Controllers\Internal;

use App\ActivityList;
use App\AgeGroup;
use App\ChildFamilyDayRegistration;
use App\ChildFamilyWeekRegistration;
use App\DayPart;
use App\Family;
use App\FamilyWeekRegistration;
use App\Http\Controllers\Controller;
use App\PlaygroundDay;
use App\Supplement;
use App\Tariff;
use App\Transaction;
use App\Week;
use App\WeekDay;
use App\Year;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class RegistrationsController extends Controller
{
    public static function getLastPlaygroundDayUntil(Year $year, $upper_bound_date)
    {
        $week = $year->weeks()
            ->whereDate('first_day_of_week', '<=', $upper_bound_date->format('Y-m-d'))
            ->orderByDesc('first_day_of_week')
            ->first();
        if (!$week)
            return $year->playground_days()->first();
        $interval = $upper_bound_date->diff(\DateTime::createFromFormat('Y-m-d', $week->first_day_of_week));
        $week_days = $year->week_days()
            ->where('days_offset', '<=', $interval->days)
            ->orderByDesc('days_offset')
            ->get();
        foreach ($week_days as $week_day) {
            $playground_day = $week->playground_days()->where('playground_days.week_day_id', '=', $week_day->id)->first();
            if ($playground_day)
                return $playground_day;
        }
        return $week->playground_days()->first();
    }

    public function show(Year $year)
    {
        $playground_day = RegistrationsController::getLastPlaygroundDayUntil($year, new \DateTimeImmutable());
        return redirect()->route('internal.registrations_for_date', ['date' => $playground_day->date()->format('Y-m-d')]);
    }

    public function showDate(Request $request, Year $year, $date_str)
    {
        $date = \DateTimeImmutable::createFromFormat('Y-m-d', $date_str);
        if (!$date)
            return $this->show($year);
        $playground_day = $year->getPlaygroundDayForDate($date);

        $filter = array();
        $filter['age_group_id'] = $request->input('filter_age_group_id');
        $filter['day_part_id'] = $request->input('filter_day_part_id');
        $filter['supplement_id'] = $request->input('filter_supplement_id');
        $filter['present'] = $request->input('filter_present');

        return view('registrations.index', [
            'playground_day' => $playground_day,
            'date' => $date,
            'selected_menu_item' => 'registrations',
            'filter' => $filter
        ]);
    }

    public function getRegistrations(Year $year, PlaygroundDay $playground_day)
    {
        return DataTables::make(
            $year->child_family_day_registrations()
                ->where([
                    ['week_id', '=', $playground_day->week_id],
                    ['week_day_id', '=', $playground_day->week_day_id]
                ])
                ->with('child')
                ->with('age_group')
                ->with('day_part')
                ->with('supplements')
        )->make(true);
    }

    public function showFindFamily(Request $request, Year $year, Week $week)
    {
        return view('registrations.find_family', [
            'week' => $week,
            'all_weeks' => $year->weeks()
        ]);
    }

    public function showEditRegistration(Request $request, Year $year, Family $family, Week $week)
    {
        $this->removeFamilyWeekRegistrationIfEmpty($week, $family);
        if ($request->has('today')) {
            $today = \DateTimeImmutable::createFromFormat('Y-m-d', $request->input('today'));
        } else {
            $today = new \DateTimeImmutable();
        }

        return view('registrations.edit_week_registration', [
            'family' => $family,
            'week' => $week,
            'today' => $today
        ]);
    }

    private function removeFamilyWeekRegistrationIfEmpty(Week $week, Family $family)
    {
        $family_week_registration = $family->family_week_registrations()->where('week_id', '=', $week->id)->first();
        if (!$family_week_registration)
            return;
        foreach ($family_week_registration->child_family_week_registrations()->get() as $child_family_week_registration) {
            if ($child_family_week_registration->is_empty()) {
                $child_family_week_registration->delete();
            }
        }
        if ($family_week_registration->is_empty()) {
            $family_week_registration->delete();
        }
    }

    private function updateFamilyWeekRegistration(Year $year, Week $week, Family $family, $data)
    {
        $family_week_registration = $family->family_week_registrations()
            ->where('week_id', '=', $week->id)->first();
        if (!$family_week_registration) {
            $family_week_registration = new FamilyWeekRegistration([
                'family_id' => $family->id,
                'week_id' => $week->id,
                'year_id' => $year->id
            ]);
        }

        $tariff = $year->tariffs()->findOrFail($data['tariff_id']);
        $family_week_registration->tariff()->associate($tariff);
        $family_week_registration->save();

        $children_data = $data['children'];
        $default_day_part = $year->getDefaultDayPart();

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
                    if ($day_data) {
                        $day_part = $year->day_parts()->find($day_data['day_part_id']);
                        $age_group = $year->age_groups()->find($day_data['age_group_id']);
                        if ($day_data['attended']) {
                            $attended = true;
                        }
                    }
                    $child_family_day_registration->day_part()->associate($day_part ? $day_part : $default_day_part);
                    $child_family_day_registration->age_group()->associate($age_group ? $age_group : $child->age_group());
                    $child_family_day_registration->attended = $attended;

                    $child_family_day_registration->save();

                    $supplements_data = $day_data ? $day_data['supplements'] : [];
                    foreach ($year->supplements as $supplement) {
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
                $activity_list = $year->activity_lists()->findOrFail($activity_list_id);
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

    private function updateTransaction(Year $year, Family $family, $data, $expected_money)
    {
        $received_money = $data['received_money'];
        if (!$received_money) {
            $received_money = 0;
        }
        $transaction = new Transaction(array(
            'amount_paid' => $received_money,
            'amount_expected' => $expected_money,
            'remarks' => $data['transaction_remarks'],
            'year_id' => $year->id
        ));
        $admin_session = $year->getActiveAdminSession();
        $transaction->admin_session()->associate($admin_session);
        $transaction->family()->associate($family);
        $transaction->save();
    }

    public function submitRegistrationData(Request $request, Year $year, Week $week, Family $family)
    {
        $this->removeFamilyWeekRegistrationIfEmpty($week, $family);
        $data = $request->all();
        $data = FamilyWeekRegistration::cleanRegistrationData($data);

        $old_saldo = $family->getCurrentSaldo();

        $this->updateFamilyWeekRegistration($year, $week, $family, $data);

        $new_saldo = $family->getCurrentSaldo();

        $this->updateTransaction($year, $family, $data, $new_saldo - $old_saldo);

        return $this->getRegistrationData($year, $week, $family);
    }


    public function getRegistrationData(Year $year, Week $week, Family $family)
    {
        $this->removeFamilyWeekRegistrationIfEmpty($week, $family);
        $data = FamilyWeekRegistration::getRegistrationDataArray($week, $family);
        $data['price_difference'] = 0;
        $data['saldo'] = $family->getCurrentSaldo();
        return $data;
    }


    public function submitRegistrationDataForPrices(Request $request, Year $year, Week $week, Family $family)
    {
        $this->removeFamilyWeekRegistrationIfEmpty($week, $family);
        $data = $request->all();
        $data = FamilyWeekRegistration::cleanRegistrationData($data);
        $data = FamilyWeekRegistration::computeRegistrationPrices($week, $data);
        $data['price_difference'] = FamilyWeekRegistration::computeTotalPriceDifference($family, $week, $data);
        $data['saldo'] = $family->getCurrentSaldo();
        return $data;
    }

}
