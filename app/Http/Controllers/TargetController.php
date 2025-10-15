<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Project;
use App\Models\Target;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TargetController extends Controller
{
    public function store(Request $request, Asset $asset): RedirectResponse
    {
        $payload = $request->validate([
            'label' => 'required|string|max:255',
            'endpoint' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $asset->targets()->create($payload);

        return redirect()
            ->route('projects.show', $asset->project)
            ->with('success', 'Target added successfully.');
    }

    public function show(Project $project, Target $target)
    {
        $this->assertTargetBelongsToProject($target, $project);

        $target->load([
            'asset',
            'findings' => function ($query) {
                $query->latest();
            },
            'findings.cvssVector',
            'findings.attachments',
        ]);

        return view('targets.show', [
            'project' => $project,
            'target' => $target,
        ]);
    }

    public function edit(Asset $asset, Target $target)
    {
        $this->assertTargetBelongsToAsset($target, $asset);

        return view('targets.edit', [
            'asset' => $asset,
            'target' => $target,
            'project' => $asset->project,
        ]);
    }

    public function update(Request $request, Asset $asset, Target $target): RedirectResponse
    {
        $this->assertTargetBelongsToAsset($target, $asset);

        $payload = $request->validate([
            'label' => 'required|string|max:255',
            'endpoint' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $target->update($payload);

        return redirect()
            ->route('projects.show', $asset->project)
            ->with('success', 'Target updated.');
    }

    public function destroy(Asset $asset, Target $target): RedirectResponse
    {
        $this->assertTargetBelongsToAsset($target, $asset);
        $project = $asset->project;
        $target->delete();

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Target removed.');
    }

    private function assertTargetBelongsToProject(Target $target, Project $project): void
    {
        if ($target->asset->project_id !== $project->id) {
            abort(404);
        }
    }

    private function assertTargetBelongsToAsset(Target $target, Asset $asset): void
    {
        if ($target->asset_id !== $asset->id) {
            abort(404);
        }
    }
}
