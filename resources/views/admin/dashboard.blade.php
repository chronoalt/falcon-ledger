@extends("layouts.dashboard")

@section("title", "Admin Dashboard")

@section("content")
    <h1>Administrator Dashboard</h1>
    <div class="mt-3">
        <a href="{{ route('projects.index') }}" class="btn btn-primary">View Projects</a>
    </div>
@endsection
