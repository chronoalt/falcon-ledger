<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AssetController extends Controller
{
    use AuthorizesRequests;
    public function store(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('create', Asset::class);
        $payload = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'detail' => 'nullable|string',
        ]);

        $project->assets()->create($payload);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Asset added successfully.');
    }

    public function edit(Project $project, Asset $asset): Response
    {
        $this->authorize('update', $asset);

        return Inertia::render('Assets/Edit', [
            'project' => [
                'id' => $project->id,
                'title' => $project->title,
                'links' => [
                    'show' => route('projects.show', $project),
                ],
            ],
            'asset' => [
                'id' => $asset->id,
                'name' => $asset->name,
                'address' => $asset->address,
                'detail' => $asset->detail,
                'links' => [
                    'update' => route('projects.assets.update', [$project, $asset]),
                ],
            ],
        ]);
    }

    public function update(Request $request, Project $project, Asset $asset): RedirectResponse
    {
        $this->authorize('update', $asset);

        $payload = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'detail' => 'nullable|string',
        ]);

        $asset->update($payload);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Asset updated.');
    }

    public function destroy(Project $project, Asset $asset): RedirectResponse
    {
        $this->authorize('delete', $asset);
        $asset->delete();

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Asset removed.');
    }
}
