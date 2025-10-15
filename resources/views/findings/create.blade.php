@extends('layouts.dashboard')

@section('title', 'Create Finding')

@section('content')
    <div class="container">
        <h1>Add Finding for {{ $project->title }}</h1>

        <a href="{{ route('projects.show', $project) }}">← Back to project</a>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <p><strong>We could not save the finding:</strong></p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('projects.findings.store', $project) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    @foreach ($statuses as $statusOption)
                        <option value="{{ $statusOption }}" @selected(old('status', 'open') === $statusOption)>
                            {{ ucfirst(str_replace('_', ' ', $statusOption)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            @php
                $availableTargets = $assets->flatMap(fn ($asset) => $asset->targets);
            @endphp

            @if ($availableTargets->isEmpty())
                <div class="alert alert-warning">
                    No targets are available for this project yet. Please create assets and targets before adding findings.
                </div>
            @else
                <div class="form-group">
                    <label for="target_id">Target</label>
                    <select name="target_id" id="target_id" class="form-control" required>
                        @foreach ($assets as $asset)
                            @if ($asset->targets->isNotEmpty())
                                <optgroup label="{{ $asset->name }} {{ $asset->address ? '(' . $asset->address . ')' : '' }}">
                                    @foreach ($asset->targets as $target)
                                        <option value="{{ $target->id }}" @selected(old('target_id', $preselectedTargetId) === $target->id)>
                                            {{ $target->label }} — {{ $target->endpoint }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                </div>
            @endif

            <fieldset class="mt-4">
                <legend>CVSS v3.1 Metrics</legend>

                <div class="form-group">
                    <label for="attack_vector">Attack Vector</label>
                    <select name="attack_vector" id="attack_vector" class="form-control" required>
                        @foreach ($attackVectors as $key => $meta)
                            <option value="{{ $key }}" @selected(old('attack_vector', 'network') === $key)>
                                {{ $meta['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="attack_complexity">Attack Complexity</label>
                    <select name="attack_complexity" id="attack_complexity" class="form-control" required>
                        @foreach ($attackComplexities as $key => $meta)
                            <option value="{{ $key }}" @selected(old('attack_complexity', 'low') === $key)>
                                {{ $meta['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="privileges_required">Privileges Required</label>
                    <select name="privileges_required" id="privileges_required" class="form-control" required>
                        @foreach ($privilegesRequired as $key => $meta)
                            <option value="{{ $key }}" @selected(old('privileges_required', 'none') === $key)>
                                {{ $meta['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="user_interaction">User Interaction</label>
                    <select name="user_interaction" id="user_interaction" class="form-control" required>
                        @foreach ($userInteractions as $key => $meta)
                            <option value="{{ $key }}" @selected(old('user_interaction', 'none') === $key)>
                                {{ $meta['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="scope">Scope</label>
                    <select name="scope" id="scope" class="form-control" required>
                        @foreach ($scopeOptions as $key => $meta)
                            <option value="{{ $key }}" @selected(old('scope', 'unchanged') === $key)>
                                {{ $meta['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="confidentiality_impact">Confidentiality Impact</label>
                    <select name="confidentiality_impact" id="confidentiality_impact" class="form-control" required>
                        @foreach ($impactMetrics as $key => $meta)
                            <option value="{{ $key }}" @selected(old('confidentiality_impact', 'high') === $key)>
                                {{ $meta['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="integrity_impact">Integrity Impact</label>
                    <select name="integrity_impact" id="integrity_impact" class="form-control" required>
                        @foreach ($impactMetrics as $key => $meta)
                            <option value="{{ $key }}" @selected(old('integrity_impact', 'high') === $key)>
                                {{ $meta['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="availability_impact">Availability Impact</label>
                    <select name="availability_impact" id="availability_impact" class="form-control" required>
                        @foreach ($impactMetrics as $key => $meta)
                            <option value="{{ $key }}" @selected(old('availability_impact', 'high') === $key)>
                                {{ $meta['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </fieldset>

            <div class="form-group mt-4">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control" rows="6" required>{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label for="recommendation">Recommendation (optional)</label>
                <textarea name="recommendation" id="recommendation" class="form-control" rows="4">{{ old('recommendation') }}</textarea>
            </div>

            <div class="form-group">
                <label for="attachments">Attachments (optional)</label>
                <input type="file" name="attachments[]" id="attachments" class="form-control" multiple>
                <small class="form-text text-muted">
                    Up to {{ $attachmentLimit }} files, max {{ $attachmentMaxSizeMb }}MB each. Allowed extensions: {{ implode(', ', $allowedExtensions) }}.
                </small>
            </div>

            @if ($availableTargets->isNotEmpty())
                <button type="submit" class="btn btn-primary mt-3">Save Finding</button>
            @endif
        </form>
    </div>
@endsection
