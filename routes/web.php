<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RiskAssessmentController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\EmergencyScenarioController;
use App\Http\Controllers\OrganizationTypeController;
use App\Http\Controllers\AdminOrganizationCrudController;
use App\Http\Controllers\Admin\ExportController;

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

Route::middleware(['password.protected'])->get('/admin', function () {
    return view('admin');
})->name('admin.page');

Route::resource('/admin/emergency-scenarios', EmergencyScenarioController::class)
    ->middleware(['password.protected']);
Route::resource('/admin/organization-types', OrganizationTypeController::class)
    ->middleware(['password.protected']);
Route::resource('/admin/organizations', AdminOrganizationCrudController::class)
    ->except(['show'])
    ->middleware(['password.protected']);

Route::get('/admin/export-calculations', [ExportController::class, 'exportCalculations'])
    ->middleware(['password.protected'])
    ->name('export.calculations');

Route::get('/admin/filter-calculations', [ExportController::class, 'filterCalculations'])
    ->middleware(['password.protected'])
    ->name('filter.calculations');
