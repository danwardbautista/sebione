<?php

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

Route::get('/company', [CompanyController::class, 'index']);
Route::post('/company', [CompanyController::class, 'store']);
Route::get('/company/{id}', [CompanyController::class, 'show']);
Route::put('/company/{id}', [CompanyController::class, 'update']);
Route::delete('/company/{id}', [CompanyController::class, 'destroy']);

Route::get('/employee', [EmployeesController::class, 'index']);
Route::post('/employee', [EmployeesController::class, 'store']);
Route::get('/employee/{id}', [EmployeesController::class, 'show']);
Route::put('/employee/{id}', [EmployeesController::class, 'update']);
Route::delete('/employee/{id}', [EmployeesController::class, 'destroy']);