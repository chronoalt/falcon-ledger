<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function showDashboard(): Response
    {
        $user = Auth::user();

        if ($user && $user->hasRole("admin")) {
            return Inertia::render('Admin/Dashboard');
        }

        if ($user && $user->hasRole("pentester")) {
            return Inertia::render('Pentester/Dashboard');
        }

        abort(403, "Unauthorized");
    }
}
