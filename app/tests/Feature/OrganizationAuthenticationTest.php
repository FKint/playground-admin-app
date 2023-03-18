<?php

namespace Tests\Feature;

use App\ActivityList;
use App\AgeGroup;
use App\Child;
use App\ChildFamily;
use App\DayPart;
use App\Family;
use App\Organization;
use App\PlaygroundDay;
use App\Tariff;
use App\User;
use App\Week;
use App\WeekDay;
use App\Year;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class OrganizationAuthenticationTest extends TestCase
{
    private $actualUser;
    private $actualOrganization;
    private $actualYear;
    private $actualFamily;
    private $emptyFamily;
    private $actualChild;
    private $immutableChildFamily;
    private $actualChildFamily;
    private $childFamilyOnList;
    private $actualList;
    private $actualTariffs;
    private $actualAgeGroup;
    private $actualWeekDays;
    private $actualWeeks;
    private $actualPlaygroundDay;
    private $actualDayPart;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actualOrganization = Organization::factory()->create();
        $this->actualUser = User::factory()->for($this->actualOrganization)->create();
        $this->actualYear = Year::factory()->for($this->actualOrganization)->create();
        $this->actualTariffs = Tariff::factory()->count(2)->for($this->actualYear)->create();
        $this->actualFamily = Family::factory()->for($this->actualYear)->for($this->actualTariffs[0])->create();
        $this->emptyFamily = Family::factory()->for($this->actualYear)->for($this->actualTariffs[1])->create();
        $this->actualList = ActivityList::factory()->for($this->actualYear)->create();
        $this->actualAgeGroup = AgeGroup::factory()->for($this->actualYear)->create();

        $this->actualWeekDays = array_map(
            function ($offset) {
                return WeekDay::factory()->for($this->actualYear)->create(['days_offset' => $offset]);
            },
            [0, 1, 2, 3, 4]
        );

        $this->actualWeeks = Week::factory()->count(8)->for($this->actualYear)->create();

        $this->actualPlaygroundDay = PlaygroundDay::factory()
            ->for($this->actualYear)
            ->for($this->actualWeeks[2])
            ->for($this->actualWeekDays[1])
            ->create();

        $this->actualChild = Child::factory()->for($this->actualYear)->for($this->actualAgeGroup)->create();
        $this->actualChildFamily = ChildFamily::factory()->for($this->actualYear)->for($this->actualChild)->for($this->actualFamily)->create();
        $this->otherChild = Child::factory()->for($this->actualYear)->for($this->actualAgeGroup)->create();
        $this->immutableChildFamily = ChildFamily::factory()->for($this->actualYear)->for($this->otherChild)->for($this->actualFamily)->create();

        $this->actualDayPart = DayPart::factory()->for($this->actualYear)->create([
            'default' => true,
        ]);

        $child_for_list = Child::factory()->for($this->actualYear)->for($this->actualAgeGroup)->create();
        $this->childFamilyOnList = ChildFamily::factory()->for($this->actualYear)->for($this->actualFamily)->for($child_for_list)->create();
        $this->actualList->child_families()->syncWithoutDetaching([$this->childFamilyOnList->id => ['year_id' => $this->actualYear->id]]);
    }

    public function readOnlyApiRoutes()
    {
        return [
            // Datatables
            'age_groups' => [function () {
                return route('api.datatables.age_groups', ['year' => $this->actualYear]);
            }],
            'supplements' => [function () {
                return route('api.datatables.supplements', ['year' => $this->actualYear]);
            }],
            'day_parts' => [function () {
                return route('api.datatables.day_parts', ['year' => $this->actualYear]);
            }],
            'tariffs' => [function () {
                return route('api.datatables.tariffs', ['year' => $this->actualYear]);
            }],
            'children' => [function () {
                return route('api.datatables.children', ['year' => $this->actualYear]);
            }],
            'families' => [function () {
                return route('api.datatables.families', ['year' => $this->actualYear]);
            }],
            'family_children' => [function () {
                return route('api.datatables.family_children', ['year' => $this->actualYear, 'family' => $this->actualFamily]);
            }],
            'family_transactions' => [function () {
                return route('api.datatables.family_transactions', ['year' => $this->actualYear, 'family' => $this->actualFamily]);
            }],
            'playground_day_registrations' => [function () {
                return route('api.datatables.registrations', ['year' => $this->actualYear, 'playground_day' => $this->actualPlaygroundDay]);
            }],
            'admin_sessions' => [function () {
                return route('api.datatables.admin_sessions', ['year' => $this->actualYear]);
            }],
            'lists' => [function () {
                return route('api.datatables.lists', ['year' => $this->actualYear]);
            }],
            'list_participants' => [function () {
                return route('api.datatables.list_participants', ['year' => $this->actualYear, 'list' => $this->actualList]);
            }],
            // Typeahead
            'family_suggestions_for_child' => [function () {
                return route('api.typeahead.family_suggestions_for_child', ['year' => $this->actualYear, 'child' => $this->actualChild]);
            }],
            'child_suggestions_for_family' => [function () {
                return route('api.typeahead.child_suggestions_for_family', ['year' => $this->actualYear, 'family' => $this->actualFamily]);
            }],
            'family_suggestions' => [function () {
                return route('api.typeahead.family_suggestions', ['year' => $this->actualYear]);
            }],
            'child_family_suggestions_for_list' => [function () {
                return route('api.typeahead.child_family_suggestions_for_list', ['year' => $this->actualYear, 'list' => $this->actualList]);
            }],
            // Readonly Ajax
            'registration_data' => [function () {
                return route('api.registration_data', ['year' => $this->actualYear, 'week' => $this->actualWeeks[0], 'family' => $this->actualFamily]);
            }],
        ];
    }

    public function writeApiRoutes()
    {
        return [
            // Post requests
            'create_new_child' => [function () {
                return [
                    'route' => route('api.create_new_child', ['year' => $this->actualYear]),
                    'data' => [
                        'first_name' => 'Jos',
                        'last_name' => 'De Rudder',
                        'birth_year' => 2010,
                        'age_group_id' => $this->actualAgeGroup->id,
                        'remarks' => '',
                    ],
                ];
            }],
            'add_family_to_child' => [function () {
                return [
                    'route' => route('api.add_family_to_child', [
                        'year' => $this->actualYear,
                        'child' => $this->actualChild,
                        'family' => $this->actualFamily,
                    ]),
                    'data' => [],
                ];
            }],
            'remove_family_from_child' => [function () {
                return [
                    'route' => route('api.remove_family_from_child', [
                        'year' => $this->actualYear,
                        'child' => $this->actualChildFamily->child->id,
                        'family' => $this->actualChildFamily->family->id,
                    ]),
                    'data' => [],
                ];
            }],
            'add_child_to_family' => [function () {
                return [
                    'route' => route('api.add_child_to_family', [
                        'year' => $this->actualYear,
                        'child' => $this->otherChild->id,
                        'family' => $this->emptyFamily->id,
                    ]),
                    'data' => [],
                ];
            }],
            'submit_registration_data' => [function () {
                return [
                    'route' => route('api.submit_registration_data', [
                        'year' => $this->actualYear,
                        'week' => $this->actualWeeks[0],
                        'family' => $this->emptyFamily,
                    ]),
                    'data' => [
                        'children' => [],
                        'tariff_id' => $this->actualTariffs[0]->id,
                        'received_money' => 0,
                        'transaction_remarks' => 0,
                    ],
                ];
            }],
            'simulate_submit_registration_data' => [function () {
                return [
                    'route' => route('api.simulate_submit_registration_data', [
                        'year' => $this->actualYear,
                        'week' => $this->actualWeeks[0],
                        'family' => $this->emptyFamily,
                    ]),
                    'data' => [
                        'children' => [],
                        'tariff_id' => $this->actualTariffs[0]->id,
                        'received_money' => 0,
                        'transaction_remarks' => 0,
                    ],
                ];
            }],
            'add_participant_to_list' => [function () {
                return [
                    'route' => route('api.add_participant_to_list', [
                        'year' => $this->actualYear,
                        'activity_list' => $this->actualList,
                        'child_family' => $this->immutableChildFamily,
                    ]),
                    'data' => [],
                ];
            }],
            'remove_participant_from_list' => [function () {
                return [
                    'route' => route('api.remove_participant_from_list', [
                        'year' => $this->actualYear,
                        'activity_list' => $this->actualList,
                        'child_family' => $this->childFamilyOnList,
                    ]),
                    'data' => [],
                ];
            }],
        ];
    }

    /**
     * @dataProvider readOnlyApiRoutes
     *
     * @param mixed $routeFunction
     */
    public function testApiNotLoggedInReadOnly($routeFunction)
    {
        $this->assertGuest();
        $url = $routeFunction->bindTo($this)();
        $this->get($url)->assertStatus(401);
    }

    /**
     * @dataProvider writeAPIRoutes
     *
     * @param mixed $routeFunction
     */
    public function testApiNotLoggedInWrite($routeFunction)
    {
        $this->assertGuest();
        $request_data = $routeFunction->bindTo($this)();
        $response = $this->postJson($request_data['route'], $request_data['data'])->assertStatus(401);
    }

    /**
     * @dataProvider readOnlyApiRoutes
     *
     * @param mixed $routeFunction
     */
    public function testApiNotAuthorizedReadOnly($routeFunction)
    {
        $this->actingAs(User::factory()->create());
        $url = $routeFunction->bindTo($this)();
        $this->get($url)->assertStatus(403);
    }

    /**
     * @dataProvider writeAPIRoutes
     *
     * @param mixed $routeFunction
     */
    public function testApiNotAuthorizedWrite($routeFunction)
    {
        $this->actingAs(User::factory()->create());
        $request_data = $routeFunction->bindTo($this)();
        $response = $this->postJson($request_data['route'], $request_data['data'])->assertStatus(403);
    }

    /**
     * @dataProvider readOnlyApiRoutes
     *
     * @param mixed $routeFunction
     */
    public function testApiAuthorizedReadOnly($routeFunction)
    {
        $this->actingAs($this->actualUser);
        $url = $routeFunction->bindTo($this)();
        $this->get($url)->assertSuccessful();
    }

    /**
     * @dataProvider writeAPIRoutes
     *
     * @param mixed $routeFunction
     */
    public function testApiAuthorizedWrite($routeFunction)
    {
        $this->actingAs($this->actualUser);
        $request_data = $routeFunction->bindTo($this)();
        $response = $this->postJson($request_data['route'], $request_data['data'])->assertSuccessful();
    }
}
