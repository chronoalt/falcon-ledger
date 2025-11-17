<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the authenticated user's profile settings.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user()->load([
            'roles:id,name',
            'projects:id,title',
        ]);

        return Inertia::render('Profile/Settings', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_photo_url' => $user->profile_photo_url,
                'roles' => $user->roles->pluck('name'),
                'projects' => $user->projects->map(fn ($project) => [
                    'id' => $project->id,
                    'title' => $project->title,
                ]),
            ],
        ]);
    }

    /**
     * Update the user's name and profile photo.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        $user = $request->user();
        $user->name = trim($validated['name']);

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');

            // Clean up the previous image to avoid orphaned files.
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $user->profile_photo_path = $path;
        }

        $user->save();

        return back()->with('success', 'Profile updated.');
    }
}
