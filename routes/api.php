<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

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
Route::any('w-webhook',  [HomeController::class,'world_webhook']);
Route::any('d-webhook',  [HomeController::class,'diasy_webhook']);

Route::any('updatesec',  [HomeController::class,'updatesec']);
Route::any('cancle-sms',  [HomeController::class,'cancle_sms_timer']);


Route::any('user',  [HomeController::class,'user']);



Route::any('e_fund',  [HomeController::class,'e_fund']);
Route::any('e_check',  [HomeController::class,'e_check']);
Route::any('verify',  [HomeController::class,'verify_username']);



