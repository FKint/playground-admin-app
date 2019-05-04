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
    private $actualList;
    private $actualTariffs;
    private $actualAgeGroup;
    private $actualWeekDays;
    private $actualWeeks;
    private $actualPlaygroundDay;
    private $actualDayPart;

    private $readOnlyAPIRoutes;
    private $writeAPIRoutes;

    public function setUp()
    {
        parent::setUp();
        $this->actualOrganization = factory(Organization::class)->create();
        $this->actualUser = factory(User::class)->create(['organization_id' => $this->actualOrganization->id]);
        $this->actualYear = factory(Year::class)->create(['organization_id' => $this->actualOrganization->id]);
        $this->actualFamily = factory(Family::class)->create(['year_id' => $this->actualYear->id]);
        $this->emptyFamily = factory(Family::class)->create(['year_id' => $this->actualYear->id]);
        $this->actualList = factory(ActivityList::class)->create(['year_id' => $this->actualYear->id]);

        $this->actualTariffs = array_map(function ($o) {
            return Tariff::findOrFail($o['id']);
        }, factory(Tariff::class, 2)->create(['year_id' => $this->actualYear->id])->toArray());
        $this->actualAgeGroup = factory(AgeGroup::class)->create(['year_id' => $this->actualYear->id]);

        $this->actualWeekDays = array_map(
            function ($offset) {
                return factory(WeekDay::class)->create(['year_id' => $this->actualYear->id, 'days_offset' => $offset]);
            }, [0, 1, 2, 3, 4]);

        $this->actualWeeks = array_map(function ($o) {
            return Week::findOrFail($o['id']);
        }, factory(Week::class, 8)->create(['year_id' => $this->actualYear->id])->toArray());

        $this->actualPlaygroundDay = factory(PlaygroundDay::class)->create([
            'year_id' => $this->actualYear->id,
            'week_id' => $this->actualWeeks[array_rand($this->actualWeeks)]->id,
            'week_day_id' => $this->actualWeekDays[array_rand($this->actualWeekDays)]->id,
        ]);

        $this->actualChild = factory(Child::class)->create([
            'year_id' => $this->actualYear->id,
        ]);
        $this->actualChildFamily = factory(ChildFamily::class)->create(['year_id' => $this->actualYear->id]);
        $this->otherChild = factory(Child::class)->create(['year_id' => $this->actualYear->id]);
        $this->immutableChildFamily = factory(ChildFamily::class)->create(['year_id' => $this->actualYear->id]);

        $this->actualDayPart = factory(DayPart::class)->create([
            'default' => true,
            'year_id' => $this->actualYear->id,
        ]);

        $this->actualList->child_families()->syncWithoutDetaching([$this->actualChildFamily->id => ['year_id' => $this->actualYear->id]]);
    }

    public function readOnlyAPIRoutes()
    {
        return [
            // Datatables
            'age_groups' => [function () {
                return route('api.datatables.age_groups', ['year' => $this->actualYear]);
            }],
            'supplements' => [function () {return route('api.datatables.supplements', ['year' => $this->actualYear]);}],
            'day_parts' => [function () {return route('api.datatables.day_parts', ['year' => $this->actualYear]);}],
            'tariffs' => [function () {return route('api.datatables.tariffs', ['year' => $this->actualYear]);}],
            'children' => [function () {return route('api.datatables.children', ['year' => $this->actualYear]);}],
            'families' => [function () {return route('api.datatables.families', ['year' => $this->actualYear]);}],
            'family_children' => [function () {return route('api.datatables.family_children', ['year' => $this->actualYear, 'family' => $this->actualFamily]);}],
            'family_transactions' => [function () {return route('api.datatables.family_transactions', ['year' => $this->actualYear, 'family' => $this->actualFamily]);}],
            'playground_day_registrations' => [function () {return route('api.datatables.registrations', ['year' => $this->actualYear, 'playground_day' => $this->actualPlaygroundDay]);}],
            'admin_sessions' => [function () {return route('api.datatables.admin_sessions', ['year' => $this->actualYear]);}],
            'lists' => [function () {return route('api.datatables.lists', ['year' => $this->actualYear]);}],
            'list_participants' => [function () {return route('api.datatables.list_participants', ['year' => $this->actualYear, 'list' => $this->actualList]);}],
            // Typeahead
            'family_suggestions_for_child' => [function () {return route('api.typeahead.family_suggestions_for_child', ['year' => $this->actualYear, 'child' => $this->actualChild]);}],
            'child_suggestions_for_family' => [function () {return route('api.typeahead.child_suggestions_for_family', ['year' => $this->actualYear, 'family' => $this->actualFamily]);}],
            'family_suggestions' => [function () {return route('api.typeahead.family_suggestions', ['year' => $this->actualYear]);}],
            'child_family_suggestions_for_list' => [function () {return route('api.typeahead.child_family_suggestions_for_list', ['year' => $this->actualYear, 'list' => $this->actualList]);}],
            // Readonly Ajax
            'registration_data' => [function () {return route('api.registration_data', ['year' => $this->actualYear, 'week' => $this->actualWeeks[0], 'family' => $this->actualFamily]);}],
        ];
    }

    public function writeAPIRoutes()
    {
        return [
            // Post requests
            'create_new_child' => [function () {return [
                'route' => route('api.create_new_child', ['year' => $this->actualYear]),
                'data' => [
                    'first_name' => 'Jos',
                    'last_name' => 'De Rudder',
                    'birth_year' => 2010,
                    'age_group_id' => $this->actualAgeGroup->id,
                    'remarks' => '',
                ],
            ];}],
            'add_family_to_child' => [function () {return [
                'route' => route('api.add_family_to_child', [
                    'year' => $this->actualYear,
                    'child' => $this->actualChild,
                    'family' => $this->actualFamily,
                ]),
                'data' => [],
            ];}],
            'remove_family_from_child' => [function () {return [
                'route' => route('api.remove_family_from_child', [
                    'year' => $this->actualYear,
                    'child' => $this->actualChildFamily->child->id,
                    'family' => $this->actualChildFamily->family->id,
                ]),
                'data' => [],
            ];}],
            'add_child_to_family' => [function () {return [
                'route' => route('api.add_child_to_family', [
                    'year' => $this->actualYear,
                    'child' => $this->otherChild->id,
                    'family' => $this->emptyFamily->id,
                ]),
                'data' => [],
            ];}],
            'submit_registration_data' => [function () {return [
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
            ];}],
            'simulate_submit_registration_data' => [function () {return [
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
            ];}],
            'add_participant_to_list' => [function () {return [
                'route' => route('api.add_participant_to_list', [
                    'year' => $this->actualYear,
                    'activity_list' => $this->actualList,
                    'child_family' => $this->immutableChildFamily,
                ]),
                'data' => [],
            ];}],
            'remove_participant_from_list' => [function () {return [
                'route' => route('api.remove_participant_from_list', [
                    'year' => $this->actualYear,
                    'activity_list' => $this->actualList,
                    'child_family' => $this->actualChildFamily,
                ]),
                'data' => [],
            ];}],
        ];
    }

    /**
     * @dataProvider readOnlyAPIRoutes
     */
    public function testAPINotLoggedIn_ReadOnly($routeFunction)
    {
        $this->assertGuest();
        $url = $routeFunction->bindTo($this)();
        $this->get($url)->assertStatus(401);
    }

    /**
     * @dataProvider writeAPIRoutes
     */
    public function testAPINotLoggedIn_Write($routeFunction)
    {
        $this->assertGuest();
        $request_data = $routeFunction->bindTo($this)();
        $response = $this->postJson($request_data['route'], $request_data['data'])->assertStatus(401);
    }

    /**
     * @dataProvider readOnlyAPIRoutes
     */
    public function testAPINotAuthorized_ReadOnly($routeFunction)
    {
        $this->actingAs(factory(User::class)->create());
        $url = $routeFunction->bindTo($this)();
        $this->get($url)->assertStatus(403);
    }

    /**
     * @dataProvider writeAPIRoutes
     */
    public function testAPINotAuthorized_Write($routeFunction)
    {
        $this->actingAs(factory(User::class)->create());
        $request_data = $routeFunction->bindTo($this)();
        $response = $this->postJson($request_data['route'], $request_data['data'])->assertStatus(403);
    }

    /**
     * @dataProvider readOnlyAPIRoutes
     */
    public function testAPIAuthorized_ReadOnly($routeFunction)
    {
        $this->actingAs($this->actualUser);
        $url = $routeFunction->bindTo($this)();
        $this->get($url)->assertSuccessful();
    }

    /**
     * @dataProvider writeAPIRoutes
     */
    public function testAPIAuthorized_Write($routeFunction)
    {
        $this->actingAs($this->actualUser);
        $request_data = $routeFunction->bindTo($this)();
        $response = $this->postJson($request_data['route'], $request_data['data'])->assertSuccessful();
    }
}
