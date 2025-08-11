@extends('layouts.app')

@section('content')
<h1>Edit Task</h1>
<form method="post" action="{{ route('projects.tasks.update', [$project, $task]) }}">
    @csrf
    @method('PUT')
    <label>Title
        <input type="text" name="title" value="{{ old('title', $task->title) }}">
    </label><br>
    <label>Description
        <textarea name="description">{{ old('description', $task->description) }}</textarea>
    </label><br>
    <label>Status
        <select name="status">
            @foreach(['todo','in_progress','review','done'] as $st)
                <option value="{{ $st }}" @selected(old('status', $task->status)===$st)>{{ ucfirst(str_replace('_',' ', $st)) }}</option>
            @endforeach
        </select>
    </label><br>
    <label>Priority
        <input type="number" name="priority" value="{{ old('priority', $task->priority) }}">
    </label><br>
    <label>Assignee ID
        <input type="number" name="assignee_id" value="{{ old('assignee_id', $task->assignee_id) }}">
    </label><br>
    <label>Due At
        <input type="date" name="due_at" value="{{ old('due_at', optional($task->due_at)->format('Y-m-d')) }}">
    </label><br>
    <button type="submit">Save</button>
</form>
<form method="post" action="{{ route('projects.tasks.destroy', [$project, $task]) }}">
    @csrf
    @method('DELETE')
    <button type="submit">Delete</button>
</form>

<h2>Comments</h2>
<ul>
    @foreach($comments as $comment)
        <li><strong>{{ $comment->user->name }}</strong> ({{ $comment->created_at->format('Y-m-d H:i') }}): {{ $comment->body }}</li>
    @endforeach
</ul>

<form method="post" action="{{ route('projects.tasks.comments.store', [$project, $task]) }}">
    @csrf
    <textarea name="body">{{ old('body') }}</textarea>
    <button type="submit">Add Comment</button>
</form>
@endsection
