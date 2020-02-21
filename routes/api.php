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

//use GuzzleHttp\Psr7\Request;
//
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('/quickteller/{billerId}/payments', 'BillersController@paymentItems');
Route::get('/quickteller/categorys', 'BillersController@categories');
Route::get('/quickteller/billers', 'BillersController@billers');
Route::post('/quickteller/validate', 'BillersController@validateCustomer');
Route::post('/quickteller/payment', 'BillersController@paymentAdvise');
Route::get('/quickteller/reference', 'BillersController@requestReference');
Route::post('/quickteller/withdrawal', 'BillersController@cashwithdrawal');
