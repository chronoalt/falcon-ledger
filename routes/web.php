<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('home');
})->name("show.home");

# Authentication routes
Route::get("/register", [AuthController::class, "showRegister"])->name("show.register");
Route::get("/login", [AuthController::class, "showLogin"])->name("show.login");

Route::post("/register", [AuthController::class, "register"])->name("register");
Route::post("/login", [AuthController::class, "login"])->name("login");

# Temporary
Route::get("/dashboard", action: function() {
    return view("dashboard");
})->name("show.dashboard");