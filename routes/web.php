<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\FindingController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;

# Authentication routes
Route::middleware(["guest"])->group(function () {
    Route::get("/login", [AuthController::class, "showLogin"])->name("login");
    Route::post("/login", [AuthController::class, "login"])->name("login.store");

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                ->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                ->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->name('password.store');
});
Route::post("/logout", [AuthController::class, "logout"])->name("logout");


# Authenticated-only routes
Route::middleware(["auth"])->group(function () {
    Route::get('/', function () {
        return redirect()->route('projects.index');
    })->name('home');
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

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::post('/admin/users/{user}/projects', [UserController::class, 'addUserToProject'])->name('admin.users.projects.add');
        Route::put('/admin/users/{user}/role', [UserController::class, 'updateRole'])->name('admin.users.role.update');
        Route::delete('/admin/users/{user}/projects/{project}', [UserController::class, 'removeUserFromProject'])->name('admin.users.projects.remove');
    });
});
