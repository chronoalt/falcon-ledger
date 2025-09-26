<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PentesterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;

# Authentication routes
Route::middleware(["guest"])->controller(AuthController::class)->group(function () {
    Route::get("/login", "showLogin")->name("show.login");
    Route::post("/login", "login")->name("login");
});
Route::post("/logout", [AuthController::class, "logout"])->name("logout");

# Authenticated-only routes
Route::middleware(["auth"])->group(function () {
    Route::get("/", [DashboardController::class, "showDashboard"])->name("show.dashboard");
    Route::resource("projects", ProjectController::class);
});
