<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Project;
use App\Models\Target;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TargetController extends Controller
{
    use AuthorizesRequests;
    public function store(Request $request, Asset $asset): RedirectResponse
    {
        $this->authorize('create', Target::class);
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

    public function show(Project $project, Target $target): Response
    {
        $this->authorize('view', $target);

        $target->load([
            'asset',
            'findings' => function ($query) {
                $query->latest();
            },
            'findings.cvssVector',
            'findings.attachments',
        ]);

        return Inertia::render('Targets/Show', [
            'project' => [
                'id' => $project->id,
                'title' => $project->title,
                'links' => [
                    'show' => route('projects.show', $project),
                ],
            ],
            'target' => [
                'id' => $target->id,
                'label' => $target->label,
                'endpoint' => $target->endpoint,
                'description' => $target->description,
                'asset' => [
                    'id' => $target->asset->id,
                    'name' => $target->asset->name,
                ],
                'links' => [
                    'createFinding' => route('projects.findings.create', ['project' => $project->id, 'target_id' => $target->id]),
                ],
                'findings' => $target->findings->map(function ($finding) use ($project, $target) {
                    return [
                        'id' => $finding->id,
                        'title' => $finding->title,
                        'status' => $finding->status,
                        'cvss' => [
                            'score' => (float) ($finding->cvssVector->base_score ?? 0),
                            'severity' => ucfirst($finding->cvssVector->base_severity ?? 'unknown'),
                            'vector' => $finding->cvssVector->vector_string ?? 'N/A',
                        ],
                        'updated_human' => optional($finding->updated_at)?->diffForHumans(),
                        'links' => [
                            'show' => route('projects.targets.findings.show', [$project, $target, $finding]),
                        ],
                    ];
                })->values(),
            ],
        ]);
    }

    public function edit(Asset $asset, Target $target): Response
    {
        $this->authorize('update', $target);

        $project = $asset->project;

        return Inertia::render('Targets/Edit', [
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
            ],
            'target' => [
                'id' => $target->id,
                'label' => $target->label,
                'endpoint' => $target->endpoint,
                'description' => $target->description,
                'links' => [
                    'update' => route('assets.targets.update', [$asset, $target]),
                ],
            ],
        ]);
    }

    public function update(Request $request, Asset $asset, Target $target): RedirectResponse
    {
        $this->authorize('update', $target);

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
        $this->authorize('delete', $target);
        $project = $asset->project;
        $target->delete();

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Target removed.');
    }
}
