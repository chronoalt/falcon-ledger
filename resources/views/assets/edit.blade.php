@extends('layouts.dashboard')

@section('title', 'Edit Asset')

@section('content')
    <div class="container">
        <h1>Edit Asset</h1>
        <p class="text-muted">Project: {{ $project->title }}</p>

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

        <form action="{{ route('projects.assets.update', [$project, $asset]) }}" method="POST" class="mt-3">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $asset->name) }}" required>
            </div>

            <div class="form-group">
                <label for="address">Scope / Address</label>
                <input type="text" id="address" name="address" class="form-control" value="{{ old('address', $asset->address) }}">
            </div>

            <div class="form-group">
                <label for="detail">Description</label>
                <textarea id="detail" name="detail" class="form-control" rows="4">{{ old('detail', $asset->detail) }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
@endsection
