@extends('layouts.dashboard')

@section('title', 'Target Findings')

@section('content')
    <div class="container">
        <h1>{{ $target->label }}</h1>
        <p class="text-muted">Endpoint: {{ $target->endpoint }}</p>
        <p class="text-muted">Asset: {{ $target->asset->name }} • Project: {{ $project->title }}</p>

        <div class="mb-3">
            <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary btn-sm">← Back to assets</a>
            <a href="{{ route('projects.findings.create', ['project' => $project, 'target_id' => $target->id]) }}" class="btn btn-primary btn-sm">Add Finding</a>
        </div>

        @if ($target->description)
            <div class="mb-4">
                <h3>Description</h3>
                <p>{{ $target->description }}</p>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($target->findings->isEmpty())
            <p class="text-muted">No findings have been recorded for this target yet.</p>
        @else
            <div class="list-group">
                @foreach ($target->findings as $finding)
                    <a href="{{ route('projects.targets.findings.show', [$project, $target, $finding]) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">{{ $finding->title }}</h5>
                            <small>Updated {{ $finding->updated_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-1">
                            <strong>CVSS:</strong> {{ number_format($finding->cvssVector->base_score, 1) }} ({{ ucfirst($finding->cvssVector->base_severity) }})
                            • <strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $finding->status)) }}
                        </p>
                        <small>Vector: {{ $finding->cvssVector->vector_string }}</small>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
