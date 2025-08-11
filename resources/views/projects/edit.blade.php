@extends('layouts.app')

@section('content')
<h1>Edit Project</h1>
<form method="post" action="{{ route('projects.update', $project) }}">
    @csrf
    @method('PUT')
    <label>Client ID
        <input type="number" name="client_id" value="{{ old('client_id', $project->client_id) }}">
    </label><br>
    <label>Name
        <input type="text" name="name" value="{{ old('name', $project->name) }}">
    </label><br>
    <label>Description
        <textarea name="description">{{ old('description', $project->description) }}</textarea>
    </label><br>
    <label>Status
        <select name="status">
            @foreach(['planned','active','on-hold','completed','archived'] as $st)
                <option value="{{ $st }}" @selected(old('status', $project->status)===$st)>{{ ucfirst($st) }}</option>
            @endforeach
        </select>
    </label><br>
    <label>Priority
        <input type="number" name="priority" value="{{ old('priority', $project->priority) }}">
    </label><br>
    <label>Owner ID
        <input type="number" name="owner_id" value="{{ old('owner_id', $project->owner_id) }}">
    </label><br>
    <button type="submit">Save</button>
</form>
@endsection
