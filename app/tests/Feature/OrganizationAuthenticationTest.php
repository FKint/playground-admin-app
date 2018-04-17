<?php

namespace Tests\Feature;

use App\ActivityList;
use App\AdminSession;
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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationAuthenticationTest extends TestCase
{
    use RefreshDatabase;

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

        $this->actualWeekDays = array_map(function ($o) {
            return WeekDay::findOrFail($o['id']);
        }, factory(WeekDay::class, 5)->create(['year_id' => $this->actualYear->id])->toArray());

        $this->actualWeeks = array_map(function ($o) {
            return Week::findOrFail($o['id']);
        }, factory(Week::class, 8)->create(['year_id' => $this->actualYear->id])->toArray());

        $this->actualPlaygroundDay = factory(PlaygroundDay::class)->create([
            'year_id' => $this->actualYear->id,
            'week_id' => $this->actualWeeks[array_rand($this->actualWeeks)]->id,
            'week_day_id' => $this->actualWeekDays[array_rand($this->actualWeekDays)]->id
        ]);

        $this->actualChild = factory(Child::class)->create([
            'year_id' => $this->actualYear->id
        ]);
        $this->actualChildFamily = factory(ChildFamily::class)->create(['year_id' => $this->actualYear->id]);
        $this->immutableChildFamily = factory(ChildFamily::class)->create(['year_id' => $this->actualYear->id]);

        $this->actualDayPart = factory(DayPart::class)->create([
            'default' => true,
            'year_id' => $this->actualYear->id
        ]);
        $this->actualAdminSession = factory(AdminSession::class)->create([
            'year_id' => $this->actualYear->id
        ]);

        $this->setUpRoutes();
    }

    private function setUpRoutes()
    {
        $this->readOnlyAPIRoutes = [
            // Datatables
            'age_groups' => route('api.datatables.age_groups', ['year' => $this->actualYear]),
            'supplements' => route('api.datatables.supplements', ['year' => $this->actualYear]),
            'day_parts' => route('api.datatables.day_parts', ['year' => $this->actualYear]),
            'tariffs' => route('api.datatables.tariffs', ['year' => $this->actualYear]),
            'children' => route('api.datatables.children', ['year' => $this->actualYear]),
            'families' => route('api.datatables.families', ['year' => $this->actualYear]),
            'family_children' => route('api.datatables.family_children', ['year' => $this->actualYear, 'family' => $this->actualFamily]),
            'family_transactions' => route('api.datatables.family_transactions', ['year' => $this->actualYear, 'family' => $this->actualFamily]),
            'playground_day_registrations' => route('api.datatables.registrations', ['year' => $this->actualYear, 'playground_day' => $this->actualPlaygroundDay]),
            'admin_sessions' => route('api.datatables.admin_sessions', ['year' => $this->actualYear]),
            'lists' => route('api.datatables.lists', ['year' => $this->actualYear]),
            'list_participants' => route('api.datatables.list_participants', ['year' => $this->actualYear, 'list' => $this->actualList]),
            // Typeahead
            'family_suggestions_for_child' => route('api.typeahead.family_suggestions_for_child', ['year' => $this->actualYear, 'child' => $this->actualChild]),
            'child_suggestions_for_family' => route('api.typeahead.child_suggestions_for_family', ['year' => $this->actualYear, 'family' => $this->actualFamily]),
            'family_suggestions' => route('api.typeahead.family_suggestions', ['year' => $this->actualYear]),
            'child_family_suggestions_for_list' => route('api.typeahead.child_family_suggestions_for_list', ['year' => $this->actualYear, 'list' => $this->actualList]),
            // Readonly Ajax
            'registration_data' => route('api.registration_data', ['year' => $this->actualYear, 'week' => $this->actualWeeks[0], 'family' => $this->actualFamily]),
        ];
        $this->writeAPIRoutes = [
            // Post requests
            'create_new_child' => [
                'route' => route('api.create_new_child', ['year' => $this->actualYear]),
                'data' => [
                    'first_name' => 'Jos',
                    'last_name' => 'De Rudder',
                    'birth_year' => 2010,
                    'age_group_id' => $this->actualAgeGroup->id,
                    'remarks' => ''
                ],
            ],
            'add_family_to_child' => [
                'route' => route('api.add_family_to_child', [
                    'year' => $this->actualYear,
                    'child' => $this->actualChild,
                    'family' => $this->actualFamily
                ]),
                'data' => []
            ],
            'remove_family_from_child' => [
                'route' => route('api.remove_family_from_child', [
                    'year' => $this->actualYear,
                    'child' => $this->actualChildFamily->child->id,
                    'family' => $this->actualChildFamily->family->id,
                ]),
                'data' => []
            ],
            'add_child_to_family' => [
                'route' => route('api.add_child_to_family', [
                    'year' => $this->actualYear,
                    'child' => $this->actualChildFamily->child,
                    'family' => $this->actualChildFamily->family
                ]),
                'data' => []
            ],
            'submit_registration_data' => [
                'route' => route('api.submit_registration_data', [
                    'year' => $this->actualYear,
                    'week' => $this->actualWeeks[0],
                    'family' => $this->emptyFamily
                ]),
                'data' => [
                    'children' => [],
                    'tariff_id' => $this->actualTariffs[0]->id,
                    'received_money' => 0,
                    'transaction_remarks' => 0
                ]
            ],
            'simulate_submit_registration_data' => [
                'route' => route('api.submit_registration_data', [
                    'year' => $this->actualYear,
                    'week' => $this->actualWeeks[0],
                    'family' => $this->emptyFamily
                ]),
                'data' => [
                    'children' => [],
                    'tariff_id' => $this->actualTariffs[0]->id,
                    'received_money' => 0,
                    'transaction_remarks' => 0
                ]
            ],
            'add_participant_to_list' => [
                'route' => route('api.add_participant_to_list', [
                    'year' => $this->actualYear,
                    'activity_list' => $this->actualList,
                    'child_family' => $this->immutableChildFamily
                ]),
                'data' => []
            ],
            'remove_participant_from_list' => [
                'route' => route('api.remove_participant_from_list', [
                    'year' => $this->actualYear,
                    'activity_list' => $this->actualList,
                    'child_family' => $this->immutableChildFamily
                ]),
                'data' => []
            ]
        ];
    }

    protected function callAllAPIEndpoints($expected_status = null)
    {
        foreach ($this->readOnlyAPIRoutes as $route_name => $url) {
            $response = $this->get($url);
            if ($expected_status) {
                $response->assertStatus($expected_status);
            } else {
                $response->assertSuccessful();
            }
        }
        foreach ($this->writeAPIRoutes as $route_name => $request_data) {
            $response = $this->postJson($request_data['route'], $request_data['data']);
            if ($expected_status) {
                $response->assertStatus($expected_status);
            } else {
                $response->assertSuccessful();
            }
        }
    }

    public function testAPINotLoggedIn()
    {
        $this->assertGuest();
        $this->callAllAPIEndpoints(401);
    }

    public function testAPINotAuthorized()
    {
        $otherUser = factory(User::class)->create();
        $this->actingAs($otherUser);
        $this->callAllAPIEndpoints(403);
    }

    public function testAPIAuthorized()
    {
        $this->actingAs($this->actualUser);
        $this->callAllAPIEndpoints(null);
    }
}
