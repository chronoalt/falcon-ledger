<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    public function showLogin(): Response {
        return Inertia::render("Login");
    }

    public function login(Request $request) {
        $validated = $request->validate(rules: [
            "email" => "required|email",
            "password" => "required|string"
        ]);

        if (Auth::attempt($validated)) {
            $request->session()->regenerate();

            return redirect()->route('projects.index');
        }

        throw ValidationException::withMessages([
            "credentials" => "Incorrect credentials"
        ]);
    }

    public function logout(Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route("login");
    }
}
