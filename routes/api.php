<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

//Auth
Route::post('login'  , [AuthController::class, 'login']);
Route::post('register' , [AuthController::class, 'register']);
Route::post('verify-account' , [AuthController::class, 'verify_account']);
Route::post('logout', [AuthController::class, 'logout']);

Route::put('user', [AuthController::class, 'edit_profile']);
Route::get('user', [AuthController::class, 'get_profile']);
Route::delete('user/delete_account', [AuthController::class, 'delete_user']);
