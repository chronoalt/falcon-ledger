<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PentesterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\FindingController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\TargetController;

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
    Route::resource("projects.assets", AssetController::class)->only(["store", "edit", "update", "destroy"]);
    Route::resource("assets.targets", TargetController::class)->only(["store", "edit", "update", "destroy"]);
    Route::get("projects/{project}/{target}", [TargetController::class, "show"])
        ->name("projects.targets.show")
        ->whereUuid("target");
    Route::resource("projects.findings", FindingController::class)->only(["create", "store"]);
    Route::get("projects/{project}/{target}/{finding}", [FindingController::class, "show"])
        ->name("projects.targets.findings.show")
        ->whereUuid("target")
        ->whereUuid("finding");
    Route::get("findings/{finding}/attachments/{attachment}", [FindingController::class, "downloadAttachment"])
        ->name("findings.attachments.download");
});
