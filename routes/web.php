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
    Route::get('show', [customerController::class, 'showData']);
    Route::post('mdgrid/store', [customerController::class, 'store']);
    Route::get('mdgrid/getposition', [customerController::class, 'getPosition']);
    Route::post('mdgrid/update', [customerController::class, 'update']);
    Route::post('mdgrid/delete', [customerController::class, 'delete']);
    Route::get('form-add', [customerController::class, 'showDialogAdd']);
    Route::get('form-update/{id_customer}', [customerController::class, 'showDialogUpdate']);
    Route::get('form-delete/{id_customer}', [customerController::class, 'showDialogDelete']);
    Route::get('detail/{id_customer}', [customerController::class, 'detailMaster']);

    // report dan export
    Route::get('report', [customerController::class, 'report']);
    Route::get('export', [customerController::class, 'export']);
});



