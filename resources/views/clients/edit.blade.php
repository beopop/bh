@extends('layouts.app')

@section('content')
<h1>Edit Client</h1>

<form method="post" action="{{ route('clients.update', $client) }}">
    @csrf
    @method('PUT')
    <div><input type="text" name="name" value="{{ old('name', $client->name) }}" placeholder="Name"></div>
    <div>
        <select name="status">
            @foreach(['lead','active','paused','archived'] as $st)
                <option value="{{ $st }}" @selected(old('status', $client->status)===$st)>{{ ucfirst($st) }}</option>
            @endforeach
        </select>
    </div>
    <div><input type="email" name="contact_email" value="{{ old('contact_email', $client->contact_email) }}" placeholder="Email"></div>
    <div><input type="text" name="contact_phone" value="{{ old('contact_phone', $client->contact_phone) }}" placeholder="Phone"></div>
    <div><textarea name="notes" placeholder="Notes">{{ old('notes', $client->notes) }}</textarea></div>
    <div><input type="text" name="tags" value="{{ old('tags', implode(', ', $client->tags ?? [])) }}" placeholder="Tags"></div>
    <button type="submit">Update</button>
</form>
@endsection
