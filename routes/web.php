<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SendSMSController;
use App\Http\Controllers\admin\AdminController;


Route::get('/emailverified', function () {
    return view('verified');
})->name('verified');

Route::get('/send-sms', function () {
    return view('send-sms');
})->name('send-sms');

Route::post('/send-sms', [SendSMSController::class,'sendSMS'])->name('sendSMS');



Route::get('/reset-password/{token}', function ($token) {
    return view('reset-password', ['token' => $token]);
})->name('password.reset');


// Show success page
Route::get('/password/reset/success', function () {
    return view('password-reset-success');
})->name('password.reset.success');

// Show failure page
Route::get('/password/reset/failed', function () {
    return view('password-reset-failed');
})->name('password.reset.failed');
