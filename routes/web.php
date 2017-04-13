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

Route::get('/', function () {
    return view('index');
});

Route::get('/dashboard', 'DashboardController@show')->name('dashboard');

Route::get('/children', 'ChildrenController@show')->name('children');

Route::get('/registrations', 'RegistrationsController@show')->name('registrations');

Route::get('/settings', 'SettingsController@show')->name('settings');


Route::get('/child/edit/form', 'ChildrenController@showEditChildForm')->name('edit_child_form');
Route::post('/child/{child_id}/edit/form', 'ChildrenController@submitEditChildForm')->name('update_child_details');
Route::get('/child/families/edit/form', 'ChildrenController@showEditFamiliesForm')->name('edit_child_families_form');
