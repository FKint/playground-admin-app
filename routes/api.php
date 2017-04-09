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

Route::get('/age_groups', 'AgeGroupsController@getAgeGroups')->name('getAgeGroups');
Route::get('/supplements', 'SupplementsController@getSupplements')->name('getSupplements');
Route::get('/day_parts', 'DayPartsController@getDayParts')->name('getDayParts');
Route::get('/tariffs', 'TariffsController@getTariffs')->name('getTariffs');
Route::get('/children', 'ChildrenController@getChildren')->name('getChildren');