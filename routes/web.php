<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RiskAssessmentController;

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


Route::get('/', [RiskAssessmentController::class, 'index']);
Route::post('/calculate', [RiskAssessmentController::class, 'calculate']);
Route::post('/validate-slide', [RiskAssessmentController::class, 'validateSlide']);

/*Route::get('/', function () {
    return view('welcome');
});*/
