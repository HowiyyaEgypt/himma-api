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


/** User Routes */
Route::post('/register','UserController@register');
Route::post('/login','UserController@login');


/** Bond routes */
Route::post('/bonds', 'BondController@listMyBonds');
Route::post('/pending-bonds', 'BondController@listMyPendingBonds');
Route::post('/add-bond', 'BondController@addNewBond');
Route::post('/accept-bond', 'BondController@acceptNewBond');