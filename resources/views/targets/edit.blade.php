@extends('layouts.dashboard')

@section('title', 'Edit Target')

@section('content')
    <div class="container">
        <h1>Edit Target</h1>
        <p class="text-muted">Asset: {{ $asset->name }} • Project: {{ $project->title }}</p>

        <a href="{{ route('projects.show', $project) }}">← Back to project assets</a>

        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('assets.targets.update', [$asset, $target]) }}" method="POST" class="mt-3">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="label">Label</label>
                <input type="text" id="label" name="label" class="form-control" value="{{ old('label', $target->label) }}" required>
            </div>

            <div class="form-group">
                <label for="endpoint">Endpoint</label>
                <input type="text" id="endpoint" name="endpoint" class="form-control" value="{{ old('endpoint', $target->endpoint) }}" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4">{{ old('description', $target->description) }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
@endsection
