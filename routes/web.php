<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RiskAssessmentController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PasswordController;


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
Route::post('/validate-organization-year', [RiskAssessmentController::class, 'validateOrganizationYear'])->name('validate.organization.year');

Route::get('/organizations', [OrganizationController::class, 'index'])->name('organizations.index');
Route::post('/organizations', [OrganizationController::class, 'store'])->name('organizations.store');
Route::get('/organizations/{organization}/scenarios', [OrganizationController::class, 'getScenarios'])->name('organizations.scenarios');

Route::get('/password', [PasswordController::class, 'showForm'])->name('password.form');
Route::post('/password', [PasswordController::class, 'validatePassword'])->name('password.validate');

// Захищена сторінка
Route::middleware(['password.protected'])->get('/admin', function () {
    return view('admin');
})->name('admin.page');

/*Route::get('/', function () {
    return view('welcome');
});*/
