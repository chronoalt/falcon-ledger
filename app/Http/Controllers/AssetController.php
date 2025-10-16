<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AssetController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
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
        $this->assertAssetBelongsToProject($asset, $project);

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
        $this->assertAssetBelongsToProject($asset, $project);

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
        $this->assertAssetBelongsToProject($asset, $project);
        $asset->delete();

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Asset removed.');
    }

    private function assertAssetBelongsToProject(Asset $asset, Project $project): void
    {
        if ($asset->project_id !== $project->id) {
            abort(404);
        }
    }
}
