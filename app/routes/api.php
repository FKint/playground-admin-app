<?php

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

// Datatables
Route::name('datatables.')->prefix('datatables')->group(function () {
    Route::get('/age_groups', 'AgeGroupsController@getAgeGroups')
        ->name('age_groups');
    Route::get('/supplements', 'SupplementsController@getSupplements')
        ->name('supplements');
    Route::get('/day_parts', 'DayPartsController@getDayParts')
        ->name('day_parts');
    Route::get('/tariffs', 'TariffsController@getTariffs')
        ->name('tariffs');
    Route::get('/children', 'ChildrenController@getChildren')
        ->name('children');
    Route::get('/families', 'FamiliesController@getFamilies')
        ->name('families');
    Route::get('/family/{family}/children', 'FamiliesController@getFamilyChildren')
        ->name('family_children')
        ->middleware('model_same_year:family');
    Route::get('/family/{family}/transactions', 'FamiliesController@getFamilyTransactions')
        ->name('family_transactions')
        ->middleware('model_same_year:family');
    Route::get('/registrations/playground_day/{playground_day}', 'RegistrationsController@getRegistrations')
        ->name('registrations')
        ->middleware('model_same_year:playground_day');
    Route::get('/admin_sessions', 'AdminSessionsController@getAdminSessions')
        ->name('admin_sessions');
    Route::get('/lists', 'ListsController@getLists')
        ->name('lists');
    Route::get('/list/{list}/participants', 'ListsController@getListParticipants')
        ->name('list_participants')
        ->middleware('model_same_year:list');
    Route::get('/transactions/{date}', 'TransactionsController@getTransactionsForDate')
        ->name('transactions_for_date');
});

// Typeahead.js
Route::name('typeahead.')->prefix('typeahead')->group(function () {
    Route::get('/child/{child}/families/suggestions', 'ChildrenController@getChildFamilySuggestions')
        ->name('family_suggestions_for_child')
        ->middleware('model_same_year:child');
    Route::get('/family/{family}/children/suggestions', 'FamiliesController@getChildSuggestionsForFamily')
        ->name('child_suggestions_for_family')
        ->middleware('model_same_year:family');
    Route::get('/families/suggestions', 'FamiliesController@getFamilySuggestions')
        ->name('family_suggestions');
    Route::get('/list/{list}/child_families/suggestions', 'ListsController@getListChildFamilySuggestions')
        ->name('child_family_suggestions_for_list')
        ->middleware('model_same_year:list');
});

// Ajax
// Read-only
Route::get('/registration/week/{week}/family/{family}', 'RegistrationsController@getRegistrationData')
    ->name('registration_data')
    ->middleware('model_same_year:week,family');

Route::middleware('can:update,year')->group(function () {
    Route::post('/child/new', 'ChildrenController@submitNewChild')
        ->name('create_new_child');
    Route::post('/child/{child}/families/add/{family}', 'ChildrenController@addChildFamily')
        ->name('add_family_to_child')
        ->middleware('model_same_year:child,family');
    Route::post('/child/{child}/families/{family}/remove', 'ChildrenController@removeChildFamily')
        ->name('remove_family_from_child')
        ->middleware('model_same_year:child,family');

    Route::post('/family/{family}/children/add/{child}', 'FamiliesController@addChildToFamily')
        ->name('add_child_to_family')
        ->middleware('model_same_year:family,child');
    Route::post('/family/{family}/edit', 'FamiliesController@updateFamily')
        ->name('update_family')
        ->middleware('model_same_year:family');

    Route::post('/registration/week/{week}/family/{family}', 'RegistrationsController@submitRegistrationData')
        ->name('submit_registration_data')
        ->middleware('model_same_year:week,family');
    Route::post('/registration/week/{week}/family/{family}/prices', 'RegistrationsController@submitRegistrationDataForPrices')
        ->name('simulate_submit_registration_data')
        ->middleware('model_same_year:week,family');

    Route::post('/list/{activity_list}/participants/add/{child_family}', 'ListsController@addListChildFamily')
        ->name('add_participant_to_list')
        ->middleware('model_same_year:activity_list,child_family');
    Route::post('/list/{activity_list}/participants/{child_family}/remove', 'ListsController@removeListParticipant')
        ->name('remove_participant_from_list')
        ->middleware('model_same_year:activity_list,child_family');
});
