<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return ['Chegamos até aqui' => 'Sim'];
// });
// Route::prefix('v1')->middleware('jwt.auth')->group( function(){    
Route::middleware('jwt.auth')->group( function(){    
    Route::apiResource('car', 'CarController');
    Route::apiResource('customer', 'CustomerController');
    Route::apiResource('rent', 'RentController');
    Route::apiResource('brand', 'BrandController');
    Route::apiResource('model', 'CarModelController');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});

Route::post('login', 'AuthController@login');
// Route::group([

//     'middleware' => 'api',
//     'prefix' => 'auth'

// ], function ($router) {

    // Route::post('login', 'AuthController@login');
    // Route::post('logout', 'AuthController@logout');
    // Route::post('refresh', 'AuthController@refresh');
    // Route::post('me', 'AuthController@me');

// });