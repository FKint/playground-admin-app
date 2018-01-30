<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

Route::get('/', 'UserController@show')->name('user_dashboard');

Route::prefix('{year}')
    ->middleware(['can:view,year', 'default_year_route_parameter', 'add_year_variable_to_templates'])
    ->group(function () {
        Route::get('/dashboard', 'DashboardController@show')->name('dashboard');

        Route::get('/children', 'ChildrenController@show')->name('children');

        Route::get('/families', 'FamiliesController@show')->name('families');

        Route::get('/registrations', 'RegistrationsController@show')
            ->name('registrations');
        Route::get('/registrations/date/{date}', 'RegistrationsController@showDate')
            ->name('registrations_for_date');

        Route::get('/lists', 'ListsController@show')->name('lists');

        Route::get('/settings', 'SettingsController@show')->name('settings');

        // Edit child
        Route::get('/child/{child}/edit', 'ChildrenController@showEditChild')
            ->name('show_edit_child');
        Route::get('/child/{child}/edit/form', 'ChildrenController@loadEditChildForm')
            ->name('edit_child_form');
        Route::post('/child/{child}/edit/form', 'ChildrenController@submitEditChildForm')
            ->name('update_child_details');
        Route::get('/child/{child}/families/edit/form', 'ChildrenController@loadEditFamiliesForm')
            ->name('edit_child_families_form');
        Route::get('/child/{child}/families/link/new/form', 'ChildrenController@loadLinkNewChildFamilyForm')
            ->name('show_link_new_child_family_form');
        Route::post('/child/{child}/families/link/new/form', 'ChildrenController@submitLinkNewChildFamilyForm')
            ->name('submit_link_new_child_family_form');

        // Child info
        Route::get('/child/{child}/info', 'ChildrenController@loadChildInfoForm')
            ->name('info_child');

        // New child
        Route::get('/children/new', 'ChildrenController@showNewChild')
            ->name('show_new_child');
        Route::post('/children/new', 'ChildrenController@showSubmitNewChild')
            ->name('show_submit_new_child');

        // New family with children
        Route::get('/families/new/with_children', 'FamiliesController@showNewFamilyWithChildren')
            ->name('show_new_family_with_children');
        Route::post('/families/new/with_children', 'FamiliesController@showSubmitNewFamilyWithChildren')
            ->name('show_submit_new_family_with_children');
        Route::get('/family/{family}/children/add', 'FamiliesController@showAddChildrenToFamily')
            ->name('show_add_child_to_family');
        Route::post('/family/{family}/children/add', 'FamiliesController@showSubmitAddChildrenToFamily')
            ->name('show_submit_add_child_to_family');
        Route::get('/family/{family}/child/{child}/remove', 'FamiliesController@showRemoveChildFromNewFamilyWithChildren')
            ->name('show_remove_child_from_new_family_with_children');


        // Families
        Route::get('/family/children/table', 'FamiliesController@loadFamilyChildrenTable')
            ->name('load_family_children');
        Route::get('/family/edit/form', 'FamiliesController@loadEditFamilyForm')
            ->name('load_edit_family_form');
        Route::post('/family/{family}/edit/form', 'FamiliesController@submitEditFamilyForm')
            ->name('submit_edit_family_form');
        Route::get('/family/{family}/transactions', 'FamiliesController@showTransactions')
            ->name('show_family_transactions');

        // Edit registrations
        Route::get('/registrations/week/{week}/families/find', 'RegistrationsController@showFindFamily')
            ->name('show_find_family_registration');
        Route::get('/registrations/edit', 'RegistrationsController@showEditRegistration')
            ->name('show_edit_registration');

        // Admin sessions
        Route::get('/admin_sessions/close', 'AdminSessionsController@showCloseAdminSession')
            ->name('close_admin_session');
        Route::post('/admin_sessions/close', 'AdminSessionsController@showSubmitCloseAdminSession')
            ->name('submit_close_admin_session');
        Route::get('/admin_session/{admin_session}/edit', 'AdminSessionsController@showEditAdminSession')
            ->name('show_edit_admin_session');
        Route::post('/admin_session/{admin_session}/edit', 'AdminSessionsController@showSaveEditAdminSession')
            ->name('show_save_edit_admin_session');

        // Activity lists
        Route::get('/list/{list}', 'ListsController@showList')->name('show_list');
        Route::get('/lists/new', 'ListsController@showNewList')->name('show_create_new_list');
        Route::post('/lists/new', 'ListsController@submitNewList')->name('submit_create_new_list');
        Route::post('/list/{list}/edit', 'ListsController@submitEditList')->name('submit_edit_list');
    });