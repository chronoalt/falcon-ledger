@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <h1>{{ $project->title }}</h1>
        <p>{{ $project->description }}</p>
        <p><strong>Status:</strong> {{ $project->status }}</p>
        <p><strong>Due Date:</strong> {{ $project->due_at }}</p>

        <hr>

        <h2>Assets</h2>
        {{-- Assets section will be implemented later --}}

        <hr>

        <h2>Notes</h2>
        {{-- Notes section will be implemented later --}}

        <a href="{{ route('projects.index') }}" class="btn btn-secondary">Back to Projects</a>
    </div>
@endsection
