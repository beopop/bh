@extends('layouts.app')

@section('content')
<h1>{{ $client->name }}</h1>
<p>Status: {{ $client->status }}</p>
<p>Email: {{ $client->contact_email }}</p>
<p>Phone: {{ $client->contact_phone }}</p>
<p>Tags: {{ implode(', ', $client->tags ?? []) }}</p>
<p>Notes: {{ $client->notes }}</p>

<h2>Timeline</h2>
<ul>
@foreach ($client->activities as $activity)
    <li>{{ $activity->created_at }} - {{ $activity->description }}</li>
@endforeach
</ul>

<form method="post" action="{{ route('clients.activities.store', $client) }}">
    @csrf
    <input type="text" name="description" placeholder="New activity">
    <button type="submit">Add</button>
</form>

<a href="{{ route('clients.edit', $client) }}">Edit</a>
<form method="post" action="{{ route('clients.destroy', $client) }}" style="display:inline">
    @csrf
    @method('DELETE')
    <button type="submit">Delete</button>
</form>
@endsection
