<?php

use App\Http\Controllers\LoginApiController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistrationApiController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'index']);

Route::get('/login', [LoginController::class, 'index'])->name('home');
Route::POST('/login', [LoginController::class, 'login'])->name('login');

Route::get('/register', [RegistrationController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegistrationController::class, 'register'])->name('register');

Route::group(['middleware' => 'auth'], function () {

    Route::POST('/logout', [LoginController::class, 'logout'])->name('logout');

    // Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    // Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    // Route::post('/tasks/store', [TaskController::class, 'store'])->name('tasks.store');
    // Route::get('/tasks/{id}/edit', [TaskController::class, 'edit']);
    // Route::post('/tasks/update/{id}', [TaskController::class, 'update']);
    // Route::get('/tasks/{id}/delete', [TaskController::class, 'destroy']);

    // EXPLANATION: Instead of defining each route manually, we can use "Route::resource()" to automatically generate
    // all the necessary routes for a resourceful controller. This approach simplifies route management, ensures
    // consistency, and follows Laravel's best practices. It creates routes for index, create, store, show, edit,
    // update, and destroy actions, eliminating the need for redundant route definitions.
    Route::resource('tasks', TaskController::class);
});

// Registration/Login/Logout route for the Vue 3 SPA
Route::post('/login-spa', [LoginApiController::class, 'login']);
Route::post('/logout-spa', [LoginApiController::class, 'logout']);
Route::post('/register-spa', [RegistrationApiController::class, 'register']);
