<?php

use App\Http\Controllers\LoginControllerApi;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginControllerApi::class, 'login']);
