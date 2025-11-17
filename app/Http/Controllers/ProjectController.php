<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $user = Auth::user();
        $projectsQuery = Project::query()
            ->when(!$user->hasRole('admin'), function ($query) use ($user) {
                $query->where(function ($inner) use ($user) {
                    $inner->where('created_by', $user->id)
                        ->orWhereHas('users', function ($q) use ($user) {
                            $q->where('user_id', $user->id);
                        });
                });
            })
            ->withCount('assets')
            ->orderByDesc('created_at');

        $projects = $projectsQuery->get()
            ->map(fn (Project $project) => [
                'id' => $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'status' => $project->status,
                'due_at' => optional($project->due_at)?->toDateString(),
                'assets_count' => $project->assets_count,
                'links' => [
                    'show' => route('projects.show', $project),
                    'edit' => route('projects.edit', $project),
                    'destroy' => route('projects.destroy', $project),
                ],
            ])
            ->values();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create', Project::class);
        return Inertia::render('Projects/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Project::class);
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_at' => 'nullable|date',
        ]);

        $project = Project::create([
            'title' => $request->title,
            'description' => $request->description,
            'due_at' => $request->due_at,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): Response
    {
        $this->authorize('view', $project);
        $project->load([
            'assets.targets' => function ($query) {
                $query->withCount('findings');
            },
        ]);

        return Inertia::render('Projects/Show', [
            'project' => [
                'id' => $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'status' => $project->status,
                'due_at' => optional($project->due_at)?->toDateString(),
                'links' => [
                    'show' => route('projects.show', $project),
                    'storeAsset' => route('projects.assets.store', $project),
                ],
            ],
            'assets' => $project->assets->map(function ($asset) use ($project) {
                return [
                    'id' => $asset->id,
                    'name' => $asset->name,
                    'address' => $asset->address,
                    'detail' => $asset->detail,
                    'links' => [
                        'edit' => route('projects.assets.edit', [$project, $asset]),
                        'update' => route('projects.assets.update', [$project, $asset]),
                        'destroy' => route('projects.assets.destroy', [$project, $asset]),
                        'storeTarget' => route('assets.targets.store', $asset),
                    ],
                    'targets' => $asset->targets->map(function ($target) use ($project, $asset) {
                        return [
                            'id' => $target->id,
                            'label' => $target->label,
                            'endpoint' => $target->endpoint,
                            'description' => $target->description,
                            'findings_count' => $target->findings_count ?? $target->findings()->count(),
                            'links' => [
                                'view' => route('projects.targets.show', [$project, $target]),
                                'edit' => route('assets.targets.edit', [$asset, $target]),
                                'destroy' => route('assets.targets.destroy', [$asset, $target]),
                                'createFinding' => route('projects.findings.create', ['project' => $project->id, 'target_id' => $target->id]),
                            ],
                        ];
                    })->values(),
                ];
            })->values(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project): Response
    {
        $this->authorize('update', $project);
        $statusOptions = ['Active', 'Inactive', 'Completed'];

        return Inertia::render('Projects/Edit', [
            'project' => [
                'id' => $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'due_at' => optional($project->due_at)?->toDateString(),
                'status' => $project->status,
                'links' => [
                    'update' => route('projects.update', $project),
                ],
            ],
            'statusOptions' => $statusOptions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_at' => 'nullable|date',
            'status' => 'required|string|in:Active,Inactive,Completed',
        ]);

        $project->update([
            'title' => $request->title,
            'description' => $request->description,
            'due_at' => $request->due_at,
            'status' => $request->status,
        ]);

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }
}
