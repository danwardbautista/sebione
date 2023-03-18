<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\CompanyAPI\CompanyController;
use App\Http\Controllers\CompanyAPI\EmployeesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth:sanctum', 'cors']], function () {
    //COMPANY API
    Route::get('/company', [CompanyController::class, 'index']);
    Route::post('/company', [CompanyController::class, 'store']);
    Route::get('/company/{id}', [CompanyController::class, 'show']);
    Route::put('/company/{id}', [CompanyController::class, 'update']);
    Route::delete('/company/{id}', [CompanyController::class, 'destroy']);

    //EMPLOYEE API
    Route::get('/employee', [EmployeesController::class, 'index']);
    Route::get('/employeesNoCompany', [EmployeesController::class, 'employeesNoCompany']);
    Route::post('/employee', [EmployeesController::class, 'store']);
    Route::get('/employee/{id}', [EmployeesController::class, 'show']);
    Route::put('/employee/{id}', [EmployeesController::class, 'update']);
    Route::delete('/employee/{id}', [EmployeesController::class, 'destroy']);

    Route::get('/dashboardInfo', [CompanyController::class, 'dashboardInfo']);

    Route::get('/getuser', [AuthenticationController::class, 'getuser']);
    Route::get('/logout', [AuthenticationController::class, 'logout']);
});



Route::post('/login', [AuthenticationController::class, 'login']);
Route::get('/companylogo/{filename}', [CompanyController::class, 'fileLogoImage']);
