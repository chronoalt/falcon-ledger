<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function showDashboard() {
        $user = Auth::user();

        if ($user->hasRole("admin")) {
            return view("admin.dashboard");
        } elseif ($user->hasRole("pentester")) {
            return view("pentester.dashboard");
        }

        abort(403, "Unauthorized");
    }
}
