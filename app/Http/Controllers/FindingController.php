<?php

namespace App\Http\Controllers;

use App\Models\CvssVector;
use App\Models\Finding;
use App\Models\FindingAttachment;
use App\Models\Project;
use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FindingController extends Controller
{
    use AuthorizesRequests;
    private const ATTACHMENT_MAX_FILES = 3;
    private const ATTACHMENT_MAX_SIZE_KB = 10240; // 10 MB per file

    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'application/pdf',
        'text/plain',
        'text/markdown',
        'application/json',
        'application/zip',
        'application/x-zip-compressed',
        'application/gzip',
        'application/x-gzip',
        'application/x-tar',
        'application/x-7z-compressed',
        'text/x-python',
        'application/x-python-code',
        'text/x-shellscript',
        'application/octet-stream',
    ];

    private const ALLOWED_EXTENSIONS = [
        'jpg',
        'jpeg',
        'png',
        'gif',
        'pdf',
        'txt',
        'md',
        'json',
        'zip',
        'gz',
        'tar',
        'tgz',
        '7z',
        'py',
        'sh',
        'ps1',
    ];

    public function create(Project $project): Response
    {
        $this->authorize('create', Finding::class);
        $project->load(['assets.targets']);

        $preselectedTargetId = request()->query('target_id');
        $selectedTarget = null;

        if ($preselectedTargetId) {
            $selectedTarget = $project->targets()
                ->where('targets.id', $preselectedTargetId)
                ->first();
        }

        $preselectedTargetId = $selectedTarget?->id;

        return Inertia::render('Findings/Create', [
            'project' => [
                'id' => $project->id,
                'title' => $project->title,
                'links' => [
                    'show' => route('projects.show', $project),
                    'storeFinding' => route('projects.findings.store', $project),
                    'back' => $selectedTarget
                        ? route('projects.targets.show', [$project, $selectedTarget])
                        : route('projects.show', $project),
                ],
            ],
            'assets' => $project->assets->map(function ($asset) {
                return [
                    'id' => $asset->id,
                    'name' => $asset->name,
                    'address' => $asset->address,
                    'targets' => $asset->targets->map(fn ($target) => [
                        'id' => $target->id,
                        'label' => $target->label,
                        'endpoint' => $target->endpoint,
                    ])->values(),
                ];
            })->values(),
            'statuses' => Finding::STATUS_OPTIONS,
            'attackVectors' => CvssVector::ATTACK_VECTORS,
            'attackComplexities' => CvssVector::ATTACK_COMPLEXITIES,
            'privilegesRequired' => CvssVector::PRIVILEGES_REQUIRED,
            'userInteractions' => CvssVector::USER_INTERACTIONS,
            'scopeOptions' => CvssVector::SCOPE_OPTIONS,
            'impactMetrics' => CvssVector::IMPACT_METRICS,
            'preselectedTargetId' => $preselectedTargetId,
            'attachmentLimit' => self::ATTACHMENT_MAX_FILES,
            'attachmentMaxSizeMb' => (int) ceil(self::ATTACHMENT_MAX_SIZE_KB / 1024),
            'allowedExtensions' => self::ALLOWED_EXTENSIONS,
        ]);
    }

    public function store(Request $request, Project $project)
    {
        $this->authorize('create', Finding::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required|string|in:' . implode(',', Finding::STATUS_OPTIONS),
            'description' => 'required|string',
            'recommendation' => 'nullable|string',
            'attack_vector' => 'required|string|in:' . implode(',', array_keys(CvssVector::ATTACK_VECTORS)),
            'attack_complexity' => 'required|string|in:' . implode(',', array_keys(CvssVector::ATTACK_COMPLEXITIES)),
            'privileges_required' => 'required|string|in:' . implode(',', array_keys(CvssVector::PRIVILEGES_REQUIRED)),
            'user_interaction' => 'required|string|in:' . implode(',', array_keys(CvssVector::USER_INTERACTIONS)),
            'scope' => 'required|string|in:' . implode(',', array_keys(CvssVector::SCOPE_OPTIONS)),
            'confidentiality_impact' => 'required|string|in:' . implode(',', array_keys(CvssVector::IMPACT_METRICS)),
            'integrity_impact' => 'required|string|in:' . implode(',', array_keys(CvssVector::IMPACT_METRICS)),
            'availability_impact' => 'required|string|in:' . implode(',', array_keys(CvssVector::IMPACT_METRICS)),
            'target_id' => 'required|uuid|exists:targets,id',
            'attachments' => 'sometimes|array|max:' . self::ATTACHMENT_MAX_FILES,
            'attachments.*' => 'file|max:' . self::ATTACHMENT_MAX_SIZE_KB,
        ]);

        $target = Target::where('id', $validated['target_id'])
            ->whereHas('asset', fn($q) => $q->where('project_id', $project->id))
            ->first();

        if (!$target) {
            throw ValidationException::withMessages([
                'target_id' => 'The selected target does not belong to this project.',
            ]);
        }

        $this->authorize('view', $target);

        $cvssVector = $this->resolveCvssVector($validated);
        $uploadedFiles = Arr::wrap($request->file('attachments'));
        $this->guardAttachments($uploadedFiles);

        $finding = new Finding();
        $finding->project_id = $project->id;
        $finding->target_id = $target->id;
        $finding->cvss_vector_id = $cvssVector->id;
        $finding->title = $validated['title'];
        $finding->status = $validated['status'];
        $finding->description = $validated['description'];
        $finding->recommendation = $validated['recommendation'] ?? null;
        $finding->save();

        foreach ($uploadedFiles as $file) {
            if (is_null($file) || !$file->isValid()) {
                continue;
            }

            $storedPath = $file->store("findings/{$finding->id}", 'local');

            $finding->attachments()->create([
                'disk' => 'local',
                'path' => $storedPath,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);
        }

        return redirect()
            ->route('projects.targets.show', [$project, $target])
            ->with('success', 'Finding created successfully.');
    }

    public function show(Project $project, Target $target, Finding $finding): Response
    {
        $this->authorize('view', $finding);

        $finding->loadMissing(['cvssVector', 'attachments', 'target.asset']);

        return Inertia::render('Findings/Show', [
            'project' => [
                'id' => $project->id,
                'title' => $project->title,
            ],
            'target' => [
                'id' => $target->id,
                'label' => $target->label,
                'endpoint' => $target->endpoint,
                'links' => [
                    'view' => route('projects.targets.show', [$project, $target]),
                ],
            ],
            'finding' => [
                'id' => $finding->id,
                'title' => $finding->title,
                'status' => $finding->status,
                'description' => $finding->description,
                'recommendation' => $finding->recommendation,
                'cvss' => [
                    'score' => (float) ($finding->cvssVector->base_score ?? 0),
                    'severity' => ucfirst($finding->cvssVector->base_severity ?? 'unknown'),
                    'vector' => $finding->cvssVector->vector_string ?? 'N/A',
                ],
                'attachments' => $finding->attachments->map(fn ($attachment) => [
                    'id' => $attachment->id,
                    'original_name' => $attachment->original_name,
                    'size' => $attachment->size,
                    'links' => [
                        'download' => route('findings.attachments.download', [$finding, $attachment]),
                    ],
                ])->values(),
            ],
        ]);
    }

    public function downloadAttachment(Finding $finding, FindingAttachment $attachment)
    {
        $this->authorize('view', $finding);

        if ($attachment->finding_id !== $finding->id) {
            abort(404);
        }

        if (!Storage::disk($attachment->disk)->exists($attachment->path)) {
            abort(404, 'Attachment not found.');
        }

        return Storage::disk($attachment->disk)->download(
            $attachment->path,
            $attachment->original_name ?? basename($attachment->path)
        );
    }

    /**
     * @param array<int, \Illuminate\Http\UploadedFile|null> $files
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    private function guardAttachments(array $files): void
    {
        foreach ($files as $file) {
            if (is_null($file)) {
                continue;
            }

            if (!in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES, true)) {
                throw ValidationException::withMessages([
                    'attachments' => 'One or more files use a disallowed MIME type.',
                ]);
            }

            $extension = strtolower($file->getClientOriginalExtension());
            if ($extension !== '' && !in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
                throw ValidationException::withMessages([
                    'attachments' => 'One or more files use a disallowed file extension.',
                ]);
            }
        }
    }

    private function resolveCvssVector(array $validated): CvssVector
    {
        return CvssVector::findOrCreateFromMetrics([
            'attack_vector' => $validated['attack_vector'],
            'attack_complexity' => $validated['attack_complexity'],
            'privileges_required' => $validated['privileges_required'],
            'user_interaction' => $validated['user_interaction'],
            'scope' => $validated['scope'],
            'confidentiality_impact' => $validated['confidentiality_impact'],
            'integrity_impact' => $validated['integrity_impact'],
            'availability_impact' => $validated['availability_impact'],
        ]);
    }
}
