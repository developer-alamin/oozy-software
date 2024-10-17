<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;



Route::get('/', function () {
    return view('app');
});
Route::view('/{any}', 'app')->where('any','.*');
