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

class FindingController extends Controller
{
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

    public function create(Project $project)
    {
        $project->load(['assets.targets']);

        $preselectedTargetId = request()->query('target_id');

        return view('findings.create', [
            'project' => $project,
            'statuses' => Finding::STATUS_OPTIONS,
            'attackVectors' => CvssVector::ATTACK_VECTORS,
            'attackComplexities' => CvssVector::ATTACK_COMPLEXITIES,
            'privilegesRequired' => CvssVector::PRIVILEGES_REQUIRED,
            'userInteractions' => CvssVector::USER_INTERACTIONS,
            'scopeOptions' => CvssVector::SCOPE_OPTIONS,
            'impactMetrics' => CvssVector::IMPACT_METRICS,
            'assets' => $project->assets,
            'preselectedTargetId' => $preselectedTargetId,
            'attachmentLimit' => self::ATTACHMENT_MAX_FILES,
            'attachmentMaxSizeMb' => (int) ceil(self::ATTACHMENT_MAX_SIZE_KB / 1024),
            'allowedExtensions' => self::ALLOWED_EXTENSIONS,
        ]);
    }

    public function store(Request $request, Project $project)
    {
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
            'target_id' => 'required|uuid',
            'attachments' => 'sometimes|array|max:' . self::ATTACHMENT_MAX_FILES,
            'attachments.*' => 'file|max:' . self::ATTACHMENT_MAX_SIZE_KB,
        ]);

        $target = $this->resolveTarget($project, $validated['target_id']);
        $cvssVector = $this->resolveCvssVector($validated);

        $uploadedFiles = Arr::wrap($request->file('attachments'));
        $this->guardAttachments($uploadedFiles);

        $finding = $target->findings()->create([
            'title' => $validated['title'],
            'status' => $validated['status'],
            'description' => $validated['description'],
            'recommendation' => $validated['recommendation'] ?? null,
            'cvss_vector_id' => $cvssVector->id,
        ]);

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

    public function show(Project $project, Target $target, Finding $finding)
    {
        $this->assertFindingBelongsToTarget($finding, $target);
        $this->assertTargetBelongsToProject($target, $project);

        $finding->loadMissing(['cvssVector', 'attachments', 'target.asset']);

        return view('findings.show', [
            'project' => $project,
            'target' => $target,
            'finding' => $finding,
        ]);
    }

    public function downloadAttachment(Finding $finding, FindingAttachment $attachment)
    {
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

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    private function resolveTarget(Project $project, string $targetId): Target
    {
        $target = Target::query()
            ->where('id', $targetId)
            ->whereHas('asset', function ($query) use ($project) {
                $query->where('project_id', $project->id);
            })
            ->first();

        if (!$target) {
            throw ValidationException::withMessages([
                'target_id' => 'Selected target does not belong to this project.',
            ]);
        }

        return $target;
    }

    /**
     * @param array<string, mixed> $validated
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    private function resolveCvssVector(array $validated): CvssVector
    {
        $vector = CvssVector::query()
            ->where('attack_vector', $validated['attack_vector'])
            ->where('attack_complexity', $validated['attack_complexity'])
            ->where('privileges_required', $validated['privileges_required'])
            ->where('user_interaction', $validated['user_interaction'])
            ->where('scope', $validated['scope'])
            ->where('confidentiality_impact', $validated['confidentiality_impact'])
            ->where('integrity_impact', $validated['integrity_impact'])
            ->where('availability_impact', $validated['availability_impact'])
            ->first();

        if (!$vector) {
            throw ValidationException::withMessages([
                'cvss' => 'Unable to find a CVSS score for the selected metrics.',
            ]);
        }

        return $vector;
    }

    private function assertFindingBelongsToTarget(Finding $finding, Target $target): void
    {
        if ($finding->target_id !== $target->id) {
            abort(404);
        }
    }

    private function assertTargetBelongsToProject(Target $target, Project $project): void
    {
        if ($target->asset->project_id !== $project->id) {
            abort(404);
        }
    }
}
