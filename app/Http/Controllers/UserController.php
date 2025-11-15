<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index(): Response
    {
        $this->authorize('viewAny', User::class);

        return Inertia::render('Admin/Users/Index', [
            'users' => User::with('roles', 'projects')->get(),
            'projects' => Project::all(),
        ]);
    }

    public function addUserToProject(Request $request, User $user)
    {
        $this->authorize('update', $user);
        $request->validate([
            'project_id' => 'required|exists:projects,id',
        ]);
        $project = Project::find($request->project_id);
        $user->projects()->attach($project);

        return back();
    }

    public function removeUserFromProject(Request $request, User $user, Project $project)
    {
        $this->authorize('update', $user);
        $user->projects()->detach($project);

        return back();
    }

    public function updateRole(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'role' => 'required|string|in:admin,supervisor,pentester,client',
        ]);

        if ($user->id === Auth::id() && $user->hasRole('admin')) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $user->syncRoles([$validated['role']]);

        return back()->with('success', "User's role updated successfully.");
    }
}
