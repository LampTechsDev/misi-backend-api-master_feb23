<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\Therapist\TherapistServiceController;
use App\Http\Controllers\Therapist\TherapistController;
use App\Http\Controllers\ServiceSubCategoryController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\OccupationController;
use App\Http\Controllers\BloodGroupController;
use App\Http\Controllers\TherapistTypeController;
use App\Http\Controllers\TicketDepartmentController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\GroupUpdatedController;

use App\Http\Controllers\Therapist\TherapistDegreeController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
