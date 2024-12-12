<?php

use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;

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

Route::post('check-payment/{username}', [StudentController::class, 'checkPaymentApi']);
Route::post('login-entertainment', [StudentController::class, 'getEntertainmentLogin']);
Route::post('status-entertainment', [StudentController::class, 'getEntertainmentStatus']);
Route::get('/google-form', [FormController::class, 'formGoogle']);
