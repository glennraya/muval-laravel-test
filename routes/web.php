<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\LoginController;

use App\Http\Controllers\LoginControllerApi;
use App\Http\Controllers\RegistrationController;

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

// Login/Logout route for the Vue 3 SPA
Route::post('/login-spa', [LoginControllerApi::class, 'login']);
Route::post('/logout-spa', [LoginControllerApi::class, 'logout']);
