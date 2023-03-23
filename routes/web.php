<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\customerController;

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
    return view('welcome');
});
// Route::get('/mdgrid',[customerController::class, 'index']);
Route::prefix('customers')->group(function () {
    Route::get('mdgrid', [customerController::class, 'index']);
    // Route::get('parameter/structure', 'ParameterController@structure');
    // Route::get('parameter/{id}', 'ParameterController@show');
    // Route::post('parameter', 'ParameterController@store');
    // Route::patch('parameter/{id}', 'ParameterController@update');
    // Route::delete('parameter/{id}', 'ParameterController@destroy');

});
