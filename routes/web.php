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
    return view('age_groups');
});

Route::get('/age_groups', function(){
    $age_groups = \App\AgeGroup::all();
    return view('age_groups', [
        'age_groups' => $age_groups
    ]);
});

Route::get('/dashboard', function(){
    return view('dashboard');
});

Route::get('/children', function(){
    return view('children');
});

Route::get('/registrations', function(){
    return view('registrations');
});

Route::get('/settings', function(){
    return view('settings');
});