<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class FamilyWeekRegistration extends Model
{
    protected $fillable = ['family_id', 'week_id', 'tariff_id', 'year_id'];

    public function tariff()
    {
        return $this->belongsTo(Tariff::class);
    }

    public function week()
    {
        return $this->belongsTo(Week::class);
    }

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function child_family_week_registrations()
    {
        return ChildFamilyWeekRegistration::where([
            ['family_id', '=', $this->family_id],
            ['week_id', '=', $this->week_id],
        ]);
    }

    public function is_empty()
    {
        return $this->child_family_week_registrations()->count() == 0;
    }

    public function getTotalWeekPrice()
    {
        $registration_data = FamilyWeekRegistration::getRegistrationDataArray($this->week, $this->family);
        return FamilyWeekRegistration::computeTotalWeekPrice($registration_data);
    }
    /**
     * @return [
     *  'children' => array([
     *      int => [
     *          'days' => array([int => array([
     *              'supplements' => array([int => array([
     *                  'ordered' => bool,
     *                  'price' => decimal,
     *              ])]),
     *              'registered' => bool,
     *              'age_group_id' => int,
     *              'day_part_id' => int,
     *              'attended' => bool,
     *              'day_price' => decimal,
     *          ])]),
     *          'activity_lists' => array([
     *              'registered' => bool,
     *              'price' => decimal,
     *          ]),
     *          'whole_week_registered' => bool,
     *          'whole_week_price' => decimal,
     *      ]
     *  ]),
     *  'tariff_id' => int,
     * ]
     */
    public static function getRegistrationDataArray(Week $week, Family $family)
    {
        $family_week_registration = $family->family_week_registrations()
            ->where('week_id', '=', $week->id)
            ->first();
        $result = [
            'children' => [],
        ];
        $default_day_part = $week->year->getDefaultDayPart();
        $result['tariff_id'] = $family_week_registration ? $family_week_registration->tariff_id : $family->tariff_id;
        foreach ($family->child_families as $child_family) {
            $child = $child_family->child;
            $child_data = [
                'days' => [],
                'activity_lists' => [],
            ];
            $child_family_week_registration = $family_week_registration ?
            $family_week_registration
                ->child_family_week_registrations()
                ->where('child_id', '=', $child->id)
                ->first()
            : null;
            $child_data['whole_week_registered'] = (bool) $child_family_week_registration && $child_family_week_registration->whole_week_price;
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
                    $day_data['attended'] = (bool)$child_family_day_registration->attended;
                    foreach ($child_family_day_registration->supplements as $supplement) {
                        $day_data['supplements'][$supplement->id] = [
                            'ordered' => true,
                        ];
                    }
                } else {
                    $day_data['registered'] = false;
                    $day_data['age_group_id'] = $child->age_group_id;
                    $day_data['day_part_id'] = $default_day_part->id;
                    $day_data['attended'] = false;
                }
                $child_data['days'][$playground_day->week_day_id] = $day_data;
            }
            foreach ($child_family->activity_lists as $activity_list) {
                $child_data['activity_lists'][$activity_list->id] = [
                    'registered' => true,
                ];
            }
            $result['children'][$child->id] = $child_data;
        }
        FamilyWeekRegistration::computeRegistrationPrices($week, $result);
        return $result;
    }

    protected static function computeWeekRegistrationPrices($week, &$week_registration_data)
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
                // TODO(fkint): verify that following line is needed/useful
                $week_registration_data['children'][$child_id]['days'][$playground_day->week_day_id]['registered'] = false;
            }
        }
        foreach ($not_week_children as $nb_days => $child_ids) {
            sort($child_ids);
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

    protected static function setToBoolean(&$value)
    {
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    protected static function cleanRegistrationData(&$data)
    {
        foreach ($data['children'] as &$child_data) {
            FamilyWeekRegistration::setToBoolean($child_data['whole_week_registered']);
            foreach ($child_data['days'] as &$day_data) {
                FamilyWeekRegistration::setToBoolean($day_data['registered']);
                FamilyWeekRegistration::setToBoolean($day_data['attended']);
                $registered_for_day_or_week = $child_data['whole_week_registered'] || $day_data['registered'];
                $day_data['registered'] = !$child_data['whole_week_registered'] && $day_data['registered'];
                $day_data['attended'] = $registered_for_day_or_week && $day_data['attended'];
                foreach ($day_data['supplements'] as &$supplement) {
                    FamilyWeekRegistration::setToBoolean($supplement['ordered']);
                    $supplement['ordered'] = $registered_for_day_or_week && $supplement['ordered'];
                }
            }
            if (!isset($child_data['activity_lists'])) {
                $child_data['activity_lists'] = [];
            }
            foreach ($child_data['activity_lists'] as &$activity_list_data) {
                FamilyWeekRegistration::setToBoolean($activity_list_data['registered']);
            }
        }
        return $data;
    }

    protected static function computeRegistrationPrices($week, &$week_registration_data)
    {
        FamilyWeekRegistration::computeWeekRegistrationPrices($week, $week_registration_data);
        FamilyWeekRegistration::computeSupplementPrices($week_registration_data);
        FamilyWeekRegistration::computeActivityListPrices($week_registration_data);
        return $week_registration_data;
    }

    protected static function computeSupplementPrices(&$week_registration_data)
    {
        foreach ($week_registration_data['children'] as $child_id => &$child_data) {
            foreach ($child_data['days'] as $day_id => &$day_data) {
                foreach ($day_data['supplements'] as $supplement_id => &$supplement_data) {
                    if (!$child_data['whole_week_registered'] && !$day_data['registered']) {
                        $supplement_data['ordered'] = false;
                    }
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

    protected static function computeActivityListPrices(&$week_registration_data)
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

    protected static function computeTotalWeekPrice($week_registration_data_with_prices)
    {
        $total = 0;
        foreach ($week_registration_data_with_prices['children'] as $child_id => $child_data) {
            $total += $child_data['whole_week_price'];
            foreach ($child_data['days'] as $day_data) {
                $total += $day_data['day_price'];
                foreach ($day_data['supplements'] as $supplement_data) {
                    $total += $supplement_data['price'];
                }
            }
        }
        return $total;
    }

    protected static function computeWeekPriceDifference($family, $week, $week_registration_data_with_prices)
    {
        $current_week_registration_data_with_prices = FamilyWeekRegistration::getRegistrationDataArray($week, $family);
        $current_week_price = FamilyWeekRegistration::computeTotalWeekPrice($current_week_registration_data_with_prices);

        $new_week_price = FamilyWeekRegistration::computeTotalWeekPrice($week_registration_data_with_prices);

        return $new_week_price - $current_week_price;
    }

    protected static function computeTotalPriceDifference($family, $week, $week_registration_data_with_prices)
    {
        $week_difference = FamilyWeekRegistration::computeWeekPriceDifference($family, $week, $week_registration_data_with_prices);
        $activity_difference = FamilyWeekRegistration::computeActivityListPriceDifference($family, $week_registration_data_with_prices);
        return $week_difference + $activity_difference;
    }

    protected static function computeActivityListPriceDifference($family, $week_registration_data_with_prices)
    {
        // Positive if price is higher for $week_registration_data_with_prices than the data
        $total_difference = 0;
        foreach ($week_registration_data_with_prices['children'] as $child_id => $child_data) {
            $child = Child::findOrFail($child_id);
            $child_family = $child->child_families()->where('family_id', '=', $family->id)->first();
            foreach ($child_data['activity_lists'] as $activity_list_id => $activity_list_data) {
                $current_activity_registration = $child_family->activity_lists()->where('activity_lists.id', '=', $activity_list_id)->first();
                $current_activity_price = $activity_list_data['price'];
                $difference = $current_activity_price -
                    ($current_activity_registration ? $current_activity_registration->price : 0);
                $total_difference += $difference;
            }
        }
        return $total_difference;
    }
}
