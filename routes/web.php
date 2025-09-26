<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

# Authentication routes
Route::get("/register", [AuthController::class, "showRegister"])->name("show.register");
Route::get("/login", [AuthController::class, "showLogin"])->name("show.login");

Route::post("/register", [AuthController::class, "register"])->name("register");
Route::post("/login", [AuthController::class, "login"])->name("login");

Route::post("/logout", [AuthController::class, "logout"])->name("logout");

# Authenticated routes
Route::middleware(["auth"])->group(function() {
    Route::get("/", action: function() {
        return view("dashboard");
    })->name("show.dashboard");
});