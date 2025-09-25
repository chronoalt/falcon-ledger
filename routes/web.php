<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

# Authentication routes
Route::get("/register", [AuthController::class, "showRegister"])->name("show.register");
Route::get("/login", [AuthController::class, "showLogin"])->name("show.login");
