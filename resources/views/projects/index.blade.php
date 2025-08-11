@extends('layouts.app')

@section('content')
<h1>Projects</h1>

<a href="{{ route('projects.create') }}">Create Project</a>

<ul>
@foreach ($projects as $project)
    <li><a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a> ({{ $project->status }})</li>
@endforeach
</ul>

{{ $projects->links() }}
@endsection
