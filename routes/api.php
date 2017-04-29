<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
// Datatables
Route::get('/age_groups', 'AgeGroupsController@getAgeGroups')
    ->name('getAgeGroups');
Route::get('/supplements', 'SupplementsController@getSupplements')
    ->name('getSupplements');
Route::get('/day_parts', 'DayPartsController@getDayParts')
    ->name('getDayParts');
Route::get('/tariffs', 'TariffsController@getTariffs')
    ->name('getTariffs');
Route::get('/children', 'ChildrenController@getChildren')
    ->name('getChildren');
Route::get('/families', 'FamiliesController@getFamilies')
    ->name('getFamilies');
Route::get('/family/{family_id}/children', 'FamiliesController@getFamilyChildren')
    ->name('getFamilyChildren');
Route::get('/registrations/playground_day/{playground_day_id}', 'RegistrationsController@getRegistrations')
    ->name('getRegistrations');

// Typeahead.js
Route::get('/typeahead/child/{child_id}/families/suggestions', 'ChildrenController@getChildFamilySuggestions')
    ->name('getChildFamilySuggestions');
Route::get('/typeahead/family/{family_id}/children/suggestions', 'FamiliesController@getChildSuggestionsForFamily')
    ->name('getChildSuggestionsForFamily');
Route::get('/typeahead/families/suggestions', 'FamiliesController@getFamilySuggestions')
    ->name('getFamilySuggestions');

// Ajax
Route::post('/child/{child_id}/families/add', 'ChildrenController@addChildFamily')
    ->name('addChildFamily');
Route::post('/child/{child_id}/families/remove', 'ChildrenController@removeChildFamily')
    ->name('removeChildFamily');
Route::post('/family/{family_id}/children/add', 'FamiliesController@addChildToFamily')
    ->name('addChildToFamily');

Route::get('/registration/week/{week_id}/family/{family_id}', 'RegistrationsController@getRegistrationData')
    ->name('getRegistrationData');
Route::post('/registration/week/{week_id}/family/{family_id}', 'RegistrationsController@submitRegistrationData')
    ->name('submitRegistrationData');
Route::post('/registration/week/{week_id}/family/{family_id}/prices', 'RegistrationsController@submitRegistrationDataForPrices')
    ->name('submitRegistrationDataForPrices');