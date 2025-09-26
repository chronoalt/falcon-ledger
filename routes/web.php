<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

# Authentication routes
Route::middleware(["guest"])->controller(AuthController::class)->group(function() {
    Route::get("/login", "showLogin")->name("show.login");
    Route::post("/login", "login")->name("login");
});
Route::post("/logout", [AuthController::class, "logout"])->name("logout");

# Authenticated routes
Route::middleware(["auth"])->group(function() {
    Route::get("/", action: function() {
        return view("dashboard");
    })->name("show.dashboard");
});