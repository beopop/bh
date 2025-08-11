@extends('layouts.app')

@section('content')
<h1>{{ $project->name }}</h1>
<p>Status: {{ $project->status }}</p>
<p>Priority: {{ $project->priority }}</p>
<p>{{ $project->description }}</p>

<div class="kanban">
@foreach(['todo','in_progress','review','done'] as $st)
    <div class="kanban-column">
        <h3>{{ ucfirst(str_replace('_',' ', $st)) }}</h3>
        @foreach(($tasks[$st] ?? []) as $task)
            <div class="kanban-item">
                <a href="{{ route('projects.tasks.edit', [$project, $task]) }}">{{ $task->title }}</a>
            </div>
        @endforeach
    </div>
@endforeach
</div>

<h3>Add Task</h3>
<form method="post" action="{{ route('projects.tasks.store', $project) }}">
    @csrf
    <label>Title
        <input type="text" name="title" value="{{ old('title') }}">
    </label>
    <label>Status
        <select name="status">
            @foreach(['todo','in_progress','review','done'] as $st)
                <option value="{{ $st }}" @selected(old('status')===$st)>{{ ucfirst(str_replace('_',' ', $st)) }}</option>
            @endforeach
        </select>
    </label>
    <button type="submit">Add</button>
</form>
@endsection
