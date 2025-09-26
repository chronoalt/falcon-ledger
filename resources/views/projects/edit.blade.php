@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <h1>Edit Project</h1>
        <form action="{{ route('projects.update', $project) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ $project->title }}">
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control">{{ $project->description }}</textarea>
            </div>
            <div class="form-group">
                <label for="due_at">Due Date</label>
                <input type="date" name="due_at" id="due_at" class="form-control" value="{{ $project->due_at ? $project->due_at->format('Y-m-d') : '' }}">
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="Active" {{ $project->status == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ $project->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="Completed" {{ $project->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
@endsection
