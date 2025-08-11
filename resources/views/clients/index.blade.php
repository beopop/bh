@extends('layouts.app')

@section('content')
<h1>Clients</h1>

<form method="get" action="{{ route('clients.index') }}">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search">
    <select name="status">
        <option value="">Any status</option>
        @foreach(['lead','active','paused','archived'] as $st)
            <option value="{{ $st }}" @selected(request('status')===$st)>{{ ucfirst($st) }}</option>
        @endforeach
    </select>
    <input type="text" name="tags" value="{{ request('tags') }}" placeholder="Tags">
    <button type="submit">Filter</button>
</form>

<a href="{{ route('clients.create') }}">Create Client</a>

<ul>
@foreach ($clients as $client)
    <li><a href="{{ route('clients.show', $client) }}">{{ $client->name }}</a> ({{ $client->status }})</li>
@endforeach
</ul>

{{ $clients->withQueryString()->links() }}
@endsection
