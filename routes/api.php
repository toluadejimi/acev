<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;



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
Route::any('user',  [HomeController::class,'user']);




Route::any('balance',  [ApiController::class,'get_balance']);
Route::any('get-world-countries',  [ApiController::class,'get_world_countries']);
Route::any('get-world-services',  [ApiController::class,'get_world_services']);
Route::any('check-world-number-availability',  [ApiController::class,'check_availability']);
Route::any('rent-world-number',  [ApiController::class,'rent_world_number']);
Route::any('get-world-sms',  [ApiController::class,'get_world_sms']);
Route::any('get-usa-sms',  [ApiController::class,'get_usa_sms']);


Route::any('usa-services',  [ApiController::class,'get_usa_services']);
Route::any('rent-usa-number',  [ApiController::class,'rent_usa_number']);
Route::any('cancel-usa-number',  [ApiController::class,'cancel_usa_number']);
Route::any('cancel-world-number',  [ApiController::class,'cancel_world_number']);





