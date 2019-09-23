<?php

use Hanson\LaravelAdminRegister\Http\Controllers\LaravelAdminRegisterController;

Route::get('register', LaravelAdminRegisterController::class.'@getRegister');
Route::post('register', LaravelAdminRegisterController::class.'@postRegister')->name('register');
Route::post('register/send-code', LaravelAdminRegisterController::class.'@sendCode');
