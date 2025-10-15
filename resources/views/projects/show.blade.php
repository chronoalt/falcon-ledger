@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <h1>{{ $project->title }}</h1>
        <p>{{ $project->description }}</p>
        <p><strong>Status:</strong> {{ $project->status }}</p>
        <p><strong>Due Date:</strong> {{ $project->due_at }}</p>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <p><strong>We couldn’t complete your last action:</strong></p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <hr>

        <h2>Assets &amp; Targets</h2>
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="mb-0">Add Asset</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('projects.assets.store', $project) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="asset-name">Name</label>
                        <input type="text" name="name" id="asset-name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="asset-address">Scope / Address (optional)</label>
                        <input type="text" name="address" id="asset-address" class="form-control" value="{{ old('address') }}">
                    </div>
                    <div class="form-group">
                        <label for="asset-detail">Description (optional)</label>
                        <textarea name="detail" id="asset-detail" class="form-control" rows="3">{{ old('detail') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">Create Asset</button>
                </form>
            </div>
        </div>

        @forelse ($project->assets as $asset)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="mb-0">{{ $asset->name }}</h3>
                        @if ($asset->address)
                            <p class="mb-0"><strong>Scope:</strong> {{ $asset->address }}</p>
                        @endif
                    </div>
                    <div class="text-end">
                        <a href="{{ route('projects.assets.edit', [$project, $asset]) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('projects.assets.destroy', [$project, $asset]) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this asset? This will also delete its targets and findings.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @if ($asset->detail)
                        <p>{{ $asset->detail }}</p>
                    @endif

                    <div class="mb-4">
                        <h4>Add Target</h4>
                        <form action="{{ route('assets.targets.store', $asset) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="target-label-{{ $asset->id }}">Label</label>
                                <input type="text" name="label" id="target-label-{{ $asset->id }}" class="form-control" value="{{ old('label') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="target-endpoint-{{ $asset->id }}">Endpoint</label>
                                <input type="text" name="endpoint" id="target-endpoint-{{ $asset->id }}" class="form-control" value="{{ old('endpoint') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="target-description-{{ $asset->id }}">Description (optional)</label>
                                <textarea name="description" id="target-description-{{ $asset->id }}" class="form-control" rows="3">{{ old('description') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-secondary mt-2">Create Target</button>
                        </form>
                    </div>

                    @if ($asset->targets->isEmpty())
                        <p class="text-muted">No targets defined for this asset yet.</p>
                    @else
                        @foreach ($asset->targets as $target)
                            <div class="card mb-3">
                                <div class="card-header d-flex justify-content-between align-items-start">
                                    <div>
                                        <h4 class="mb-0">{{ $target->label }}</h4>
                                        <p class="mb-0"><strong>Endpoint:</strong> {{ $target->endpoint }}</p>
                                    </div>
                                    <div class="text-end">
                                        <a href="{{ route('assets.targets.edit', [$asset, $target]) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        <form action="{{ route('assets.targets.destroy', [$asset, $target]) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this target? This will also delete its findings.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if ($target->description)
                                        <p>{{ $target->description }}</p>
                                    @endif

                                    <p class="mb-2">
                                        <strong>Findings:</strong> {{ $target->findings_count ?? 0 }}
                                    </p>

                                    <a href="{{ route('projects.targets.show', [$project, $target]) }}" class="btn btn-primary btn-sm">View Findings</a>
                                    <a href="{{ route('projects.findings.create', ['project' => $project, 'target_id' => $target->id]) }}" class="btn btn-outline-primary btn-sm">Add Finding</a>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        @empty
            <p class="text-muted">No assets registered for this project.</p>
        @endforelse

        <hr>

        <h2>Notes</h2>
        {{-- Notes section will be implemented later --}}

        <a href="{{ route('projects.index') }}" class="btn btn-secondary">Back to Projects</a>
    </div>
@endsection
