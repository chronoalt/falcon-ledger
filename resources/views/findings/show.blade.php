@extends('layouts.dashboard')

@section('title', 'Finding Details')

@section('content')
    <div class="container">
        <h1>{{ $finding->title }}</h1>
        <p class="text-muted">
            Target: {{ $target->label }} ({{ $target->endpoint }}) • Project: {{ $project->title }}
        </p>

        <div class="mb-3">
            <a href="{{ route('projects.targets.show', [$project, $target]) }}" class="btn btn-secondary btn-sm">← Back to findings</a>
        </div>

        <dl class="row">
            <dt class="col-sm-3">Status</dt>
            <dd class="col-sm-9">{{ ucfirst(str_replace('_', ' ', $finding->status)) }}</dd>

            <dt class="col-sm-3">CVSS Score</dt>
            <dd class="col-sm-9">{{ number_format($finding->cvssVector->base_score, 1) }} ({{ ucfirst($finding->cvssVector->base_severity) }})</dd>

            <dt class="col-sm-3">Vector String</dt>
            <dd class="col-sm-9">{{ $finding->cvssVector->vector_string }}</dd>
        </dl>

        <h3>Description</h3>
        <p>{{ $finding->description }}</p>

        @if ($finding->recommendation)
            <h3>Recommendation</h3>
            <p>{{ $finding->recommendation }}</p>
        @endif

        @if ($finding->attachments->isNotEmpty())
            <h3>Attachments</h3>
            <ul>
                @foreach ($finding->attachments as $attachment)
                    <li>
                        <a href="{{ route('findings.attachments.download', [$finding, $attachment]) }}">
                            {{ $attachment->original_name ?? 'Download attachment' }}
                        </a>
                        @if ($attachment->size)
                            <small>({{ number_format($attachment->size / 1024, 2) }} KB)</small>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
